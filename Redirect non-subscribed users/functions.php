// Redirect non-subscribed users to the signup page
function custom_restrict_content_redirect() {
    if ( ! is_user_logged_in() || ! current_user_can( 'subscriber' ) ) {
        wp_redirect( home_url( '/signup/' ) ); // Replace '/signup/' with your actual signup page URL
        exit;
    }
}
add_action( 'template_redirect', 'custom_restrict_content_redirect' );
