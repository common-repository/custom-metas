// JavaScript Document
var $j = jQuery.noConflict();
$j(document).ready(function() {
	// onchange event for post change
	$j("#cm_post_type").change(function(e){
		window.location.href = $j("#cm_post_type option:selected").attr("rel");
	});
	// Avoid form submitting by pressing enter key
	$j(document).on("keypress", 'form#custom-metas-filter', function (e) {
		var code = e.keyCode || e.which;
		//console.log(e.target.id);
		if ((code == 13) && ((e.target.id == 'postCurrent') || (e.target.id == 'pageCurrent'))) {
			e.preventDefault();
			return false;
		}
	});
	
	// Ajax pagination for Posts
	$j('#postLinks a').click(function(event) {
		event.preventDefault();
		var current  = current = parseInt($j(this).attr('rel'));
		if($j(this).hasClass('disabled') == false) {
			showPost( current );
		}
	});
	
	// Ajax pagination for Posts
	$j('input#postCurrent').keyup(function(event) {
		var current = parseInt($j(this).val());
		current = (current < 1) ? 1 : ((current > tPostNumCount) ? tPostNumCount : current);
		if((event.keyCode == 13) && ($j(this).hasClass('disabled') == false)) {
			showPost( current );
		}
	});

	// Text counter for all textarea
	$j('textarea.metaArea').live('keyup', function(event) {
		//event.preventDefault();
		var value = $j(this).val(), totalCnt = parseInt($j('#textCount').val()), length = value.length, id = $j(this).attr('title');
		
		if(totalCnt >= length){
			$j('#'+id).html(totalCnt-length);
			return true;
		} else {
			$j(this).val(value.substring(0, totalCnt));
			$j('#'+id).html(totalCnt-length);
			return true;
		}	
		return false;
		
	});
	
	// if the user edits mid of the pages,navigate to them when page realoads
	if( parseInt($j('input#postCurrent').val()) > 1){
		showPost( parseInt($j('input#postCurrent').val() ) );
	}
	
	// popup window close should replace parent 
	// close popup window refereshes the page
	// alert( $j(".TB_overlayBG").attr("id") );
	
});

/*** function to render content from ajax page */
function showPost( current ){
	var postPerPage = $j('#postPerPage').val(),  tPostNumCount = $j('#tPostNumCount').val(), offset = (current-1) * postPerPage, oldHtml = $j('#postTable tbody').html(), html = '', nextNum = current+1, prevNum = current-1, totalCnt = parseInt($j('#textCount').val());
		
	showLoader();
	var req = $j.ajax({
					url: gVar.plugin_url+'/custom-metas/ajax/ajax.php',
					type: 'POST',
					data: {getData:1, postType:gVar.currentPostType, postPerPage:postPerPage, offset:offset}
				});
	req.done(function(response){
		hideLoader();
		if(response){
			var data = $j.parseJSON(response);
			var k = '';
			for(k in data) {
				html += '<tr id="post-'+data[k]['ID']+'" class="post-'+data[k]['ID']+' type-post status-publish format-standard hentry category-uncategorized alternate iedit author-self" valign="top">';
				html += '	<td class="post-title page-title column-title"><strong>'+data[k]['post_title']+'</strong>';
				html += '<a href="' + data[k]['previewlink']+ '" target="_blank">Preview</a> | ';
				html += '<a onclick="addEventToClose()" href="' + data[k]['editlink'] + '" class="thickbox">Edit</a> | ';
				html += '<br clear="all" />';
				html += '<span class="permalink">Permalink: ' + data[k]['permalink']+ ' </span>';
				html += '<input type="hidden" name="pId[]" value="'+data[k]['ID']+'" /></td>';
				html += '	<td class="metaDesc column-mDesc"><textarea class="metaArea" name="metaDesc[]" rows="2" cols="50" title="d_'+data[k]['post_name']+'">'+data[k]['metaDesc']+'</textarea><span class="metaTxtCount" id="d_'+data[k]['post_name']+'">'+(totalCnt-parseInt(data[k]['metaDesc'].length))+'</span></td>';
				html += '	<td class="metaKey column-mKey"><textarea class="metaArea" name="metaKey[]" rows="2" cols="50" title="k_'+data[k]['post_name']+'">'+data[k]['metaKeywords']+'</textarea><span class="metaTxtCount" id="k_'+data[k]['post_name']+'">'+(totalCnt-parseInt(data[k]['metaKeywords'].length))+'</span></td>';
				html +='</tr>';
			}
			$j('#postTable tbody').html(html);
			$j('a.first-post, a.prev-post, a.next-post, a.last-post').removeClass('disabled');
			$j('a.prev-post').attr('rel', prevNum);
			$j('a.next-post').attr('rel', nextNum);
			$j('#postCurrent').val(current);
			if(prevNum == 0) {
				$j('a.first-post').addClass('disabled');
				$j('a.prev-post').addClass('disabled');
			}
			if(nextNum >= (parseInt(tPostNumCount)+1)) {
				$j('a.next-post').attr('rel', parseInt(tPostNumCount));
				$j('a.next-post').addClass('disabled');
				$j('a.last-post').addClass('disabled');
			}
		}
	});
	
}
/** function for showing loader **/
function showLoader(){
	jQuery(".loader").css("display", "inline-block");	
}

function hideLoader(){
	jQuery(".loader").hide();	
}

function addEventToClose(){
		setTimeout(function(){
			$j(".tb-close-icon, .TB_overlayBG").click(function(){ showPost( parseInt($j('input#postCurrent').val() )) });
		}, 100 );
}