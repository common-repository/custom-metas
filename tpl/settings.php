<?php
	$txtCnt = $this->getCharCount();
	$cmDefaultMetaKeyword = $this->getDefaultMetaKeyword();
	$cmDefaultMetaDescription = $this->getDefaultMetaDesc();
	$cmCustomMetaOverride = $this->getOverrideMeta();
	$userPostTypes = $this->getUserPostTypes();
	$postTypes = $this->getPostTypes();
	$currentPostType = $this->getDefaultPostType();
?>
<div class="wrap settings">
	<form id="custom-metas-filter" action="" method="post">
		<div class="icon32" id="icon-options-general"><br></div><h2>Settings</h2><br />
    		<h3> General </h3>
        <table class="form-table">
            <tbody>
            	<tr valign="top">
                    <th scope="row"><label for="postPerPage">Number of Post per Page</label></th>
                    <td><input type="text" class="regular-text" value="<?php echo $this->getPostPerPage();?>" id="postPerPage" name="postPerPage" />
                    </td>
              </tr>
            	<tr valign="top">
                    <th scope="row"><label for="">Post Types</label>
                    </th>
                    <td>
                    		<?php foreach( $postTypes as $post_slug => $postType ):
												// check already saved 
												$checked = "";
												if( in_array( $post_slug, $userPostTypes )){ $checked = "checked = \"checked\"";}
												// disable post and page option from unchcking 
												$disabled = $hiddenForDisabled = $nameSuffix = "";
												
												if( $post_slug == "post" || $post_slug == "page" ){ 
													$disabled = "disabled = \"disabled\"";
													$nameSuffix = "disabled";
													echo $hiddenForDisabled = "<input type=\"hidden\" value=\"{$post_slug}\" name=\"userPostTypes[]\" />";	
												}
												?>
                        	<input title="<?php echo $postType->label;?>" <?php echo $checked . $disabled;?> type="checkbox" value="<?php echo $post_slug;?>" name="userPostTypes<?php echo $nameSuffix;?>[]" class="user-post-type" id="userPostType_<?php echo $post_slug;?>"/>
                          <label for="userPostType_<?php echo $post_slug;?>"><?php echo $postType->label;?></label>&nbsp;&nbsp;
                        <?php endforeach;?>
												<p class="description">All the available post types are shown here, you can tell to custom meta to which post type it can customize meta </p>                        
                    </td>
              </tr>
            	<tr valign="top">
                    <th scope="row"><label for="defaultPostType">Default Post Type</label>
                    </th>
                    <td>
                    	<select name="defaultPostType" id="defaultPostType">
                    	<?php foreach( $userPostTypes as $key => $userPostType ):
													$selected = "";
													if( $userPostType  == $currentPostType ){
														$selected = "selected=\"selected\" ";
													}
											?>
                      	<option <?php echo $selected;?> value="<?php echo $userPostType;?>"><?php echo $postTypes[$userPostType]->label;?></option>
                      <?php endforeach;?>
                      </select>
                    <p class="description">Custom metas open this post type by default, You can still make a choice there</p>                      
                    </td>
              </tr>                            
           </tbody>
         </table>        
        <hr/>
        <h3> Meta Data </h3>
        
        <table class="form-table">
            <tbody>
            	<tr valign="top">
                    <th scope="row"><label for="txtCount">Meta Characters limit</label></th>
                    <td><input type="text" class="regular-text" value="<?php echo $txtCnt; ?>" id="txtCount" name="txtCount" />
                    		<input type="hidden" name="textCount" id="textCount" value="<?php echo $txtCnt; ?>" />
                    </td>
                </tr>
            	<tr valign="top">
                    <th scope="row"><label for="txtCount">Default Meta keywords</label></th>
                    <td><textarea cols="50" rows="2" class="metaArea" value="<?php echo $defaultMetaKeywords; ?>" id="defaultMetaKeywords" name="defaultMetaKeywords" title="defaultMetaKeywordCount"><?php echo $cmDefaultMetaKeyword;?></textarea><br />
											<span class="metaTxtCount" id="defaultMetaKeywordCount"><?php echo $txtCnt;?></span>                    
                    </td>
              </tr>
            	<tr valign="top">
                    <th scope="row"><label for="txtCount">Default Meta description</label></th>
                    <td><textarea cols="50" rows="2" class="metaArea"  value="<?php echo $defaultMetaDescription; ?>" id="defaultMetaDescription" name="defaultMetaDescription" title="defaultMetaDescriptionCount"><?php echo $cmDefaultMetaDescription;?></textarea><br />
                    <span class="metaTxtCount" id="defaultMetaDescriptionCount"><?php echo $txtCnt;?></span>
                    </td>
              </tr>
                                
            	<tr valign="top">
                    <th scope="row"><label for="txtCount">Meta Data Override</label></th>
                    <td>
                    	<input <?php echo ( $cmCustomMetaOverride == "default" )?' checked="checked"':'';?> type="radio" name="customMetaOverride" value="default" id="customMetaOverrideDefault"/>
                      <label for="customMetaOverrideDefault">Use Default Meta Data for all Posts/Pages</label><p class="description">This will ignore the own meta data for Post/Page</p>
                      <input <?php echo ( $cmCustomMetaOverride == "own" )?' checked="checked"':'';?> type="radio" value="own" name="customMetaOverride" id="customMetaOverrideOwn"/>
                      <label for="customMetaOverrideOwn">Use Own Post/Page Meta Data</label><p class="description">This will ignore the Default Meta data for all Post/Page</p>
                      <input <?php echo ( $cmCustomMetaOverride == "both" )?' checked="checked"':'';?> type="radio" value="both" name="customMetaOverride" id="customMetaOverrideBoth"/>
                      <label for="customMetaOverrideBoth">Both</label><p class="description">Default Meta Data for Null Meta Values and Own Meta data for Post/Page</p>
                    </td>
                </tr>                
            </tbody>
            <tfoot>
            	<tr><td></td>
              <td>        <p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit" /></p>
							</td>
            </tfoot>
				</table>
	</form>
	<br class="clear" />
</div>

<script>
$j = jQuery.noConflict();
$j(".user-post-type").change(function(){
	var checkBoxValue = $j(this).val();
	var checkBoxTitle = $j(this).attr("title");
	if( $j(this).is(":checked") ){ //  add an option if user  checked
		var newOption = "<option value='" + checkBoxValue + "'>" + checkBoxTitle + "</option>";
		$j("#defaultPostType").append( newOption );
	} else { // renmove the option when user unchecked
		$j("#defaultPostType option[value='" + checkBoxValue+ "']").remove();
	}
});
</script>