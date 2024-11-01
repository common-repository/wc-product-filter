<?php
/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access

class class_wc_product_filter_shortcodes{

	function __construct() {

        add_shortcode( 'WCProductFilter', array( $this, 'display_WCProductFilter' ) );
	}



    public function display_WCProductFilter($atts, $content = null ) {
        $atts = shortcode_atts(
            array(
                'id' => "",

            ), $atts);

        $html = '';
        $post_id = $atts['id'];

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script('wc_product_filter');
        wp_localize_script('wc_product_filter', 'wc_pf_admin_ajax', array( 'wc_pf_admin_ajaxurl' => admin_url( 'admin-ajax.php')));

        wp_enqueue_style( 'wc_product_filter' );
        wp_enqueue_style( 'jquery-ui' );

        ob_start();
        include WCProductFilter_plugin_dir.'/templates/WCProductFilter/WCProductFilter.php';
        //echo $html;
        return ob_get_clean();
        //return $html;

    }






}

new class_wc_product_filter_shortcodes();