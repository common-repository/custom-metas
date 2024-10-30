<?php
	// check for permalinks enabled or disabled 
	 if (! get_option('permalink_structure') ){
			echo "<div style='width:95%;' class='update-nag'>Please <a target='_blank' href='" . admin_url("options-permalink.php") . "'>Change Permalinks Settings</a> to enable the permalink updation</div>";
	}
	
	// enable popup thickbox 
	add_thickbox();
	// post type manipulation
	$currentPageNo = ( isset($_GET['paged']) && $_GET['paged'] != "")?$_GET['paged']:1;
	$txtCnt = $this->settings->getCharCount();
	$postTypes  = $this->settings->getPostTypes();
	$currentPostTypeTitle = $postTypes[$currentPostType]->label;
	$userPostTypes    = $this->settings->getUserPostTypes (); 
?>
<div class="wrap">
	<form id="custom-metas-filter" action="admin.php?page=custom-metas&amp;noheader=true" method="post">
		<div id="icon-post" class="icon32 icon32-posts-post"><br /></div><h2><?php echo $currentPostTypeTitle;?> Meta Tags<span class="loader"></span></h2>
     <?php
			$tPostNumCount = ceil($tPostCount/$this->settings->getPostPerPage());
		?>
        <input type="hidden" name="postPerPage" id="postPerPage" value="<?php echo $postPerPage; ?>" />
        <input type="hidden" name="textCount" id="textCount" value="<?php echo $txtCnt; ?>" />
        <div class="tablenav top">
       	<select name="cm_post_type" id="cm_post_type">
        	<option value="" rel="<?php echo admin_url("admin.php?page=custom-metas");?>">--Select Post Type --</option>
        <?php foreach( $userPostTypes as $key => $postSlug ):
						if( $currentPostType == $postSlug ){
							$selected = " selected =\"selected\" ";
						} else {
							$selected = "";
						}
				?>
        		<option rel="<?php echo admin_url("admin.php?page=custom-metas&amp;cm_post_type=$postSlug");?>" <?php echo $selected;?> value="<?php echo $postSlug;?>"> <?php echo $postTypes[$postSlug]->label;?></option>
        <?php endforeach;?>
        </select>
			<div class="tablenav-pages">
       <span class="displaying-num"><?php echo $tPostCount; ?> items</span>
				<span id="postLinks" class="pagination-links">
                	<a href="#" title="Go to the first page" rel="1" class="first-post disabled">&laquo;</a>
                    <a href="#" title="Go to the previous page" rel="0" class="prev-post disabled">&lsaquo;</a>
                    <span class="paging-input">
                    	<input type="text" size="2" value="<?php echo $currentPageNo;?>" name="paged" title="Current page" id="postCurrent" class="current-page" /> of <span class="total-pages"><?php echo $tPostNumCount; ?></span>
                    </span>
                    <?php if($tPostNumCount == 1) { ?>
                    <a href="#" title="Go to the next page" rel="1" class="next-post disabled">&rsaquo;</a>
                    <a href="#" title="Go to the last page" rel="1" class="last-post disabled">&raquo;</a>
                    <?php } else { ?>
                    <a href="#" title="Go to the next page" rel="2" class="next-post">&rsaquo;</a>
                    <a href="#" title="Go to the last page" rel="<?php echo $tPostNumCount; ?>" class="last-post">&raquo;</a>
                    <?php } ?>
                    <input type="hidden" name="tPostNumCount" id="tPostNumCount" value="<?php echo $tPostNumCount; ?>" />
                </span>
            </div>
			<br class="clear">
		</div>
        <br class="clear" />
        <table class="wp-list-table widefat fixed posts" id="postTable" cellspacing="0">
            <thead>
                <tr>
                    <th scope='col' id='title' class='manage-column column-title' style="">Title</th>
                    <th scope='col' id='metaDesc' class='manage-column column-mDesc' style="">Meta Description</th>
                    <th scope='col' id='metaKey' class='manage-column column-mKey' style="">Meta Keywords</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th scope='col' class='manage-column column-title' style="">Title</th>
                    <th scope='col' class='manage-column column-mDesc' style="">Meta Description</th>
                    <th scope='col' class='manage-column column-mKey' style="">Meta Keywords</th>
                </tr>
            </tfoot>
        
            <tbody>
                <?php 
                    if (count($postArr) > 0) {
                        foreach ($postArr as $post):
                            setup_postdata($post);
                            $metaDesc1 = htmlspecialchars(get_post_meta($post->ID, 'metaDesc', true));
                            $metaKeywords1 = htmlspecialchars(get_post_meta($post->ID, 'metaKeywords', true));
														$fullPermaLink = get_permalink( $post->ID );
														if (get_option('permalink_structure') ){
															$slug 	= basename( $fullPermaLink );
															$baseUrl = str_replace($slug, "", $fullPermaLink );
															$baseUrl = rtrim($baseUrl, "/") . "/";
															$permaLinkStr = "$baseUrl<input type=\"text\" value=\"$slug\" name=\"slug[]\" />";
														} else {
															$permaLinkStr = $fullPermaLink;
														}														
                ?>
                    <tr id="post-<?php echo $post->ID; ?>" class="post-<?php echo $post->ID; ?> type-post status-publish format-standard hentry category-uncategorized alternate iedit author-self" valign="top">
                        <td class="post-title page-title column-title"><strong><?php echo get_the_title($post->ID); ?></strong>
												<!-- edit perma link section -->
                      <a href="<?php echo $fullPermaLink;?>" class="thickbox" target="_blank">Preview</a> | 
                      <a onclick="addEventToClose()" href="<?php echo admin_url("post.php?post={$post->ID}&action=edit&cm_modal_window=1&TB_iframe=true&width=1000");?>" class="thickbox">Edit</a> |
                      <br clear="all" />
                      <span class="permalink">Permalink: <?php echo $permaLinkStr;?> </span>
                        
                        <!-- edit permalink section -->
                        <input type="hidden" name="pId[]" value="<?php echo $post->ID; ?>" />
                        </td>			
                        <td class="metaDesc column-mDesc"><textarea class="metaArea" name="metaDesc[]" rows="2" cols="50" title="<?php echo 'd_'.$post->post_name; ?>"><?php echo $metaDesc1; ?></textarea><span class="metaTxtCount" id="<?php echo 'd_'.$post->post_name; ?>"><?php echo ($txtCnt-strlen($metaDesc1)); ?></span></td>
                        <td class="metaKey column-mKey"><textarea class="metaArea" name="metaKey[]" rows="2" cols="50" title="<?php echo 'k_'.$post->post_name; ?>"><?php echo $metaKeywords1; ?></textarea><span class="metaTxtCount" id="<?php echo 'k_'.$post->post_name; ?>"><?php echo ($txtCnt-strlen($metaKeywords1)); ?></span></td>
                    </tr>
                <?php 
                        endforeach;
                    } else { 
                ?>
                    <tr class="post type-post status-publish format-standard hentry category-uncategorized alternate iedit author-self" valign="top">
                        <tr class="no-items"><td colspan="3" class="colspanchange">No posts found.</td></tr>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <br class="clear" />
        <p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit" /></p>
	</form>
	<br class="clear" />
</div>