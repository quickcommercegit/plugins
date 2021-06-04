<?php
add_action('admin_menu', 'dm_custom_update_settings_page');

function dm_custom_update_settings_page()
{
    add_menu_page('Dynamic Menu Settings', 'Dynamic Menu Settings', 'manage_options', 'dm-settings', 'dm_custom_settings_page_callback' , '' );
    add_action( 'admin_init', 'register_dm_custom_settings' );
}

function dm_custom_settings_page_callback()
{
    if (isset($_POST['manual_update'])) {
       update_option('dm_update_status', 'pending');
       update_option('dm_last_update_time', time());

       $languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc' );
        if (!empty($languages)) {
            foreach ($languages as $key => $data) {
                update_option('dm_update_status_wpml_lang_'.$key, 'pending');
                update_option('dm_last_update_time_wpml_lang_'.$key, time());
            }
        }
    }
    ?>

    <div class="wrap">
        <h1><?php _e('Dynamic Menu Settings', 'dynamic-menu') ?></h1>

        <form method="post" action="options.php">
            <?php settings_fields( 'dm-custom-settings' ); ?>
            <?php do_settings_sections( 'dm-custom-settings' ); ?>
            <table class="form-table dm-settings-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Select dynamic menu cache update type', 'dynamic-menu') ?></th>
                    <td>
                        <div class="settings-sub-row"><input type="radio" name="dm_update_type" value="manual" <?php echo get_option('dm_update_type') == 'manual' ? 'checked': '' ?>> <?php _e('Manual - You need to press the "Manual Update" button to update the dynamic menu', 'dynamic-menu') ?></div>
                        <div class="settings-sub-row"><input type="radio" name="dm_update_type" value="daily" <?php echo get_option('dm_update_type') == 'daily' ? 'checked': '' ?>> <?php _e('Daily - Dynamic menu will update automatically daily', 'dynamic-menu') ?> </div>
                    </td>
                </tr>
            </table>
            <p><i><?php _e('To enable the dynamic menu cache, you need to select any update method from above.', 'dynamic-menu') ?></i></p>
            <?php submit_button(); ?> 
        </form>

        <form method="post" action="">
            <input type="submit" name="manual_update" class="dm-manual-update-btn button" value="<?php _e('Manual Update', 'dynamic-menu') ?>">
        </form>
        
    </div>
    <?php
}

function register_dm_custom_settings()
{
    register_setting( 'dm-custom-settings', 'dm_update_type' );
    register_setting( 'dm-custom-settings', 'dm_update_status' );
    register_setting( 'dm-custom-settings', 'dm_last_update_time' );
}

function general_admin_notice(){
    global $pagenow;
    if ( $pagenow == 'options-general.php' ) {
         echo '<div class="notice notice-warning is-dismissible">
             <p>This notice appears on the settings page.</p>
         </div>';
    }
}
add_action('admin_notices', 'general_admin_notice');