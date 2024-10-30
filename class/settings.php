<?php
class CustomMetaSettings{
	/**
	 * character count allowed for textarea
	 */
	private $charCount = 200;
	
	/**
	 * default Meta Data keyword
	 */
	private $defaultMetaDataKeyword;

	/**
	 * default Meta Data Description
	 */
	private $defaultMetaDataDesc;	
	/**
	 * meta data override status 
	 * available modes default, own, both
	 * default => This will ignore the own meta data for Post/Page
	 * own 		 => This will ignore the Default Meta data for all Post/Page
	 * both	   => Default Meta Data for Null Meta Values and Own Meta data for Post/Page
	 */ 
	private $overrideMeta = "both";
	
	/**
	 * number of posts / pages per page for pagination
	 */
	private $postPerPage = 5;
	
  /***
	 * default post type to show when custom meta is opening
	 * default valus is 'page'
	 */
	private $defaultPostType = 'page';
	
	/**
	 * whether to show developer ads or not in admin footer
	 */
	public $showAds = true;


 /***
  * user selected post types 
	*/
	private $userPostTypes = array("page", "post");

  public function __construct(){
	}
	
	
	/**
	 * getter and setter for the settings parameters
	 * getter will get the value using get_option
	 * setter will update the value using update_option
	 */
	/*** common getter setter **/
	 private function getter( $key, $default = NULL ){
	 		// option key => cm_post_per_page
			$value = get_option( $key );
			if( $value == ""){ // if the option not set, the default value will be used 
				$value = $default;
			}
			return $value;	 		
	 }
	 private function setter( $key, $value ){
	 update_option($key, $value);
	 		/*if( trim($value) != "" ){
	 			update_option($key, $value);
			}	*/ 		
	 }
	 /**** post per page getter and setter **/
	 public function getPostPerPage(){
	 	return $this->getter( "cm_post_per_page", $this->postPerPage );
	 }
	 
	 public function setPostPerPage( $value ){
	 		$this->setter( "cm_post_per_page", $value );
	 }
	 
	 /*** char count getter and setter **/
	 public function getCharCount(){
		return $this->getter( "cm_txt_cnt", $this->charCount );
	 }
	 
	 public function setCharCount( $value ){
	 		$this->setter( "cm_txt_cnt", $value );
	 }	 
	 
	 /*** default meta data ***/
	 public function getDefaultMetaKeyword(){
		return $this->getter( "cm_default_meta_keywords", $this->defaultMetaKeyword );
	 }
	 
	 public function setDefaultMetaKeyword( $value ){
	 		$this->setter( "cm_default_meta_keywords", $value );
	 }	
	 
	 public function getDefaultMetaDesc(){
		return $this->getter( "cm_default_meta_desc", $this->defaultMetaDesc );
	 }
	 
	 public function setDefaultMetaDesc( $value ){
	 		$this->setter( "cm_default_meta_desc", $value );
	 }		 	 
	 
	 public function getOverrideMeta(){
		return $this->getter( "cm_custom_meta_override", $this->overrideMeta );
	 }
	 
	 public function setOverrideMeta( $value ){
	 		$this->setter( "cm_custom_meta_override", $value );
	 }	
	 
	 public function getShowAds (){
	 		return $this->getter( "cm_show_ads", (int) $this->showAds );
	 } 	 
	 
	 public function setShowAds( $value ){
	 		$this->setter( "cm_show_ads", $value );
	 }
	 
	 /***
	  * get default post type 
		*/
	 public function getDefaultPostType(){
		return $this->getter( "cm_default_post_type", $this->defaultPostType );
	 }
	 
	 public function setDefaultPostType( $value ){
	 		$this->setter( "cm_default_post_type", $value );
	 }
	 
	 /***
	  * geter and setter for user post types
		*/		
	 public function getUserPostTypes(){
		return $this->getter( "cm_user_post_types", $this->userPostTypes );
	 }
	 
	 public function setUserPostTypes( $value ){
	 		$this->setter( "cm_user_post_types", $value );
	 }		
		
		
	 
	 /***
	 * get post types 
	 */
	 public function getPostTypes( $userSelected = false ){
	 		if( !$userSelected ){
					$postType = '';
					$postType1 = get_post_types(array('public' => true, '_builtin' => true),  "object");
					$postType2 = get_post_types(array('public' => true, '_builtin' => false),  "object");
					if(count($postType1) > 0) {
						foreach($postType1 as $key => $value):
							$postType[$key] = $value;
						endforeach;
					}
					if(count($postType2) > 0) {
						foreach($postType2 as $key => $value):
							$postType[$key] = $value;
						endforeach;
					}
					return $postType;
			} 
			
			// get ehe user selected post types from option
			return $this->getUserPostTypes();
	 }
			
	/**
	 * show the settings page 
	 */
	public function show() {
		// Save all meta tag values at one time
		if (isset($_POST['submit']) && (trim($_POST['submit']) == 'Save Changes')) {
			// update the count
			if( is_numeric($_POST["txtCount"]) && (int) $_POST["txtCount"] != 0 ){ // 0 gives always problem, so block it here
				$this->setCharCount( $_POST['txtCount'] );
			}	
			// update post per page 
			if( is_numeric($_POST["postPerPage"]) && (int) $_POST["postPerPage"] != 0 ){ // 0 gives always problem, so block it here
				$this->setPostPerPage( $_POST["postPerPage"] );
			}
		
			// save other values 
			$this->setDefaultMetaKeyword( $_POST['defaultMetaKeywords'] );
			$this->setDefaultMetaDesc( $_POST['defaultMetaDescription'] );
			$this->setOverrideMeta( $_POST['customMetaOverride'] );
			$this->setUserPostTypes( $_POST['userPostTypes'] );
			//$this->setUserPostTypes( array("page", "post") );
			$this->setDefaultPostType( $_POST['defaultPostType'] );
			
			// update default post type 
			
		
		}
		
		// show the settings form 
		include_once(CM_PLUGINS_TPL_PATH . '/settings.php');
	} // Custom Meta page Content goes here
	
	/**
	 * show some google ads for custom meta developers 
	 */
	public function admin_footer(){
?>
        <div style="width:500px; margin:0 auto;">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Big twt banner in Joomla creators -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:468px;height:60px"
                 data-ad-client="ca-pub-8343970849137803"
                 data-ad-slot="7131194437"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
        <br clear="all" />
        <br clear="all" />        
<?php
	}	 
}
?>