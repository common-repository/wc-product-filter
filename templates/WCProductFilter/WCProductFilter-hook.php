<?php
if ( ! defined('ABSPATH')) exit;  // if direct access




function WCProductFilter_pre_get_posts_query( $query_args ) {

    //var_dump($query_args);

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :"";


    if($WCProductFilter):

        $keyword = isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) :"";
        $_product_cat = isset($_GET['_product_cat']) ? wcpf_recursive_sanitize_arr($_GET['_product_cat']) :"";
        $_product_tag = isset($_GET['_product_tag']) ? sanitize_text_field($_GET['_product_tag']) :"";
        $_price = isset($_GET['_price']) ? sanitize_text_field($_GET['_price']) :"";

        $_attr_color = isset($_GET['_attr_color']) ? wcpf_recursive_sanitize_arr($_GET['_attr_color']) :"";
        $_attr_size = isset($_GET['_attr_size']) ? wcpf_recursive_sanitize_arr($_GET['_attr_size']) :"";


        $_order = isset($_GET['_order']) ? sanitize_text_field($_GET['_order']) :"";
        $_orderby = isset($_GET['_orderby']) ? sanitize_text_field($_GET['_orderby']) :"";
        $_onsale = isset($_GET['_onsale']) ? sanitize_text_field($_GET['_onsale']) :"";
        $_stock_status = isset($_GET['_stock_status']) ? sanitize_text_field($_GET['_stock_status']) :"";
        $_sku = isset($_GET['_sku']) ? sanitize_text_field($_GET['_sku']) :"";

        //$_product_type = isset($_GET['_product_type']) ? sanitize_text_field($_GET['_product_type']) :"";


        if($keyword){
            $query_args->set( 's', $keyword );
        }

        if($_product_cat){

            $tax_query = (array) $query_args->get( 'tax_query' );

            //var_dump($_product_cat);


            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $_product_cat,
                'operator' => 'IN'
            );

            $query_args->set( 'tax_query', $tax_query );
        }


        if($_attr_color){

            $tax_query = (array) $query_args->get( 'tax_query' );

            //var_dump($_product_cat);


            $tax_query[] = array(
                'taxonomy' => 'pa_color',
                'field' => 'slug',
                'terms' => $_attr_color,
                'operator' => 'IN'
            );

            $query_args->set( 'tax_query', $tax_query );
        }


        if($_attr_size){

            $tax_query = (array) $query_args->get( 'tax_query' );

            //var_dump($_product_cat);


            $tax_query[] = array(
                'taxonomy' => 'pa_size',
                'field' => 'slug',
                'terms' => $_attr_size,
                'operator' => 'IN'
            );

            $query_args->set( 'tax_query', $tax_query );
        }


        if($_product_tag){

            $tax_query = (array) $query_args->get( 'tax_query' );

            $_product_tag = explode(',', $_product_tag);


            $tax_query_tag = array();
            $tax_query_tag['relation'] = 'OR';

            if($_product_tag)
                foreach ($_product_tag as $tag){

                    $tax_query_tag[] = array(
                        'taxonomy' => 'product_tag',
                        'field' => 'name',
                        'terms' => $tag,
                        //'operator' => 'IN'
                    );
                }

            $tax_query = array($tax_query_tag);

            //var_dump($tax_query_tag);
            //var_dump($tax_query);

            $query_args->set( 'tax_query', $tax_query );
        }





        if($_price){

            $meta_query = (array) $query_args->get( 'meta_query' );

            $_price_array = explode('-', $_price);
            $_price_max = (int)isset($_price_array[0]) ? $_price_array[0]: 0;
            $_price_min = (int)isset($_price_array[1]) ? $_price_array[1]: 0;


            $meta_query[] =  array(
                //'relation' => 'AND',
                array(
                    'key' => '_price',
                    'value' => array($_price_max,$_price_min ),
                    'compare' => 'BETWEEN',
                    'type' => 'NUMERIC'
                ),

            );

            //echo var_export($meta_query, true);

            $query_args->set( 'meta_query', $meta_query );
        }










        if($_order){
            $query_args->set( 'order', $_order );
        }

        if($_orderby){
            //var_dump($_orderby);

            if($_orderby == 'rating'){

                $query_args->set( 'orderby', 'meta_value_num' );
                $query_args->set( 'meta_key', '_wc_average_rating' );
            }
            elseif ($_orderby == 'price'){
                $query_args->set( 'orderby', 'meta_value_num' );
                $query_args->set( 'meta_key', '_price' );
            }
            elseif ($_orderby == 'popularity'){
                $query_args->set( 'orderby', 'meta_value_num' );
                $query_args->set( 'meta_key', 'total_sales' );
            }
            else{
                $query_args->set( 'orderby', $_orderby );
            }





        }

        if($_onsale){

            $meta_query = (array) $query_args->get( 'meta_query' );

            $meta_query[] =  array(
                'relation' => 'OR',
                array( // Variable products type
                    'key'           => '_min_variation_sale_price',
                    'value'         => 0,
                    'compare'       => '>',
                    'type'          => 'numeric'
                ),
                array(
                    'key' => '_sale_price',
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            );


            $query_args->set( 'meta_query', $meta_query );
        }


        if($_stock_status){

            $tax_query = (array) $query_args->get( 'tax_query' );

            $tax_query[] = array(
                'taxonomy' => 'product_visibility',
                'field' => 'name',
                'terms' => 'outofstock',
                'operator' => 'NOT IN',
            );


            $query_args->set( 'tax_query', $tax_query );
        }

        if($_sku){

            $meta_query = (array) $query_args->get( 'meta_query' );
            $meta_query[] =
                array(
                    'key' => '_sku',
                    'value' => $_sku,
                    'compare' => 'IN'
                );

            $query_args->set( 'meta_query', $meta_query );
        }







    endif;



}
add_action( 'woocommerce_product_query', 'WCProductFilter_pre_get_posts_query',99 );



add_action('WCProductFilter_fields','WCProductFilter_field_keyword', 30);
function WCProductFilter_field_keyword(){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('keyword', $wc_pf_hide_input_fields)) return;

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :"";
    $keyword = isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) :"";

    if(!$WCProductFilter):
        $keyword = '';
    endif;


    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Keyword','wc-product-filter'); ?></label>
        </div>
        <div class="input-wrapper">
            <input type="search" placeholder="<?php echo __('Type Keyword','wc-product-filter'); ?>" name="keyword" value="<?php echo esc_attr($keyword); ?>">
        </div>
    </div>
    <?php
}




add_action('WCProductFilter_fields','WCProductFilter_field_price_range',30);
function WCProductFilter_field_price_range(){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('price_range', $wc_pf_hide_input_fields)) return;

    wp_enqueue_script('jquery-ui-slider');
    wp_enqueue_script('jquery-ui-core');

    wp_enqueue_style('jquery-ui');

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :"";
    $_price = isset($_GET['_price']) ? sanitize_text_field($_GET['_price']) :"";

    $get_shop_max_price = WCProductFilter_get_shop_max_price();
    $woocommerce_currency_symbol = get_woocommerce_currency_symbol();

    //var_dump($get_shop_max_price);


    if(!$WCProductFilter):
        $_price = '';
    endif;

    if(!empty($_price)){

        $_price_array = explode('-', $_price);
        $_price_min = isset($_price_array[0]) ? $_price_array[0]: 0;
        $_price_max = isset($_price_array[1]) ? $_price_array[1]: 0;
    }
    else{
        $_price_max = isset($get_shop_max_price[1]) ?$get_shop_max_price[1] : '100';
        $_price_min = isset($get_shop_max_price[0]) ?$get_shop_max_price[0] : '0';
    }


    $store_max_price = isset($get_shop_max_price[1]) ?$get_shop_max_price[1] : 100;
    $store_min_price = isset($get_shop_max_price[0]) ?$get_shop_max_price[0] : 0;

    $currency_symbol = $woocommerce_currency_symbol;

    ?>


    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Price range','wc-product-filter'); ?></label>
        </div>
        <div class="input-wrapper">

            <div id="price-display"></div>
            <input type="hidden" id="price" name="_price" value="<?php echo esc_attr($_price); ?>">
            <div class="range-slider" id=""></div>


        </div>
    </div>




    <script>
        jQuery(document).ready(function($) {
            $( ".range-slider" ).slider({
                range: true,
                min: <?php echo $store_min_price; ?>,
                max: <?php echo $store_max_price; ?>,
                values: [<?php echo $_price_min; ?>, <?php echo $_price_max; ?>],
                change: function( event, ui ) {
                    //console.log(ui.values[ 0 ] +'-'+ui.values[ 1 ]);

                    form = jQuery('.WCProductFilter form');
                    form_data 	= form.serializeArray();
                    paged = 1;
                    wcpf_update_product(form_data, paged);
                },
                slide: function( event, ui ) {
                    $( "#price-display" ).html( "<?php echo $currency_symbol; ?>" + ui.values[ 0 ] + "-<?php echo $currency_symbol; ?>" + ui.values[ 1 ] );
                    $( "#price" ).val( ui.values[ 0 ] + "-" + ui.values[ 1 ] );

                }
            });

            $( "#price-display" ).html( "<?php echo $currency_symbol; ?>" + $( ".range-slider" ).slider( "values", 0 ) + " - <?php echo $currency_symbol; ?>" + $( ".range-slider" ).slider( "values", 1 ) );
            $( "#price" ).val( "" + $( ".range-slider" ).slider( "values", 0 ) + "-" + $( ".range-slider" ).slider( "values", 1 ) );

        })


    </script>



    <?php
}



add_action('WCProductFilter_fields','WCProductFilter_field_categories',30);
function WCProductFilter_field_categories(){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('categories', $wc_pf_hide_input_fields)) return;

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :"";
    $_product_cat = isset($_GET['_product_cat']) ? wcpf_recursive_sanitize_arr($_GET['_product_cat']) : array();

    if(!$WCProductFilter):
        $_product_cat = array();
    endif;


    $product_cats = get_terms( array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    ) );

    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Categories','wc-product-filter'); ?></label>
        </div>
        <div class="input-wrapper">
            <select name="_product_cat[]" multiple >
                <?php

                if(!empty($product_cats)):
                    foreach ($product_cats as $product_cat){

                        $term_id = $product_cat->term_id;
                        $name = $product_cat->name;
                        $count = $product_cat->count;
                        $slug = $product_cat->slug;
                        ?>
                        <option value="<?php echo esc_attr($slug); ?>" <?php if(in_array($slug, $_product_cat)) echo 'selected';?> ><?php echo esc_html($name); ?>(<?php echo esc_html($count); ?>)</option>
                        <?php

                    }
                endif;
                ?>

            </select>
        </div>
    </div>
    <?php
}


add_action('WCProductFilter_fields','WCProductFilter_field_attr_color',30);
function WCProductFilter_field_attr_color(){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('color', $wc_pf_hide_input_fields)) return;

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :"";
    $_attr_color = isset($_GET['_attr_color']) ? wcpf_recursive_sanitize_arr($_GET['_attr_color']) : array();

    if(!$WCProductFilter):
        $_attr_color = array();
    endif;


    $product_cats = get_terms( array(
        'taxonomy' => 'pa_color',
        'hide_empty' => false,
    ) );

    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Color','wc-product-filter'); ?></label>
        </div>
        <div class="input-wrapper">

            <?php

            if(!empty($product_cats)):
                foreach ($product_cats as $product_cat){

                    $term_id = isset($product_cat->term_id) ? $product_cat->term_id : '';
                    $name = isset($product_cat->name) ? $product_cat->name : '';
                    $count = isset($product_cat->count) ? $product_cat->count : '';
                    $slug = isset($product_cat->slug) ? $product_cat->slug : '';

                    if($term_id):
                        ?>
                        <label><input type="checkbox" name="_attr_color[]" <?php if(in_array($slug, $_attr_color)) echo 'checked';?> value="<?php echo esc_attr($slug); ?>"><?php echo esc_html($name); ?>(<?php echo esc_html($count); ?>)</label><br>
                    <?php
                    endif;


                }
            endif;
            ?>


        </div>
    </div>
    <?php
}





add_action('WCProductFilter_fields','WCProductFilter_field_attr_size',30);
function WCProductFilter_field_attr_size(){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('size', $wc_pf_hide_input_fields)) return;

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :"";
    $_attr_size = isset($_GET['_attr_size']) ? wcpf_recursive_sanitize_arr($_GET['_attr_size']) : array();

    if(!$WCProductFilter):
        $_attr_size = array();
    endif;


    $product_cats = get_terms( array(
        'taxonomy' => 'pa_size',
        'hide_empty' => false,
    ) );

    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Size','wc-product-filter'); ?></label>
        </div>
        <div class="input-wrapper">

            <?php

            if(!empty($product_cats)):
                foreach ($product_cats as $product_cat){

                    $term_id = isset($product_cat->term_id) ? $product_cat->term_id : '';
                    $name = isset($product_cat->name) ? $product_cat->name : '';
                    $count = isset($product_cat->count) ? $product_cat->count : '';
                    $slug = isset($product_cat->slug) ? $product_cat->slug : '';

                    if(!empty($term_id)):
                        ?>
                        <label><input type="checkbox" name="_attr_size[]" <?php if(in_array($slug, $_attr_size)) echo 'checked';?> value="<?php echo esc_attr($slug); ?>"><?php echo esc_html($name); ?>(<?php echo esc_html($count); ?>)</label><br>
                    <?php
                    endif;


                }
            endif;
            ?>


        </div>
    </div>
    <?php
}












add_action('WCProductFilter_fields','WCProductFilter_field_tags',30);
function WCProductFilter_field_tags(){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('tags', $wc_pf_hide_input_fields)) return;

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :"";
    $_product_tag = isset($_GET['_product_tag']) ? sanitize_text_field($_GET['_product_tag']) :"";

    if(!$WCProductFilter):
        $_product_tag = '';
    endif;


    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Tags','wc-product-filter'); ?></label>
        </div>
        <div class="input-wrapper">

            <input placeholder="<?php echo __('Tag 1, Tag 2','wc-product-filter'); ?>" type="search" name="_product_tag" value="<?php echo esc_attr($_product_tag); ?>">
        </div>
    </div>
    <?php
}












add_action('WCProductFilter_fields','WCProductFilter_field_order',30);
function WCProductFilter_field_order(){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('order', $wc_pf_hide_input_fields)) return;

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :"";
    $_order = isset($_GET['_order']) ? sanitize_text_field($_GET['_order']) :"";

    if(!$WCProductFilter):
        $_order = '';
    endif;

    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Order','wc-product-filter'); ?></label>
        </div>
        <div class="input-wrapper">
            <select name="_order" >
                <option value="DESC" <?php if($_order == 'DESC') echo 'selected';?>><?php echo __('DESC','wc-product-filter'); ?></option>
                <option value="ASC" <?php if($_order == 'ASC') echo 'selected';?>><?php echo __('ASC','wc-product-filter'); ?></option>
            </select>
        </div>
    </div>
    <?php
}


add_action('WCProductFilter_fields','WCProductFilter_field_orderby',30);
function WCProductFilter_field_orderby(){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('orderby', $wc_pf_hide_input_fields)) return;

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :"";
    $_orderby = isset($_GET['_orderby']) ? sanitize_text_field($_GET['_orderby']) :"";

    if(!$WCProductFilter):
        $_orderby = '';
    endif;


    $WCProductFilter_orderby_args = array(
        'price'=>__('Product Price', 'wc-product-filter'),
        'date'=>__('Product Date', 'wc-product-filter'),
        'rating'=>__('Product Rating', 'wc-product-filter'),
        'popularity'=>__('Product Popularity', 'wc-product-filter'),
        'title'=>__('Product Title', 'wc-product-filter'),
        'rand'=>__('Randomly', 'wc-product-filter'),
        'menu_order'=>__('Menu Order', 'wc-product-filter'),
    );


    $WCProductFilter_orderby_args = apply_filters('WCProductFilter_orderby_args', $WCProductFilter_orderby_args);

    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Orderby','wc-product-filter'); ?></label>
        </div>
        <div class="input-wrapper">
            <select name="_orderby" >
                <option value=""><?php echo __('None','wc-product-filter'); ?></option>
                <?php
                foreach ($WCProductFilter_orderby_args as $arg_key=>$arg){
                    ?>
                    <option value="<?php echo esc_attr($arg_key); ?>" <?php if($_orderby == $arg_key) echo 'selected';?>><?php echo esc_html($arg); ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>
    <?php
}



add_action('WCProductFilter_fields','WCProductFilter_field_onsale',30);
function WCProductFilter_field_onsale(){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('onsale', $wc_pf_hide_input_fields)) return;

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :"";
    $_onsale = isset($_GET['_onsale']) ? sanitize_text_field($_GET['_onsale']) :"";

    if(!$WCProductFilter):
        $_onsale = '';
    endif;

    ?>
    <div class="field-wrapper">
        <div class="input-wrapper">
            <label><input type="checkbox" name="_onsale" <?php if($_onsale == '1') echo 'checked';?> value="1"><?php echo __('Display Onsale','wc-product-filter'); ?></label>
        </div>
    </div>
    <?php
}


add_action('WCProductFilter_fields','WCProductFilter_field_in_stock',30);
function WCProductFilter_field_in_stock(){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('stock', $wc_pf_hide_input_fields)) return;

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :"";
    $_stock_status = isset($_GET['_stock_status']) ? sanitize_text_field($_GET['_stock_status']) :"";

    if(!$WCProductFilter):
        $_stock_status = '';
    endif;

    ?>
    <div class="field-wrapper">

        <div class="input-wrapper">
            <label><input type="checkbox" name="_stock_status" <?php if($_stock_status == '1') echo 'checked';?> value="1"><?php echo __('In stock','wc-product-filter'); ?></label>

        </div>
    </div>
    <?php
}



add_action('WCProductFilter_fields','WCProductFilter_field_submit',30);
function WCProductFilter_field_submit(){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('submit', $wc_pf_hide_input_fields)) return;


    ?>
    <div class="field-wrapper">
        <input type="submit" value="<?php echo __('Submit','wc-product-filter'); ?>">
    </div>
    <?php
}




//add_action('WCProductFilter_fields','WCProductFilter_field_product_type',30);
function WCProductFilter_field_product_type(){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('keyword', $wc_pf_hide_input_fields)) return;

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :"";
    $_product_type = isset($_GET['_product_type']) ? sanitize_text_field($_GET['_product_type']) :"";

    if(!$WCProductFilter):
        $_product_type = '';
    endif;

    $product_types = wc_get_product_types();

    //var_dump($product_types);

    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Product types','wc-product-filter'); ?></label>
        </div>
        <div class="input-wrapper">
            <select name="_product_type" >
                <option value="">All</option>
                <?php

                foreach ($product_types as $product_type=>$product_type_name){
                    ?>
                    <option value="<?php echo esc_attr($product_type); ?>" <?php if($_product_type == $product_type) echo 'selected';?>><?php echo esc_html($product_type_name); ?></option>
                    <?php
                }
                ?>


            </select>
        </div>
    </div>
    <?php
}










add_action('WCProductFilter_fields','WCProductFilter_field_sku',30);
function WCProductFilter_field_sku(){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('sku', $wc_pf_hide_input_fields)) return;

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :"";
    $_sku = isset($_GET['_sku']) ? sanitize_text_field($_GET['_sku']) :"";

    if(!$WCProductFilter):
        $_sku = '';
    endif;


    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('SKU','wc-product-filter'); ?></label>
        </div>
        <div class="input-wrapper">
            <input type="search" name="_sku" value="<?php echo esc_attr($_sku); ?>">
        </div>
    </div>
    <?php
}




//add_action('WCProductFilter_fields','WCProductFilter_field_my_custom_input', 99);
function WCProductFilter_field_my_custom_input(){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('keyword', $wc_pf_hide_input_fields)) return;

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :""; // check this to ensure for is submitted from WCProductFilter.
    $_custom_input = isset($_GET['_custom_input']) ? sanitize_text_field($_GET['_custom_input']) :""; // Do not forget to sanitization

    if(!$WCProductFilter):
        $_custom_input = '';
    endif;

    /*
     * you can check conditional here.
     *
     * if(is_shop()):
     * execute code only shop page
     * endif;
     *
     * */

    if(is_shop()):
        // this will only display under shop page and hide others page
        ?>
        <div class="field-wrapper">
            <div class="label-wrapper">
                <label class=""><?php echo __('Custom Input','wc-product-filter'); ?></label>
            </div>
            <div class="input-wrapper">
                <input type="search" placeholder="<?php echo __('Custom input','wc-product-filter'); ?>" name="_custom_input" value="<?php echo esc_attr($_custom_input); ?>">
            </div>
        </div>
    <?php
    endif;

}




//add_action( 'woocommerce_product_query', 'WCProductFilter_pre_get_posts_query_my_custom_input', 99 );
function WCProductFilter_pre_get_posts_query_my_custom_input( $query_args ){

    $wc_pf_hide_input_fields = get_option('wc_pf_hide_input_fields');
    if(!empty($wc_pf_hide_input_fields) && in_array('keyword', $wc_pf_hide_input_fields)) return;

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) : "";
    $_custom_input = isset($_GET['_custom_input']) ? sanitize_text_field($_GET['_custom_input']) :""; // Do not forget to sanitization

    if($WCProductFilter && $_custom_input){

        // you can get ans set query arguments
        // try var_dump($query_args) to see available query arguments, like tax_query, meta_query, order, orderby

        $s = $query_args->get( 's' );
        $query_args->set( 's', $_custom_input );
    }

}
