<?php
// Create settings page for API key and logs display
add_action('admin_menu', 'acculynx_add_admin_menu');
add_action('admin_init', 'acculynx_settings_init');

function acculynx_add_admin_menu() {
    add_menu_page('AccuLynx Integration', 'AccuLynx', 'manage_options', 'acculynx_integration', 'acculynx_options_page');
}

function acculynx_settings_init() {
    register_setting('acculynxIntegration', 'acculynx_api_key');

    add_settings_section(
        'acculynx_integration_section',
        __('API Settings', 'wordpress'),
        null,
        'acculynxIntegration'
    );

    add_settings_field(
        'acculynx_api_key',
        __('API Key', 'wordpress'),
        'acculynx_api_key_render',
        'acculynxIntegration',
        'acculynx_integration_section'
    );
}

function acculynx_api_key_render() {
    $options = get_option('acculynx_api_key');
    ?>
    <input type='text' name='acculynx_api_key' value='<?php echo esc_attr($options); ?>'>
    <?php
}

function acculynx_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>AccuLynx Integration Settings</h2>
        <?php
        settings_fields('acculynxIntegration');
        do_settings_sections('acculynxIntegration');
        submit_button();
        ?>
    </form>
    <h3>Debug Logs</h3>
    <textarea readonly style="width: 100%; height: 300px;"><?php echo esc_textarea(file_get_contents(WP_CONTENT_DIR . '/debug.log')); ?></textarea>
    <?php
}
