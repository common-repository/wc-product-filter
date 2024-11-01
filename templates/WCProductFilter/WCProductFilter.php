<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

$shop_page_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );

?>
<div class="WCProductFilter sidebar">
    <form action="<?php echo $shop_page_url; ?>" method="get">
        <input type="hidden" name="WCProductFilter" value="Y">
        <?php
        do_action('WCProductFilter_fields');
        ?>

    </form>
</div>
