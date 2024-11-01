<?php	
if ( ! defined('ABSPATH')) exit;  // if direct access


$wc_pf_settings_tab = array();

$wc_pf_settings_tab[] = array(
    'id' => 'general',
    'title' => sprintf(__('%s General','job-board-manager'),'<i class="fas fa-list-ul"></i>'),
    'priority' => 1,
    'active' => true,
);

$wc_pf_settings_tab[] = array(
    'id' => 'support',
    'title' => sprintf(__('%s Support','job-board-manager'),'<i class="far fa-copy"></i>'),
    'priority' => 2,
    'active' => false,
);

$wc_pf_settings_tab = apply_filters('wc_pf_settings_tabs', $wc_pf_settings_tab);

$tabs_sorted = array();
foreach ($wc_pf_settings_tab as $page_key => $tab) $tabs_sorted[$page_key] = isset( $tab['priority'] ) ? $tab['priority'] : 0;
array_multisort($tabs_sorted, SORT_ASC, $wc_pf_settings_tab);



wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'jquery-ui-core' );
wp_enqueue_script('jquery-ui-sortable');
wp_enqueue_style( 'settings-tabs' );
wp_enqueue_script('settings-tabs');




?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div><h2><?php echo sprintf(__('%s Settings', 'job-board-manager'), WCProductFilter_plugin_name)?></h2>
		<form  method="post" action="<?php echo str_replace( '%7E', '~', esc_url_raw($_SERVER['REQUEST_URI'])); ?>">
	        <input type="hidden" name="wc_pf_hidden" value="Y">
            <?php
            if(!empty($_POST['wc_pf_hidden'])){

                $nonce = sanitize_text_field($_POST['_wpnonce']);

                if(wp_verify_nonce( $nonce, 'wc_pf_nonce' ) && $_POST['wc_pf_hidden'] == 'Y') {


                    // wcpf_recursive_sanitize_arr
                    // sanitize_text_field
                    $wc_pf_posts_per_page = isset($_POST['wc_pf_posts_per_page']) ?  sanitize_text_field($_POST['wc_pf_posts_per_page']) : '';
                    update_option('wc_pf_posts_per_page', $wc_pf_posts_per_page);


                    $wc_pf_hide_input_fields = isset($_POST['wc_pf_hide_input_fields']) ?  wcpf_recursive_sanitize_arr($_POST['wc_pf_hide_input_fields']) : '';
                    update_option('wc_pf_hide_input_fields', $wc_pf_hide_input_fields);



                    do_action('wc_pf_settings_save');

                    ?>
                    <div class="updated notice  is-dismissible"><p><strong><?php _e('Changes Saved.', 'job-board-manager' ); ?></strong></p></div>

                    <?php
                }
            }
            ?>
            <div class="settings-tabs vertical has-right-panel">

                <div class="settings-tabs-right-panel">
                    <?php
                    foreach ($wc_pf_settings_tab as $tab) {
                        $id = $tab['id'];
                        $active = $tab['active'];

                        ?>
                        <div class="right-panel-content <?php if($active) echo 'active';?> right-panel-content-<?php echo $id; ?>">
                            <?php

                            do_action('wc_pf_settings_tabs_right_panel_'.$id);
                            ?>

                        </div>
                        <?php

                    }
                    ?>
                </div>

                <ul class="tab-navs">
                    <?php
                    foreach ($wc_pf_settings_tab as $tab){
                        $id = $tab['id'];
                        $title = $tab['title'];
                        $active = $tab['active'];
                        $data_visible = isset($tab['data_visible']) ? $tab['data_visible'] : '';
                        $hidden = isset($tab['hidden']) ? $tab['hidden'] : false;
                        ?>
                        <li <?php if(!empty($data_visible)):  ?> data_visible="<?php echo $data_visible; ?>" <?php endif; ?> class="tab-nav <?php if($hidden) echo 'hidden';?> <?php if($active) echo 'active';?>" data-id="<?php echo $id; ?>"><?php echo $title; ?></li>
                        <?php
                    }
                    ?>
                </ul>



                <?php
                foreach ($wc_pf_settings_tab as $tab){
                    $id = $tab['id'];
                    $title = $tab['title'];
                    $active = $tab['active'];
                    ?>

                    <div class="tab-content <?php if($active) echo 'active';?>" id="<?php echo $id; ?>">
                        <?php
                        do_action('wc_pf_settings_tabs_content_'.$id, $tab);
                        ?>


                    </div>

                    <?php
                }
                ?>

            </div>

            <div class="clear clearfix"></div>
            <p class="submit">
                <?php wp_nonce_field( 'wc_pf_nonce' ); ?>
                <input class="button button-primary" type="submit" name="Submit" value="<?php _e('Save Changes','job-board-manager' ); ?>" />
            </p>
		</form>
</div>
