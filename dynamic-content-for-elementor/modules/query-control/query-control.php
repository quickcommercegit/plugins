<?php

namespace DynamicContentForElementor\Modules\QueryControl;

use Elementor\Core\Base\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class QueryControl extends Module {

	/**
	 * Module constructor.
	 *
	 * @since 1.6.0
	 * @param array $args
	 */
	public function __construct() {
		$this->add_actions();
	}

	/**
	 * Get Name
	 *
	 * Get the name of the module
	 *
	 * @since  1.6.0
	 * @return string
	 */
	public function get_name() {
		return 'dce-query-control';
	}

	/**
	 * Add Actions
	 *
	 * Registeres actions to Elementor hooks
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function add_actions() {
		add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );
	}

	public function ajax_call_filter_autocomplete( array $data ) {

		if ( empty( $data['query_type'] ) || empty( $data['q'] ) ) {
			throw new \Exception( 'Bad Request' );
		}

		$results = call_user_func( [ $this, 'get_' . $data['query_type'] ], $data );

		return [
			'results' => $results,
		];
	}

	protected function get_options( $data ) {
		$results = [];
		$fields = \DynamicContentForElementor\Helper::get_options( $data['q'] );
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field_key => $field_name ) {
				$results[] = [
					'id' => $field_key,
					'text' => $field_name,
				];
			}
		}
		return $results;
	}

	protected function get_fields( $data ) {
		$results = [];
		if ( $data['object_type'] == 'any' ) {
			$object_types = array( 'post', 'user', 'term' );
		} else {
			$object_types = array( $data['object_type'] );
		}
		foreach ( $object_types as $object_type ) {
			$function = 'get_' . $object_type . '_fields';
			$fields = \DynamicContentForElementor\Helper::{$function}( $data['q'] );
			if ( ! empty( $fields ) ) {
				foreach ( $fields as $field_key => $field_name ) {
					$results[] = [
						'id' => $field_key,
						'text' => ( $data['object_type'] == 'any' ? '[' . $object_type . '] ' : '' ) . $field_name,
					];
				}
			}
		}
		return $results;
	}

	protected function get_terms_fields( $data ) {
		$results = [];
		$results = $this->get_fields( $data );
		$terms = \DynamicContentForElementor\Helper::get_taxonomy_terms( null, true, $data['q'] );
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $field_key => $field_name ) {
				$term = \DynamicContentForElementor\Helper::get_term_by( 'id', $field_key );
				$field_key = 'term_' . $term->slug;
				$results[] = [
					'id' => $field_key,
					'text' => ( $data['object_type'] == 'any' ? '[taxonomy_term] ' : '' ) . $field_name,
				];
			}
		}
		return $results;
	}

	protected function get_taxonomies_fields( $data ) {
		$results = [];
		$results = $this->get_fields( $data );
		$taxonomies = \DynamicContentForElementor\Helper::get_taxonomies( false, null, $data['q'] );
		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $field_key => $field_name ) {
				if ( $field_key ) {
					$field_key = 'taxonomy_' . $field_key;
					$results[] = [
						'id' => $field_key,
						'text' => '[taxonomy] ' . $field_name,
					];
				}
			}
		}
		return $results;
	}

	protected function get_metas( $data ) {
		$results = [];
		$function = 'get_' . $data['object_type'] . '_metas';
		$fields = \DynamicContentForElementor\Helper::{$function}( false, $data['q'] );
		foreach ( $fields as $field_key => $field_name ) {
			if ( $field_key ) {
				$results[] = [
					'id' => $field_key,
					'text' => $field_name,
				];
			}
		}
		return $results;
	}

	protected function get_posts( $data ) {
		$results = [];
		$object_type = ( ! empty( $data['object_type'] ) ) ? $data['object_type'] : 'any';

		if ( $object_type == 'type' ) {
			$list = \DynamicContentForElementor\Helper::get_post_types();
			if ( ! empty( $list ) ) {
				foreach ( $list as $akey => $alist ) {
					if ( strlen( $data['q'] ) > 2 ) {
						if ( strpos( $akey, $data['q'] ) === false && strpos( $alist, $data['q'] ) === false ) {
							continue;
						}
					}
					$results[] = [
						'id' => $akey,
						'text' => $alist,
					];
				}
			}
		} else {
			$query_params = [
				'post_type' => $object_type,
				's' => $data['q'],
				'posts_per_page' => -1,
			];
			if ( 'attachment' === $query_params['post_type'] ) {
				$query_params['post_status'] = 'inherit';
			}
			$query = new \WP_Query( $query_params );
			foreach ( $query->posts as $post ) {
				$post_title = $post->post_title;
				if ( empty( $data['object_type'] ) || $object_type == 'any' ) {
					$post_title = '[' . $post->ID . '] ' . $post_title . ' (' . $post->post_type . ')';
				}
				if ( ! empty( $data['object_type'] ) && $object_type == 'elementor_library' ) {
					$etype = get_post_meta( $post->ID, '_elementor_template_type', true );
					$post_title = '[' . $post->ID . '] ' . $post_title . ' (' . $post->post_type . ' > ' . $etype . ')';
				}

				$results[] = [
					'id' => $post->ID,
					'text' => $post_title,
				];
			}
		}
		return $results;
	}

	protected function get_acf( $data ) {
		$results = [];
		$types = ( ! empty( $data['object_type'] ) ) ? $data['object_type'] : array();
		$acfs = \DynamicContentForElementor\Helper::get_acf_fields( $types );
		if ( ! empty( $acfs ) ) {
			foreach ( $acfs as $akey => $acf ) {
				if ( strlen( $data['q'] ) > 2 ) {
					if ( strpos( $akey, $data['q'] ) === false && strpos( $acf, $data['q'] ) === false ) {
						continue;
					}
				}
				$results[] = [
					'id' => $akey,
					'text' => $acf,
				];
			}
		}
		return $results;
	}

	protected function get_acf_flexible_content_layouts( $data ) {
		$groups = acf_get_field_groups();
		$layouts = [];
		foreach ( $groups as $group ) {
			$group_fields = acf_get_fields( $group );
			foreach ( $group_fields as $fields ) {
				if ( $fields['type'] == 'flexible_content' ) {
					foreach ( $fields as $field_key => $field_value ) {
						if ( is_array( $field_value ) && self::array_key_matches_regex( '/layout_[a-zA-Z0-9]+/', $field_value ) ) {
							foreach ( $field_value as $layout_single ) {
									$layouts[] = [
										'id' => $layout_single['name'],
										'text' => $layout_single['name'],
									];
							}
						}
					}
				}
			}
		}
		return $layouts;
	}

	protected function get_acfposts( $data ) {
		$data['object_type'] = array( 'text', 'textarea', 'select', 'number', 'date_time_picker', 'date_picker', 'oembed', 'file', 'url', 'image', 'wysiwyg' );
		$results = $this->get_acf( $data );
		$results[] = array(
			'id' => 'title',
			'text' => __( 'Core > Title [post_title] (text)', 'dynamic-content-for-elementor' ),
		);
		$results[] = array(
			'id' => 'content',
			'text' => __( 'Core > Content [post_content] (text)', 'dynamic-content-for-elementor' ),
		);
		$results[] = array(
			'id' => 'taxonomy',
			'text' => __( 'Core > Taxonomy MetaData (taxonomies)', 'dynamic-content-for-elementor' ),
		);
		$results[] = array(
			'id' => 'date',
			'text' => __( 'Core > Date [post_date] (datetime)', 'dynamic-content-for-elementor' ),
		);
		return $results;
	}

	protected function array_key_matches_regex( $regex, $array ) {
		$postkeys = array_keys( $array );
		foreach ( $postkeys as $key ) {
			if ( preg_match( $regex, $key ) ) {
				return $key;
			}
		}
		return false;
	}

	protected function get_terms( $data ) {
		$results = [];
		$taxonomies = ( ! empty( $data['object_type'] ) ) ? $data['object_type'] : get_object_taxonomies( '' );
		$query_params = [
			'taxonomy' => $taxonomies,
			'search' => $data['q'],
			'hide_empty' => false,
		];
		$terms = get_terms( $query_params );
		foreach ( $terms as $term ) {
			$term_name = $term->name;
			if ( empty( $data['object_type'] ) ) {
				$taxonomy = get_taxonomy( $term->taxonomy );
				$term_name = $taxonomy->labels->singular_name . ': ' . $term_name;
			}
			$results[] = [
				'id' => $term->term_id,
				'text' => $term_name,
			];
		}
		return $results;
	}

	protected function get_users( $data ) {
		$results = [];

		$object_type = ( ! empty( $data['object_type'] ) ) ? $data['object_type'] : false;

		if ( $object_type == 'role' ) {
			$list = \DynamicContentForElementor\Helper::get_roles();
			$list['visitor'] = 'Visitor (non logged User)';
			if ( ! empty( $list ) ) {
				foreach ( $list as $akey => $alist ) {
					if ( strlen( $data['q'] ) > 2 ) {
						if ( strpos( $akey, $data['q'] ) === false && strpos( $alist, $data['q'] ) === false ) {
							continue;
						}
					}
					$results[] = [
						'id' => $akey,
						'text' => $alist,
					];
				}
			}
		} else {
			$query_params = [
				'search' => '*' . $data['q'] . '*',
			];
			if ( empty( $data['object_type'] ) ) {
				$query_params['role__in'] = \DynamicContentForElementor\Helper::str_to_array( ',', $data['object_type'] );
			}
			$users = get_users( $query_params ); // Array of WP_User objects
			foreach ( $users as $user ) {
				$results[] = [
					'id' => $user->ID,
					'text' => $user->display_name,
				];
			}
		}
		return $results;
	}

	protected function get_authors( $data ) {
		$results = [];
		$query_params = [
			'who' => 'authors',
			'has_published_posts' => true,
			'fields' => [
				'ID',
				'display_name',
			],
			'search' => '*' . $data['q'] . '*',
			'search_columns' => [
				'user_login',
				'user_nicename',
			],
		];
		$user_query = new \WP_User_Query( $query_params );
		foreach ( $user_query->get_results() as $author ) {
			$results[] = [
				'id' => $author->ID,
				'text' => $author->display_name,
			];
		}
		return $results;
	}

	/**
	 * Calls function to get value titles depending on ajax query type
	 *
	 * @since  1.6.0
	 * @return array
	 */
	public function ajax_call_control_value_titles( $request ) {
		$results = call_user_func( [ $this, 'get_value_titles_for_' . $request['query_type'] ], $request );
		return $results;
	}

	protected function get_value_titles_for_acf( $request ) {
		$ids = (array) $request['id'];
		$results = [];
		foreach ( $ids as $aid ) {
			$acf = \DynamicContentForElementor\Helper::get_acf_field_post( $aid );
			if ( $acf ) {
				$results[ $aid ] = $acf->post_title;
			}
		}
		return $results;
	}

	protected function get_value_titles_for_acf_flexible_content_layouts( $request ) {
		$ids = (array) $request['id'];
		$results = [];
		foreach ( $ids as $aid ) {
			$results[ $aid ] = $aid;
		}
		return $results;
	}

	protected function get_value_titles_for_acfposts( $request ) {
		$ids = (array) $request['id'];
		$results = $this->get_value_titles_for_acf( $request );

		$core['title'] = __( 'Title', 'dynamic-content-for-elementor' );
		$core['content'] = __( 'Content', 'dynamic-content-for-elementor' );
		$core['taxonomy'] = __( 'Taxonomy MetaData', 'dynamic-content-for-elementor' );
		$core['date'] = __( 'Date', 'dynamic-content-for-elementor' );

		foreach ( $ids as $aid ) {
			if ( isset( $core[ $aid ] ) ) {
				$results[ $aid ] = $core[ $aid ];
			}
		}
		return $results;
	}

	protected function get_value_titles_for_metas( $request ) {
		$ids = (array) $request['id'];
		$results = [];
		$function = 'get_' . $request['object_type'] . '_metas';
		foreach ( $ids as $aid ) {
			$fields = \DynamicContentForElementor\Helper::{$function}( false, $aid );
			foreach ( $fields as $field_key => $field_name ) {
				if ( in_array( $field_key, $ids ) ) {
					$results[ $field_key ] = $field_name;
				}
			}
		}
		return $results;
	}

	protected function get_value_titles_for_fields( $request ) {
		$ids = (array) $request['id'];
		$results = [];
		if ( $request['object_type'] == 'any' ) {
			$object_types = array( 'post', 'user', 'term' );
		} else {
			$object_types = array( $request['object_type'] );
		}
		foreach ( $object_types as $object_type ) {
			$function = 'get_' . $object_type . '_fields';
			foreach ( $ids as $aid ) {
				$fields = \DynamicContentForElementor\Helper::{$function}( $aid );
				if ( ! empty( $fields ) ) {
					foreach ( $fields as $field_key => $field_name ) {
						if ( in_array( $field_key, $ids ) ) {
							$results[ $field_key ] = $field_name;
						}
					}
				}
			}
		}
		return $results;
	}

	protected function get_value_titles_for_posts( $request ) {
		$ids = (array) $request['id'];
		$results = [];
		$is_ctp = false;
		if ( ! empty( $ids ) ) {
			$first = reset( $ids );
			$is_ctp = ! is_numeric( $first );
		}
		if ( $is_ctp ) {
			$post_types = \DynamicContentForElementor\Helper::get_post_types();
			if ( ! empty( $ids ) ) {
				foreach ( $ids as $aid ) {
					if ( isset( $post_types[ $aid ] ) ) {
						$results[ $aid ] = $post_types[ $aid ];
					}
				}
			}
		} else {
			$query = new \DynamicContentForElementor\DCE_Query([
				'post_type' => 'any',
				'post__in' => $ids,
				'posts_per_page' => -1,
			]);
			foreach ( $query->posts as $post ) {
				$results[ $post->ID ] = $post->post_title;
			}
		}
		return $results;
	}

	protected function get_value_titles_for_terms( $request ) {
		$ids = (array) $request['id'];
		$results = [];
		$tid = reset( $ids );
		if ( is_numeric( $tid ) ) {
			$query_params = [
				'include' => $ids,
			];
		} else {
			$query_params = [
				'slug' => $ids,
			];
		}
		$terms = get_terms( $query_params );
		foreach ( $terms as $term ) {
			$results[ $term->term_id ] = $term->name;
		}
		return $results;
	}

	protected function get_value_titles_for_taxonomies( $request ) {
		$ids = (array) $request['id'];
		$results = [];
		foreach ( $ids as $value ) {
			$taxonomies = \DynamicContentForElementor\Helper::get_taxonomies( false, null, $value );
			if ( ! empty( $taxonomies ) ) {
				foreach ( $taxonomies as $field_key => $field_name ) {
					if ( $field_key ) {
						$results[ $field_key ] = $field_name;
					}
				}
			}
		}
		return $results;
	}

	protected function get_value_titles_for_users( $request ) {
		$ids = (array) $request['id'];
		$results = [];
		$is_role = false;
		if ( ! empty( $ids ) ) {
			$first = reset( $ids );
			$is_role = ! is_numeric( $first );
		}
		if ( $is_role ) {
			$roles = \DynamicContentForElementor\Helper::get_roles();
			$roles['visitor'] = 'Visitor (non logged User)';
			if ( ! empty( $ids ) ) {
				foreach ( $ids as $aid ) {
					if ( isset( $roles[ $aid ] ) ) {
						$results[ $aid ] = $roles[ $aid ];
					}
				}
			}
		} else {
			$query_params = [
				'fields' => [
					'ID',
					'display_name',
				],
				'include' => $ids,
			];
			$user_query = new \WP_User_Query( $query_params );
			foreach ( $user_query->get_results() as $user ) {
				$results[ $user->ID ] = $user->display_name;
			}
		}
		return $results;
	}

	protected function get_value_titles_for_authors( $request ) {
		$ids = (array) $request['id'];
		$results = [];
		$query_params = [
			'who' => 'authors',
			'has_published_posts' => true,
			'fields' => [
				'ID',
				'display_name',
			],
			'include' => $ids,
		];
		$user_query = new \WP_User_Query( $query_params );
		foreach ( $user_query->get_results() as $author ) {
			$results[ $author->ID ] = $author->display_name;
		}
		return $results;
	}

	protected function get_value_titles_for_terms_fields( $request ) {
		$ids = (array) $request['id'];
		$ids_post = array();
		$ids_term = array();
		foreach ( $ids as $aid ) {
			if ( substr( $aid, 0, 5 ) == 'term_' ) {
				$ids_term[] = substr( $aid, 5 );
			} else {
				$ids_post[] = $aid;
			}
		}
		$results = [];
		if ( ! empty( $ids_post ) ) {
			$request['id'] = $ids_post;
			$posts = $this->get_value_titles_for_fields( $request );
			if ( ! empty( $posts ) ) {
				foreach ( $posts as $key => $value ) {
					$results[ $key ] = $value;
				}
			}
		}
		if ( ! empty( $ids_term ) ) {
			$request['id'] = $ids_term;
			$terms = $this->get_value_titles_for_terms( $request );
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $key => $value ) {
					$results[ 'term_' . $key ] = $value;
				}
			}
		}
		return $results;
	}

	protected function get_value_titles_for_taxonomies_fields( $request ) {
		$ids = (array) $request['id'];
		$ids_post = array();
		$ids_tax = array();
		foreach ( $ids as $aid ) {
			if ( substr( $aid, 0, 9 ) == 'taxonomy_' ) {
				$ids_tax[] = substr( $aid, 9 );
			} else {
				$ids_post[] = $aid;
			}
		}
		$results = [];
		if ( ! empty( $ids_post ) ) {
			$request['id'] = $ids_post;
			$posts = $this->get_value_titles_for_fields( $request );
			if ( ! empty( $posts ) ) {
				foreach ( $posts as $key => $value ) {
					$results[ $key ] = $value;
				}
			}
		}
		if ( ! empty( $ids_tax ) ) {
			$request['id'] = $ids_tax;
			$taxonomies = $this->get_value_titles_for_taxonomies( $request );
			if ( ! empty( $taxonomies ) ) {
				foreach ( $taxonomies as $key => $value ) {
					$results[ 'taxonomy_' . $key ] = $value;
				}
			}
		}
		return $results;
	}

	public function register_ajax_actions( $ajax_manager ) {
		$ajax_manager->register_ajax_action( 'dce_query_control_value_titles', [ $this, 'ajax_call_control_value_titles' ] );
		$ajax_manager->register_ajax_action( 'dce_query_control_filter_autocomplete', [ $this, 'ajax_call_filter_autocomplete' ] );
	}

}
