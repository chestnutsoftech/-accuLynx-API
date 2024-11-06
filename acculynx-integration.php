<?php
// Hook into Elementor form submission
// Hook into Elementor form submission
// Hook into Elementor form submission
add_action('elementor_pro/forms/new_record', 'send_elementor_lead_to_acculynx', 10, 2);

function send_elementor_lead_to_acculynx($record, $handler) {
    $raw_data = $record->get('fields');
    $api_key = get_option('acculynx_api_key');

    // Debugging: Log incoming data
    error_log('Incoming Elementor Form Data: ' . print_r($raw_data, true));

    // Define the mapping of AccuLynx fields to potential Elementor field names
    $field_mappings = array(
        "FirstName" => ["first_name", "fname", "name"], // Possible matches for first name
        "LastName" => ["last_name", "lname", "field_2c08abf","field_c1c732b"],
        "street" => ["address", "street_address"],
        "City" => ["city"],
        "State" => ["state", "province"],
        "zip" => ["zip", "postal_code","field_5475c10"],
        "phoneNumber1" => ["phone", "contact_number","field_3b52748","field_9115d88"],
        "emailAddress" => ["email", "email_address","field_a61fd11"],
        "workType" => ["interested_in", "interest","field_92aaca6","field_e90bc52","field_e90bc52"],
        "notes" => ["additional_info", "message", "notes","field_40e7634"],
        "crossReference" => ["field_e00d27b"],
    );

    $lead_data = array();

    // Loop through each AccuLynx field and try to find a match in the Elementor form data
    foreach ($field_mappings as $acculynx_field => $possible_names) {
        foreach ($possible_names as $name) {
            if (isset($raw_data[$name])) {
                $lead_data[$acculynx_field] = sanitize_text_field($raw_data[$name]['value']);
                break; // Stop looking once we find a match
            }
        }
    }

    // Debugging: Log the prepared lead data
    error_log('Prepared Lead Data: ' . print_r($lead_data, true));

    // Send the data to AccuLynx
    $response = wp_remote_post('https://api.acculynx.com/api/v1/leads', array(
        'method' => 'POST',
        'body' => json_encode($lead_data),
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ),
    ));

    // Debugging: Log the response from AccuLynx
    if (is_wp_error($response)) {
        error_log('Error response from AccuLynx: ' . $response->get_error_message());
    } else {
        $response_body = wp_remote_retrieve_body($response);
        error_log('AccuLynx Response Body: ' . $response_body);
    }
}

