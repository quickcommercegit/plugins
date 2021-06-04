
jQuery(document).ready(function () {
	jQuery( ".sub-menu" ).each(function( index ) {
		if(!jQuery(this).hasClass('nav-dropdown')) {
			jQuery(this).addClass('nav-dropdown nav-dropdown-bold');
		}
	});

	 	jQuery('.has-dropdown').find('a.nav-top-link:first').append('<span class="fusion-caret"><i class="fusion-dropdown-indicator"></i></span>');
    
    
})