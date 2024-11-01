<?php
if ( ! defined('ABSPATH')) exit;  // if direct access


//add_action( 'woocommerce_settings_tabs', 'wc_pf_add_custom_setting_tab' );
//
//function wc_pf_add_custom_setting_tab() {
//    //link to custom tab
//    $current_tab = ( isset( $_GET['tab']) && $_GET['tab'] == 'wc_pf' ) ? 'nav-tab-active' : '';
//    echo "<a href='admin.php?page=wc-settings&tab=custom' class='nav-tab $current_tab'>".__( 'Products Filter', 'domain' )."</a>";
//}
//
//
//
//add_action( 'woocommerce_settings_custom', 'wc_pf_custom_tab_content' );
//function wc_pf_custom_tab_content() {
//    // content
//}


add_action('wc_pf_settings_tabs_content_general', 'wc_pf_settings_tabs_content_general');

function wc_pf_settings_tabs_content_general(){

    $pickp_settings_tabs_field = new pickp_settings_tabs_field();
    $wc_pf_posts_per_page = get_option('wc_pf_posts_per_page');
    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');


    $input_fields = array(
        'keyword'=>'Keyword',
        'price_range'=>'Price range',
        'categories'=>'Categories',
        'size'=>'Attributes: Size',
        'color'=>'Attributes: Color',
        'tags'=>'Tags',
        'order'=>'Order',
        'orderby'=>'Orderby',
        'onsale'=>'Onsale',
        'stock'=>'Stock',
        'sku'=>'SKU',
        'submit'=>'Submit',
    );

    $input_fields = apply_filters('wc_pf_input_fields', $input_fields);


    ?>
    <div class="section">
        <div class="section-title"><?php echo __('General settings', 'job-board-manager'); ?></div>
        <p class="description section-description"><?php echo __('Customize setting for general options.', 'job-board-manager'); ?></p>

        <?php

        $args = array(
            'id'		=> 'wc_pf_posts_per_page',
            //'parent'		=> '',
            'title'		=> __('Posts per page ','job-board-manager'),
            'details'	=> __('Set number to display posts per page','job-board-manager'),
            'type'		=> 'text',
            'value'		=> $wc_pf_posts_per_page,
            'default'		=> 10,
        );

        $pickp_settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'wc_pf_hide_input_fields',
            //'parent'		=> '',
            'title'		=> __('Hide input fields','job-board-manager'),
            'details'	=> __('Choose which input fields you want to hide.','job-board-manager'),
            'type'		=> 'checkbox',
            'label_style'		=> 'display:block', //inline, block
            'value'		=> $wc_pf_hide_input_fields,
            'default'		=> array(),
            'args'		=> $input_fields,
        );

        $pickp_settings_tabs_field->generate_field($args);


        ?>


    </div>
    <?php



}


add_action('wc_pf_settings_tabs_content_support', 'wc_pf_settings_tabs_content_support');

function wc_pf_settings_tabs_content_support(){

    ?>
    <div class="section">
        <div class="section-title"><?php echo __('Help & Support', 'job-board-manager'); ?></div>
        <p class="description section-description"><?php echo __('Feel free to contact with us if you have any issues.', 'job-board-manager'); ?></p>


        <div class="setting-field">
            <div class="field-lable">Get support</div>
            <div class="field-input">
                <a href="https://pickplugins.com/forum/" class="button">Create support ticket</a>

                <p >Post on our form for free.</p>

                <a href="https://www.pickplugins.com/documentation/wc-product-filter/?ref=wordpress.org" class="button">Documentation</a>

                <p >Want to customize please read our documentation first.</p>


            </div>
        </div>

    </div>
    <?php


}