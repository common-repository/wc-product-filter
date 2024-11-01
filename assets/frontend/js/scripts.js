jQuery(document).ready(function($)
	{

		
		$(document).on('change submit', '.WCProductFilter form', function(e){

			e.preventDefault();
			form = jQuery('.WCProductFilter form');
			//form_data 	= form.serializeArray();
			form_data2 	= form.serialize();
			console.log(form_data2);

			paged = 1;
			wcpf_update_product(form_data2, paged);


		})




		$(document).on('click', '.woocommerce-pagination.WCProductFilter a', function(e){

			e.preventDefault();
			form = jQuery('.WCProductFilter form');
			//form_data 	= form.serializeArray();
			form_data2 	= form.serialize();

			paged = jQuery(this).text();

			wcpf_update_product(form_data2, paged);


		})




	});


function wcpf_update_product(form_data, paged) {

	jQuery('.products ').addClass('loading');
	jQuery('.woocommerce-pagination').addClass('WCProductFilter');

	jQuery.ajax(
		{
			type: 'POST',
			context: this,
			url:wc_pf_admin_ajax.wc_pf_admin_ajaxurl,
			data: {
				"action" 	: "wc_product_filter_form_submit",
				"form_data" : form_data,
				"paged" : paged,
			},
			success: function( response ) {

				var data = JSON.parse( response );


				loop_html = data['loop_html'];
				//form_data = data['form_data'];
				pagination = data['pagination'];
				result_count = data['result_count'];

				//console.log(form_data);

				jQuery('.products ').html(loop_html);
				jQuery('.products ').removeClass('loading');
				jQuery('.woocommerce-result-count ').html(result_count);

				paged = jQuery('.woocommerce-pagination').html(pagination);

				//jQuery('.page-numbers').html('<li><span aria-current="page" class="page-numbers">Load more →</span></li>');

				//pTags.unwrap();
			} });

}