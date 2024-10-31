<?php
/**
 * Plugin Name: Qooqie Call Tracking
 * Plugin URI: https://qooqie.com/call-tracking
 * Description: Voeg het Qooqie Call Tracking script toe aan je website.
 * Version: 1.0
 * Author: Qooqie N.V.
 * Author URI: https://qooqie.com
 * License: GPL2
 */

// Hook for adding admin menu
add_action('admin_menu', 'QQCT_add_admin_menu');

// Function to add the admin menu
function QQCT_add_admin_menu() {
    add_options_page(
        'Qooqie Call Tracking',
        'Qooqie Call Tracking',
        'manage_options',
        'qooqie-call-tracking',
        'QQCT_admin_page'
    );
}

// Admin page content
function QQCT_admin_page() {
    ?>
    <h2>Qooqie Call Tracking</h2>
    <form action="options.php" method="post">
        <?php
        settings_fields('QQCT_options_group');
        do_settings_sections('qooqie-call-tracking');
        submit_button();
        ?>
    </form>
    <?php
}

// Register settings, sections, and fields
add_action('admin_init', 'QQCT_settings_init');
function QQCT_settings_init() {
    register_setting('QQCT_options_group', 'QQCT_options', 'QQCT_options_validate');
    add_settings_section('QQCT_main', 'Script toevoegen', 'QQCT_section_text', 'qooqie-call-tracking');
    add_settings_field('QQCT_subtenant_id', 'Subtenant ID', 'QQCT_subtenant_id_setting_string', 'qooqie-call-tracking', 'QQCT_main');
    add_settings_field('QQCT_region_code', 'Land', 'QQCT_region_code_setting_string', 'qooqie-call-tracking', 'QQCT_main');
}

// Validate user input
function QQCT_options_validate($input) {
    // Validate and sanitize options, add validation code here
    return $input;
}


// Section text
function QQCT_section_text() {
    echo '<p>Vul de onderstaande velden in overeenkomstig met het Qooqie Call Tracking script in uw Qooqie-account.</p>';
    // Example script in code editor style
    echo '<p><strong>Voorbeeldscript:</strong></p>';
    echo '<pre style="background-color: #f7f7f7; border: 1px solid #ccc; padding: 10px; border-radius: 5px; overflow: auto; font-family: monospace; margin-top: 10px; max-width: 100%; max-height: 200px;">
&lt;script&gt;
    var _calltracking = _calltracking || {};
    _calltracking[\'subtenant_id\'] = \'JOUW_SUBTENANT_ID\';
    _calltracking[\'api_url\'] = \'https://api.qooqie.com\';
    _calltracking[\'region_code\'] = \'JOUW_REGIO_CODE\';
    (function() {
        var q = document.createElement(\'script\');
        q.type = \'text/javascript\'; q.async = true;
        q.src = \'https://api.qooqie.com/bundle\';
        q.charset = \'UTF-8\';
        var s = document.getElementsByTagName(\'script\')[0];
        s.parentNode.insertBefore(q, s);
    })();
&lt;/script&gt;
</pre>';
    echo '<p>Kopieer de waarde van <code>JOUW_SUBTENANT_ID</code> en de waarde van <code>JOUW_REGIO_CODE</code> en vul deze in via onderstaand formulier.</p>';
}

// Subtenant ID field
function QQCT_subtenant_id_setting_string() {
    $options = get_option('QQCT_options');
    $subtenant_id = isset($options['subtenant_id']) ? $options['subtenant_id'] : '';
    echo "<input id='QQCT_subtenant_id' name='QQCT_options[subtenant_id]' type='number' style='width: 200px;' min='0' value='" . esc_attr($subtenant_id) . "' />";
}

// Region Code field
function QQCT_region_code_setting_string() {
    $options = get_option('QQCT_options');
    $region_code = isset($options['region_code']) ? $options['region_code'] : 'NL';

    // Define the list of countries and their region codes
    $countries = array(
        'AT' => 'Austria',
        'BE' => 'Belgium',
        'BG' => 'Bulgaria',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DE' => 'Germany',
        'DK' => 'Denmark',
        'EE' => 'Estonia',
        'ES' => 'Spain',
        'FI' => 'Finland',
        'FR' => 'France',
        'GR' => 'Greece',
        'HR' => 'Croatia',
        'HU' => 'Hungary',
        'IE' => 'Ireland',
        'IT' => 'Italy',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'LV' => 'Latvia',
        'MT' => 'Malta',
        'NL' => 'Nederland',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'RO' => 'Romania',
        'SE' => 'Sweden',
        'SI' => 'Slovenia',
        'SK' => 'Slovakia'
    );

    // Start the select element
    echo "<select id='QQCT_region_code' name='QQCT_options[region_code]'>";

    // Loop through the countries array to create each option
    foreach ($countries as $code => $name) {
        echo "<option value='" . esc_attr($code) . "' " . selected($region_code, $code, false) . ">" . esc_html($name) . "</option>";
    }

    // Close the select element
    echo "</select>";
}

// Hook to inject script into footer
add_action('wp_footer', 'QQCT_inject_script');

function QQCT_inject_script() {
    $options = get_option('QQCT_options');
    $subtenant_id = esc_js($options['subtenant_id']);
    $region_code = esc_js($options['region_code']);

    if (!empty($subtenant_id) && !empty($region_code)) {
        echo "<script>
                var _calltracking = _calltracking || {};
                _calltracking['subtenant_id'] = " . esc_js($subtenant_id) . ";
                _calltracking['api_url'] = 'https://api.qooqie.com';
                _calltracking['region_code'] = '" . esc_js($region_code) . "';
                (function() {
                    var q   = document.createElement('script');
                    q.type  = 'text/javascript'; q.async = true;
                    q.src   = 'https://api.qooqie.com/bundle';
                    q.charset = 'UTF-8';
                    var s   = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(q, s);
                })();
              </script>";
    }
}
