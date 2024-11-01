=== PickPlugins Product Filter for WooCommerce ===
	Contributors: PickPlugins
	Donate link: https://www.pickplugins.com/
	Tags: product filter, woocommerce product filter, woocommerce products filter, ajax product filter, woocommerce filter, woocommerce search
	Requires at least: 3.8
	Tested up to: 6.2
	Stable tag: 1.0.9
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html

	Filter shop product by advance filter and search.

== Description ==

Every WooCommerce site needs a good WooCommerce product filter system in order to be a good e-commerce site. As an online shop contains a ton of products so it's kinda hard for anyone to find anything instantly.When a customer visits a e-commerce site, he or she in a hurry. Usually, online shopping is much more preferable to many people as it takes less time to do the shopping. So when a person is in hurry and has to do his or her shopping, what he or she will need?  they will search for a search input box on that site. In that search box, they will type their desire product name, color etc and the product will come to their screen. They will buy that!! boom!! shopping is done. This kind of task is only possible by adding an excellent WooCommerce product filter plugin. Pickplugin has developed such type of plugin for WooCommerce Search plugin.


### PickPlugins product filter for WooCommerce by [http://pickplugins.com](http://pickplugins.com)

* [Live Demo](http://www.pickplugins.com/demo/wc-product-filter/shop/?ref=wordpress.org)
* [Documentation](https://www.pickplugins.com/documentation/wc-product-filter/?ref=wordpress.org)
* [Support](https://www.pickplugins.com/support/?ref=wordpress.org)

### VIDEO TUTORIAL

https://www.youtube.com/watch?v=CXEJT96jMKU

### Plugin Features

* Filter by keywords
* Filter by product categories
* Filter by product tags
* Filter by price range
* Filter by order & order by
* Filter onsale product
* Filter in-stock
* Filter by SKU

### How to use?

Please go to "Widgets" page and see there is a widget "WC Product Filter", you can use this on sidebars.
There is no option currently, we will update soon.

### How to add custom search field and filter products?

you can add custom search field by action hook and filter products as well,

### Step 1:

Add search input field

`
add_action('WCProductFilter_fields','WCProductFilter_field_my_custom_input', 30);
function WCProductFilter_field_my_custom_input(){

    $WCProductFilter = isset($_GET['WCProductFilter']) ? sanitize_text_field($_GET['WCProductFilter']) :""; // check this to ensure for is submitted from WCProductFilter.
    $_custom_input = isset($_GET['_custom_input']) ? sanitize_text_field($_GET['_custom_input']) :""; // Do not forget to sanitization

    if(!$WCProductFilter):
        $_custom_input = '';
    endif;


    /*
     *
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
                <input type="search" placeholder="<?php echo __('Custom input','wc-product-filter'); ?>" name="_custom_input" value="<?php echo $_custom_input; ?>">
            </div>
        </div>
    <?php
    endif;

}`

### Step 2

verify input variable and put on product query arguments

`
function wc_pf_query_args_custom_field($args, $form_data){

    //default search query
    $args['s'] = isset($form_data['_custom_input']) ? $form_data['_custom_input'] : '';

    return $args;

}
add_action('wc_pf_query_args', 'wc_pf_query_args_custom_field');`


### How to remove existing input fields?

By action hook you can simply remove any input fields exist.

`
remove_action('WCProductFilter_fields','WCProductFilter_field_keyword',30);
remove_action('WCProductFilter_fields','WCProductFilter_field_categories',30);
remove_action('WCProductFilter_fields','WCProductFilter_field_tags',30);
remove_action('WCProductFilter_fields','WCProductFilter_field_price_range',30);
remove_action('WCProductFilter_fields','WCProductFilter_field_order',30);
remove_action('WCProductFilter_fields','WCProductFilter_field_orderby',30);
remove_action('WCProductFilter_fields','WCProductFilter_field_onsale',30);
remove_action('WCProductFilter_fields','WCProductFilter_field_in_stock',30);
remove_action('WCProductFilter_fields','WCProductFilter_field_keyword',30);
remove_action('WCProductFilter_fields','WCProductFilter_field_sku',30);
`




== Installation ==

1. Install as regular WordPress plugin.<br />
2. Go your plugin setting via WordPress Dashboard and find "<strong>PickPlugins product filter for WooCommerce</strong>" activate it.<br />


== Screenshots ==

1. screenshot-1
2. screenshot-2
3. screenshot-3
4. screenshot-4
5. screenshot-5
6. screenshot-6
7. screenshot-7


== Changelog ==

	= 1.0.9 =
    * 2020-08-01 fix - undefined index issue fixed.
    * 2020-08-01 add - search result count added.

	= 1.0.8 =
    * 2020-04-24 fix - general function name update to avoid conflict.

	= 1.0.7 =
    * 2020-04-18 fix - security issue update

	= 1.0.6 =
    * 15/12/2019 update - WooCommerce latest version compatibility

	= 1.0.5 =
    * 15/12/2019 add - option to hide submit button

	= 1.0.4 =
    * 31/10/2019 fix - WooCommerce currency issue fixed.
    * 31/10/2019 fix - localhost link removed
    * 31/10/2019 fix - optimize css and js file load.

	= 1.0.3 =
    * 16/10/2019 add - hide default input fields


	= 1.0.2 =
    * 16/10/2019 add - ajax search.

	= 1.0.0 =
    * 06/11/2018 Initial release.
