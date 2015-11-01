<?php
/*
Plugin Name: Might I Recommend...
Description: Would sir care to try this post? Or perhaps this one?
Version:     1.0
Author:      Cat Rymer
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

require 'similarposts.php';

class clr_post_recommendations_engine {

	function __construct() {
		add_filter( 'the_content', array($this, 'add_recommendations') );
	}

	function add_recommendations ( $content ) {
		if ( is_single() ) {
			$post_categories = $this->current_post_details();
			$recommendedpost = $this->grab_similar_posts( $post_categories );
			$content = $content . "<hr />" . "<p>" . "MIGHT I RECOMMEND..." . "</p>" . $this->my_test ( $recommendedpost );
		}
		return $content;
	}

	//get the categories for the current post
	function current_post_details() {
		$postid = get_the_ID();
		$post_categories = wp_get_post_categories( $postid );
		return $post_categories;
	}

	//grab the recommended post and save it as a transient
	function grab_similar_posts ( $post_categories ) {
		global $similar_posts_finder;
		$postid = strval( get_the_ID() );
		$transient = "recommendation" . $postid;
		$recommendedpost = get_transient( $transient );
		if ( $recommendedpost === false ) {
			$recommendedpost = $similar_posts_finder->recommended_post_builder ( $post_categories );
			set_transient( $transient, $recommendedpost, HOUR_IN_SECONDS );
		}
		return $recommendedpost;
	}

	//build the display of the recommended post
	function my_test ( $recommendedpost ) {
		$title = $recommendedpost->post_name; 
		$url = get_permalink( $recommendedpost->ID );
		$snippet = substr( $recommendedpost->post_content, 0, 100 );
		$recommendation = "<strong>$title - </strong><br /><em> $snippet" . "..." . " <a href='$url'>(more)</a></em>";
		return $recommendation;
	}

}

new clr_post_recommendations_engine;
