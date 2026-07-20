<?php

namespace FBCallNow\Core;

/**
 * Default plugin settings
 * 
 * @package FBCallNow\Core
 * @since 3.0.0
 */
class Defaults {
    
    /**
     * Get basic settings defaults
     * 
     * @return array Basic settings default values
     */
    public static function get_basic_settings() {
        return array(
            'enable' => true,
            'button_text' => __('Call Now', 'fb-call-now'),
            'phone_number' => '',
            'button_color' => '#007cba',
            'text_color' => '#ffffff',
            'button_shape' => 'pill',
            'horizontal_position' => 'right',
            'vertical_position' => 10,
            'delete_data_on_uninstall' => false
        );
    }
    
    /**
     * Get pro settings defaults
     * 
     * @return array Pro settings default values
     */
    public static function get_pro_settings() {
        return array(
            'days_visible' => array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'),
            'time_windows' => array(
                'monday'    => array('start' => '00:00', 'end' => '23:00', 'wrap' => false),
                'tuesday'   => array('start' => '00:00', 'end' => '23:00', 'wrap' => false),
                'wednesday' => array('start' => '00:00', 'end' => '23:00', 'wrap' => false),
                'thursday'  => array('start' => '00:00', 'end' => '23:00', 'wrap' => false),
                'friday'    => array('start' => '00:00', 'end' => '23:00', 'wrap' => false),
                'saturday'  => array('start' => '00:00', 'end' => '23:00', 'wrap' => false),
                'sunday'    => array('start' => '00:00', 'end' => '23:00', 'wrap' => false),
            ),
            'start_time' => '00:00',
            'end_time' => '23:00',
            'wrap_to_next_day' => false,
            'device_visibility' => array('desktop', 'tablet', 'mobile'),
            'debug_logging' => false
        );
    }
    
    /**
     * Get all default settings combined
     * 
     * @return array All default settings
     */
    public static function get_all_settings() {
        return array(
            'basic' => self::get_basic_settings(),
            'pro' => self::get_pro_settings()
        );
    }
}