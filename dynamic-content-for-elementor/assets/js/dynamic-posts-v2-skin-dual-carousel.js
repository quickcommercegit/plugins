;var galleryThumbs = null;
var Widget_DCE_Dynamicposts_dualcarousel_Handler = function ($scope, $) {

    var smsc = null;

	var elementSettings = get_Dyncontel_ElementSettings($scope);
    var id_scope = $scope.attr('data-id');
	var id_post = $scope.attr('data-post-id');

    galleryThumbs = null;

	var elementSwiper = $scope.find('.dce-dualcarousel-gallery-thumbs');

	var slidesPerView = Number(elementSettings[DCE_dynposts_skinPrefix+'thumbnails_slidesPerView']);

	var elementorBreakpoints = elementorFrontend.config.breakpoints;
	var dceSwiperOptions = {
		spaceBetween: Number(elementSettings[DCE_dynposts_skinPrefix+'dualcarousel_gap']) || 0,
		slidesPerView: slidesPerView || 'auto',
	    autoHeight: true,
	    watchOverflow: true,
	    watchSlidesProgress: true,
	    centeredSlides: true,
	    loop: true,
	};

    var responsivePoints = dceSwiperOptions.breakpoints = {};
    responsivePoints[elementorBreakpoints.lg] = {
        slidesPerView: Number(elementSettings[DCE_dynposts_skinPrefix+'thumbnails_slidesPerView']) || 'auto',
        spaceBetween: Number(elementSettings[DCE_dynposts_skinPrefix+'dualcarousel_gap']) || 0,
    };
    responsivePoints[elementorBreakpoints.md] = {
        slidesPerView: Number(elementSettings[DCE_dynposts_skinPrefix+'thumbnails_slidesPerView_tablet']) || Number(elementSettings[DCE_dynposts_skinPrefix+'thumbnails_slidesPerView']) || 'auto',
        spaceBetween: Number(elementSettings[DCE_dynposts_skinPrefix+'dualcarousel_gap_tablet']) || Number(elementSettings[DCE_dynposts_skinPrefix+'dualcarousel_gap']) || 0,
    };
    responsivePoints[elementorBreakpoints.xs] = {
        slidesPerView: Number(elementSettings[DCE_dynposts_skinPrefix+'thumbnails_slidesPerView_mobile']) || Number(elementSettings[DCE_dynposts_skinPrefix+'thumbnails_slidesPerView_tablet']) || Number(elementSettings[DCE_dynposts_skinPrefix+'thumbnails_slidesPerView']) || 'auto',
        spaceBetween: Number(elementSettings[DCE_dynposts_skinPrefix+'dualcarousel_gap_mobile']) || Number(elementSettings[DCE_dynposts_skinPrefix+'dualcarousel_gap_tablet']) || Number(elementSettings[DCE_dynposts_skinPrefix+'spaceBetween']) || 0,
    };
    dceSwiperOptions = $.extend(dceSwiperOptions, responsivePoints);
    //
    if(smsc) smsc.remove();
    if(galleryThumbs) galleryThumbs.destroy();
    galleryThumbs = new Swiper(elementSwiper[0], dceSwiperOptions);


    Widget_DCE_Dynamicposts_carousel_Handler($scope, $);


};

jQuery(window).on('elementor/frontend/init', function () {

    elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.dualcarousel', Widget_DCE_Dynamicposts_dualcarousel_Handler);
});
