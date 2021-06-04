<?php

class PAFE_License_Service {

    private static function get_domain( $url ) {
        return preg_replace( '/^www\./', '', wp_parse_url( $url, PHP_URL_HOST ) );
    }

    public static function login( $credential ) {
        $body = [
                'domain'       => self::get_domain( get_option( 'siteurl' ) ),
                'pro_version'  => PAFE_PRO_VERSION,
                'wp_version' => get_bloginfo( 'version' ),
                'php_version' => PHP_VERSION,
            ] + $credential;

        $response = wp_remote_post(
            'https://pafe.piotnet.com/connect/v1/get_license.php',
            [
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'body'        => $body,
                'sslverify'   => false,
            ]
        );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            return [
                'error' => "Can't connect to PAFE: " . $error_message,
            ];
        }

        $response_body = wp_remote_retrieve_body( $response );
        if ( is_wp_error( $response_body ) ) {
            $error_message = $response_body->get_error_message();
            return [
                'error' => "Can't retrieve body from PAFE: " . $error_message,
            ];
        }

        $response_data = json_decode( $response_body, true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return [
                'error' => "Can't parse data from PAFE.",
            ];
        }

        if ( $response_data['status'] === 'OK' ) {
            update_option( 'piotnet_addons_for_elementor_pro_license', $response_data['license'] );
            delete_option('piotnet-addons-for-elementor-pro-username');
            delete_option('piotnet-addons-for-elementor-pro-password');
        }

        return $response_data;
    }

    public static function remove_site( $credential ) {
        $body = [
                'domain'       => self::get_domain( get_option( 'siteurl' ) ),
                'pro_version'  => PAFE_PRO_VERSION,
                'wp_version' => get_bloginfo( 'version' ),
                'php_version' => PHP_VERSION,
            ] + $credential;

        $response = wp_remote_post(
            'https://pafe.piotnet.com/connect/v1/remove_site.php',
            [
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'body'        => $body,
                'sslverify'   => false,
            ]
        );

        delete_option('piotnet_addons_for_elementor_pro_license');

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            return [
                'error' => "Can't connect to PAFE: " . $error_message,
            ];
        }

        $response_body = wp_remote_retrieve_body( $response );
        if ( is_wp_error( $response_body ) ) {
            $error_message = $response_body->get_error_message();
            return [
                'error' => "Can't retrieve body from PAFE: " . $error_message,
            ];
        }

        return null;
    }
}

function PAFE_do_login() {
    if (!isset($_POST['username'])) {
        return [ 'error' => 'Please fill username' ];
    } else if (!isset($_POST['password'])) {
        return [ 'error' => 'Please fill password' ];
    } else {
        $credential = [
            'username'     => $_POST['username'],
            'password'     => $_POST['password'],
        ];
        return PAFE_License_Service::login($credential);
    }
}

function PAFE_do_remove_license() {
    $license = get_option( 'piotnet_addons_for_elementor_pro_license' );
    if(empty($license)) {
        return null;
    }

    $credential = [
        'license_key' => $license['license_key']
    ];
    return PAFE_License_Service::remove_site($credential);
}

$pafe_username = get_option('piotnet-addons-for-elementor-pro-username');
$pafe_password = get_option('piotnet-addons-for-elementor-pro-password');

if (isset($_POST['action']) && $_POST['action'] == 'active_license'){
    $login_response = PAFE_do_login();
} else if (isset($_POST['action']) && $_POST['action'] == 'remove_license'){
    $login_response = PAFE_do_remove_license();
} else if (!empty($pafe_username) && !empty($pafe_password)) {
    $credential = [
        'username'     => $pafe_username,
        'password'     => $pafe_password,
    ];
    $login_response = PAFE_License_Service::login($credential);
} else if ( !isset($_POST['action']) ) {
    $license = get_option( 'piotnet_addons_for_elementor_pro_license' );
    if(!empty($license)) {
        $credential = [
            'license_key' => $license['license_key']
        ];
        $login_response = PAFE_License_Service::login($credential);
    }
}

$message = '';
if (!empty($login_response) && !empty($login_response['error'])) {
    $message = $login_response['error'];
}

$license = get_option( 'piotnet_addons_for_elementor_pro_license' );
$has_license = PAFE_has_license($license);
$has_valid_license = PAFE_has_valid_license($license);

?>
<div class="wrap">
	<div class="pafe-header">
		<div class="pafe-header__left">
			<div class="pafe-header__logo">
				<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/images/piotnet-logo.png'; ?>" alt="">
			</div>
			<h2 class="pafe-header__headline"><?php _e('Piotnet Addons For Elementor Settings (PAFE PRO)','pafe'); ?></h2>
		</div>
		<div class="pafe-header__right">
				<a class="pafe-header__button pafe-header__button--gradient" href="https://pafe.piotnet.com/?wpam_id=1" target="_blank"><?php if( $has_license != 1 ) { _e('GO PRO NOW','pafe'); } else { _e('GO TO PAFE','pafe'); } ?></a>
		</div>
	</div>
	<div class="pafe-wrap">

		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('Tutorials','pafe'); ?></h3>
				<a href="https://pafe.piotnet.com/?wpam_id=1" target="_blank">https://pafe.piotnet.com/?wpam_id=1</a>
				<h3><?php _e('Support','pafe'); ?></h3>
				<a href="mailto:support@piotnet.com">support@piotnet.com</a>
				<h3><?php _e('Reviews','pafe'); ?></h3>
				<a href="https://wordpress.org/support/plugin/piotnet-addons-for-elementor/reviews/?filter=5#new-post" target="_blank">https://wordpress.org/plugins/piotnet-addons-for-elementor/#reviews</a>
			</div>
            <div class="pafe-bottom__right">
                <div class="pafe-license">
                    <h3><?php _e('License','pafe'); ?></h3>
                    <div class="pafe-license__description">
                        <?php
                        if (!empty($message)) {
                            ?>
                            <div class="pafe-license__description">Status: <?php echo $message; ?></div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                    if (!$has_license) {
                        ?>
                        <?php _e('Enter Your Account at ','pafe'); ?><a href="https://pafe.piotnet.com/my-account/" target="_blank">https://pafe.piotnet.com/my-account/</a> <?php _e('to enable all features and receive new updates.','pafe'); ?>
                        <form method="post" action="#">
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row"><?php _e('Username','pafe'); ?></th>
                                    <td><input type="text" name="username" value="" class="regular-text"/></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Password','pafe'); ?></th>
                                    <td><input type="password" name="password" value="" class="regular-text"/></td>
                                </tr>
                            </table>
                            <input type="hidden" name="action" value="active_license">
                            <p class="submit">
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="Login & Active">
                            </p>
                            <br>
                        </form>
                        <?php
                    } else {
                        $status = $license['status'];
                        $license_key = $license['license_key'];
                        $mask_license_key = '**********' . substr( $license_key, -10 );

                        $lifetime = $license['lifetime'];
                        $expired_at = $license['expired_at'];
                        $expired_at_str = gmdate("Y-m-d\TH:i:s\Z", $expired_at);

                        if ($status == 'A' && !$lifetime && $expired_at < time()) {
                            $status = 'E';
                        }

                        if ($status === 'A') {
                            ?>
                            <div class="pafe-license__description">License key: <?php echo $mask_license_key; ?><br>Type: <strong><?php echo $license['license_name']; ?></strong><br>Activated sites: <?php echo $license['activated_site_total']; ?><br>Total sites: <?php echo $license['unlimited_site'] ? "Unlimited" : $license['site_total']; ?> sites<br>Expired at: <?php echo $lifetime ? "Lifetime" : $expired_at_str; ?></div>
                            <?php
                        } else if ($status === 'E') {
                            ?>
                            <div class="pafe-license__description">License key: <?php echo $mask_license_key; ?><br>Status: Your license has <strong>Expired</strong> at <?php echo $expired_at_str;?>.<br>Please renew your license today, to keep getting new updates and use full features.</div>
                            <?php
                        } else if ($status === 'D') {
                            ?>
                            <div class="pafe-license__description">License key: <?php echo $mask_license_key; ?><br>Status: Your license is <strong>Disabled</strong>.<br>Please change to a valid license, to keep getting new updates and use full features.</div>
                            <?php
                        } else if ($status === 'I') {
                            ?>
                            <div class="pafe-license__description">License key: <?php echo $mask_license_key; ?><br>Status: Your license is <strong>Invalid</strong>.<br>Please change to a valid license, to keep getting new updates and use full features.</div>
                            <?php
                        } else if ($status === 'F') {
                            ?>
                            <div class="pafe-license__description">License key: <?php echo $mask_license_key; ?><br>Status: Your license is <strong>Full</strong> (<?php echo $license['activated_site_total']; ?> of <?php echo $license['site_total']; ?> sites).<br>Please extend your license or deactivate other site, to keep getting new updates and use full features.</div>
                            <?php
                        } else if ($status === 'L') {
                            ?>
                            <div class="pafe-license__description">License key: <?php echo $mask_license_key; ?><br>Status: Your license is <strong>Locked</strong> due has changed IP address.<br>Please remove and re-active your license.<br>If it still occurs, please connect to support.</div>
                            <?php
                        } else {
                            ?>
                            <div class="pafe-license__description">License key: <?php echo $mask_license_key; ?><br>Unknown status: <?php echo $license['status']; ?>.<br>Please remove and re-active your license.</div>
                            <?php
                        }
                        ?>
                        <form method="post" action="#">
                            <input type="hidden" name="action" value="remove_license">
                            <p class="submit">
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="Remove license">
                            </p>
                            <br>
                        </form>
                        <?php
                    }
                    ?>
                </div>
            </div>
		</div>

		<hr>

		<form method="post" action="options.php" data-pafe-features>
	    <?php settings_fields( 'piotnet-addons-for-elementor-features-settings-group' ); ?>
	    <?php do_settings_sections( 'piotnet-addons-for-elementor-features-settings-group' ); ?>
	    <div class="pafe-toggle-features">
	    	<br>
	    	<br>
	    	<div class="pafe-toggle-features__button" data-pafe-toggle-features-enable>Enable All</div>
	    	<div class="pafe-toggle-features__button pafe-toggle-features__button--disable" data-pafe-toggle-features-disable>Disable All</div>
	    	<div class="pafe-toggle-features__button" data-pafe-features-save><?php _e('Save Settings','pafe'); ?></div>
	    	<br>
	    </div>
	    <?php
	    	// var_dump(PAFE_FEATURES_FREE);
	    	if ( !defined('PAFE_VERSION') ) :
	    ?>
	    	<p><?php _e('Please Install or Active Free Version on Wordpress Repository to Enable Free Features','pafe'); ?> <a href="https://wordpress.org/plugins/piotnet-addons-for-elementor">https://wordpress.org/plugins/piotnet-addons-for-elementor</a></p>
		<?php endif; ?>
            <?php
                require_once( __DIR__ . '/features.php' );
                $features = json_decode( PAFE_FEATURES, true );
                //$features_free = array();
                $features_all = array();

               /* if (defined('PAFE_FEATURES_FREE')) {
                    $features_free = json_decode( PAFE_FEATURES_FREE, true );
                }

                if (!empty($features_free)) {
                    foreach ($features_free as $feature) {
                        unset($feature['extension']);
                        unset($feature['form-builder']);
                        unset($feature['widget']);

                        $features_all[] = $feature;
                    }
                }*/

                if (!empty($features)) {
                    foreach ($features as $feature) {
                        if (!in_array($feature, $features_all)) {
                            $features_all[] = $feature;
                        }
                    }
                }

                foreach ($features_all as &$feature) {
                    if (!PAFE_has_valid_license($license)) {
                        if (get_option($feature['option'], 2) == 1) {
                            update_option($feature['option'], 3);
                        }
                        if (get_option($feature['option'], 2) == 2) {
                            update_option($feature['option'], '');
                        }
                    } else {
                        if (get_option($feature['option'], 2) == 3) {
                            update_option($feature['option'], 1);
                        }
                    }

                    $feature_disable = '';
                    $feature_enable = 0;
                    if (defined('PAFE_VERSION') && !$feature['pro'] || defined('PAFE_PRO_VERSION') && $feature['pro']) {
                        $feature_enable = esc_attr(get_option($feature['option'], 2));
                        if ($feature_enable == 2) {
                            $feature_enable = 1;
                        }
                    }

                    if (!defined('PAFE_VERSION') && !$feature['pro'] || !defined('PAFE_PRO_VERSION') && $feature['pro']) {
                        $feature_enable = 0;
                        $feature_disable = 1;
                    }

                    $feature['feature_enable'] = $feature_enable;
                    $feature['feature_disable'] = $feature_disable;
                }
            ?>

            <h2 class="pafe-features__headline">Form Builder</h2>
            <ul class="pafe-features">
            <?php
                foreach ($features_all as $feature) :
                    if (isset($feature['form-builder']) && $feature['form-builder'] == true) :
            ?>
                <li>
                    <label class="pafe-switch">
                        <input type="checkbox"<?php if( empty( $feature['feature_disable'] ) ) : ?> name="<?php echo $feature['option']; ?>"<?php endif; ?> value="1" <?php checked( $feature['feature_enable'], 1 ); ?><?php if( !empty( $feature['feature_disable'] ) ) { echo ' disabled'; } ?>>
                        <span class="pafe-slider round"></span>
                    </label>
                    <a href="<?php echo $feature['url']; ?>" target="_blank"><?php echo $feature['name']; ?><?php if( $feature['pro'] ) : ?><span class="pafe-pro-version"></span><?php endif; ?></a>
                </li>
            <?php endif; endforeach; ?>
            </ul>

            <h2 class="pafe-features__headline">Extensions</h2>
            <ul class="pafe-features">
            <?php
                foreach ($features_all as $feature) :
                    if (isset($feature['extension']) && $feature['extension'] == true) :
            ?>
                <li>
                    <label class="pafe-switch">
                        <input type="checkbox"<?php if( empty( $feature['feature_disable'] ) ) : ?> name="<?php echo $feature['option']; ?>"<?php endif; ?> value="1" <?php checked( $feature['feature_enable'], 1 ); ?><?php if( !empty( $feature['feature_disable'] ) ) { echo ' disabled'; } ?>>
                        <span class="pafe-slider round"></span>
                    </label>
                    <a href="<?php echo $feature['url']; ?>" target="_blank"><?php echo $feature['name']; ?><?php if( $feature['pro'] ) : ?><span class="pafe-pro-version"></span><?php endif; ?></a>
                </li>
            <?php endif; endforeach; ?>
            </ul>

            <h2 class="pafe-features__headline">Widgets</h2>
            <ul class="pafe-features">
            <?php
                foreach ($features_all as $feature) :
                    if (isset($feature['widget']) && $feature['widget'] == true) :
            ?>
                <li>
                    <label class="pafe-switch">
                        <input type="checkbox"<?php if( empty( $feature['feature_disable'] ) ) : ?> name="<?php echo $feature['option']; ?>"<?php endif; ?> value="1" <?php checked( $feature['feature_enable'], 1 ); ?><?php if( !empty( $feature['feature_disable'] ) ) { echo ' disabled'; } ?>>
                        <span class="pafe-slider round"></span>
                    </label>
                    <a href="<?php echo $feature['url']; ?>" target="_blank"><?php echo $feature['name']; ?><?php if( $feature['pro'] ) : ?><span class="pafe-pro-version"></span><?php endif; ?></a>
                </li>
            <?php endif; endforeach; ?>
            </ul>
			<div class="pafe-toggle-features">
		    	<br>
		    	<br>
		    	<div class="pafe-toggle-features__button" data-pafe-toggle-features-enable>Enable All</div>
		    	<div class="pafe-toggle-features__button pafe-toggle-features__button--disable" data-pafe-toggle-features-disable>Disable All</div>
		    	<div class="pafe-toggle-features__button" data-pafe-features-save><?php _e('Save Settings','pafe'); ?></div>
		    	<br>
		    </div>
		</form>

        <?php if( get_option( 'pafe-features-form-google-sheets-connector', 2 ) == 2 || get_option( 'pafe-features-form-google-sheets-connector', 2 ) == 1 ) : ?>

            <hr>
            <div class="pafe-bottom">
                <div class="pafe-bottom__left">
                    <h3><?php _e('Google Sheets Integration','pafe'); ?></h3>
                    <iframe width="100%" height="250" src="https://www.youtube.com/embed/NidLGA0k8mI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
                <div class="pafe-bottom__right">
                    <div class="pafe-license">
                        <form method="post" action="options.php">
                            <?php settings_fields( 'piotnet-addons-for-elementor-pro-google-sheets-group' ); ?>
                            <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-google-sheets-group' ); ?>
                            <?php

                            $client_id     = esc_attr( get_option( 'piotnet-addons-for-elementor-pro-google-sheets-client-id' ) );
                            $client_secret = esc_attr( get_option( 'piotnet-addons-for-elementor-pro-google-sheets-client-secret' ) );
                            $redirect =  get_admin_url(null,'admin.php?page=piotnet-addons-for-elementor'); //For PAFE

                            if ( empty( $_GET['connect_type']) && ! empty( $_GET['code'] ) ) {  //For PAFE
                                // Authorization
                                $code = $_GET['code'];
                                // Token
                                $url  = 'https://accounts.google.com/o/oauth2/token';
                                $curl = curl_init();
                                $data = "code=$code&client_id=$client_id&client_secret=$client_secret&redirect_uri=" . urlencode($redirect) . "&grant_type=authorization_code";

                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => "https://accounts.google.com/o/oauth2/token",
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_TIMEOUT => 30,
                                    CURLOPT_CUSTOMREQUEST => "POST",
                                    CURLOPT_POSTFIELDS => $data,
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER => array(
                                        "Content-Type: application/x-www-form-urlencoded"
                                    ),
                                ));

                                $response = curl_exec($curl);
                                curl_close($curl);
                                //echo $response;
                                $array = json_decode( $response );

                                if ( ! empty( $array->access_token ) && ! empty( $array->refresh_token ) && ! empty( $array->expires_in ) ) {
                                    $pafe_ggsheets_expired_at = time() + $array->expires_in;
                                    update_option( 'piotnet-addons-for-elementor-pro-google-sheet-expires', $array->expires_in );
                                    update_option( 'piotnet-addons-for-elementor-pro-google-sheets-expired-token', $pafe_ggsheets_expired_at );
                                    update_option( 'piotnet-addons-for-elementor-pro-google-sheets-access-token', $array->access_token );
                                    update_option( 'piotnet-addons-for-elementor-pro-google-sheets-refresh-token', $array->refresh_token );
                                }
                            }
                            ?>
                            <div style="padding-top: 30px;">
                                <b><a href="https://console.developers.google.com/flows/enableapi?apiid=sheets.googleapis.com" target="_blank"><?php _e('Click here to Sign into your Gmail account and access Google Sheets’s application registration','pafe'); ?></a></b>
                            </div>
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row"><?php _e('Client ID','pafe'); ?></th>
                                    <td><input type="text" name="piotnet-addons-for-elementor-pro-google-sheets-client-id" value="<?php echo $client_id; ?>" class="regular-text"/></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Client Secret','pafe'); ?></th>
                                    <td><input type="text" name="piotnet-addons-for-elementor-pro-google-sheets-client-secret" value="<?php echo $client_secret; ?>" class="regular-text"/></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Authorized redirect URI','pafe'); ?></th>
                                    <td><input type="text" readonly="readonly" value="<?php echo $redirect; ?>" class="regular-text"/></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Authorizaion','pafe'); ?></th>
                                    <td>
                                        <?php if ( !empty($client_id) && !empty($client_secret) ) : ?>
                                            <a class="pafe-toggle-features__button" href="https://accounts.google.com/o/oauth2/auth?redirect_uri=<?php echo $redirect; ?>&client_id=<?php echo $client_id; ?>&response_type=code&scope=https://www.googleapis.com/auth/spreadsheets&approval_prompt=force&access_type=offline">Authorize</a>
                                        <?php else : ?>
                                            <?php _e('To setup Gmail integration properly you should save Client ID and Client Secret.','pafe'); ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                            <?php submit_button(__('Save Settings','pafe')); ?>
                        </form>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <?php if( get_option( 'pafe-features-form-google-calendar-connector', 2 ) == 2 || get_option( 'pafe-features-form-google-calendar-connector', 2 ) == 1 ) : ?>
            <hr>
            <div class="pafe-bottom">
                <div class="pafe-bottom__left">
                    <h3><?php esc_html_e( 'Google Calendar Integration', 'pafe' ); ?></h3>
                </div>

                <div class="pafe-bottom__right">
                    <div class="pafe-license">
                        <form method="post" action="options.php">
                            <?php settings_fields( 'piotnet-addons-for-elementor-pro-google-calendar-group' ); ?>
                            <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-google-calendar-group' ); ?>
                            <?php
                            $redirect      =  get_admin_url(null,'admin.php?page=piotnet-addons-for-elementor&connect_type=google_calendar');
                            //$redirect      = "http://localhost/wp-admin/admin.php?page=piotnet-addons-for-elementor&connect_type=google_calendar";
                            $gg_cld_client_id     = esc_attr( get_option( 'piotnet-addons-for-elementor-pro-google-calendar-client-id' ) );
                            $gg_cld_client_secret = esc_attr( get_option( 'piotnet-addons-for-elementor-pro-google-calendar-client-secret' ) );
                            $client_api_key = esc_attr( get_option( 'piotnet-addons-for-elementor-pro-google-calendar-client-api-key' ) );

                            if ( ! empty( $_GET['connect_type'] ) && $_GET['connect_type'] == 'google_calendar' && ! empty( $_GET['code'] ) ) {
                                // Authorization
                                $code = $_GET['code'];
                                // Token
                                $curl = curl_init();
                                $data = "code=$code&client_id=$gg_cld_client_id&client_secret=$gg_cld_client_secret&redirect_uri=" . urlencode($redirect) . "&grant_type=authorization_code";

                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => "https://accounts.google.com/o/oauth2/token",
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_TIMEOUT => 30,
                                    CURLOPT_CUSTOMREQUEST => "POST",
                                    CURLOPT_POSTFIELDS => $data,
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER => array(
                                        "Content-Type: application/x-www-form-urlencoded"
                                    ),
                                ));
                                $response = curl_exec($curl);
                                curl_close($curl);
                                //echo $response;
                                $array = json_decode( $response );
                                if ( ! empty( $array->access_token ) && ! empty( $array->refresh_token ) && ! empty( $array->expires_in ) ) {
                                    $piotnetforms_ggcalendar_expired_at = time() + $array->expires_in;
                                    update_option( 'piotnet-addons-for-elementor-pro-google-calendar-expires', $array->expires_in );
                                    update_option( 'piotnet-addons-for-elementor-pro-google-calendar-expired-token', $piotnetforms_ggcalendar_expired_at );
                                    update_option( 'piotnet-addons-for-elementor-pro-google-calendar-access-token', $array->access_token );
                                    update_option( 'piotnet-addons-for-elementor-pro-google-calendar-refresh-token', $array->refresh_token );
                                    function pafe_google_calendar_get_calendar_id($access_token, $client_api_key) {
                                        $curl = curl_init();

                                        curl_setopt_array( $curl, array(
                                            CURLOPT_URL            => "https://www.googleapis.com/calendar/v3/users/me/calendarList?minAccessRole=writer&key=$client_api_key",
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_TIMEOUT        => 30,
                                            CURLOPT_CUSTOMREQUEST  => "GET",
                                            CURLOPT_SSL_VERIFYPEER => false,
                                            CURLOPT_HTTPHEADER     => array(
                                                "Authorization: Bearer $access_token",
                                                "Accept: application/json"
                                            ),
                                        ));
                                        $response = curl_exec( $curl );
                                        curl_close( $curl );

                                        $response = json_decode($response);
                                        //print_r($response);
                                        $gg_calendar_items = $response->items;
                                        $gg_calendar_id = null;
                                        foreach ( $gg_calendar_items as $gg_calendar_item ) {
                                            $gg_calendar_item_id = $gg_calendar_item->id;
                                            if (empty($gg_calendar_id)) {
                                                $gg_calendar_id = $gg_calendar_item_id;
                                            }
                                            if ( !empty($gg_calendar_item->primary) && $gg_calendar_item->primary == 1 ) {
                                                $gg_calendar_id = $gg_calendar_item_id;
                                                break;
                                            }
                                        }
                                        return $gg_calendar_id;
                                    }
                                    $gg_calendar_id = pafe_google_calendar_get_calendar_id($array->access_token, $client_api_key);
                                    update_option('piotnet-addons-for-elementor-pro-google-calendar-id', $gg_calendar_id);
                                }
                            }
                            ?>
                            <div style="padding-top: 30px;">
                                <b><a href="https://console.developers.google.com/" target="_blank"><?php esc_html_e( 'Click here to Sign into your Gmail account and access Google Calendar’s application registration', 'piotnetforms' ); ?></a></b>
                            </div>
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row"><?php esc_html_e( 'Client ID', 'pafe' ); ?></th>
                                    <td><input type="text" name="piotnet-addons-for-elementor-pro-google-calendar-client-id" value="<?php echo $gg_cld_client_id; ?>" class="regular-text"/></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php esc_html_e( 'Client Secret', 'pafe' ); ?></th>
                                    <td><input type="text" name="piotnet-addons-for-elementor-pro-google-calendar-client-secret" value="<?php echo $gg_cld_client_secret; ?>" class="regular-text"/></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php esc_html_e( 'API Key', 'pafe' ); ?></th>
                                    <td><input type="text" name="piotnet-addons-for-elementor-pro-google-calendar-client-api-key" value="<?php echo $client_api_key; ?>" class="regular-text"/></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php esc_html_e( 'Authorized redirect URI', 'pafe' ); ?></th>
                                    <td><input type="text" readonly="readonly" value="<?php echo $redirect; ?>" class="regular-text"/></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php esc_html_e( 'Authorize', 'pafe' ); ?></th>
                                    <td>
                                        <?php if ( ! empty( $gg_cld_client_id ) && ! empty( $gg_cld_client_secret ) ) : ?>
                                            <a class="piotnetforms-toggle-features__button" href="https://accounts.google.com/o/oauth2/auth?redirect_uri=<?php echo urlencode($redirect); ?>&client_id=<?php echo $gg_cld_client_id; ?>&response_type=code&scope=https://www.googleapis.com/auth/calendar.readonly https://www.googleapis.com/auth/calendar.events&approval_prompt=force&access_type=offline">Authorize</a>
                                        <?php else : ?>
                                            <?php esc_html_e( 'To setup Gmail integration properly you should save Client ID and Client Secret.', 'pafe' ); ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                            <?php submit_button( __( 'Save Settings', 'pafe' ) ); ?>
                        </form>
                    </div>
                </div>
            </div>


        <?php endif; ?>

		<?php if( get_option( 'pafe-features-address-autocomplete-field', 2 ) == 2 || get_option( 'pafe-features-address-autocomplete-field', 2 ) == 1 ) : ?>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('Google Maps Integration','pafe'); ?></h3>
				<iframe width="100%" height="250" src="https://www.youtube.com/embed/_YhQWreCZwA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-google-maps-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-google-maps-group' ); ?>
					    <?php
					    	$google_maps_api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-google-maps-api-key') );
					    ?>
					    <div style="padding-top: 30px;">
					    	<b><a href="https://cloud.google.com/maps-platform/?apis=maps,places" target="_blank"><?php _e('Click here to get Google Maps API Key','pafe'); ?></a></b>
					    </div>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('Google Maps API Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-google-maps-api-key" value="<?php echo $google_maps_api_key; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>
		<br>
		<?php endif; ?>

		<?php if( get_option( 'pafe-features-stripe-payment', 2 ) == 2 || get_option( 'pafe-features-stripe-payment', 2 ) == 1 ) : ?>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('Stripe Integration','pafe'); ?></h3>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-stripe-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-stripe-group' ); ?>
					    <?php
					    	$publishable_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-stripe-publishable-key') );
					    	$secret_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-stripe-secret-key') );
					    ?>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('Publishable Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-stripe-publishable-key" value="<?php echo $publishable_key; ?>" class="regular-text"/></td>
					        </tr>
					        <tr valign="top">
					        <th scope="row"><?php _e('Secret Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-stripe-secret-key" value="<?php echo $secret_key; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>

		<?php endif; ?>

		<?php if( get_option( 'pafe-features-paypal-payment', 2 ) == 2 || get_option( 'pafe-features-paypal-payment', 2 ) == 1 ) : ?>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('Paypal Integration','pafe'); ?></h3>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-paypal-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-paypal-group' ); ?>
					    <?php
					    	$client_id = esc_attr( get_option('piotnet-addons-for-elementor-pro-paypal-client-id') );
					    ?>
					    <table class="form-table">
					    	<div style="padding-top: 30px;">
						    	<b><a href="https://developer.paypal.com/developer/applications/" target="_blank"><?php _e('Click here to Create app and get the Client ID','pafe'); ?></a></b>
						    </div>
					        <tr valign="top">
					        <th scope="row"><?php _e('Client ID','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-paypal-client-id" value="<?php echo $client_id; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>

		<?php endif; ?>
        <?php if( get_option( 'pafe-features-hubspot', 2 ) == 2 || get_option( 'pafe-features-hubspot', 2 ) == 1 ) : ?>
            <hr>
            <div class="pafe-bottom">
                <div class="pafe-bottom__left">
                    <h3><?php _e('Hubspot Integration','pafe'); ?></h3>
                </div>
                <div class="pafe-bottom__right">
                    <div class="pafe-license">
                        <form method="post" action="options.php">
                            <?php settings_fields( 'piotnet-addons-for-elementor-pro-hubspot-group' ); ?>
                            <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-hubspot-group' ); ?>
                            <?php $hubspot_api = esc_attr( get_option( 'piotnet-addons-for-elementor-pro-hubspot-api-key' ) ); ?>

                            <div style="padding-top: 30px;">
                                <b><a href="https://app.hubspot.com/api-key/14540594/call-log" target="_blank"><?php _e('Click here to get the API key','pafe'); ?></a></b>
                            </div>
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row"><?php _e('API Key','pafe'); ?></th>
                                    <td><input type="text" name="piotnet-addons-for-elementor-pro-hubspot-api-key" value="<?php echo $hubspot_api; ?>" class="regular-text"/></td>
                                </tr>
                            </table>
                            <?php submit_button(__('Save Settings','pafe')); ?>
                        </form>
                    </div>
                </div>
            </div>

        <?php endif; ?>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('MailChimp Integration','pafe'); ?></h3>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-mailchimp-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-mailchimp-group' ); ?>
					    <?php
					    	$api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-mailchimp-api-key') );
					    ?>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('API Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-mailchimp-api-key" value="<?php echo $api_key; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('MailerLite Integration','pafe'); ?></h3>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-mailerlite-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-mailerlite-group' ); ?>
					    <?php
					    	$api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-mailerlite-api-key') );
					    ?>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('API Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-mailerlite-api-key" value="<?php echo $api_key; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>
		
		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('Sendinblue Integration','pafe'); ?></h3>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-sendinblue-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-sendinblue-group' ); ?>
					    <?php
					    	$sendinblue_api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-sendinblue-api-key') );
					    ?>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('API Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-sendinblue-api-key" value="<?php echo $sendinblue_api_key; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('ActiveCampaign Integration','pafe'); ?></h3>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-activecampaign-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-activecampaign-group' ); ?>
					    <?php
					    	$api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-activecampaign-api-key') );
					    	$api_url = esc_attr( get_option('piotnet-addons-for-elementor-pro-activecampaign-api-url') );
					    ?>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('API Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-activecampaign-api-key" value="<?php echo $api_key; ?>" class="regular-text"/></td>
					        </tr>
					        <tr valign="top">
					        <th scope="row"><?php _e('API URL','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-activecampaign-api-url" value="<?php echo $api_url; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('GetResponse Integration','pafe'); ?></h3>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-getresponse-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-getresponse-group' ); ?>
					    <?php
					    	$getresponseapi_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-getresponse-api-key') );
					    ?>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('API Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-getresponse-api-key" value="<?php echo $getresponseapi_key; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>

		<?php if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) : ?>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('reCAPTCHA (v3) Integration','pafe'); ?></h3>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-recaptcha-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-recaptcha-group' ); ?>
					    <?php
					    	$site_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-recaptcha-site-key') );
					    	$secret_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-recaptcha-secret-key') );
					    ?>
					    <div style="padding-top: 30px;" data-pafe-dropdown>
					    	<b><a href="#" data-pafe-dropdown-trigger><?php _e('Click here to view tutorial','pafe'); ?></a></b>
					    	<div data-pafe-dropdown-content>
					    		<p>Very first thing you need to do is register your website on Google reCAPTCHA to do that click <a href="https://www.google.com/recaptcha/admin" target="_blank">here</a>.</p>

								<p>Login to your Google account and create the app by filling the form. Select the reCAPTCHA v3 and in that select “I am not a robot” checkbox option.</p>
								<div>
								<img src="<?php echo plugin_dir_url( __FILE__ ); ?>google-recaptcha-1.jpg">
								</div>

								<p>Once submitted, Google will provide you with the following two information: Site key, Secret key.</p>
								<div>
								<img src="<?php echo plugin_dir_url( __FILE__ ); ?>google-recaptcha-2.jpg">
								</div>
					    	</div>
					    </div>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('Site Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-recaptcha-site-key" value="<?php echo $site_key; ?>" class="regular-text"/></td>
					        </tr>
					        <tr valign="top">
					        <th scope="row"><?php _e('Secret Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-recaptcha-secret-key" value="<?php echo $secret_key; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>

        <hr>
        <div class="pafe-bottom">
            <div class="pafe-bottom__left">
                <h3><?php _e('Twilio Integration','pafe'); ?></h3>
            </div>
            <div class="pafe-bottom__right">
                <div class="pafe-license">
                    <form method="post" action="options.php">
                        <?php settings_fields( 'piotnet-addons-for-elementor-pro-twilio-group' ); ?>
                        <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-twilio-group' ); ?>
                        <?php
                            $account_sid = esc_attr( get_option('piotnet-addons-for-elementor-pro-twilio-account-sid') );
                            $author_token = esc_attr( get_option('piotnet-addons-for-elementor-pro-twilio-author-token') );
                        ?>
                        <table class="form-table">
                            <tr valign="top">
                            <th scope="row"><?php _e('Account SID','pafe'); ?></th>
                            <td><input type="text" name="piotnet-addons-for-elementor-pro-twilio-account-sid" value="<?php echo $account_sid; ?>" class="regular-text"/></td>
                            </tr>
                            <tr valign="top">
                            <th scope="row"><?php _e('Author Token','pafe'); ?></th>
                            <td><input type="text" name="piotnet-addons-for-elementor-pro-twilio-author-token" value="<?php echo $author_token; ?>" class="regular-text"/></td>
                            </tr>
                        </table>
                        <?php submit_button(__('Save Settings','pafe')); ?>
                    </form>
                </div>
            </div>
        </div>

        <hr>
        <div class="pafe-bottom">
            <div class="pafe-bottom__left">
                <h3><?php _e('Sendfox Integration','pafe'); ?></h3>
            </div>
            <div class="pafe-bottom__right">
                <div class="pafe-license">
                    <form method="post" action="options.php">
                        <?php settings_fields( 'piotnet-addons-for-elementor-pro-sendfox-group' ); ?>
                        <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-sendfox-group' ); ?>
                        <?php
                            $sendfox_access_token = esc_attr( get_option('piotnet-addons-for-elementor-pro-sendfox-access-token') );
                        ?>
                        <table class="form-table">
                            <tr valign="top">
                            <th scope="row"><?php _e('SendFox Personal Aceess Token','pafe'); ?></th>
                            <td><input type="text" name="piotnet-addons-for-elementor-pro-sendfox-access-token" value="<?php echo $sendfox_access_token; ?>" class="regular-text"/></td>
                            </tr>
                        </table>
                        <?php submit_button(__('Save Settings','pafe')); ?>
                    </form>
                </div>
            </div>
        </div>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('Zoho Integration','pafe'); ?></h3>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-zoho-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-zoho-group' ); ?>
					    <?php
							$zoho_domain = esc_attr( get_option('piotnet-addons-for-elementor-pro-zoho-domain') );
							$client_id = esc_attr( get_option('piotnet-addons-for-elementor-pro-zoho-client-id') );
							$redirect_url = admin_url('admin.php?page=piotnet-addons-for-elementor');
							$client_secret = esc_attr( get_option('piotnet-addons-for-elementor-pro-zoho-client-secret') );
							$token = esc_attr( get_option('piotnet-addons-for-elementor-pro-zoho-token') );
							$refresh_token = esc_attr( get_option('piotnet-addons-for-elementor-pro-zoho-refresh-token') );
							$zoho_domains = ["accounts.zoho.com", "accounts.zoho.com.au", "accounts.zoho.eu", "accounts.zoho.in", "accounts.zoho.com.cn"]
					    ?>
					    <table class="form-table">
						<tr valign="top">
					        <th scope="row"><?php _e('Domain','pafe'); ?></th>
					        <td>
								<select name="piotnet-addons-for-elementor-pro-zoho-domain">
									<?php foreach($zoho_domains as $zoho){
											if($zoho_domain == $zoho){
												echo '<option value="'.$zoho.'" selected>'.$zoho.'</option>';
											}else{
												echo '<option value="'.$zoho.'">'.$zoho.'</option>';
											}
										}
									?>
								</select>
							</td>
					        </tr>
					        <tr valign="top">
					        <th scope="row"><?php _e('Client ID','pafe'); ?></th>
					        <td>
								<input type="text" name="piotnet-addons-for-elementor-pro-zoho-client-id" value="<?php echo $client_id; ?>" class="regular-text"/>
								<a target="_blank" href="https://accounts.zoho.com/developerconsole">How to create client id and Screct key</a>
							</td>
					        </tr>
					        <tr valign="top">
					        <th scope="row"><?php _e('Client Secret','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-zoho-client-secret" value="<?php echo $client_secret; ?>" class="regular-text"/></td>
					        </tr>
							<tr valign="top">
					        <th scope="row"><?php _e('Authorization Redirect URI','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-zoho-redirect-url" value="<?php echo $redirect_url; ?>" class="regular-text" readonly/></td>
					        </tr>
					    </table>
						<div class="piotnet-addons-zoho-admin-api">
					    <?php submit_button(__('Save Settings','pafe')); ?>
						<?php
							$scope_module = 'ZohoCRM.modules.all,ZohoCRM.settings.all';
							$oauth = 'https://'.$zoho_domain.'/oauth/v2/auth?scope='.$scope_module.'&client_id='.$client_id.'&response_type=code&access_type=offline&redirect_uri='.$redirect_url.'';
							echo '<p class="piotnet-addons-zoho-admin-api-authenticate submit"><a class="button button-primary" href="'.$oauth.'" authenticate-zoho-crm disabled>Authenticate Zoho CRM</a></p>';
						?>
						<?php if(!empty($_REQUEST['code']) && !empty($_REQUEST['accounts-server'])):
							$url_get_token = 'https://'.$zoho_domain.'/oauth/v2/token?client_id='.$client_id.'&grant_type=authorization_code&client_secret='.$client_secret.'&redirect_uri='.$redirect_url.'&code='.$_REQUEST['code'].'';
							$zoho_response = wp_remote_post($url_get_token, array());
							if(!empty($zoho_response['body'])){
								$zoho_response = json_decode($zoho_response['body']);
								if(empty($zoho_response->error)){
									update_option('zoho_access_token', $zoho_response->access_token);
									update_option('zoho_refresh_token', $zoho_response->refresh_token);
									update_option('zoho_api_domain', $zoho_response->api_domain);
									echo "Success";
								}else{
									echo $zoho_response->error;
								}
							}

						?>
						</div>
						<?php endif; ?>
					</form>
				</div>
			</div>
		</div>

		<?php endif; ?>
		
	</div>
</div>
