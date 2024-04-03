<?php
/**
 * Register a custom Google Fonts control to the WordPress customizer
 *
 * @since   1.0.0
 * @package Polygon_Customizer_Boilerplate
 */





if ( ! function_exists( 'polygon_register_customizer_control_google_fonts' ) ) {
	/**
	 * Register Google Fonts control.
	 *
	 * Register a custom Google Fonts control to the WordPress customizer.
	 *
	 * @since 1.0.0
	 * @param array $wp_customize Array with all customizer data.
	 */
	function polygon_register_customizer_control_google_fonts( $wp_customize ) {
		if ( ! isset( $wp_customize ) ) {
			return;
		}



		/**
		 * Create a Google Fonts control
		 *
		 * This class creates a custom Google Fonts control for the WordPress
		 * customizer. Example:
		 *
		 * $wp_customize->add_setting(
		 *     'temporary',
		 *     array(
		 *         'default'           => 'Open Sans',
		 *         'sanitize_callback' => 'sanitize_text_field',
		 *     )
		 * );
		 *
		 * $wp_customize->add_control(
		 *     new Polygon_Customize_Google_Fonts_Control(
		 *         $wp_customize,
		 *         'temporary',
		 *         array(
		 *             'label'       => esc_html__( 'Temporary', 'polygon' ),
		 *             'description' => esc_html__( 'This is a temporary description.', 'polygon' ),
		 *             'section'     => 'example_settings_section',
		 *             'fonts'       => array(	'Open Sans', 'Noto Sans', 'Droid Sans' ),
		 *             // If 'fonts' is available, the next parameters will not be used.
		 *             'api_key'     => 'API-KEY',
		 *             'amount'      => 30,          // Number of fonts.
		 *             'cache_time'  => 30,          // Number of days.
		 *         )
		 *     )
		 * );
		 *
		 * @since 1.0.0
		 */
		class Polygon_Customize_Google_Fonts_Control extends WP_Customize_Control {

			/**
			 * Control type.
			 *
			 * @since 1.0.0
			 * @var   string
			 */
			public $type = 'google-fonts';

			/**
			 * API Key for Google Fonts.
			 *
			 * @since 1.0.0
			 * @var   string
			 */
			public $api_key = null;

			/**
			 * Number of fonts to retreive.
			 *
			 * @since 1.0.0
			 * @var   int|string
			 */
			public $amount = 'all';

			/**
			 * Cache time in days.
			 *
			 * @since 1.0.0
			 * @var   int
			 */
			public $cache_time = 365;

			/**
			 * Specific fonts to display.
			 *
			 * @since 1.0.0
			 * @var   string
			 */
			public $fonts = null;





			/**
			 * Render control content.
			 *
			 * Render our custom control inside the WordPress customizer.
			 *
			 * @since 1.0.0
			 */
			public function render_content() {
				if ( $this->fonts ) {
					if ( is_array( $this->fonts ) ) {
						$fonts = $this->fonts;
					} else {
						return;
					}
				} else {
					$fonts = $this->get_fonts();
					if ( ! $fonts ) {
						return;
					}
				}

				?>
					<?php if ( ! empty( $this->label ) ) { ?>
						<span class="customize-control-title">
							<?php echo esc_html( $this->label ); ?>
						</span>
					<?php } ?>

					<?php if ( ! empty( $this->description ) ) { ?>
						<span class="description customize-control-description">
							<?php echo esc_html( $this->description ); ?>
						</span>
					<?php } ?>

					<select <?php $this->link(); ?>>
						<?php
							if ( $this->fonts ) {
								foreach ( $fonts as $font ) {
									printf( '<option value="%s" %s>%s</option>', $font, selected( $this->value(), $font ), $font );
								}
							} else {
								foreach ( $fonts as $font => $value ) {
									printf( '<option value="%s" %s>%s</option>', $value->family, selected( $this->value(), $value->family ), $value->family );
								}
							}
						?>
					</select>
				<?php
			}





			/**
			 * Get Google Fonts from the API or cache.
			 *
			 * Get the list of Google Fonts using the API or from saved transients.
			 *
			 * @since  1.0.0
			 * @access private
			 * @return array   Array of Google Fonts.
			 */
			private function get_fonts() {
				$api_key    = $this->api_key;
				$cache_time = DAY_IN_SECONDS * $this->cache_time;

				if ( ! $api_key ) {
					return null;
				}



				// Set the 'polygon_google_fonts' transient if not available.
				if ( ! get_transient( 'polygon_google_fonts' ) ) {
					$cloud_url   = 'https://www.googleapis.com/webfonts/v1/webfonts?key=' . $api_key;
					$local_url   = get_template_directory_uri() . '/assets/fonts/google-fonts/google-fonts.json?ver=' . POLYGON_THEME_VERSION;    // Change this to match your location.
					$cloud_data  = wp_remote_get( $cloud_url, array( 'sslverify' => false ) );
					$local_data  = wp_remote_get( $local_url, array( 'sslverify' => false ) );

					if ( ! is_wp_error( $cloud_data ) ) {
						$content = json_decode( $cloud_data['body'] );

						if ( isset( $content->error ) ) {
							if ( ! is_wp_error( $local_data ) ) {
								$content = json_decode( $local_data['body'] );
							} else {
								return null;
							}
						}
					} else {
						if ( ! is_wp_error( $local_data ) ) {
							$content = json_decode( $local_data['body'] );
						} else {
							return null;
						}
					}

					set_transient( 'polygon_google_fonts', $content, $cache_time );
				}



				// Set the 'polygon_popular_google_fonts' transient if not available.
				if ( ! get_transient( 'polygon_popular_google_fonts' ) ) {
					$cloud_url   = 'https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&key=' . $api_key;
					$local_url   = get_template_directory_uri() . '/assets/fonts/google-fonts/google-fonts-popular.json?ver=' . POLYGON_THEME_VERSION;    // Change this to match your location.
					$cloud_data  = wp_remote_get( $cloud_url, array( 'sslverify' => false ) );
					$local_data  = wp_remote_get( $local_url, array( 'sslverify' => false ) );

					if ( ! is_wp_error( $cloud_data ) ) {
						$content = json_decode( $cloud_data['body'] );

						if ( isset( $content->error ) ) {
							if ( ! is_wp_error( $local_data ) ) {
								$content = json_decode( $local_data['body'] );
							} else {
								return null;
							}
						}
					} else {
						if ( ! is_wp_error( $local_data ) ) {
							$content = json_decode( $local_data['body'] );
						} else {
							return null;
						}
					}

					set_transient( 'polygon_popular_google_fonts', $content, $cache_time );
				}



				// Get fonts data from transients.
				if ( 'all' == $this->amount ) {
					$content = get_transient( 'polygon_google_fonts' );
					return $content->items;
				} else {
					$content = get_transient( 'polygon_popular_google_fonts' );
					return array_slice( $content->items, 0, $this->amount );
				}
			}
		}
	}
	add_action( 'customize_register', 'polygon_register_customizer_control_google_fonts', 0 );
}
