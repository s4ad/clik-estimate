<?php
/**
 * Plugin Name: Clik Estimate
 * Plugin URI: https://fb.me/sa4db
 * Description: A plugin to add estimated delivery date to product page.
 * Author: Saad BOUTERAA
 * Author URI: https://fb.me/sa4db
 * Version: 1.0
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

class WC_Clik_Estimate {

    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_clik_estimate', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_clik_estimate', __CLASS__ . '::update_settings' );
        add_action('woocommerce_single_product_summary', __CLASS__ . '::add_estimation',25);

    }
    
    
    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['clik_estimate'] = __( 'Clik Estimate', 'woocommerce-clik-estimate' );
        return $settings_tabs;
    }


    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }

    function add_estimation()
    {

        $delivery_min = (int)get_option( 'wc_clik_estimate_min' );
        $delivery_max = (int)get_option( 'wc_clik_estimate_max' );



        $is_arabic = (int)get_option( 'wc_clik_estimate_is_arabic' );

        $text_before = get_option( 'wc_clik_estimate_text_before' );
        $text_and = get_option( 'wc_clik_estimate_text_and' );


        $estimation_min = date('d M Y',strtotime('+'.$delivery_min.' days'));
        $estimation_max = date('d M Y',strtotime('+'.$delivery_max.' days'));
        
        $date_min = date('M',strtotime('+'.$delivery_min.' days'));
        $date_max = date('M',strtotime('+'.$delivery_max.' days'));

        $text = '<div style="background-color:#eee;text-align:center">'.__($text_before.' ').$estimation_min.__(' '.$text_and.' ').$estimation_max.'</div>';
        
        if($is_arabic){

            $months = ["Jan" => "جانفي", "Feb" => "فيفري", "Mar" => "مارس", "Apr" => "أفريل", "May" => "ماي", "Jun" => "جوان", "Jul" => "جويلية", "Aug" => "أوت", "Sep" => "سبتمبر", "Oct" => "أكتوبر", "Nov" => "نوفمبر", "Dec" => "ديسمبر"];
        
            $text = str_replace($date_min, $months[$date_min], $text);
            $text = str_replace($date_max, $months[$date_max], $text);
        
        }
        

        echo $text;
    }



    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }


    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings() {

        $settings = array(
            'section_title' => array(
                'name'     => __( 'Edit Delivery estimate', 'woocommerce-clik-estimate' ),
                'type'     => 'title',
                'desc'     => 'Edit delivery estimation dates by entering minimum and maximum delivery days.',
                'id'       => 'wc_clik_estimate_section_title'
            ),
            'is_arabic' => array(
                'name'=> __('In Arabic', 'woocommerce-clik-estimate'),
                'type'=> 'radio',
                'options' => array('1'=>'yes','0'=>'no'),
                'desc' => 'Translate months names to arabic',
                'id' => 'wc_clik_estimate_is_arabic',
            ),
            'min' => array(
                'name' => __( 'Minimum', 'woocommerce-clik-estimate' ),
                'type' => 'number',
                'default'=> '3',
                'custom_attributes' => array('size'=>'2', 'min'=>'0'),
                'desc' => __( 'Minimum days the order will be delivered in', 'woocommerce-clik-estimate' ),
                'id'   => 'wc_clik_estimate_min'
            ),
            'max' => array(
                'name' => __( 'Maximum', 'woocommerce-clik-estimate' ),
                'type' => 'number',
                'default' => '7',
                'custom_attributes' => array('size'=>'2', 'min'=>'0'),
                'desc' => __( 'Maximum days the order will be delivered', 'woocommerce-clik-estimate' ),
                'id'   => 'wc_clik_estimate_max'
            ),
            'text_before' => array(
                'name' => __( 'Text', 'woocommerce-clik-estimate' ),
                'type' => 'text',
                'default' => 'Livraison estimée entre',
                'custom_attributes' => array('size'=>'35'),
                'desc' => __( 'Text : Livraison estimée entre', 'woocommerce-clik-estimate' ),
                'id'   => 'wc_clik_estimate_text_before'
            ),
            'text_and' => array(
                'name' => __( 'And', 'woocommerce-clik-estimate' ),
                'type' => 'text',
                'default' => 'et',
                'custom_attributes' => array('size'=>'8'),
                'desc' => __( 'Text : Et', 'woocommerce-clik-estimate' ),
                'id'   => 'wc_clik_estimate_text_and'
            ),
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_clik_estimate_section_end'
            )
        );

        return apply_filters( 'wc_clik_estimate_settings', $settings );
    }

}

WC_Clik_Estimate::init();