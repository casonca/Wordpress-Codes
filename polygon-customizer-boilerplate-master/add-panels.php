<?php
/**
 * Add panels to the WordPress customizer
 *
 * @since   1.0.0
 * @package Polygon_Customizer_Boilerplate
 */





if ( ! function_exists( 'polygon_register_customizer_panels' ) ) {
	/**
	 * Register customizer panels.
	 *
	 * Add panels to the WordPress customizer.
	 *
	 * @since 1.0.0
	 * @param array $wp_customize Array with all customizer data.
	 */
	function polygon_register_customizer_panels( $wp_customize ) {
		if ( ! isset( $wp_customize ) ) {
			return;
		}



		/*
		 * Example panel
		 *
		 * This panel contains all the parameters you can use when creating a new
		 * customizer panel.
		 */
		$wp_customize->add_panel(
			'example_panel',
			array(
				'title'           => esc_html__( 'Example', 'polygon' ),
				'description'     => esc_html__( 'This is an example panel you can use as a starting point for new customizer panels.', 'polygon' ),
				'priority'        => 10,
				'capability'      => 'edit_theme_options',
				'theme_supports'  => 'polygon-portfolio',
				'active_callback' => 'active_callback_function',
			)
		);





		/*
		 * Layout panel
		 *
		 * This is an aditional panel registered using only the required parameters.
		 * Use it for sections that control how your website is displayed.
		 */
		$wp_customize->add_panel(
			'layout_panel',
			array(
				'title'           => esc_html__( 'Layout', 'polygon' ),
				'description'     => esc_html__( 'This panel contains the sections that control how your website layout is displayed.', 'polygon' ),
				'priority'        => 20,
			)
		);





		/*
		 * General panel
		 *
		 * This is an aditional panel registered using only the required parameters.
		 * Use it for sections that control general aspects of your website ond default WordPress sections.
		 */
		$wp_customize->add_panel(
			'general_panel',
			array(
				'title'           => esc_html__( 'General', 'polygon' ),
				'description'     => esc_html__( 'This panel contains the sections that control general aspects of your website.', 'polygon' ),
				'priority'        => 30,
			)
		);
	}
	add_action( 'customize_register', 'polygon_register_customizer_panels' );
}
