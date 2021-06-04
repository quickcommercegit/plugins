
jQuery(document).ready(function () {
	jQuery(function(){
		jQuery(".edit-menu-item-exclude").multiselect({
			buttonWidth: '384px'
		});
		jQuery(".edit-menu-item-exclude").multiselectfilter();

		jQuery(".edit-menu-item-include").multiselect({
			buttonWidth: '384px'
		});
		jQuery(".edit-menu-item-include").multiselectfilter();
	  });

	// show/hide remaining option fields if option value is Category Tree
	jQuery( function() {
		jQuery(document).on( 'change', '.select-field-custom-orderby',function(){
			var orderValue = jQuery(this).val();
			showHideCustomFields( orderValue  );
		})
	});

	var dynamicMenuOrderby =  jQuery(".select-field-custom-orderby").val();
	showHideCustomFields( dynamicMenuOrderby );
});

function showHideCustomFields( orderValue ) {
	if( orderValue == 'tree' ) {
				jQuery('.field-custom-not-tree').css('display','none');
			}else {
				jQuery('.field-custom-not-tree').css('display','block');
			}
}