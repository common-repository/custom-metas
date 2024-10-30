<?php
	// Intialize wordpress stuffs in ajax file for below operations
	require_once('../../../../wp-load.php');
	
	if (!is_user_logged_in()) {
		$res = array('error' => 'You Must Be Logged In to Access This page');
		echo json_encode($res);
		exit;
	}
	if(!current_user_can('edit_posts')) {
		$res = array('error' => 'Sorry you are not authorized to access this file');
		echo json_encode($res);
		exit;
	}
	
	extract($_POST);
	if($getData==1) {
		$args1 = array('posts_per_page' => $postPerPage, 'offset' => $offset, 'orderby' => 'ID', 'order' => 'ASC', 'post_type' => $postType, 'post_status' => 'publish');
		$postArr = get_posts($args1);
		$finalRes = '';
		$i = 0;
		foreach($postArr as $post):
            setup_postdata($post);
			$metaDesc1 = htmlspecialchars(get_post_meta($post->ID, 'metaDesc', true));
			$metaKeywords1 = htmlspecialchars(get_post_meta($post->ID, 'metaKeywords', true));
			$finalRes[$i]['ID'] = $post->ID;
			$finalRes[$i]['post_title'] = get_the_title($post->ID);
			$finalRes[$i]['post_name'] = $post->post_name;
			$finalRes[$i]['metaDesc'] = $metaDesc1;
			$finalRes[$i]['metaKeywords'] = $metaKeywords1;
			$fullPermaLink = get_permalink( $post->ID );
			if (get_option('permalink_structure') ){
				$slug 	= basename( $fullPermaLink );
				$baseUrl = str_replace($slug, "", $fullPermaLink );
				$baseUrl = rtrim($baseUrl, "/") . "/";
				$permaLinkStr = "$baseUrl<input type=\"text\" value=\"$slug\" name=\"slug[]\" />";
			} else {
				$permaLinkStr = $fullPermaLink;
			}		
			$finalRes[$i]['permalink'] = $permaLinkStr;		
			$finalRes[$i]['editlink'] = admin_url("post.php?post={$post->ID}&action=edit&cm_modal_window=1&TB_iframe=true&width=1000");		
			$finalRes[$i]['previewlink'] = $fullPermaLink;		
			$i++;
		endforeach;
		echo json_encode($finalRes);
	}
?>