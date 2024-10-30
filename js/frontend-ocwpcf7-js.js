jQuery( document ).ready(function() {

    var forms = document.getElementsByTagName('form');

    for (var i = 0; i < forms.length; i++) {

        jQuery('select.wpcf7-posts', forms[i]).each(function() {
            var post_placeholder = jQuery(this).attr('placeholder');
            jQuery(this).select2({
                        placeholder : post_placeholder,
                        allowClear : false,
                        minimumResultsForSearch : Infinity,
                        templateResult: formatOptions
            });

        });

        jQuery('select.wpcf7-products', forms[i]).each(function() {
            var placeholder = jQuery(this).attr('placeholder');
           jQuery(this).select2({
                        placeholder : placeholder,
                        allowClear : false,
                        minimumResultsForSearch : Infinity,
                        templateResult: formatOptions_product
            });

        });
     }
   
});

 function formatOptions (state) {
            if (!state.id) { return state.text; }
            var ids = jQuery(state.element).data('id');
            var imageformat = jQuery(state.element).data('image');
            var width = jQuery(state.element).data('width');
            if(imageformat != undefined) {
                thumbnail = "<img style='width:" + width + "px; display: inline-block;' src='" + imageformat + "''  />";
            } else {
                thumbnail = '';
            }
            var descriptiondata = jQuery(state.element).data('content');
            var types = jQuery(state.element).data('types');
            if(types == 'product'){
            	 if(descriptiondata === undefined){
		            	description = '';
		            } else {
		            	description = '<strong>Price </strong>'+descriptiondata; 
		          }	
            } else {
                 if(descriptiondata === undefined){
		            	description = '';
		            } else {
		            	description = descriptiondata; 
		          }	
            }
            var $state = jQuery(
            '<div class="ocwpcf7_main"><div class="ocwpcf7_left_box">' + thumbnail + '</div><div class="ocwpcf7_right_box"><div class="ocwpcf7_title" >' + state.text + '</div><div class="ocwpcf7_description" >' + description + '</div></div></div>'
            );
    return $state;
}

 function formatOptions_product (state) {
            if (!state.id) { return state.text; }
            var pro_imageformat = jQuery(state.element).data('pro_image_url');
            var pro_contentdata = jQuery(state.element).data('pro_content');
            var width = jQuery(state.element).data('width');
            if(pro_imageformat != undefined) {
               thumbnail = "<img style='width:" + width + "px; display: inline-block;' src='" + pro_imageformat + "''  />";
            } else {
                thumbnail = '';
            }
            if(pro_contentdata === undefined){
		            	pro_description = '';
		            } else {
		            	pro_description = '<strong>Price </strong>'+pro_contentdata; 
		          }	
            var $state = jQuery(
            '<div class="ocwpcf7_main woocommerce"><div class="ocwpcf7_left_box">' + thumbnail + '</div><div class="ocwpcf7_right_box"><div class="ocwpcf7_title" >' + state.text + '</div><div class="ocwpcf7_description" >' + pro_description + '</div></div></div>'
            );
    return $state;
}