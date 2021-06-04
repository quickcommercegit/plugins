<?php
/**
 * Content helper.
 *
 * @package Ultimate_Dashboard_Pro
 */

namespace UdbPro\Helpers;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use Udb\Helpers\Content_Helper as Free_Content_Helper;

/**
 * Class to setup content helper.
 */
class Content_Helper extends Free_Content_Helper {

	/**
	 * Check whether or not post is built with Elementor.
	 *
	 * @param int $post_id ID of the post being checked.
	 * @return bool
	 */
	public function is_built_with_elementor( $post_id ) {
		return ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->documents->get( $post_id )->is_built_with_elementor() ? true : false );
	}

	/**
	 * Check whether or not post is built with Beaver Builder.
	 *
	 * @param int $post_id ID of the post being checked.
	 * @return bool
	 */
	public function is_built_with_beaver( $post_id ) {
		return ( class_exists( '\FLBuilderModel' ) && \FLBuilderModel::is_builder_enabled( $post_id ) ? true : false );
	}

	/**
	 * Check whether the post type of the given post id is checked in Brizy settings.
	 *
	 * @see wp-content/plugins/brizy/editor.php
	 *
	 * @param int $post_id The post ID to check.
	 */
	public function supported_in_brizy_post_types( $post_id ) {

		$post = get_post( $post_id );

		$brizy_editor         = \Brizy_Editor::get();
		$supported_post_types = $brizy_editor->supported_post_types();

		if ( in_array( $post->post_type, $supported_post_types, true ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Check whether or not post is built with Brizy Builder.
	 *
	 * @param int $post_id ID of the post being checked.
	 * @return bool
	 */
	public function is_built_with_brizy( $post_id ) {

		if ( class_exists( '\Brizy_Editor_Post' ) ) {

			if ( ! $this->supported_in_brizy_post_types( $post_id ) ) {
				return false;
			}

			try {
				$post = \Brizy_Editor_Post::get( $post_id );

				if ( is_object( $post ) && method_exists( $post, 'uses_editor' ) && $post->uses_editor() ) {
					return true;
				}
			} catch ( Exception $e ) {
				return false;
			}
		}

		return false;

	}

	/**
	 * Check whether or not post is built with Divi Builder.
	 *
	 * @param int $post_id ID of the post being checked.
	 * @return bool
	 */
	public function is_built_with_divi( $post_id ) {
		return ( function_exists( 'et_pb_is_pagebuilder_used' ) && et_pb_is_pagebuilder_used( $post_id ) ? true : false );
	}

	/**
	 * Check whether or not post is built with WordPress block editor.
	 *
	 * @param int $post_id ID of the post being checked.
	 * @return bool
	 */
	public function is_built_with_blocks( $post_id ) {
		if ( version_compare( $GLOBALS['wp_version'], '5.0', '<' ) ) {
			return false;
		}

		if ( ! function_exists( 'has_blocks' ) || ! has_blocks( $post_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the editor/ builder of the given post.
	 *
	 * @param int $post_id ID of the post being checked.
	 * @return string The content editor name.
	 */
	public function get_content_editor( $post_id ) {

		if ( $this->is_built_with_elementor( $post_id ) ) {
			return 'elementor';
		} elseif ( $this->is_built_with_beaver( $post_id ) ) {
			return 'beaver';
		} elseif ( $this->is_built_with_brizy( $post_id ) ) {
			return 'brizy';
		} elseif ( $this->is_built_with_divi( $post_id ) ) {
			return 'divi';
		} elseif ( $this->is_built_with_blocks( $post_id ) ) {
			return 'block';
		}

		return 'default';

	}

	/**
	 * Get active page builders.
	 *
	 * @return array The list of builder names.
	 */
	public function get_active_page_builders() {

		$names = array();

		if ( defined( 'ELEMENTOR_VERSION' ) || defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			array_push( $names, 'elementor' );
		}

		if ( defined( 'ET_BUILDER_VERSION' ) ) {
			array_push( $names, 'divi' );
		}

		if ( defined( 'FL_BUILDER_VERSION' ) ) {
			array_push( $names, 'beaver' );
		}

		if ( defined( 'BRIZY_VERSION' ) ) {
			array_push( $names, 'brizy' );
		}

		return $names;

	}

	/**
	 * Parse content with the specified builder in admin area.
	 *
	 * @param WP_Post|int $post Either the admin page's post object or post ID.
	 * @param string      $builder_name The content builder name.
	 */
	public function output_content_using_builder( $post, $builder_name ) {

		if ( is_int( $post ) ) {
			$post_id = $post;
			$post    = get_post( $post_id );
		} elseif ( is_object( $post ) && property_exists( $post, 'ID' ) ) {
			$post_id = $post->ID;
		} else {
			return;
		}

		if ( 'elementor' === $builder_name ) {

			$elementor = \Elementor\Plugin::$instance;

			$elementor->frontend->register_styles();
			$elementor->frontend->enqueue_styles();

			echo $elementor->frontend->get_builder_content( $post_id, true );

			$elementor->frontend->register_scripts();
			$elementor->frontend->enqueue_scripts();

		} elseif ( 'beaver' === $builder_name ) {

			echo do_shortcode( '[fl_builder_insert_layout id="' . $post_id . '"]' );

		} elseif ( 'divi' === $builder_name ) {

			$style_suffix = et_load_unminified_styles() ? '' : '.min';

			wp_enqueue_style( 'et-builder-modules-style', ET_BUILDER_URI . '/styles/frontend-builder-plugin-style' . $style_suffix . '.css', array(), ET_BUILDER_VERSION );

			$post_content = $post->post_content;
			$post_content = et_builder_get_layout_opening_wrapper() . $post_content . et_builder_get_layout_closing_wrapper();
			$post_content = et_builder_get_builder_content_opening_wrapper() . $post_content . et_builder_get_builder_content_closing_wrapper();

			echo apply_filters( 'the_content', $post_content );

		} elseif ( 'brizy' === $builder_name ) {

			$this->render_brizy_content( $post_id );

		} else {

			echo apply_filters( 'the_content', $post->post_content );

		}

	}

	/**
	 * Prepare Brizy output.
	 *
	 * This source can be found at `is_view_page` condition inside `initialize_front_end` function
	 * in brizy/public/main.php file.
	 *
	 * What we don't use from that function:
	 * - template_include hook
	 * - preparePost private function
	 * - plugin_live_composer_fixes private function
	 * - remove `wpautop` filter from `the_content` (moved to our `render_brizy_content` function)
	 *
	 * @see wp-content/plugins/brizy/public/main.php
	 *
	 * @param int    $post_id The post id.
	 * @param string $location The output location.
	 *                  Accepts "frontend", and other values (such as "admin_page", "dashboard").
	 */
	public function prepare_brizy_output( $post_id, $location = 'admin_page' ) {

		$brizy_post   = \Brizy_Editor_Post::get( $post_id );
		$brizy_public = \Brizy_Public_Main::get( $brizy_post );

		if ( 'admin_page' === $location || 'dashboard' === $location ) {

			/**
			 * Check if the post needs to be compiled.
			 *
			 * Let's compile it if it hasn't been compiled.
			 * However, when compiling it, it takes sometime.
			 *
			 * That's why it takes time / very slow when
			 * first time visiting the dashboard / admin page
			 * or first time visiting it after the post being updated with Brizy.
			 *
			 * However, in the next visit (since it has been compiled), it will be much faster.
			 *
			 * @see wp-content/plugins/brizy/public/main.php
			 * @see wp-content/plugins/brizy/editor/post.php
			 */
			$needs_compile = ! $brizy_post->isCompiledWithCurrentVersion() || $brizy_post->get_needs_compile();

			if ( $needs_compile ) {
				$brizy_post->compile_page();
				$brizy_post->saveStorage();
				$brizy_post->savePost();
			}

			// The value of $body_class is array, let's convert it to string.
			$body_classes = $brizy_public->body_class_frontend( array() );
			$body_classes = implode( ' ', $body_classes );

			add_filter(
				'admin_body_class',
				function ( $classes ) use ( $body_classes ) {
					return $classes . ' ' . $body_classes;
				}
			);

			// Insert the compiled head and content.
			add_action( 'admin_head', array( $brizy_public, 'insert_page_head' ) );
			add_action( 'admin_enqueue_scripts', array( $brizy_public, '_action_enqueue_preview_assets' ), 9999 );
			add_filter( 'the_content', array( $brizy_public, 'insert_page_content' ), - 12000 );
			add_action( 'brizy_template_content', array( $brizy_public, 'brizy_the_content' ) );

		} else {

			// Insert the compiled head and content.
			add_filter( 'body_class', array( $brizy_public, 'body_class_frontend' ) );
			add_action( 'wp_head', array( $brizy_public, 'insert_page_head' ) );
			add_action( 'admin_bar_menu', array( $brizy_public, 'toolbar_link' ), 999 );
			add_action( 'wp_enqueue_scripts', array( $brizy_public, '_action_enqueue_preview_assets' ), 9999 );
			add_filter( 'the_content', array( $brizy_public, 'insert_page_content' ), - 12000 );
			add_action( 'brizy_template_content', array( $brizy_public, 'brizy_the_content' ) );

		}

	}

	/**
	 * Render Brizy content.
	 *
	 * @see wp-content/plugins/brizy/public/main.php
	 *
	 * @param int $post_id The post id.
	 */
	public function render_brizy_content( $post_id ) {

		// @see wp-content/plugins/brizy/public/main.php
		remove_filter( 'the_content', 'wpautop' );

		$brizy_post   = \Brizy_Editor_Post::get( $post_id );
		$brizy_public = \Brizy_Public_Main::get( $brizy_post );

		$brizy_public->brizy_the_content();

		// Let's bring back the filter after rendering the content.
		add_filter( 'the_content', 'wpautop' );

	}

	/**
	 * Get saved templates for specified page builder.
	 *
	 * @param string $builder The page builder name.
	 * @return array The saved templates.
	 */
	public function get_page_builder_templates( $builder ) {

		$templates = array();

		if ( 'elementor' === $builder ) {
			$builder_posts = get_posts(
				array(
					'post_type'   => 'elementor_library',
					'post_status' => 'publish',
					'numberposts' => -1,
				)
			);

			foreach ( $builder_posts as $builder_post ) {
				array_push(
					$templates,
					array(
						'id'      => $builder_post->ID,
						'title'   => $builder_post->post_title,
						'builder' => 'elementor',
					)
				);
			}
		} elseif ( 'divi' === $builder ) {
			$builder_posts = get_posts(
				array(
					'post_type'   => 'et_pb_layout',
					'post_status' => 'publish',
					'numberposts' => -1,
				)
			);

			foreach ( $builder_posts as $builder_post ) {
				array_push(
					$templates,
					array(
						'id'      => $builder_post->ID,
						'title'   => $builder_post->post_title,
						'builder' => 'divi',
					)
				);
			}
		} elseif ( 'beaver' === $builder ) {
			if ( class_exists( '\FLBuilderModel' ) ) {
				$builder_posts = get_posts(
					array(
						'post_type'   => 'fl-builder-template',
						'post_status' => 'publish',
						'numberposts' => -1,
					)
				);

				foreach ( $builder_posts as $builder_post ) {
					array_push(
						$templates,
						array(
							'id'      => $builder_post->ID,
							'title'   => $builder_post->post_title,
							'builder' => 'beaver',
						)
					);
				}
			}
		} elseif ( 'brizy' === $builder ) {
			$builder_posts = get_posts(
				array(
					'post_type'   => 'brizy_template',
					'post_status' => 'publish',
					'numberposts' => -1,
				)
			);

			foreach ( $builder_posts as $builder_post ) {
				array_push(
					$templates,
					array(
						'id'      => $builder_post->ID,
						'title'   => $builder_post->post_title,
						'builder' => 'brizy',
					)
				);
			}
		}

		return $templates;

	}

}
