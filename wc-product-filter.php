<?php
/*
Plugin Name: PickPlugins Product Filter for WooCommerce
Plugin URI: https://www.pickplugins.com/item/woocommerce-product-filter
Description: Advance filter & search for shop page to find product easily.
Version: 1.0.9
WC requires at least: 3.0.0
WC tested up to: 5.5
Author: PickPlugins
Author URI: http://pickplugins.com
Text Domain: wc-product-filter
Domain Path: /languages
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class WCProductFilter{
	
	public function __construct(){

		define('WCProductFilter_plugin_url', plugins_url('/', __FILE__)  );
		define('WCProductFilter_plugin_dir', plugin_dir_path( __FILE__ ) );
        define('WCProductFilter_plugin_name', 'Product Filter' );

        require_once( WCProductFilter_plugin_dir . 'includes/class-settings-tabs.php');


        require_once( WCProductFilter_plugin_dir . 'includes/class-settings.php');
        require_once( WCProductFilter_plugin_dir . 'includes/functions.php');
        require_once( WCProductFilter_plugin_dir . 'includes/class-widget-wc-product-filter.php');
        require_once( WCProductFilter_plugin_dir . 'includes/class-wc-product-filter-shortcodes.php');
        require_once( WCProductFilter_plugin_dir . 'templates/WCProductFilter/WCProductFilter-hook.php');
        require_once( WCProductFilter_plugin_dir . 'includes/functions-settings.php');



		add_action( 'wp_enqueue_scripts', array( $this, 'WCProductFilter_front_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'WCProductFilter_admin_scripts' ) );
		
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ));

        add_action( 'widgets_init', array( $this, 'widget_register' ) );
		add_filter('widget_text', 'do_shortcode');
	}
	

	
	public function load_textdomain() {


        $locale = apply_filters( 'plugin_locale', get_locale(), 'wc-product-filter' );
        load_textdomain('wc-product-filter', WP_LANG_DIR .'/wc-product-filter/wc-product-filter-'. $locale .'.mo' );

        load_plugin_textdomain( 'wc-product-filter', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );

		}
    public function widget_register() {
        register_widget( 'WidgetWCProductFilter' );
    }
	
	public function WCProductFilter_install(){
		
		do_action( 'WCProductFilter_action_install' );
		
		}		
		
	public function WCProductFilter_uninstall(){
		
		do_action( 'WCProductFilter_action_uninstall' );
		}		
		
	public function WCProductFilter_deactivation(){
		
		do_action( 'WCProductFilter_action_deactivation' );
		}
	
	
	public function WCProductFilter_front_scripts(){



        wp_register_style('wc_product_filter', plugins_url( 'assets/frontend/css/style.css', __FILE__ ));
        wp_register_script('wc_product_filter', plugins_url( '/assets/frontend/js/scripts.js' , __FILE__ ) , array( 'jquery' ));
        wp_register_style('jquery-ui', plugins_url( 'assets/global/css/jquery-ui.css', __FILE__ ));




		}

	public function WCProductFilter_admin_scripts(){

        wp_register_style('settings-tabs', plugins_url( 'assets/admin/css/settings-tabs.css', __FILE__ ));
        wp_register_script('settings-tabs', plugins_url( '/assets/admin/js/settings-tabs.js' , __FILE__ ) , array( 'jquery' ));




		}

	}

new WCProductFilter();
