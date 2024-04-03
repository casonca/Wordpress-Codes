// Hook into the completion of the registration process
function custom_complete_registration( $user_id ) {
    // Integrate with your affiliate plugin here
    // For demonstration purposes, let's assume the affiliate plugin provides a function `add_affiliate()` to add the user as an affiliate
    if ( function_exists( 'add_affiliate' ) ) {
        add_affiliate( $user_id );
    }
    // You can also perform any additional actions after registration completion
    // For example, log the user in automatically
    wp_set_auth_cookie( $user_id, true );
    // Redirect the user to a thank you page
    wp_redirect( home_url( '/thank-you/' ) );
    exit;
}
add_action( 'user_register', 'custom_complete_registration' );
