<?php
/**
 * Main class file for managing meta data pages
 * @author yuaraj1986
 * @authorEmail yuva@codelooms.com
 *
 **/
class CustomMeta {
	/***
	 * settings variable 
	 */
	 public $settings;
	 
	/***
	 * db instance 
	 */
	 public $db;
	 
	 /***
	 * base file path 
	 */
	 public $filePath;
	 
	 /** constructor **/
	 public function __construct(){
	 		global $wpdb;
	 		$this->settings = new CustomMetaSettings();
			$this->db 			= $wpdb;
			$this->filePath = 'custom-metas/index.php';
			
			// make some wordpress hooks 
			$this->initiateHooks();
	 }
	 
	/**
	 * Initiate needed hooks for the plugin
	 **/
	 private function initiateHooks(){
			add_action('wp_head', array($this, 'add_wp_head'), 1);
			add_action('admin_init', array($this, 'add_admin_head'), 1);
			
			add_action('simple_edit_form', array($this, 'meta_form_fields'), 5);
			add_action('edit_form_advanced', array($this, 'meta_form_fields'), 5);
			add_action('edit_page_form', array($this, 'meta_form_fields'), 5);
			
			add_action('edit_post', array($this, 'updateMeta'), 5);
			add_action('publish_post', array($this, 'updateMeta'), 5);
			add_action('save_post', array($this, 'updateMeta'), 5);
			add_action('edit_page_form', array($this, 'updateMeta'), 5);	
			
			// admin menu 
			add_action("admin_menu", array( $this, "adminMenu") ); 	
			
			// hide the toolbar for popups 
			add_action("admin_head", array($this, "hideLeftMenu"));
			
			// redirection with cm_modal_window param in popup up save
			add_filter('redirect_post_location', array($this, "redirectToReferer"));
			
			// Plugin row meta update
			add_filter('plugin_row_meta', array($this, 'pluginRowMeta'), 10, 2);
	 }
	/**
	 * Add needed css and js files for admin section for custom meta plugin
	 * hooked with admin_init
	 */
	public function add_admin_head() {
		if(is_admin()){
			wp_enqueue_script('admin_script', plugins_url('custom-metas/js/scripts.js'), array('jquery'));
			wp_enqueue_style('admin_style', plugins_url('custom-metas/css/cl-custom-meta.css'));
			// post type will be used for ajax operations on paging
			$currentPostType = ($_GET['cm_post_type'])?$_GET['cm_post_type']:$this->settings->getDefaultPostType();
			$gVar = array('plugin_url' => plugins_url(''), "currentPostType" => $currentPostType);
    		wp_localize_script( 'admin_script', 'gVar', $gVar );
		}	
	} //add_admin_head function ends here
	
	/**
	 * hide the left side menu bar using stylesheet for the modal window
	 */
	public function hideLeftMenu(){
		if( is_admin() && isset($_GET['cm_modal_window']) ){
	?>
		<style type="text/css">
        #wpcontent, #footer { margin-left: 10px; }
				#adminmenu, #adminmenu .wp-submenu, #adminmenuback, #adminmenuwrap, #wpadminbar, #wpfooter, .add-new-h2{
					display:none !important;
				}				
    </style>
        <script type="text/javascript">
        jQuery(document).ready( function($) {
            $('#adminmenuback, #adminmenuwrap').remove();
        });     
        </script>	
		<?php
		}
	}
	public function redirectToReferer( $location ){
		if( is_admin() ){ // for admin only 
			// check referer field contains cm_modal_window
			parse_str(parse_url( $_POST['_wp_http_referer'], PHP_URL_QUERY ), $queryArray );
			if( $queryArray['cm_modal_window'] == 1 ){
				// add_query_arg( array('cm_modal_window', "1"), $location );
				if( strpos($location, "?") === false ){ // check alreay any querystring
					$location .= "?cm_modal_window=1";
				} else {
					$location .= "&cm_modal_window=1";
				}
			}
		}
		return $location;
	}
	/**
	 * Add meta data for the posts 
	 */
	function add_wp_head() {	
		global $post;
?>
      <meta name="description" content="<?php echo $this->getCustomMeta( $post->ID, 'metaDesc' ); ?>" /> 
      <meta name="keywords" content="<?php echo $this->getCustomMeta( $post->ID, 'metaKeywords' ); ?>" /> 
<?php		
	} //add_wp_head function ends here

	/**
	 *
	 */
	function meta_form_fields() {
	    global $post;
	    $metaDesc = htmlspecialchars(get_post_meta($post->ID, 'metaDesc', true));
	    $metaKeywords = htmlspecialchars(get_post_meta($post->ID, 'metaKeywords', true));
			$slug = $post->post_name;
			include_once(CM_PLUGINS_TPL_PATH . '/meta-data-form-single.php');
	} //post_fields function ends here
	
	/**
	 * save meta data in the post / page screen
	 **/
	function updateMeta($postId) {
	    $metaEdit = $_POST["metaEdit"];
	    if (isset($metaEdit) && !empty($metaEdit)) {
		    $metaDesc = stripslashes($_POST["metaDesc"]);
		    $metaKeywords = stripslashes($_POST["metaKeywords"]);
 
 			// Save meta desc. and keyword
		    if (isset($metaDesc) && !empty($metaDesc)) {
				// Delete meta desc, if already exist
		    	delete_post_meta($postId, 'metaDesc');
			    add_post_meta($postId, 'metaDesc', $metaDesc);
		    }
		    if (isset($metaKeywords) && !empty($metaKeywords)) {
				// Delete meta keyword, if already exist
		    	delete_post_meta($postId, 'metaKeywords');
			    add_post_meta($postId, 'metaKeywords', $metaKeywords);
		    }
	    }
			//print_r( $_POST );
			//exit;
	} //customize_metas function ends here
	
	/**
	 * function for getting the meta data based on the post id 
	 */
	public function getCustomMeta( $postId = NULL, $metaType = NULL ){
		// get default meta data for some purpose
		$defaultMetaDesc  =  $this->settings->getDefaultMetaDesc();
		$defaultMetaKeywords = $this->settings->getDefaultMetaKeyword();
		$globalMetaOverride = $this->settings->getOverrideMeta();

		switch( $globalMetaOverride ){
			case 'default':
			if( $metaType == "metaDesc" ){
				$metaData = $defaultMetaDesc;
			}
			if( $metaType == "metaKeywords" ){
				$metaData = $defaultMetaKeywords; 
			}
			break;
			case 'own':
				$metaData = get_post_meta( $postId, $metaType, true); 
			break;
			case 'both':
			default:
				$metaData = get_post_meta( $postId, $metaType, true);
				if( $metaData == "" && $metaType == "metaDesc" ){
					$metaData = $defaultMetaDesc;
				}
				
				if( $metaData == "" && $metaType == "metaKeywords" ){
					$metaData = $defaultMetaKeywords;
				}
		}

		return $metaData;
	}	
	/***
	 * add menu to admin
	 */ 
	public function adminMenu() {
		global $pagenow;
		$iconUrl = plugin_dir_url( CM_PLUGIN_FILE ) . 'css/images/icon-small.png';		
		$pluginPage = add_menu_page('Custom Metas', 'Custom Metas', 'edit_posts', 'custom-metas', array($this, 'showPost'), $iconUrl, 30); 
		$pluginSubPage = add_submenu_page('custom-metas', 'Settings', 'Settings', 'edit_posts', 'custom-metas-settings', array($this->settings, 'show'));
		add_action('admin_footer-'.$pluginPage, array($this->settings, 'admin_footer'));
		add_action('admin_footer-'.$pluginSubPage, array($this->settings, 'admin_footer'));
		
		if( $pagenow == 'plugins.php' ) {
			add_action( 'in_plugin_update_message-' . $this->filePath, array($this, 'in_plugin_update_message'));
		}
	} // Hook to register new page for plugin
	
	/**
	 * show the posts based on the post type and save also taking place here 
	 */
	public function showPost( ) {
		// Save all meta tag values at one time
		if (isset($_POST['submit']) && (trim($_POST['submit']) == 'Save Changes')) {
			$pIds = $_POST['pId'];
			if(count($pIds) > 0) {
				foreach ($pIds as $k => $pId):
					$metaDesc = stripslashes($_POST["metaDesc"][$k]);
					$metaKeywords = stripslashes($_POST["metaKey"][$k]);
					
					// Save meta desc. and keyword
					if (isset($metaDesc) && !empty($metaDesc)) {
						// Delete meta desc, if already exist
						delete_post_meta($pId, 'metaDesc');
						add_post_meta($pId, 'metaDesc', $metaDesc);
					}
					if (isset($metaKeywords) && !empty($metaKeywords)) {
						// Delete meta keyword, if already exist
						delete_post_meta($pId, 'metaKeywords');
						add_post_meta($pId, 'metaKeywords', $metaKeywords);
					}
					
					// update the slug 
					$this->updateSlug( $_POST['slug'][$k], $pId ); 
				endforeach;
			}
			// redirect to avoid refresh page post sending again 
			// check for post type and page id 
			$extraQueryString = NULL;
			if( isset($_POST['cm_post_type'] )){
				$extraQueryString .= "&cm_post_type=" . $_POST['cm_post_type'];
			}

			if( $_POST['paged'] > 1 ){
				$extraQueryString .= "&paged=" . $_POST['paged'];
			}
			wp_redirect( admin_url("admin.php?page=custom-metas" . $extraQueryString) );
		}
		
		// Fetch all Published Posts
		$postPerPage = $this->settings->getPostPerPage();
		// if it is not coming from querystring, we will get it from settings
		$currentPostType = ($_GET['cm_post_type'])?$_GET['cm_post_type']:$this->settings->getDefaultPostType();
		
		$postArg = array('posts_per_page' => -1, 'offset' => 0, 'orderby' => 'ID', 'order' => 'ASC', 'post_type' => $currentPostType, 'post_status' => 'publish');
		$tPostCount = count(get_posts($postArg));
		// get dynamic offset if the user edits in mid of the pages 
		$args1 = array('posts_per_page' => $postPerPage, 'offset' => 0, 'orderby' => 'ID', 'order' => 'ASC', 'post_type' => $currentPostType, 'post_status' => 'publish');
		$postArr = get_posts($args1);
		include_once(CM_PLUGINS_TPL_PATH . '/meta-data-form-multiple.php');
	} // Custom Meta page Content goes here

	/**
	 * update the slug for post 
	 * @param [new slug], @param [post id]
	 */
	private function updateSlug( $newSlug, $postId ){
		global $wpdb;

		$post = get_post( $postId );
		$newSlug = sanitize_title( $newSlug );		
		if( $newSlug != $post->post_name ){ // generate new slug if user gives different than the existing slug
			$clean_slug = wp_unique_post_slug( $newSlug, $post->ID, $post->post_status, $post->post_type, $post->post_parent );
			$sqlSlugUpdate = "UPDATE " . $wpdb->posts. " SET post_name = '" . $clean_slug . "' " . 'WHERE ID = ' . $postId;
			$wpdb->query( $sqlSlugUpdate );
		}
	}
	
	/**
	 * Get plugin update message from readme.txt change log
	 */
	public function in_plugin_update_message( $plugin_data, $r ) {
		$version = apply_filters('acf/get_info', 'version');
		$readme = wp_remote_fopen( 'http://plugins.svn.wordpress.org/custom-metas/trunk/readme.txt' );
		$regexp = '/== Changelog ==(.*)= ' . $version . ' =/sm';
		$o = '';
		
		if( !$readme ) {
			return;
		}
	
		preg_match( $regexp, $readme, $matches );
	
		if( !isset($matches[1]) ) {
			return;
		}
	
		$changelog = explode('*', $matches[1]);
		array_shift( $changelog );
	
		if( !empty($changelog) ) {
			$o .= '<div class="acf-plugin-update-info">';
			$o .= '<h3>' . __("What's new", 'acf') . '</h3>';
			$o .= '<ul>';
	
			foreach( $changelog as $item ) {
				$item = explode('http', $item);
				$o .= '<li>' . $item[0];
				if( isset($item[1]) ) {
					$o .= '<a href="http' . $item[1] . '" target="_blank">' . __("credits",'acf') . '</a>';
				}
				$o .= '</li>';
			}
			$o .= '</ul></div>';
		}
		echo $o;
	}
	
	/**
	 * Addition of links related to the plugin
	 * @param  array  $links The existing plugin info links
	 * @param  string $file  The plugin the links are for
	 */
	public function pluginRowMeta( $links, $file ) {
		// plugin row meta only to this plugin
		if ( $file !== $this->filePath  )
			return $links;

		/* array_merge appends the links to the end */
		return array_merge( $links, array(
			'<a href="https://wordpress.org/plugins/custom-metas/" title="Visit Wordpress Custom Metas plugin page" target="_blank">About</a>',
			'<a href="https://wordpress.org/plugins/custom-metas/stats/" title="Download Statistics of Custom Metas plugin" target="_blank">Stats</a>',
			'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KJJMZRBXNWTZL" title="Support Custom Metas plugin" target="_blank" class="button">Donate Link</a>'
		) );
	}
	
} // 
?>