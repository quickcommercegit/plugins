<?php



add_action('admin_menu', 'DM_license_menu');

function DM_license_menu() {

    add_options_page('Dynamic Menu licensing', 'Dynamic Menu licensing', 'manage_options', __FILE__, 'DM_license_management_page');

}



function DM_license_management_page() {

    echo '<div class="wrap">';

    echo '<h2>Dynamic Menu licensing</h2>';



    /*** License activate button was clicked ***/

    if (isset($_REQUEST['activate_license'])) {

        $license_key = $_REQUEST['DM_license_key'];



        // API query parameters

        $api_params = array(

            'slm_action' => 'slm_activate',

            'license_key' => $license_key,

            'secret_key' => DM_SPECIAL_SECRET_KEY,

            'item_reference' => urlencode(DM_ITEM_REFERENCE),

            'registered_domain' => $_SERVER['SERVER_NAME'],

            'time' => time()

        );

		 

        // Send query to the license manager server



        $query = esc_url_raw(add_query_arg($api_params, DM_LICENSE_SERVER_URL));

        $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));

        // var_dump($response);



        $api_parameters = array(

            'slm_action' => 'slm_check',

            'secret_key' => DM_SPECIAL_SECRET_KEY,

            'license_key' => $license_key,

            'item_reference' => urlencode(DM_ITEM_REFERENCE),

            'registered_domain' => $_SERVER['SERVER_NAME'],

            'time' => time()

        );

        $querys = esc_url_raw(add_query_arg($api_parameters, DM_LICENSE_SERVER_URL));

        $apiResponse = wp_remote_get($querys, array('timeout' => 20, 'sslverify' => false));

        update_option('last_DM_update_check',time()); 

        update_option('last_DM_update_check_data',$apiResponse);  

            // Check for error in the response

        if (is_wp_error($response)){

          



            $class = 'notice notice-error is-dismissible';

            $message = __( 'Unexpected Error! The query returned with an error.', 'dynamic-menu' );



            printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 

        }





        // License data.

        $license_data = json_decode(wp_remote_retrieve_body($response));

        

        if($license_data->result == 'success'){//Success was returned for the license activation



            $class = 'notice notice-success is-dismissible';

            $message = __($license_data->message, 'dynamic-menu' );



            printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 



            //Save the license key in the options table

            update_option('DM_license_key', $license_key); 

            echo '<style> .dm_license_error_link { display : none !important; } </style>';



        } else{

            //Show error to the user. Probably entered incorrect license key.

            $class = 'notice notice-error is-dismissible';

            $message = __( $license_data->message, 'dynamic-menu' );



            printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 

        }



    }

    /*** End of license activation ***/

    

    /*** License activate button was clicked ***/

    if (isset($_REQUEST['deactivate_license'])) {

        $license_key = $_REQUEST['DM_license_key'];



        // API query parameters

        $params = array(

            'slm_action' => 'slm_deactivate',

            'secret_key' => DM_SPECIAL_SECRET_KEY,

            'license_key' => $license_key,

            'registered_domain' => $_SERVER['SERVER_NAME'],

            'item_reference' => urlencode(DM_ITEM_REFERENCE),

            'time' => time()

        );



        // Send query to the license manager server

        $query = esc_url_raw(add_query_arg($params, DM_LICENSE_SERVER_URL));

        $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));





        $api_parameters = array(

            'slm_action' => 'slm_check',

            'secret_key' => DM_SPECIAL_SECRET_KEY,

            'license_key' => $license_key,

            'registered_domain' => $_SERVER['SERVER_NAME'],

            'item_reference' => urlencode(DM_ITEM_REFERENCE),

            'time' => time()

        );

        $querys = esc_url_raw(add_query_arg($api_parameters, DM_LICENSE_SERVER_URL));

        $apiResponse = wp_remote_get($querys, array('timeout' => 20, 'sslverify' => false));

        update_option('last_DM_update_check',time()); 

        update_option('last_DM_update_check_data',$apiResponse);  

        

        // Check for error in the response

        if (is_wp_error($response)){

            echo "Unexpected Error! The query returned with an error.";

        }



        // License data.

        $license_data = json_decode(wp_remote_retrieve_body($response));

        //pr($license_data);die;

        if($license_data->result == 'success'){//Success was returned for the license activation



            $class = 'notice notice-success is-dismissible';

            $message = __( $license_data->message, 'dynamic-menu' );



            printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 

            

            //Remove the licensse key from the options table. It will need to be activated again.

            update_option('DM_license_key','');

            //pr($license_data);die;



        } else {

            $class = 'notice notice-error is-dismissible';

            $message = __( $license_data->message, 'dynamic-menu' );



            printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 

        }

        

    }

    /*** End of DM license deactivation ***/

    ?>

    <p>Please enter the license key for this product to activate it. You were given a license key when you purchased this item.</p>

    <form action="" method="post">

        <table class="form-table">

            <tr>

                <th style="width:100px;"><label for="DM_license_key">License Key</label></th>

                <td ><input class="regular-text" type="text" id="DM_license_key" name="DM_license_key"  value="<?php echo get_option('DM_license_key'); ?>" ></td>

            </tr>

        </table>

        <p class="submit">

            <input type="submit" name="activate_license" value="Activate" class="button-primary" />

            <input type="submit" name="deactivate_license" value="Deactivate" class="button" />

        </p>

    </form>

    <?php

    

    echo '</div>';

}