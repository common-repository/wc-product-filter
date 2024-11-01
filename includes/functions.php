<?php
if ( ! defined('ABSPATH')) exit;  // if direct access


function wcpf_recursive_sanitize_arr($array) {

    foreach ( $array as $key => &$value ) {
        if ( is_array( $value ) ) {
            $value = wcpf_recursive_sanitize_arr($value);
        }
        else {
            $value = wp_kses_post( $value );
        }
    }

    return $array;
}


function  WCProductFilter_get_shop_max_price(){

    $wp_query_product_cat = -1;
    $woocommerce_hide_out_of_stock_items = 'no';


    global $wpdb, $wp_query;

    if( method_exists('WC_Query', 'get_main_tax_query') && method_exists('WC_Query', 'get_main_tax_query') &&
        class_exists('WP_Meta_Query') && class_exists('WP_Tax_Query') ) {
        $query_string = array(
            'select' => "SELECT MIN(cast(FLOOR(br_prices.meta_value) as decimal)) as min_price, MAX(cast(CEIL(br_prices.meta_value) as decimal)) as max_price
                    FROM {$wpdb->posts}",
            'join'   => " INNER JOIN {$wpdb->postmeta} as br_prices ON ({$wpdb->posts}.ID = br_prices.post_id)",
            'where'  => " WHERE {$wpdb->posts}.post_type = 'product'
                    AND {$wpdb->posts}.post_status = 'publish'
                    AND  br_prices.meta_key = '_price' AND br_prices.meta_value > 0");


        //var_dump($query_string);

        $tax_query  = array();
        $args       = $wp_query->query_vars;
        $meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();
        foreach ( $meta_query as $key => $query ) {
            if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
                unset( $meta_query[ $key ] );
            }
        }
        if ( ! empty( $args['product_cat'] ) ) {
            $tax_query[ 'product_cat' ] = array(
                'taxonomy' => 'product_cat',
                'terms'    => array( $args['product_cat'] ),
                'field'    => 'slug',
            );
        }
        $queried_object = $wp_query->get_queried_object_id();
        if( ! empty($queried_object) ) {
            $query_object = $wp_query->get_queried_object();
            if( ! empty($query_object->taxonomy) && ! empty($query_object->slug) ) {
                $tax_query[ $query_object->taxonomy ] = array(
                    'taxonomy' => $query_object->taxonomy,
                    'terms'    => array( $query_object->slug ),
                    'field'    => 'slug',
                );
            }
        }
        $meta_query = new WP_Meta_Query( $meta_query );
        $tax_query  = new WP_Tax_Query( $tax_query );
        $meta_query_sql  = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
        $tax_query_sql   = $tax_query->get_sql( $wpdb->posts, 'ID' );
        $query_string['join'] = $query_string['join'].' '. $tax_query_sql['join'] . $meta_query_sql['join'];
        $query_string['where'] = $query_string['where'].' '. $tax_query_sql['where'] . $meta_query_sql['where'];
        $query_string = implode( ' ', $query_string );

        $prices = $wpdb->get_row($query_string);


        //var_dump($prices);



        $price_range = false;
        if( isset($prices->min_price) && isset($prices->max_price) && $prices->min_price != $prices->max_price ) {
            $price_range = array(
                apply_filters( 'WCProductFilter_price_range_min', $prices->min_price ),
                apply_filters( 'WCProductFilter_price_range_max', $prices->max_price )
            );
        }




        return apply_filters( 'WCProductFilter_price_range', $price_range );

    }

   //var_dump($query_string);


}











function wc_product_filter_form_submit(){

    $response 			= array();
    $input_items 			= array();
    $wc_pf_posts_per_page = get_option('wc_pf_posts_per_page', 10);

    $form_data = isset($_POST['form_data']) ? ($_POST['form_data']) : array();


    //error_log(serialize($form_data));

    parse_str($form_data, $input_items);

    //error_log(serialize($input_items));
    //error_log(serialize($input_items));

    $paged = isset($_POST['paged']) ? sanitize_text_field($_POST['paged']) : 1;


//    foreach ($form_data as $index => $data){
//        $name = $data['name'];
//        $value = $data['value'];
//
//        if($name =='_product_cat[]' || $name =='_attr_color[]' || $name =='_attr_size[]' ){
//            $input_items[$name] .= $value.'|';
//        }else{
//            $input_items[$name] = $value;
//        }
//
//
//    }



    if(!empty($input_items)){
        global $product;

        $keyword = isset($input_items['keyword']) ? sanitize_text_field($input_items['keyword']) :"";
        $_product_cat = "";




        if(isset($input_items['_product_cat'])){
            if(is_array($input_items['_product_cat'])){
                $_product_cat =  wcpf_recursive_sanitize_arr($input_items['_product_cat']);
            }else{
                $_product_cat =  sanitize_text_field($input_items['_product_cat']);
            }
        }





        $_product_tag = isset($input_items['_product_tag']) ? sanitize_text_field($input_items['_product_tag']) :"";




        $_price = isset($input_items['_price']) ? sanitize_text_field($input_items['_price']) :"";

        $_attr_color = '';

        if(isset($input_items['_attr_color'])){
            if(is_array($input_items['_attr_color'])){
                $_attr_color =  wcpf_recursive_sanitize_arr($input_items['_attr_color']);
            }else{
                $_attr_color =  sanitize_text_field($input_items['_attr_color']);
            }
        }

        $_attr_size = '';

        if(isset($input_items['_attr_size'])){
            if(is_array($input_items['_attr_size'])){
                $_attr_size =  wcpf_recursive_sanitize_arr($input_items['_attr_size']);
            }else{
                $_attr_size =  sanitize_text_field($input_items['_attr_size']);
            }
        }



        //$_attr_size = isset($input_items['_attr_size']) ? ($input_items['_attr_size']) :"";


        $_order = isset($input_items['_order']) ? sanitize_text_field($input_items['_order']) :"";
        $_orderby = isset($input_items['_orderby']) ? sanitize_text_field($input_items['_orderby']) :"";
        $_onsale = isset($input_items['_onsale']) ? sanitize_text_field($input_items['_onsale']) :"";
        $_stock_status = isset($input_items['_stock_status']) ? sanitize_text_field($input_items['_stock_status']) :"";
        $_sku = isset($input_items['_sku']) ? sanitize_text_field($input_items['_sku']) :"";

        $tax_query = array();
        $meta_query = array();



        $args = array();
        $args['post_type'] = 'product';
        $args['posts_per_page'] = $wc_pf_posts_per_page;
        $args['paged'] = $paged;


        if($keyword){
            $args['s'] = $keyword;
        }

        if(!empty($_product_cat)){

            //$_product_cat = array_filter(explode('|', $_product_cat));


            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $_product_cat,
                'operator' => 'IN'
            );

        }


        if($_attr_color){

            //$_attr_color = array_filter(explode('|', $_attr_color));


            $tax_query[] = array(
                'taxonomy' => 'pa_color',
                'field' => 'slug',
                'terms' => $_attr_color,
                'operator' => 'IN'
            );

        }


        if($_attr_size){

            //$_attr_size = array_filter(explode('|', $_attr_size));


            $tax_query[] = array(
                'taxonomy' => 'pa_size',
                'field' => 'slug',
                'terms' => $_attr_size,
                'operator' => 'IN'
            );

        }


        if($_product_tag){
            $_product_tag = explode(',', $_product_tag);

            $tax_query_tag = array();
            $tax_query_tag['relation'] = 'OR';

            if($_product_tag)
                $tax_query_tag[] = array(
                    'taxonomy' => 'product_tag',
                    'field' => 'name',
                    'terms' => $_product_tag,
                    //'operator' => 'IN'
                );


            $tax_query = array($tax_query_tag);

        }





        if($_price){

            //$_price = str_replace('$','',$_price);

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

        }



        if($_order){
            $args['order'] = $_order;
        }

        if($_orderby){
            //var_dump($_orderby);

            if($_orderby == 'rating'){

                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_wc_average_rating';

            }
            elseif ($_orderby == 'price'){

                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_price';

            }
            elseif ($_orderby == 'popularity'){

                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = 'total_sales';


            }
            else{

                $args['orderby'] = $_orderby;

            }





        }

        if($_onsale){


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


        }


        if($_stock_status){


            $tax_query[] = array(
                'taxonomy' => 'product_visibility',
                'field' => 'name',
                'terms' => 'outofstock',
                'operator' => 'NOT IN',
            );

        }

        if($_sku){

            $meta_query[] =
                array(
                    'key' => '_sku',
                    'value' => $_sku,
                    'compare' => 'IN'
                );

        }

        $args['tax_query'] = $tax_query;
        $args['meta_query'] = $meta_query;

        $args = apply_filters('wc_pf_query_args',$args, $form_data);


        $count_results = '0';

        $ajax_query = new WP_Query( $args );

//        error_log($ajax_query->post_count);
//        error_log($ajax_query->found_posts);
//
//
        $post_count = $ajax_query->post_count;
        $found_posts = $ajax_query->found_posts;
        $max_num_pages = $ajax_query->max_num_pages;

        // Results found
        if( $ajax_query->have_posts() ){

            // Start "saving" results' HTML
            $results_html = '';
            ob_start();

            while( $ajax_query->have_posts() ) {

                $ajax_query->the_post();
                echo wc_get_template_part( 'content', 'product' );

            }


            $response['loop_html'] = ob_get_clean();

            ob_start();

            $shop_page_id = wc_get_page_id( 'shop' );
            $shop_page_url = $shop_page_id ? get_permalink( $shop_page_id ) : '';




            $big = 999999999; // need an unlikely integer
            echo paginate_links( array(
                'base' => $shop_page_url.'page/%#%',
                'format' => '?paged=%#%',
                'current' => max( 1, $paged ),
                'type'               => 'list',
                'total' => $max_num_pages
            ) );

            $response['pagination'] = ob_get_clean();

            //error_log($paged);


            if($paged == 1){

                if($found_posts < $wc_pf_posts_per_page){
                    $post_count_range = "1 - $found_posts";
                }else{
                    $post_count_range = "1 - $wc_pf_posts_per_page";
                }




            }
            elseif($paged == $max_num_pages){
                //$post_count_range = $post_count;
                $post_count_range = ((($paged-1)*$wc_pf_posts_per_page) + 1).' - '.($found_posts);




            }
            else{
//                $post_count_min = ($post_count*$paged);
                //$post_count_max = ($wc_pf_posts_per_page*$paged);
                $post_count_range = ((($paged-1)*$wc_pf_posts_per_page) + 1).' - '.($wc_pf_posts_per_page*$paged);

                //$post_count_range = $post_count_max;
            }





            //$post_count = ($paged == 1) ? $post_count : ($post_count*$paged);

            $response['result_count'] = sprintf(__('Showing %s of %s results','wc-product-filter'), $post_count_range, $found_posts);



            wp_reset_postdata();

            // "Save" results' HTML as variable


        } else {

            // Start "saving" results' HTML
            $results_html = '';
            ob_start();

            echo __("No product found!",'wc-product-filter');

            // "Save" results' HTML as variable
            $results_html = ob_get_clean();

        }


    }







    //$response['loop_html'] = $loop_html;
    //$response['form_data'] = $form_data;
    //$response['pagination'] = $pagination_html;




    echo json_encode( $response );
    die();
}
add_action('wp_ajax_wc_product_filter_form_submit', 'wc_product_filter_form_submit');
add_action('wp_ajax_nopriv_wc_product_filter_form_submit', 'wc_product_filter_form_submit');

