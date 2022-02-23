<?php
/*
* Plugin Name: WPBakery Industry Component
* Description: Result is a single WPbackery custom component. A single component with heading and a repeater with 1 to 4(max) elements inside of it.
* Plugin URI:  https://github.com/sergWP/wpb_component
* Author URI:  https://github.com/sergWP
* Version:     1.0
*/

class WPB_Industry_Shortcode {

    /**
     * Main constructor
     */
    public function __construct() {

        // Registers the shortcode in WordPress
        add_shortcode( 'wpb_industry', __CLASS__ . '::output' );

        // Map shortcode to WPBakery so you can access it in the builder
        if ( function_exists( 'vc_lean_map' ) ) {
            vc_lean_map( 'wpb_industry', __CLASS__ . '::map' );
        }

    }

    function register() {
        add_action('wp_enqueue_scripts',[$this,'enqueue_front']);
    }

    //Enqueue Front
    public function enqueue_front() {
        wp_enqueue_style('jsIndustryStyle', plugins_url('/assets/front/styles.min.css', __FILE__));
    }

    /**
     * Shortcode output
     */
    public static function output($atts) {

        // Extract shortcode attributes (based on the vc_lean_map function - see next function)
        $atts = vc_map_get_attributes( 'wpb_industry', $atts );

        // Define output and open element div.
        $output = '<section class="industry-section">';

        // Display custom heading if enabled and set.
        if ( isset( $atts['show_heading'] )
            && 'yes' === $atts['show_heading']
            && ! empty( $atts['heading'] )
        ) {
            $output .= '<h2 class="industry__heading">' . esc_html( $atts['heading'] ) . '</h2>';
        }

        // Display content.
        $items = vc_param_group_parse_atts($atts['box_repeater_items']);

        if ( $items ) {
            $i = 1;
            $output .= '<div class="flex flex-start industry__wrapper">';
            foreach ($items as $item) {
                $output .= '<div class="flex flex-start industry__item">
                    <div class="industry__icon">' . wp_get_attachment_image($item["box_repeater_items_img"], "full") . '</div>
                    <div class="flex industry__content">
                        <h3 class="industry__content-title">' . $item["box_repeater_items_title"] .'</h3>
                        <p class="industry__description">' . $item["box_repeater_items_description"] . '</p>
                    </div>
                </div>';
                if(++$i > 4) break;
            }
            $output .= '</div>';
        } else {
            $output .= 'No content';
        }

        // Close element.
        $output .= '</section>';

        // Return output
        return $output;

    }

    /**
     * Map shortcode to WPBakery
     *
     * This is an array of all your settings which become the shortcode attributes ($atts)
     * for the output. See the link below for a description of all available parameters.
     *
     * @since 1.0.0
     * @link  https://kb.wpbakery.com/docs/inner-api/vc_map/
     */
    public static function map() {
        return array(
            'name'        => esc_html__( 'WPB Industries', 'locale' ),
            'description' => esc_html__( 'Repeater with 1 to 4 elements inside of it', 'locale' ),
            'base'        => 'wpb_industry',
            'params'      => array(
                array(
                    'type'       => 'dropdown',
                    'heading'    => esc_html__( 'Show Heading?', 'locale' ),
                    'param_name' => 'show_heading',
                    'value'      => array(
                        esc_html__( 'No', 'locale' )  => 'no',
                        esc_html__( 'Yes', 'locale' ) => 'yes',
                    ),
                ),
                array(
                    'type'       => 'textfield',
                    'heading'    => esc_html__( 'Heading', 'locale' ),
                    'param_name' => 'heading',
                    'dependency' => array( 'element' => 'show_heading', 'value' => 'yes' ),
                ),
                array(
                    'type' => 'param_group',
                    'param_name' => 'box_repeater_items',
                    'params' => array(
                        array(
                            "type" => "attach_image",
                            "holder" => "img",
                            "class" => "",
                            "heading" => __( "Image", "my-text-domain" ),
                            "param_name" => "box_repeater_items_img",
                            "value" => __( "", "my-text-domain" ),
                        ),
                        array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "admin_label" => true,
                            "heading" => __("Title", "my-text-domain"),
                            "param_name" => "box_repeater_items_title",
                            "value" => __("", "my-text-domain"),
                        ),
                        array(
                            "type" => "textarea",
                            "class" => "",
                            "admin_label" => true,
                            "heading" => __("Description", "my-text-domain"),
                            "param_name" => "box_repeater_items_description",
                            "value" => __("", "my-text-domain"),
                        ),
                    ),
                )
            )
        );
    }
}

if(class_exists('WPB_Industry_Shortcode')) {
    $wpbindustry = new WPB_Industry_Shortcode();
    $wpbindustry->register();
}