<?php
namespace DynamicContentForElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Metabox {

	public function __construct() {
		$this->init();
	}

	public function init() {
	}

	public static function init_template_system() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$class = get_class();

		// metabox Template in page
		add_action( 'add_meta_boxes', $class . '::dce_metabox_template', 1, 2 );
		add_action( 'save_post', $class . '::dce_save_metaboxdata_template', 1, 2 );

		// metabox Template in elementor_library for Demo
		add_action( 'add_meta_boxes', $class . '::dce_metabox_demoID', 1, 2 );
		add_action( 'save_post', $class . '::dce_save_metaboxdata_demoID', 1, 2 );

		// metabox Template for terms
		add_action( 'admin_init', $class . '::dce_taxonomybox_init' );
	}

	public static function dce_metabox_template( $post_type = 'post', $post = false ) {
		$class = get_class();
		if ( in_array( $post_type, Helper::get_types_registered() ) ) {
			add_meta_box( 'dce_metabox', __( 'Dynamic Content Template System', 'dynamic-content-for-elementor' ), $class . '::dce_metabox_template_select', null, 'side' ); //, 'post', 'normal', 'default' );
		}
	}

	public static function dce_metabox_template_select( $post_object ) {

		$html = '';

		$templates = Helper::get_all_template( true );
		$dyncontel_elementor_templates = get_post_meta( $post_object->ID, 'dyncontel_elementor_templates', true );
		if ( ! empty( $templates ) ) {
			$html .= '<label for="dce_post_template"><strong>' . esc_html__( 'Assign an Elementor Template', 'dynamic-content-for-elementor' )
				. '</strong></label><br /><select id="dce_post_template" name="dyncontel_elementor_templates" class="js-dce-select">';
			foreach ( $templates as $akey => $atmp ) {
				$selected = ( $dyncontel_elementor_templates && $dyncontel_elementor_templates == $akey ) ? ' selected="selected"' : '';
				$html .= '<option value="' . $akey . '"' . $selected . '>' . $atmp . '</option>';
			}
			$html .= '<select>';

			if ( $post_object->post_parent ) {
				$dyncontel_elementor_templates_parent = get_post_meta( $post_object->ID, 'dyncontel_elementor_templates_parent', true );
				$html .= '<br /><label for="dce_post_template_parent"><input type="checkbox" value="1" name="dyncontel_elementor_templates_parent" id="dce_post_template_parent"' . ( $dyncontel_elementor_templates_parent ? ' checked' : '' ) . '>' . __( 'From Parent', 'dynamic-content-for-elementor' ) . '</label>';
			}
		}

		echo $html;
	}

	public static function dce_save_metaboxdata_template( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// if post type is different from our selected one, do nothing
		if ( in_array( $post->post_type, Helper::get_types_registered() ) ) {
			if ( isset( $_POST['dyncontel_elementor_templates'] ) ) {
				update_post_meta( $post_id, 'dyncontel_elementor_templates', sanitize_text_field( $_POST['dyncontel_elementor_templates'] ) );
			}

			if ( isset( $_POST['dyncontel_elementor_templates_parent'] ) ) {
				update_post_meta( $post_id, 'dyncontel_elementor_templates_parent', sanitize_text_field( $_POST['dyncontel_elementor_templates_parent'] ) );
			} else {
				delete_post_meta( $post_id, 'dyncontel_elementor_templates_parent' );
			}
		}
		return $post_id;
	}

	public static function dce_metabox_demoID( $post_type, $post ) {
		$class = get_class();
		if ( $post_type == 'elementor_library' ) {
			add_meta_box( 'dce_metabox', __( 'Template Preview', 'dynamic-content-for-elementor' ), $class . '::dce_metabox_demoID_post', null, 'side' ); //, 'post', 'normal', 'default' );
		}
	}


	public static function dce_metabox_demoID_post( $post_object ) {
		$html = '';
		$all_posts = Helper::get_all_posts( null, true );

		$proModule = WP_PLUGIN_DIR . '/elementor-pro/modules/theme-builder/module.php';
		if ( file_exists( $proModule ) && Helper::is_elementorpro_active() ) {
			include_once $proModule;
			$document = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_document( $post_object->ID );
			// On view theme document show it's preview content.
			if ( $document ) {
				$preview_type = $document->get_settings( 'preview_type' );
				$preview_id = $document->get_settings( 'preview_id' );
				$demo_id = $preview_id;
				update_post_meta( $post_object->ID, 'demo_id', $preview_id );
			}
		}

		$demo_id = get_post_meta( $post_object->ID, 'demo_id', true );

		if ( ! empty( $all_posts ) ) {
			$html .= '<label for="dce_post_demoid"><strong><span class="dashicons dashicons-admin-network"></span> ' . esc_html__( 'Select post', 'dynamic-content-for-elementor' )
					. '</strong></label><br /><select id="dce_post_demoid" name="demo_id" class="js-dce-select">';
			foreach ( $all_posts as $tkey => $ttmp ) {
				if ( isset( $ttmp['options'] ) ) {
					$html .= '<optgroup label="' . $ttmp['label'] . '">';
					foreach ( $ttmp['options'] as $akey => $atmp ) {
						$selected = ( $demo_id && $demo_id == $akey ) ? ' selected="selected"' : '';
						$html .= '<option value="' . $akey . '"' . $selected . '>' . $atmp . '</option>';
					}
					$html .= '</optgroup>';
				} else {
					$selected = ( $demo_id && $demo_id == $tkey ) ? ' selected="selected"' : '';
					$html .= '<option value="' . $tkey . '"' . $selected . '>' . $ttmp . '</option>';
				}
			}
			$html .= '<select></p>';
		}
		echo $html;
	}

	public static function dce_save_metaboxdata_demoID( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// if post type is different from our selected one, do nothing
		if ( $post->post_type == 'elementor_library' ) {
			if ( isset( $_POST['demo_id'] ) ) {
				update_post_meta( $post_id, 'demo_id', sanitize_text_field( $_POST['demo_id'] ) );

				// se esiste anche Elementor Pro aggiorno pure lui
				$proSettings = get_post_meta( $post_id, '_elementor_page_settings', true );
				if ( empty( $proSettings ) ) {
					$proSettings = array();
				}
				$proSettings['preview_id'] = sanitize_text_field( $_POST['demo_id'] );

				update_post_meta( $post_id, '_elementor_page_settings', $proSettings );
			}
		}
		return $post_id;
	}


	/**
	* custom option and settings
	*/
	// ************************************** SETTINGS INIT
	public static function dce_taxonomybox_init() {
		$args = array(
			'public' => true,
		);

		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'

		$class = get_class();

		$taxonomyesRegistered = get_taxonomies( $args, $output, $operator );
		foreach ( $taxonomyesRegistered as $chiave ) {
			add_action( $chiave . '_add_form_fields', $class . '::taxonomyname_metabox_add', 10, 1 );
			add_action( $chiave . '_edit_form_fields', $class . '::taxonomyname_metabox_edit', 10, 1 );
			add_action( 'created_' . $chiave, $class . '::save_taxonomyname_metadata', 10, 1 );
			add_action( 'edited_' . $chiave, $class . '::save_taxonomyname_metadata', 10, 1 );
		}
	}

	// Add metabox Terms ----------------------------------------------
	public static function taxonomyname_metabox_add( $tag ) {
		?>
		<div id="dce_termbox" class="dce-term-box">
			<div class="dce-term-head">
				<h3><?php _e( 'Dynamic Template', 'dynamic-content-for-elementor' ); ?></h3>
			</div>
			<div class="form-field dce-term dce-term-add">
		<?php
		echo self::render_select_metabox( $tag, 'add' ); ?>
			</div>
		</div>
			<style>#dce_termbox { display: none; }</style>
			<?php
	}

	public static function taxonomyname_metabox_edit( $tag ) {
		?>
		<tr class="form-field dce-term dce-term-edit">
			<th scope="row" valign="top">
				<label for="dynamic_content"><?php _e( 'Dynamic Template', 'dynamic-content-for-elementor' ); ?></label>
			</th>
			<td>
				<?php
				echo self::render_select_metabox( $tag, 'edit' ); ?>
			</td>
		</tr>
		<?php
	}

	public static function render_select_metabox( $tag, $mode ) {
		$templates = Helper::get_all_template( true );
		$isSel = ''; ?>
		<label><?php _e( 'Head', 'dynamic-content-for-elementor' ); ?></label>
		<select class="js-dce-select" id="dynamic_content_head" name="dynamic_content_head"> <!--Supplement an id here instead of using 'name' 'dyncontel_field_archive'.$chiave -->
		<?php
		foreach ( $templates as $key => $value ) {
			if ( $mode == 'edit' ) {
				$isSel = ( get_term_meta( $tag->term_id, 'dynamic_content_head', true ) == $key ? ' selected' : '' );
			} ?>
				<option value="<?php echo $key; ?>"<?php echo $isSel; ?>><?php echo $value; ?></option>
				<?php
		} ?>
		</select>
		<br>
		<label><?php _e( 'Blocks/Canvas', 'dynamic-content-for-elementor' ); ?></label>
		<select class="js-dce-select" id="dynamic_content_block" name="dynamic_content_block"> <!--Supplement an id here instead of using 'name' 'dyncontel_field_archive'.$chiave -->
			}
		<?php
		foreach ( $templates as $key => $value ) {
			if ( $mode == 'edit' ) {
				$isSel = ( get_term_meta( $tag->term_id, 'dynamic_content_block', true ) == $key ? ' selected' : '' );
			} ?>
				<option value="<?php echo $key; ?>"<?php echo $isSel; ?>><?php echo $value; ?></option>
				<?php
		} ?>
		</select>
		<br>
		<label><?php _e( 'Single', 'dynamic-content-for-elementor' ); ?></label>
		<select class="js-dce-select" id="dynamic_content_single" name="dynamic_content_single"> <!--Supplement an id here instead of using 'name' 'dyncontel_field_archive'.$chiave -->
		<?php
		foreach ( $templates as $key => $value ) {
			if ( $mode == 'edit' ) {
				$isSel = ( get_term_meta( $tag->term_id, 'dynamic_content_single', true ) == $key ? ' selected' : '' );
			} ?>
				<option value="<?php echo $key; ?>"<?php echo $isSel; ?>><?php echo $value; ?></option>
				<?php
		} ?>
		</select>
			<?php
	}

	public static function save_taxonomyname_metadata( $term_id ) {
		if ( isset( $_POST['dynamic_content_head'] ) ) {
			update_term_meta( $term_id, 'dynamic_content_head', sanitize_text_field( $_POST['dynamic_content_head'] ) );
		}
		if ( isset( $_POST['dynamic_content_block'] ) ) {
			update_term_meta( $term_id, 'dynamic_content_block', sanitize_text_field( $_POST['dynamic_content_block'] ) );
		}
		if ( isset( $_POST['dynamic_content_single'] ) ) {
			update_term_meta( $term_id, 'dynamic_content_single', sanitize_text_field( $_POST['dynamic_content_single'] ) );
		}
	}
}
