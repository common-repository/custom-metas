<?php
/**
 * This file renders the meta data fields ( meta description and meta keyword ) in the post or page editing / adding screen
 * 
 **/
	$txtCnt = $this->settings->getCharCount();
?>
<div id="authordiv" class="postbox ">
    <div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Meta Tags</span></h3>
    <div class="inside">
        <!--form name="metaForm" action="" method="post" -->
            <input value="metaEdit" type="hidden" name="metaEdit" />
        	<input type="hidden" name="textCount" id="textCount" value="<?php echo $txtCnt; ?>" />
            <table class="links-table">
                <tr>
                    <th scope="row"><label for="metaDesc">Meta description</label></th>
                    <td><textarea class="metaArea" id="metaDesc" name="metaDesc" rows="2" tabindex="7" title="<?php echo 'd_'.$slug; ?>"><?php echo $metaDesc ?></textarea><span class="metaTxtCount" id="<?php echo 'd_'.$slug; ?>"><?php echo ($txtCnt-strlen($metaDesc)); ?></span></td>
                </tr>
                <tr>
                    <th scope="row"><label for="metaKeywords">Meta keywords</label></th>
                    <td><textarea class="metaArea" id="metaKeywords" name="metaKeywords" rows="2" tabindex="8" title="<?php echo 'k_'.$slug; ?>"><?php echo $metaKeywords ?></textarea><span class="metaTxtCount" id="<?php echo 'k_'.$slug; ?>"><?php echo ($txtCnt-strlen($metaDesc)); ?></span></td>
                </tr>
            </table>
        <!--/form-->
    </div>
</div>
