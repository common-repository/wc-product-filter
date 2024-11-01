<?php
/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access

class WidgetWCProductFilter extends WP_Widget {

	function __construct() {
		
		parent::__construct( 'widget_WCProductFilter', __('WC Product Filter', 'wc-product-filter'), array( 'description' => __( 'Display WooCommerce Product Filter', 'wc-product-filter' ), ) );
	}

	public function widget( $args, $instance ) {
		
		$title 			= apply_filters( 'widget_title', $instance['title'] );




		echo $args['before_widget'];
		if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
		echo do_shortcode("[WCProductFilter]");
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		
		$title 			= isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'WC Product Filter', 'wc-product-filter' );

		echo "<p>";
		echo "<label for='{$this->get_field_id( 'title' )}'>".__('Title','wc-product-filter')." : </label>";
		echo "<input class='widefat' id='{$this->get_field_id( 'title' )}' name='{$this->get_field_name( 'title' )}' type='text' value='{$title}' />";
		echo "</p>";
		

	}
	
	public function update( $new_instance, $old_instance ) {
		
		$instance = array();
		$instance['title'] 			= isset( $new_instance['title'] ) 			? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}