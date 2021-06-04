(function ($, elementorFrontend) {

	"use strict";

	var JetWooProductGallery = {

		init: function () {

			var self = JetWooProductGallery,
				widgets = {
					'jet-woo-product-gallery-grid.default': self.productGalleryGrid,
					'jet-woo-product-gallery-modern.default': self.productGalleryModern,
					'jet-woo-product-gallery-anchor-nav.default': self.productGalleryAnchorNav,
					'jet-woo-product-gallery-slider.default': self.productGallerySlider,
				};


			$.each(widgets, function (widget, callback) {
				elementorFrontend.hooks.addAction('frontend/element_ready/' + widget, callback);
			});

			if ( $( '.woocommerce div.product' ).hasClass( 'product-type-variable' ) ) {
				$(document)
					.on( 'show_variation', function ( form, variation ) {
						self.showVariationImage( variation );
					} )
					.on( 'reset_image', function ( form, variation ) {
						self.showVariationImage( variation );
					} );
			}

		},

		showVariationImage: function ( variation ) {
			var $product = $(document).find( '.product' ),
				$product_gallery = $product.find( '.jet-woo-product-gallery' );

			$.each($product_gallery, function () {

				var variation_images_data = $(this).data('variation-images'),
					$infiniteLoop = $(this).find('.jet-woo-product-gallery-slider').data('swiper-settings'),
					$product_img_wrap = null,
					$gallery_img = null,
					$featuredImage = $( this ).children().data( 'featured-image' ),
					gallerySettings = $( this ).data('gallery-settings'),
					galleryVideoFirst = gallerySettings.videoFirst,
					index = galleryVideoFirst ? 1 : 0;

				if ( $infiniteLoop && $infiniteLoop['loop'] ) {
					$product_img_wrap = $(this).find('.jet-woo-product-gallery__image-item[data-swiper-slide-index = "' + index + '"]');
					$gallery_img = $(this).find('.jet-woo-swiper-control-thumbs__item[data-swiper-slide-index = "' + index + '"] img');
				} else {
					$product_img_wrap = $(this).find( '.jet-woo-product-gallery__image-item' ).eq( index );
					$gallery_img = $(this).find( '.jet-woo-swiper-control-thumbs__item:eq( index ) img' );
				}

				var $product_img = $product_img_wrap.find( '.wp-post-image' ),
					$product_link = $product_img_wrap.find( 'a' ).eq( 0 );

				if ( ! $featuredImage ) {
					$product_img = $product_img_wrap.find( '.wp-post-gallery' );
				}

				if ( variation && variation.image && variation.image.src && variation.image.src.length > 1 ) {
					var variation_image_data = variation_images_data[variation.image_id];

					setVariationImageAtts( variation, variation_image_data );
				} else {
					resetVariationImageAtts();
				}

				function setVariationImageAtts( variation, variation_image_data ) {
					$product_img.wc_set_variation_attr( 'src', variation_image_data.src );
					$product_img.wc_set_variation_attr( 'height', variation_image_data.src_h );
					$product_img.wc_set_variation_attr( 'width', variation_image_data.src_w );
					$product_img.wc_set_variation_attr( 'srcset', variation_image_data.srcset );
					$product_img.wc_set_variation_attr( 'sizes', variation_image_data.sizes );
					$product_img.wc_set_variation_attr( 'title', variation.image.title );
					$product_img.wc_set_variation_attr( 'data-caption', variation.image.caption );
					$product_img.wc_set_variation_attr( 'alt', variation.image.alt );
					$product_img.wc_set_variation_attr( 'data-src', variation_image_data.src );
					$product_img.wc_set_variation_attr( 'data-large_image', variation_image_data.full_src );
					$product_img.wc_set_variation_attr( 'data-large_image_width', variation_image_data.full_src_w );
					$product_img.wc_set_variation_attr( 'data-large_image_height', variation_image_data.full_src_h );

					$product_img_wrap.wc_set_variation_attr( 'data-thumb', variation_image_data.src );

					$product_link.wc_set_variation_attr( 'href', variation.image.full_src );

					$gallery_img.wc_set_variation_attr( 'src', variation.image.thumb_src );
					$gallery_img.wc_set_variation_attr( 'width', variation.image.thumb_src_w );
					$gallery_img.wc_set_variation_attr( 'height', variation.image.thumb_src_h );
					$gallery_img.wc_set_variation_attr( 'srcset', '' );
				}

				function resetVariationImageAtts() {
					$product_img.wc_reset_variation_attr( 'src' );
					$product_img.wc_reset_variation_attr( 'width' );
					$product_img.wc_reset_variation_attr( 'height' );
					$product_img.wc_reset_variation_attr( 'srcset' );
					$product_img.wc_reset_variation_attr( 'sizes' );
					$product_img.wc_reset_variation_attr( 'title' );
					$product_img.wc_reset_variation_attr( 'data-caption' );
					$product_img.wc_reset_variation_attr( 'alt' );
					$product_img.wc_reset_variation_attr( 'data-src' );
					$product_img.wc_reset_variation_attr( 'data-large_image' );
					$product_img.wc_reset_variation_attr( 'data-large_image_width' );
					$product_img.wc_reset_variation_attr( 'data-large_image_height' );

					$product_img_wrap.wc_reset_variation_attr( 'data-thumb' );

					$product_link.wc_reset_variation_attr( 'href' );

					$gallery_img.wc_reset_variation_attr( 'src' );
					$gallery_img.wc_reset_variation_attr( 'width' );
					$gallery_img.wc_reset_variation_attr( 'height' );
				}

			});

			$( document ).trigger( 'jet-woo-gallery-variation-image-change' );

		},

		productGallerySlider: function ( $scope ) {

			let slider = $scope.find( '.jet-woo-product-gallery-slider' ),
				settings = slider.data( 'swiper-settings' ),
				thumbSettings = slider.data( 'swiper-thumb-settings' ),
				sliderItemsCount = slider.find( '.jet-woo-product-gallery__image-item' ).length,
				sliderFeaturedItemsCount = slider.find( '.jet-woo-product-gallery__image-item.featured' ).length,
				paginationType = settings['paginationType'],
				thumbnails = $scope.find( '.jet-woo-swiper-gallery-thumbs' );

			if ( sliderItemsCount > sliderFeaturedItemsCount ) {

				let defaultSettings = {
					slidesPerView: 1,
					touchReleaseOnEdges: true,
					autoHeight: 'vertical' !== settings['direction'] ? settings['autoHeight'] : true,
					navigation: {
						nextEl: '.jet-swiper-button-next',
						prevEl: '.jet-swiper-button-prev',
					},
					on: {
						init: () => {
							JetWooProductGallery.productGallery( $scope );
						}
					}
				};

				var galleryThumbs, galleryTop, withThumbnails = false;

				if ( settings['showPagination'] ) {
					if ( 'thumbnails' === paginationType ) {
						var defaultThumbSettings = {
							slidesPerView: 1,
							loop: settings['loop'],
							direction: settings['direction'],
							freeMode: settings['loop'],
							autoHeight: 'vertical' !== settings['direction'] ? settings['autoHeight'] : false,
							watchSlidesVisibility: true,
							watchSlidesProgress: true,
							navigation: {
								nextEl: '.jet-swiper-button-next',
								prevEl: '.jet-swiper-button-prev',
							}
						};

						if ( 'undefined' === typeof Swiper ) {
							const asyncSwiper = elementorFrontend.utils.swiper;

							withThumbnails = true;

							new asyncSwiper( thumbnails, $.extend( {}, defaultThumbSettings, thumbSettings ) ).then( ( newThumbSwiperInstance ) => {
								defaultSettings.thumbs = {
									swiper: newThumbSwiperInstance
								};
							} ).then( () => {
								new asyncSwiper( slider, $.extend( {}, defaultSettings, settings ) ).then ( ( newSwiperInstance ) => {
									galleryTop = newSwiperInstance;
								} );
							} );
						} else {
							galleryThumbs = new Swiper( thumbnails, $.extend( {}, defaultThumbSettings, thumbSettings ) );

							defaultSettings.thumbs = {
								swiper: galleryThumbs
							};
						}

						var thumbnailsSliderItemsCount = thumbnails.find( '.jet-woo-swiper-control-thumbs__item' ).length,
							enableNavigation;

						if( $( window ).width() < 768 ) {
							enableNavigation = thumbnailsSliderItemsCount > thumbSettings['breakpoints'][0]['slidesPerView'];
						} else if ( $( window ).width() < 1025 ) {
							enableNavigation = thumbnailsSliderItemsCount > thumbSettings['breakpoints'][768]['slidesPerView'];
						} else {
							enableNavigation = thumbnailsSliderItemsCount > thumbSettings['breakpoints'][1025]['slidesPerView'];
						}

						if ( ! enableNavigation ) {
							thumbnails.find( '.jet-swiper-nav' ).hide();
						}

					} else {
						defaultSettings.pagination = {
							el: '.swiper-pagination',
							clickable: true
						}
					}
				}

				if ( 'undefined' === typeof Swiper ) {
					const asyncSwiper = elementorFrontend.utils.swiper;

					if ( ! withThumbnails ) {
						new asyncSwiper( slider, $.extend( {}, defaultSettings, settings ) ).then( ( newSwiperInstance ) => {
							galleryTop = newSwiperInstance;
						} );
					}
				} else {
					galleryTop =  new Swiper( slider, $.extend( {}, defaultSettings, settings ) );
				}

				galleryTop.on( 'click', function ( event ) {

					let $playButton = $( event.target ).parents().hasClass( 'jet-woo-product-gallery--with-video' ),
						$selfHosted = 'self_hosted' === $scope.find( '.jet-woo-product-gallery' ).data( 'gallery-settings' ).videoType;

					if ( $playButton && $selfHosted ) {
						setTimeout( function () {
							galleryTop.updateAutoHeight( 300 );
						}, 100 );
					}

				});

				var variationChange = false;

				$(document).on( 'jet-woo-gallery-variation-image-change', () => {
					setTimeout( function () {
						galleryTop.updateAutoHeight( 300 );
					}, 100 );

					var gallerySettings = $scope.find('.jet-woo-product-gallery').data('gallery-settings'),
						galleryVideoFirst = gallerySettings.videoFirst,
						index = 0;

					if ( ! galleryTop ) {
						return false;
					}

					if ( variationChange && galleryVideoFirst ) {
						index = 1;
					}

					if ( settings['loop'] ) {
						galleryTop.slideToLoop( index, 300, true );
					} else {
						galleryTop.slideTo( index, 300, true );
					}

					variationChange = true;
				} );

				if ( settings['loop'] ) {
					slider.find( '.swiper-slide-duplicate video.jet-woo-product-video-player ').removeAttr( "autoplay" );
				}
			} else {
				JetWooProductGallery.productGallery($scope);
				$scope.find( '.jet-swiper-nav' ).hide();
			}

		},

		productGalleryGrid: function ($scope) {
			JetWooProductGallery.productGallery($scope);
		},

		productGalleryModern: function ($scope) {
			JetWooProductGallery.productGallery($scope);
		},

		productGalleryAnchorNav: function ($scope) {
			var item = $scope.find('.jet-woo-product-gallery__image-item'),
				navItems = $scope.find('.jet-woo-product-gallery-anchor-nav-items'),
				navController = $scope.find('.jet-woo-product-gallery-anchor-nav-controller'),
				navControllerItem = navController.find('li a'),
				dataNavItems = [],
				active = 0,
				autoScroll = false,
				scrollOffset = 0,
				scrollPos = 0,
				$wpAdminBar = $('#wpadminbar');

			if ($wpAdminBar.length) {
				scrollOffset = $wpAdminBar.outerHeight();
			}

			JetWooProductGallery.productGallery($scope);

			setControllerItemsData();
			stickyNavController();

			$(window).scroll(function () {
				if (!autoScroll) {
					setControllerItemsData();
					scrollPos = $(document).scrollTop();
					setCurrentControllerItem();
				}
			});

			scrollPos = $(document).scrollTop();
			setCurrentControllerItem();

			$(navControllerItem).on('click', function () {
				setCurrentControllerItem();

				var index = $(this).data('index'),
					pos = dataNavItems[index];

				autoScroll = true;

				$(navController).find('a.current-item').removeClass('current-item');
				$(this).addClass('current-item');

				active = index;

				if ( $( this ).parents().hasClass( 'quick-view-product' ) ) {
					let popupContainer = $( this ).closest( '.jet-popup__container-inner' );

					$( popupContainer ).animate({scrollTop: pos - $( popupContainer ).offset().top + 1}, 'fast', function () {
						autoScroll = false;
					});
				} else {
					$('html, body').animate({scrollTop: pos - scrollOffset + 1}, 'fast', function () {
						autoScroll = false;
					});
				}

				return false;
			});

			function setControllerItemsData() {
				$(item).each(function () {
					var id = $(this).attr('id');
					dataNavItems[id] = $(this).offset().top;
				});
			}

			function setCurrentControllerItem() {
				for (var index in dataNavItems) {
					if (scrollPos >= (dataNavItems[index] - scrollOffset)) {
						$(navController).find('a.current-item').removeClass('current-item');
						$(navController).find('a[data-index="' + index + '"]').addClass('current-item');
					}
				}
			}

			function stickyNavController() {
				var stickyActiveDown = false,
					activeSticky = false,
					bottomedOut = false;

				$(window).on('scroll', function () {
					var windowTop = $(window).scrollTop(),
						navItemsHeight = $(navItems).outerHeight(true),
						navControllerHeight = $(navController).outerHeight(true),
						navItemsTop = $(navItems).offset().top,
						navControllerTop = $(navController).offset().top,
						navItemsBottom = navItemsTop + navItemsHeight,
						navControllerBottom = navControllerTop + navControllerHeight;

					if (navItemsBottom - navControllerHeight - scrollOffset <= windowTop) {
						return;
					}

					if (activeSticky === true && bottomedOut === false) {
						$(navController).css({
							"top": (windowTop - navItemsTop + scrollOffset) + 'px'
						});
					}

					if (windowTop < navControllerTop && windowTop < navControllerBottom) {
						stickyActiveDown = false;
						activeSticky = true;
						$(navController).css({
							"top": (windowTop - navItemsTop + scrollOffset) + 'px'
						});
					}

					if (stickyActiveDown === false && windowTop > navItemsTop) {
						stickyActiveDown = true;
						activeSticky = true;
						bottomedOut = false;
					}

					if (stickyActiveDown === false && navItemsTop > windowTop) {
						stickyActiveDown = false;
						activeSticky = false;
						bottomedOut = false;
						$(navController).removeAttr("style");
					}
				});
			}
		},

		productGallery: function ($scope) {
			var id = $scope.data('id'),
				settings = $scope.find('.jet-woo-product-gallery').data('gallery-settings'),
				sliderSettings = $scope.find( '.jet-woo-product-gallery-slider' ).data( 'swiper-settings' ),
				$galleryImages = $scope.find('.jet-woo-product-gallery__image-item:not(.swiper-slide-duplicate) .jet-woo-product-gallery__image:not(.image-with-placeholder)'),
				$galleryZoomImages = $scope.find('.jet-woo-product-gallery__image--with-zoom'),
				$galleryImagesData = getImagesData(),
				$galleryPhotoSwipeTrigger = $scope.find('.jet-woo-product-gallery__trigger'),
				galleryPhotoSwipeSettings = {
					mainClass: 'jet-woo-product-gallery-' + id,
					captionEl: settings.caption ? settings.caption : '',
					fullscreenEl: settings.fullscreen ? settings.fullscreen : false,
					zoomEl: settings.zoom ? settings.zoom : false,
					shareEl: settings.share ? settings.share : false,
					counterEl: settings.counter ? settings.counter : false,
					arrowEl: settings.arrows ? settings.arrows : false,
					closeOnScroll: false
				},
				photoSwipeTemplate = $('.jet-woo-product-gallery-pswp')[0],
				$galleryVideoPopupTrigger = $scope.find('.jet-woo-product-video__popup-button'),
				$galleryVideoPopupOverlay = $scope.find('.jet-woo-product-video__popup-overlay'),
				$galleryVideoIframe = $scope.find('.jet-woo-product-video-iframe'),
				galleryVideoIframeSrc = $galleryVideoIframe[0] ? $galleryVideoIframe[0].src : false,
				$galleryVideoPlayer = $scope.find('.jet-woo-product-video-player')[0],
				$galleryVideoDefaultPlayer = $scope.find('.jet-woo-product-video-mejs-player'),
				galleryVideoDefaultPlayerControls = $galleryVideoDefaultPlayer.data('controls') || ['playpause', 'current', 'progress', 'duration', 'volume', 'fullscreen'],
				$galleryVideoOverlay = $scope.find('.jet-woo-product-video__overlay'),
				galleryVideoHasOverlay = $galleryVideoOverlay.length > 0,
				galleryVideoAutoplay = settings.videoAutoplay,
				galleryVideoFirst = settings.videoFirst;

			if (settings.enableGallery) {
				$galleryPhotoSwipeTrigger.on('click.JetWooProductGallery', initPhotoSwipe);

				$(document).on('jet-woo-gallery-variation-image-change', function () {
					$galleryImagesData = getImagesData();
				});

			}

			if (settings.enableZoom) {
				initZoom();
				$(document).on('jet-woo-gallery-variation-image-change', initZoom);
			}

			if (settings.hasVideo) {
				initProductVideo();
			}

			$('.jet-woo-product-gallery__image-item').find('img').on('click', function (e) { e.preventDefault(); });

			function initPhotoSwipe(e) {
				e.preventDefault();

				if ($('body').hasClass('elementor-editor-active')) {
					return;
				}

				var target = $(e.target),
					hasPlaceholder = $scope.find('.jet-woo-product-gallery__image-item.featured').hasClass('no-image'),
					clickedItem = target.parents('.jet-woo-product-gallery__image-item'),
					clickedItemData = clickedItem.data( 'swiper-slide-index' ),
					index;

				if ( clickedItemData ) {
					index = clickedItemData;
				} else {
					index = $(clickedItem).index();
				}

				if (hasPlaceholder || galleryVideoFirst) {
					index -= 1;
				}

				galleryPhotoSwipeSettings.index = index;

				var photoSwipe = new PhotoSwipe(photoSwipeTemplate, PhotoSwipeUI_Default, $galleryImagesData, galleryPhotoSwipeSettings);

				photoSwipe.init();

			}

			function initZoom() {
				var flag = false,
					zoomSettings = {
						magnify: settings.zoomMagnify,
						touch: false
					};

				$galleryZoomImages.each(function (index, item) {
					var image = $(item).find('img'),
						galleryWidth = image.parent().width(),
						imageWidth = image.data('large_image_width');

					if (imageWidth > galleryWidth) {
						flag = true;
					}
				});

				if (flag) {
					if ('ontouchstart' in document.documentElement) {
						zoomSettings.on = 'click';
					}

					$galleryZoomImages.trigger('zoom.destroy');
					$galleryZoomImages.zoom(zoomSettings);
				}
			}

			function initProductVideo() {

				switch (settings.videoIn) {
					case 'content':
						if ($galleryVideoOverlay[0]) {
							$galleryVideoOverlay.on('click.JetWooProductGallery', function (event) {
								if ( $galleryVideoPlayer ) {
									defaultPlayerStartPlay( event.target );
								}

								if ( $galleryVideoIframe[0] ) {
									iframePlayerStartPlay( event );
								}
							});

							if ( galleryVideoAutoplay && $galleryVideoIframe[0]) {
								iframePlayerStartPlay( event );
							}
						}

						if ($galleryVideoPlayer) {
							$($galleryVideoPlayer).on('play.JetWooProductGallery', function (event) {
								if (galleryVideoHasOverlay) {
									$galleryVideoOverlay.remove();
									galleryVideoHasOverlay = false;
								}
							});
						}

						if ($galleryVideoDefaultPlayer[0]) {
							defaultPlayerInit();
						}
						break;
					case 'popup':
						defaultPlayerInit();
						$galleryVideoPopupTrigger.on('click.JetWooProductGallery', function (event) {
							videoPopupOpen();
						});

						$galleryVideoPopupOverlay.on('click.JetWooProductGallery', function (event) {
							videoPopupClose();
						});
						break;
				}

				function videoPopupOpen() {
					$galleryVideoPopupTrigger.siblings('.jet-woo-product-video__popup-content').addClass('jet-woo-product-video__popup--show');
					if ($galleryVideoPlayer) {
						$galleryVideoPlayer.play();

						if (!galleryVideoAutoplay) {
							$galleryVideoPlayer.pause();
							$galleryVideoPlayer.currentTime = 0;
						}
					}

					if ($galleryVideoIframe[0]) {
						$galleryVideoIframe[0].src = galleryVideoIframeSrc;

						if (galleryVideoAutoplay) {
							$galleryVideoIframe[0].src = $galleryVideoIframe[0].src.replace('&autoplay=0', '&autoplay=1');
						}

					}
				}

				function videoPopupClose() {
					$galleryVideoPopupTrigger.siblings('.jet-woo-product-video__popup-content').removeClass('jet-woo-product-video__popup--show');
					if ($galleryVideoIframe[0]) {
						$galleryVideoIframe[0].src = '';
					}
					if ($galleryVideoPlayer) {
						$galleryVideoPlayer.currentTime = 0;
						$galleryVideoPlayer.pause();
					}
				}

				function defaultPlayerInit() {
					$galleryVideoDefaultPlayer.mediaelementplayer({
						videoVolume: 'horizontal',
						hideVolumeOnTouchDevices: false,
						enableProgressTooltip: false,
						features: galleryVideoDefaultPlayerControls,
						autoplay: false,
					}).load();
				}

				function defaultPlayerStartPlay( target ) {
					let $videoPlayer = $( target ).parents( '.jet-woo-product-video__overlay' ).siblings().find('.jet-woo-product-video-player')[1];

					$videoPlayer.play();

					$galleryVideoOverlay.remove();
					galleryVideoHasOverlay = false;
				}

				function iframePlayerStartPlay( event ) {
					if ( ! galleryVideoAutoplay ) {
						var $videoTarget = $( event.target ).parents( '.jet-woo-product-video__overlay' ).siblings().find( '.jet-woo-product-video-iframe' );

						$videoTarget[0].src = $videoTarget[0].src.replace('&autoplay=0', '&autoplay=1');
					} else {
						$galleryVideoIframe.each( function() {
							if ( $( this ).parents( '.jet-woo-product-gallery__image-item' ).hasClass( 'swiper-slide-duplicate' ) ) {
								$( this )[0].src = $( this )[0].src.replace('&autoplay=1', '&autoplay=0');
							}
						} );
					}

					$galleryVideoOverlay.remove();
					galleryVideoHasOverlay = false;
				}
			}

			function getImagesData() {
				var data = [];

				if ($galleryImages.length > 0) {
					$galleryImages.each(function (i, element) {
						var img = $(element).find('img');

						if (img.length) {
							var largeImageSrc = img.attr('data-large_image'),
								largeImageWidth = img.attr('data-large_image_width'),
								largeImageHeight = img.attr('data-large_image_height'),
								imageData = {
									src: largeImageSrc,
									w: largeImageWidth,
									h: largeImageHeight,
									title: img.attr('data-caption') ? img.attr('data-caption') : img.attr('title')
								};
							data.push(imageData);
						}
					});
				}

				return data;
			}

		},

	};

	$(window).on('elementor/frontend/init', JetWooProductGallery.init);

}(jQuery, window.elementorFrontend));