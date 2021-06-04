<?php

if ( ! class_exists( 'Jet_Engine_Booking_Forms_Dashboard_Tab' ) ) {

	class Jet_Engine_Booking_Forms_Dashboard_Tab extends \Jet_Engine\Dashboard\Base_Tab {

		public function slug(): string {
			return 'forms';
		}

		public function label(): string {
			return __( 'Forms Settings', 'jet-engine' );
		}

		public function load_config(): array {
			return apply_filters( 'jet-engine/dashboard/forms-config', array() );
		}

	}
}