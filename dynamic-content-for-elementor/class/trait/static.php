<?php
namespace DynamicContentForElementor;

trait Trait_Static {

	public static function get_post_orderby_options() {
		$orderby = array(
			'ID' => 'Post ID',
			'author' => 'Post Author',
			'title' => 'Title',
			'date' => 'Date',
			'modified' => 'Last Modified Date',
			'parent' => 'Parent ID',
			'rand' => 'Random',
			'comment_count' => 'Comment Count',
			'menu_order' => 'Menu Order',
			'meta_value_num' => 'Meta Value NUM',
			'meta_value_date' => 'Meta Value DATE',
			'meta_value' => 'Meta Value',
			'none' => 'None',
			'type' => 'Type',
			'relevance' => 'Relevance',
			'post__in' => 'Preserve Post ID order given',
		);
		return $orderby;
	}

	public static function get_anim_timing_functions() {
		$tf_p = [
			'linear' => __( 'Linear', 'dynamic-content-for-elementor' ),
			'ease' => __( 'Ease', 'dynamic-content-for-elementor' ),
			'ease-in' => __( 'Ease In', 'dynamic-content-for-elementor' ),
			'ease-out' => __( 'Ease Out', 'dynamic-content-for-elementor' ),
			'ease-in-out' => __( 'Ease In Out', 'dynamic-content-for-elementor' ),
			'cubic-bezier(0.755, 0.05, 0.855, 0.06)' => __( 'easeInQuint', 'dynamic-content-for-elementor' ),
			'cubic-bezier(0.23, 1, 0.32, 1)' => __( 'easeOutQuint', 'dynamic-content-for-elementor' ),
			'cubic-bezier(0.86, 0, 0.07, 1)' => __( 'easeInOutQuint', 'dynamic-content-for-elementor' ),
			'cubic-bezier(0.6, 0.04, 0.98, 0.335)' => __( 'easeInCirc', 'dynamic-content-for-elementor' ),
			'cubic-bezier(0.075, 0.82, 0.165, 1)' => __( 'easeOutCirc', 'dynamic-content-for-elementor' ),
			'cubic-bezier(0.785, 0.135, 0.15, 0.86)' => __( 'easeInOutCirc', 'dynamic-content-for-elementor' ),
			'cubic-bezier(0.95, 0.05, 0.795, 0.035)' => __( 'easeInExpo', 'dynamic-content-for-elementor' ),
			'cubic-bezier(0.19, 1, 0.22, 1)' => __( 'easeOutExpo', 'dynamic-content-for-elementor' ),
			'cubic-bezier(1, 0, 0, 1)' => __( 'easeInOutExpo', 'dynamic-content-for-elementor' ),
			'cubic-bezier(0.6, -0.28, 0.735, 0.045)' => __( 'easeInBack', 'dynamic-content-for-elementor' ),
			'cubic-bezier(0.175, 0.885, 0.32, 1.275)' => __( 'easeOutBack', 'dynamic-content-for-elementor' ),
			'cubic-bezier(0.68, -0.55, 0.265, 1.55)' => __( 'easeInOutBack', 'dynamic-content-for-elementor' ),
		];
		return $tf_p;
	}

	public static function number_format_currency() {
		$nf_c = [
			'en-US' => __( 'English (US)', 'dynamic-content-for-elementor' ),
			'af-ZA' => __( 'Afrikaans', 'dynamic-content-for-elementor' ),
			'sq-AL' => __( 'Albanian', 'dynamic-content-for-elementor' ),
			'ar-AR' => __( 'Arabic', 'dynamic-content-for-elementor' ),
			'hy-AM' => __( 'Armenian', 'dynamic-content-for-elementor' ),
			'ay-BO' => __( 'Aymara', 'dynamic-content-for-elementor' ),
			'az-AZ' => __( 'Azeri', 'dynamic-content-for-elementor' ),
			'eu-ES' => __( 'Basque', 'dynamic-content-for-elementor' ),
			'be-BY' => __( 'Belarusian', 'dynamic-content-for-elementor' ),
			'bn-IN' => __( 'Bengali', 'dynamic-content-for-elementor' ),
			'bs-BA' => __( 'Bosnian', 'dynamic-content-for-elementor' ),
			'en-GB' => __( 'British English', 'dynamic-content-for-elementor' ),
			'bg-BG' => __( 'Bulgarian', 'dynamic-content-for-elementor' ),
			'ca-ES' => __( 'Catalan', 'dynamic-content-for-elementor' ),
			'ck-US' => __( 'Cherokee', 'dynamic-content-for-elementor' ),
			'hr-HR' => __( 'Croatian', 'dynamic-content-for-elementor' ),
			'cs-CZ' => __( 'Czech', 'dynamic-content-for-elementor' ),
			'da-DK' => __( 'Danish', 'dynamic-content-for-elementor' ),
			'nl-NL' => __( 'Dutch', 'dynamic-content-for-elementor' ),
			'nl-BE' => __( 'Dutch (Belgi?)', 'dynamic-content-for-elementor' ),
			'en-UD' => __( 'English (Upside Down)', 'dynamic-content-for-elementor' ),
			'en-US' => __( 'English (US)', 'dynamic-content-for-elementor' ),
			'eo-EO' => __( 'Esperanto', 'dynamic-content-for-elementor' ),
			'et-EE' => __( 'Estonian', 'dynamic-content-for-elementor' ),
			'fo-FO' => __( 'Faroese', 'dynamic-content-for-elementor' ),
			'tl-PH' => __( 'Filipino', 'dynamic-content-for-elementor' ),
			'fi-FI' => __( 'Finnish', 'dynamic-content-for-elementor' ),
			'fi-FI' => __( 'Finland', 'dynamic-content-for-elementor' ),
			'fb-FI' => __( 'Finnish (test)', 'dynamic-content-for-elementor' ),
			'fr-CA' => __( 'French (Canada)', 'dynamic-content-for-elementor' ),
			'fr-FR' => __( 'French (France)', 'dynamic-content-for-elementor' ),
			'gl-ES' => __( 'Galician', 'dynamic-content-for-elementor' ),
			'ka-GE' => __( 'Georgian', 'dynamic-content-for-elementor' ),
			'de-DE' => __( 'German', 'dynamic-content-for-elementor' ),
			'el-GR' => __( 'Greek', 'dynamic-content-for-elementor' ),
			'gn-PY' => __( 'Guaran?', 'dynamic-content-for-elementor' ),
			'gu-IN' => __( 'Gujarati', 'dynamic-content-for-elementor' ),
			'he-IL' => __( 'Hebrew', 'dynamic-content-for-elementor' ),
			'hi-IN' => __( 'Hindi', 'dynamic-content-for-elementor' ),
			'hu-HU' => __( 'Hungarian', 'dynamic-content-for-elementor' ),
			'is-IS' => __( 'Icelandic', 'dynamic-content-for-elementor' ),
			'id-ID' => __( 'Indonesian', 'dynamic-content-for-elementor' ),
			'ga-IE' => __( 'Irish', 'dynamic-content-for-elementor' ),
			'it-IT' => __( 'Italian', 'dynamic-content-for-elementor' ),
			'ja-JP' => __( 'Japanese', 'dynamic-content-for-elementor' ),
			'jv-ID' => __( 'Javanese', 'dynamic-content-for-elementor' ),
			'kn-IN' => __( 'Kannada', 'dynamic-content-for-elementor' ),
			'kk-KZ' => __( 'Kazakh', 'dynamic-content-for-elementor' ),
			'km-KH' => __( 'Khmer', 'dynamic-content-for-elementor' ),
			'tl-ST' => __( 'Klingon', 'dynamic-content-for-elementor' ),
			'ko-KR' => __( 'Korean', 'dynamic-content-for-elementor' ),
			'ku-TR' => __( 'Kurdish', 'dynamic-content-for-elementor' ),
			'la-VA' => __( 'Latin', 'dynamic-content-for-elementor' ),
			'lv-LV' => __( 'Latvian', 'dynamic-content-for-elementor' ),
			'fb-LT' => __( 'Leet Speak', 'dynamic-content-for-elementor' ),
			'li-NL' => __( 'Limburgish', 'dynamic-content-for-elementor' ),
			'lt-LT' => __( 'Lithuanian', 'dynamic-content-for-elementor' ),
			'mk-MK' => __( 'Macedonian', 'dynamic-content-for-elementor' ),
			'mg-MG' => __( 'Malagasy', 'dynamic-content-for-elementor' ),
			'ms-MY' => __( 'Malay', 'dynamic-content-for-elementor' ),
			'ml-IN' => __( 'Malayalam', 'dynamic-content-for-elementor' ),
			'mt-MT' => __( 'Maltese', 'dynamic-content-for-elementor' ),
			'mr-IN' => __( 'Marathi', 'dynamic-content-for-elementor' ),
			'mn-MN' => __( 'Mongolian', 'dynamic-content-for-elementor' ),
			'ne-NP' => __( 'Nepali', 'dynamic-content-for-elementor' ),
			'se-NO' => __( 'Northern S?mi', 'dynamic-content-for-elementor' ),
			'nb-NO' => __( 'Norwegian (bokmal)', 'dynamic-content-for-elementor' ),
			'nn-NO' => __( 'Norwegian (nynorsk)', 'dynamic-content-for-elementor' ),
			'ps-AF' => __( 'Pashto', 'dynamic-content-for-elementor' ),
			'fa-IR' => __( 'Persian', 'dynamic-content-for-elementor' ),
			'pl-PL' => __( 'Polish', 'dynamic-content-for-elementor' ),
			'pt-BR' => __( 'Portuguese (Brazil)', 'dynamic-content-for-elementor' ),
			'pt-PT' => __( 'Portuguese (Portugal)', 'dynamic-content-for-elementor' ),
			'pa-IN' => __( 'Punjabi', 'dynamic-content-for-elementor' ),
			'qu-PE' => __( 'Quechua', 'dynamic-content-for-elementor' ),
			'ro-RO' => __( 'Romanian', 'dynamic-content-for-elementor' ),
			'rm-CH' => __( 'Romansh', 'dynamic-content-for-elementor' ),
			'ru-RU' => __( 'Russian', 'dynamic-content-for-elementor' ),
			'sa-IN' => __( 'Sanskrit', 'dynamic-content-for-elementor' ),
			'sr-RS' => __( 'Serbian', 'dynamic-content-for-elementor' ),
			'zh-CN' => __( 'Simplified Chinese (China)', 'dynamic-content-for-elementor' ),
			'sk-SK' => __( 'Slovak', 'dynamic-content-for-elementor' ),
			'sl-SI' => __( 'Slovenian', 'dynamic-content-for-elementor' ),
			'so-SO' => __( 'Somali', 'dynamic-content-for-elementor' ),
			'es-LA' => __( 'Spanish', 'dynamic-content-for-elementor' ),
			'es-CL' => __( 'Spanish (Chile)', 'dynamic-content-for-elementor' ),
			'es-CO' => __( 'Spanish (Colombia)', 'dynamic-content-for-elementor' ),
			'es-MX' => __( 'Spanish (Mexico)', 'dynamic-content-for-elementor' ),
			'es-ES' => __( 'Spanish (Spain)', 'dynamic-content-for-elementor' ),
			'es-VE' => __( 'Spanish (Venezuela)', 'dynamic-content-for-elementor' ),
			'sw-KE' => __( 'Swahili', 'dynamic-content-for-elementor' ),
			'sv-SE' => __( 'Swedish', 'dynamic-content-for-elementor' ),
			'sy-SY' => __( 'Syriac', 'dynamic-content-for-elementor' ),
			'tg-TJ' => __( 'Tajik', 'dynamic-content-for-elementor' ),
			'ta-IN' => __( 'Tamil', 'dynamic-content-for-elementor' ),
			'tt-RU' => __( 'Tatar', 'dynamic-content-for-elementor' ),
			'te-IN' => __( 'Telugu', 'dynamic-content-for-elementor' ),
			'th-TH' => __( 'Thai', 'dynamic-content-for-elementor' ),
			'zh-HK' => __( 'Traditional Chinese (Hong Kong)', 'dynamic-content-for-elementor' ),
			'zh-TW' => __( 'Traditional Chinese (Taiwan)', 'dynamic-content-for-elementor' ),
			'tr-TR' => __( 'Turkish', 'dynamic-content-for-elementor' ),
			'uk-UA' => __( 'Ukrainian', 'dynamic-content-for-elementor' ),
			'ur-PK' => __( 'Urdu', 'dynamic-content-for-elementor' ),
			'uz-UZ' => __( 'Uzbek', 'dynamic-content-for-elementor' ),
			'vi-VN' => __( 'Vietnamese', 'dynamic-content-for-elementor' ),
			'cy-GB' => __( 'Welsh', 'dynamic-content-for-elementor' ),
			'xh-ZA' => __( 'Xhosa', 'dynamic-content-for-elementor' ),
			'yi-DE' => __( 'Yiddish', 'dynamic-content-for-elementor' ),
			'zu-ZA' => __( 'Zulu', 'dynamic-content-for-elementor' ),
		];
		return $nf_c;
	}

	public static function get_gsap_ease() {
		$tf_p = [
			'easeNone' => __( 'None', 'dynamic-content-for-elementor' ),
			'easeIn' => __( 'In', 'dynamic-content-for-elementor' ),
			'easeOut' => __( 'Out', 'dynamic-content-for-elementor' ),
			'easeInOut' => __( 'InOut', 'dynamic-content-for-elementor' ),
		];
		return $tf_p;
	}

	public static function get_gsap_timing_functions() {
		$tf_p = [
			'Power0' => __( 'Linear', 'dynamic-content-for-elementor' ),
			'Power1' => __( 'Power1', 'dynamic-content-for-elementor' ),
			'Power2' => __( 'Power2', 'dynamic-content-for-elementor' ),
			'Power3' => __( 'Power3', 'dynamic-content-for-elementor' ),
			'Power4' => __( 'Power4', 'dynamic-content-for-elementor' ),
			'SlowMo' => __( ' SlowMo', 'dynamic-content-for-elementor' ),
			'Back' => __( 'Back', 'dynamic-content-for-elementor' ),
			'Elastic' => __( 'Elastic', 'dynamic-content-for-elementor' ),
			'Bounce' => __( 'Bounce', 'dynamic-content-for-elementor' ),
			'Circ' => __( 'Circ', 'dynamic-content-for-elementor' ),
			'Expo' => __( 'Expo', 'dynamic-content-for-elementor' ),
			'Sine' => __( 'Sine', 'dynamic-content-for-elementor' ),
		];
		return $tf_p;
	}

	public static function get_anim_in() {
		$anim = [
			[
				'label' => 'Fading',
				'options' => [
					'fadeIn' => 'Fade In',
					'fadeInDown' => 'Fade In Down',
					'fadeInLeft' => 'Fade In Left',
					'fadeInRight' => 'Fade In Right',
					'fadeInUp' => 'Fade In Up',
				],
			],
			[
				'label' => 'Zooming',
				'options' => [
					'zoomIn' => 'Zoom In',
					'zoomInDown' => 'Zoom In Down',
					'zoomInLeft' => 'Zoom In Left',
					'zoomInRight' => 'Zoom In Right',
					'zoomInUp' => 'Zoom In Up',
				],
			],
			[
				'label' => 'Bouncing',
				'options' => [
					'bounceIn' => 'Bounce In',
					'bounceInDown' => 'Bounce In Down',
					'bounceInLeft' => 'Bounce In Left',
					'bounceInRight' => 'Bounce In Right',
					'bounceInUp' => 'Bounce In Up',
				],
			],
			[
				'label' => 'Sliding',
				'options' => [
					'slideInDown' => 'Slide In Down',
					'slideInLeft' => 'Slide In Left',
					'slideInRight' => 'Slide In Right',
					'slideInUp' => 'Slide In Up',
				],
			],
			[
				'label' => 'Rotating',
				'options' => [
					'rotateIn' => 'Rotate In',
					'rotateInDownLeft' => 'Rotate In Down Left',
					'rotateInDownRight' => 'Rotate In Down Right',
					'rotateInUpLeft' => 'Rotate In Up Left',
					'rotateInUpRight' => 'Rotate In Up Right',
				],
			],
			[
				'label' => 'Attention Seekers',
				'options' => [
					'bounce' => 'Bounce',
					'flash' => 'Flash',
					'pulse' => 'Pulse',
					'rubberBand' => 'Rubber Band',
					'shake' => 'Shake',
					'headShake' => 'Head Shake',
					'swing' => 'Swing',
					'tada' => 'Tada',
					'wobble' => 'Wobble',
					'jello' => 'Jello',
				],
			],
			[
				'label' => 'Light Speed',
				'options' => [
					'lightSpeedIn' => 'Light Speed In',
				],
			],
			[
				'label' => 'Specials',
				'options' => [
					'rollIn' => 'Roll In',
				],
			],
		];
		return $anim;
	}

	public static function get_anim_out() {
		$anim = [
			[
				'label' => 'Fading',
				'options' => [
					'fadeOut' => 'Fade Out',
					'fadeOutDown' => 'Fade Out Down',
					'fadeOutLeft' => 'Fade Out Left',
					'fadeOutRight' => 'Fade Out Right',
					'fadeOutUp' => 'Fade Out Up',
				],
			],
			[
				'label' => 'Zooming',
				'options' => [
					'zoomOut' => 'Zoom Out',
					'zoomOutDown' => 'Zoom Out Down',
					'zoomOutLeft' => 'Zoom Out Left',
					'zoomOutRight' => 'Zoom Out Right',
					'zoomOutUp' => 'Zoom Out Up',
				],
			],
			[
				'label' => 'Bouncing',
				'options' => [
					'bounceOut' => 'Bounce Out',
					'bounceOutDown' => 'Bounce Out Down',
					'bounceOutLeft' => 'Bounce Out Left',
					'bounceOutRight' => 'Bounce Out Right',
					'bounceOutUp' => 'Bounce Out Up',
				],
			],
			[
				'label' => 'Sliding',
				'options' => [
					'slideOutDown' => 'Slide Out Down',
					'slideOutLeft' => 'Slide Out Left',
					'slideOutRight' => 'Slide Out Right',
					'slideOutUp' => 'Slide Out Up',
				],
			],
			[
				'label' => 'Rotating',
				'options' => [
					'rotateOut' => 'Rotate Out',
					'rotateOutDownLeft' => 'Rotate Out Down Left',
					'rotateOutDownRight' => 'Rotate Out Down Right',
					'rotateOutUpLeft' => 'Rotate Out Up Left',
					'rotateOutUpRight' => 'Rotate Out Up Right',
				],
			],
			[
				'label' => 'Attention Seekers',
				'options' => [
					'bounce' => 'Bounce',
					'flash' => 'Flash',
					'pulse' => 'Pulse',
					'rubberBand' => 'Rubber Band',
					'shake' => 'Shake',
					'headShake' => 'Head Shake',
					'swing' => 'Swing',
					'tada' => 'Tada',
					'wobble' => 'Wobble',
					'jello' => 'Jello',
				],
			],
			[
				'label' => 'Light Speed',
				'options' => [
					'lightSpeedOut' => 'Light Speed Out',
				],
			],
			[
				'label' => 'Specials',
				'options' => [
					'rollOut' => 'Roll Out',
				],
			],
		];
		return $anim;
	}

	public static function get_anim_open() {
		$anim_p = [
			'noneIn' => _x( 'None', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'enterFromFade' => _x( 'Fade', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'enterFromLeft' => _x( 'Left', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'enterFromRight' => _x( 'Right', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'enterFromTop' => _x( 'Top', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'enterFromBottom' => _x( 'Bottom', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'enterFormScaleBack' => _x( 'Zoom Back', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'enterFormScaleFront' => _x( 'Zoom Front', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'flipInLeft' => _x( 'Flip Left', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'flipInRight' => _x( 'Flip Right', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'flipInTop' => _x( 'Flip Top', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'flipInBottom' => _x( 'Flip Bottom', 'Ajax Page', 'dynamic-content-for-elementor' ),
		];

		return $anim_p;
	}

	public static function get_anim_close() {
		$anim_p = [
			'noneOut' => _x( 'None', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'exitToFade' => _x( 'Fade', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'exitToLeft' => _x( 'Left', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'exitToRight' => _x( 'Right', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'exitToTop' => _x( 'Top', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'exitToBottom' => _x( 'Bottom', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'exitToScaleBack' => _x( 'Zoom Back', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'exitToScaleFront' => _x( 'Zoom Front', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'flipOutLeft' => _x( 'Flip Left', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'flipOutRight' => _x( 'Flip Right', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'flipOutTop' => _x( 'Flip Top', 'Ajax Page', 'dynamic-content-for-elementor' ),
			'flipOutBottom' => _x( 'Flip Bottom', 'Ajax Page', 'dynamic-content-for-elementor' ),
		];

		return $anim_p;
	}

	public static function bootstrap_button_sizes() {
		return [
			'xs' => __( 'Extra Small', 'dynamic-content-for-elementor' ),
			'sm' => __( 'Small', 'dynamic-content-for-elementor' ),
			'md' => __( 'Medium', 'dynamic-content-for-elementor' ),
			'lg' => __( 'Large', 'dynamic-content-for-elementor' ),
			'xl' => __( 'Extra Large', 'dynamic-content-for-elementor' ),
		];
	}

	public static function get_sql_operators() {
		$compare = self::get_wp_meta_compare();
		$compare['IS NULL'] = 'IS NULL';
		$compare['IS NOT NULL'] = 'IS NOT NULL';
		return $compare;
	}

	public static function get_wp_meta_compare() {
		// meta_compare (string) - Operator to test the 'meta_value'. Possible values are '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'NOT EXISTS', 'REGEXP', 'NOT REGEXP' or 'RLIKE'. Default value is '='.
		return array(
			'=' => '=',
			'>' => '&gt;',
			'>=' => '&gt;=',
			'<' => '&lt;',
			'<=' => '&lt;=',
			'!=' => '!=',
			'LIKE' => 'LIKE',
			'RLIKE' => 'RLIKE',
			'NOT LIKE' => 'NOT LIKE',
			'IN' => 'IN (...)',
			'NOT IN' => 'NOT IN (...)',
			'BETWEEN' => 'BETWEEN',
			'NOT BETWEEN' => 'NOT BETWEEN',
			'EXISTS' => 'EXISTS',
			'NOT EXISTS' => 'NOT EXISTS',
			'REGEXP' => 'REGEXP',
			'NOT REGEXP' => 'NOT REGEXP',
		);
	}

	public static function get_gravatar_styles() {
		$gravatar_images = array(
			'404' => '404 (empty with fallback)',
			'retro' => '8bit',
			'monsterid' => 'Monster (Default)',
			'wavatar' => 'Cartoon face',
			'indenticon' => 'The Quilt',
			'mp' => 'Mystery',
			'mm' => 'Mystery Man',
			'robohash' => 'RoboHash',
			'blank' => 'transparent GIF',
			'gravatar_default' => 'The Gravatar logo',
		);
		return $gravatar_images;
	}

	public static function get_post_formats() {
		return array(
			'standard' => 'Standard',
			'aside' => 'Aside',
			'chat' => 'Chat',
			'gallery' => 'Gallery',
			'link' => 'Link',
			'image' => 'Image',
			'quote' => 'Quote',
			'status' => 'Status',
			'video' => 'Video',
			'audio' => 'Audio',
		);
	}

	public static function get_button_sizes() {
		return [
			'xs' => __( 'Extra Small', 'elementor' ),
			'sm' => __( 'Small', 'elementor' ),
			'md' => __( 'Medium', 'elementor' ),
			'lg' => __( 'Large', 'elementor' ),
			'xl' => __( 'Extra Large', 'elementor' ),
		];
	}

	public static function get_jquery_display_mode() {
		return [
			'' => __( 'None', 'dynamic-content-for-elementor' ),
			'slide' => __( 'Slide', 'dynamic-content-for-elementor' ),
			'fade' => __( 'Fade', 'dynamic-content-for-elementor' ),
		];
	}

	public static function get_string_comparison() {
		return array(
			'empty' => 'empty',
			'not_empty' => 'not empty',
			'equal_to' => 'equals to',
			'not_equal' => 'not equals',
			'gt' => 'greater than',
			'ge' => 'greater than or equal',
			'lt' => 'less than',
			'le' => 'less than or equal',
			'contain' => 'contains',
			'not_contain' => 'not contains',
			'is_checked' => 'is checked',
			'not_checked' => 'not checked',
		);
	}

}
