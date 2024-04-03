// Initialize the custom table upon plugin activation
register_activation_hook( __FILE__, 'custom_create_verification_table' );

function custom_create_verification_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'email_verification';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        email varchar(100) NOT NULL,
        token varchar(32) NOT NULL,
        verified tinyint(1) NOT NULL DEFAULT 0,
        PRIMARY KEY (id)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

// 2. Send verification email after registration
function custom_process_registration() {
    if( isset($_POST['email']) ) {
        $email = sanitize_email($_POST['email']);
        if ( email_exists( $email ) ) {
            echo 'This email is already registered.';
            return;
        }
        $verification_token = wp_generate_password( 32, false );
        global $wpdb;
        $table_name = $wpdb->prefix . 'email_verification';
        $wpdb->insert(
            $table_name,
            array(
                'email' => $email,
                'token' => $verification_token,
            )
        );
        // Send verification email with link containing the token
        $verification_link = add_query_arg(array('email' => $email, 'token' => $verification_token), site_url('/verify-email'));
        $email_subject = 'Please verify your email address';
        $email_message = 'Click the following link to verify your email address: ' . $verification_link;
        wp_mail($email, $email_subject, $email_message);
        // Display a message indicating that a verification email has been sent
        echo 'A verification email has been sent to ' . $email;
    }
}
add_action('admin_post_custom_process_registration', 'custom_process_registration');

// 3. Verify email address
function custom_verify_email() {
    if( isset($_GET['email']) && isset($_GET['token']) ) {
        $email = sanitize_email($_GET['email']);
        $verification_token = sanitize_text_field($_GET['token']);
        global $wpdb;
        $table_name = $wpdb->prefix . 'email_verification';
        $result = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table_name WHERE email = %s AND token = %s",
            $email,
            $verification_token
        ) );
        if ( $result ) {
            // Update verification status
            $wpdb->update(
                $table_name,
                array( 'verified' => 1 ),
                array( 'email' => $email )
            );
            // Complete the registration process
            // Redirect the user to a confirmation page
            wp_redirect( home_url( '/registration-confirmed/' ) );
            exit;
        } else {
            // Verification failed
            // Redirect the user to an error page
            wp_redirect( home_url( '/verification-error/' ) );
            exit;
        }
    }
}
add_action('init', 'custom_verify_email');
