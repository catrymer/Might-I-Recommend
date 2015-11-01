<?php

class clr_similar_posts_finder {

	function recommended_post_builder ( $post_categories ) {

		$category_matches = $this->category_post_grabber( $post_categories );
		$recommendedpost = $this->scoring( $post_categories, $category_matches );
		return $recommendedpost;

	}

	//grab an array of all posts that have at least one category that matches a category from the current post
	function category_post_grabber( $post_categories ) {

		$args = array( "posts_per_page" => -1, "category__in" => $post_categories );
		$category_matches = get_posts( $args );
		return $category_matches;

	}

	//calculate a score for each post based on category matches to the current post; highest score wins
	function scoring( $post_categories, $category_matches ) {

		$top_score = 0;
		$postid = get_the_ID();
		foreach( $category_matches as $post ) {
			$current_score = 0;
			$match_post_categories = wp_get_post_categories( $post->ID );
			foreach( $match_post_categories as $match_post_cat ) {
				if( in_array( $match_post_cat, $post_categories ) ) {
					++$current_score;
				}
			}
			if( $current_score > $top_score && $postid != $post->ID ) {
				$top_score = $current_score;
				$recommendedpost = $post;
			}
		}
		if( empty( $recommendedpost ) ) {
			$recommendedpost = current( get_posts( 'orderby=rand&posts_per_page=1' ) );
		}
		return $recommendedpost;
	}

}

$similar_posts_finder = new clr_similar_posts_finder;
