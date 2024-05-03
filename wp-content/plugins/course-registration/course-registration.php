<?php
/*
Plugin Name: Course Registration Plugin
Description: Plugin for course registration functionality.
Version: 1.0
Author: 
*/

// Function to create course registration table in the database on plugin activation
function create_course_registration_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'course_registrations';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        fullname varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        date datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'create_course_registration_table');

// Function to handle course registration form submission
function handle_course_registration() {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_registration'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'course_registrations';

        $fullname = sanitize_text_field($_POST['fullname']);
        $email = sanitize_email($_POST['email']);

        $data = array(
            'fullname' => $fullname,
            'email' => $email
        );

        $wpdb->insert($table_name, $data);
        // Redirect user to a thank you page or display a success message
    }
}
add_action('admin_post_submit_registration', 'handle_course_registration');

// Function to display course registration form
function course_registration_form() {
    // HTML code for registration form
    $form_html = '<form action="' . esc_url(admin_url('admin-post.php')) . '" method="post">
                    <input type="hidden" name="action" value="submit_registration">
                    <label for="fullname">Full Name:</label>
                    <input type="text" id="fullname" name="fullname" required>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <input type="submit" name="submit_registration" value="Register">
                 </form>';

    return $form_html;
}
add_shortcode('course_registration', 'course_registration_form');

// Function to display course registration list in admin dashboard
function course_registration_admin_page() {
    global $wpdb;

    $registrations = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}course_registrations");

    echo '<div class="wrap">';
    echo '<h2>Course Registration List</h2>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Full Name</th>';
    echo '<th>Email</th>';
    echo '<th>Date</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($registrations as $registration) {
        echo '<tr>';
        echo '<td>' . $registration->id . '</td>';
        echo '<td>' . $registration->fullname . '</td>';
        echo '<td>' . $registration->email . '</td>';
        echo '<td>' . $registration->date . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

function add_course_registration_admin_menu() {
    add_menu_page(
        'Course Registration',
        'Course Registration',
        'manage_options',
        'course-registration-admin',
        'course_registration_admin_page',
        'dashicons-businessman',
        30
    );
}
add_action('admin_menu', 'add_course_registration_admin_menu');

