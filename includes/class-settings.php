<?php
if ( ! defined('ABSPATH')) exit;  // if direct access


class class_wc_pf_settings{
	

    public function __construct(){

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );
    }
	

	
	
	public function admin_menu() {



        add_submenu_page( 'edit.php?post_type=product', __( 'Product Filter', 'job-board-manager' ), __( 'Product Filter', 'job-board-manager' ), 'manage_options', 'wc_pf_settings', array( $this, 'wc_pf_settings' ) );


		do_action( 'wc_pf_action_admin_menus' );
		
	}


	public
    function wc_pf_settings(){
		
		include( 'menu/settings.php' );
		}







	}


new class_wc_pf_settings();

