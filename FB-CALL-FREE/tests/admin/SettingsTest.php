<?php

namespace FBCallNow\Tests\Admin;

use PHPUnit\Framework\TestCase;
use FBCallNow\Admin\Settings;

/**
 * Settings class tests
 * 
 * @package FBCallNow\Tests\Admin
 */
class SettingsTest extends TestCase {
    
    private $settings;
    
    /**
     * Set up test environment
     */
    public function setUp(): void {
        parent::setUp();
        
        // Mock WordPress functions
        if (!function_exists('sanitize_text_field')) {
            function sanitize_text_field($str) {
                return trim(strip_tags($str));
            }
        }
        
        if (!function_exists('sanitize_hex_color')) {
            function sanitize_hex_color($color) {
                if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
                    return $color;
                }
                return '';
            }
        }
        
        if (!function_exists('absint')) {
            function absint($maybeint) {
                return abs(intval($maybeint));
            }
        }
        
        if (!function_exists('add_settings_error')) {
            function add_settings_error($setting, $code, $message, $type = 'error') {
                // Mock function for testing
            }
        }
        
        if (!function_exists('__')) {
            function __($text, $domain = 'default') {
                return $text;
            }
        }
        
        $this->settings = new Settings();
    }
    
    /**
     * Test basic settings sanitization with valid data
     */
    public function test_sanitize_basic_settings_valid() {
        $input = array(
            'enable' => '1',
            'button_text' => 'Call Us Now',
            'phone_number' => '+1-555-123-4567',
            'button_color' => '#ff0000',
            'text_color' => '#ffffff',
            'horizontal_position' => 'left',
            'vertical_position' => '5',
            'delete_data_on_uninstall' => '1'
        );
        
        $result = $this->settings->sanitize_basic_settings($input);
        
        $this->assertTrue($result['enable']);
        $this->assertEquals('Call Us Now', $result['button_text']);
        $this->assertEquals('+1-555-123-4567', $result['phone_number']);
        $this->assertEquals('#ff0000', $result['button_color']);
        $this->assertEquals('#ffffff', $result['text_color']);
        $this->assertEquals('left', $result['horizontal_position']);
        $this->assertEquals(5, $result['vertical_position']);
        $this->assertTrue($result['delete_data_on_uninstall']);
    }
    
    /**
     * Test basic settings sanitization with invalid phone number
     */
    public function test_sanitize_basic_settings_invalid_phone() {
        $input = array(
            'phone_number' => '555-123-4567' // Missing +1-
        );
        
        $result = $this->settings->sanitize_basic_settings($input);
        
        // Should fallback to default
        $this->assertEquals('+1-234-567-8910', $result['phone_number']);
    }
    
    /**
     * Test basic settings sanitization with invalid color
     */
    public function test_sanitize_basic_settings_invalid_color() {
        $input = array(
            'button_color' => 'red', // Invalid hex color
            'text_color' => '#gggggg' // Invalid hex color
        );
        
        $result = $this->settings->sanitize_basic_settings($input);
        
        // Should fallback to defaults
        $this->assertEquals('#007cba', $result['button_color']);
        $this->assertEquals('#ffffff', $result['text_color']);
    }
    
    /**
     * Test basic settings sanitization with out-of-range vertical position
     */
    public function test_sanitize_basic_settings_invalid_position() {
        $input = array(
            'vertical_position' => '15', // Out of range (1-10)
            'horizontal_position' => 'center' // Invalid option
        );
        
        $result = $this->settings->sanitize_basic_settings($input);
        
        $this->assertEquals(10, $result['vertical_position']); // Clamped to max
        $this->assertEquals('right', $result['horizontal_position']); // Default fallback
    }
    
    /**
     * Test pro settings sanitization with valid data
     */
    public function test_sanitize_pro_settings_valid() {
        $input = array(
            'days_visible' => array('monday', 'tuesday', 'friday'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'wrap_to_next_day' => '1',
            'device_visibility' => array('desktop', 'mobile')
        );
        
        $result = $this->settings->sanitize_pro_settings($input);
        
        $this->assertCount(3, $result['days_visible']);
        $this->assertContains('monday', $result['days_visible']);
        $this->assertContains('tuesday', $result['days_visible']);
        $this->assertContains('friday', $result['days_visible']);
        $this->assertEquals('09:00', $result['start_time']);
        $this->assertEquals('17:00', $result['end_time']);
        $this->assertTrue($result['wrap_to_next_day']);
        $this->assertCount(2, $result['device_visibility']);
        $this->assertContains('desktop', $result['device_visibility']);
        $this->assertContains('mobile', $result['device_visibility']);
    }
    
    /**
     * Test pro settings sanitization with invalid days
     */
    public function test_sanitize_pro_settings_invalid_days() {
        $input = array(
            'days_visible' => array('monday', 'invalid_day', 'tuesday')
        );
        
        $result = $this->settings->sanitize_pro_settings($input);
        
        // Should only contain valid days
        $this->assertCount(2, $result['days_visible']);
        $this->assertContains('monday', $result['days_visible']);
        $this->assertContains('tuesday', $result['days_visible']);
        $this->assertNotContains('invalid_day', $result['days_visible']);
    }
    
    /**
     * Test pro settings sanitization with no days selected
     */
    public function test_sanitize_pro_settings_no_days() {
        $input = array(
            'days_visible' => array()
        );
        
        $result = $this->settings->sanitize_pro_settings($input);
        
        // Should default to all days
        $this->assertCount(7, $result['days_visible']);
        $this->assertContains('monday', $result['days_visible']);
        $this->assertContains('sunday', $result['days_visible']);
    }
    
    /**
     * Test pro settings sanitization with invalid time format
     */
    public function test_sanitize_pro_settings_invalid_time() {
        $input = array(
            'start_time' => '25:00', // Invalid hour
            'end_time' => '9:30' // Invalid format (should be HH:00)
        );
        
        $result = $this->settings->sanitize_pro_settings($input);
        
        // Should fallback to defaults
        $this->assertEquals('00:00', $result['start_time']);
        $this->assertEquals('23:00', $result['end_time']);
    }
    
    /**
     * Test pro settings sanitization with start time later than end time
     */
    public function test_sanitize_pro_settings_invalid_time_order() {
        $input = array(
            'start_time' => '18:00',
            'end_time' => '09:00',
            'wrap_to_next_day' => '' // Not checked
        );
        
        $result = $this->settings->sanitize_pro_settings($input);
        
        // Should be corrected to defaults due to validation error
        $this->assertEquals('00:00', $result['start_time']);
        $this->assertEquals('23:00', $result['end_time']);
    }
    
    /**
     * Test pro settings sanitization with wrap to next day enabled
     */
    public function test_sanitize_pro_settings_wrap_enabled() {
        $input = array(
            'start_time' => '18:00',
            'end_time' => '09:00',
            'wrap_to_next_day' => '1' // Checked
        );
        
        $result = $this->settings->sanitize_pro_settings($input);
        
        // Should be valid with wrap enabled
        $this->assertEquals('18:00', $result['start_time']);
        $this->assertEquals('09:00', $result['end_time']);
        $this->assertTrue($result['wrap_to_next_day']);
    }
    
    /**
     * Test pro settings sanitization with invalid devices
     */
    public function test_sanitize_pro_settings_invalid_devices() {
        $input = array(
            'device_visibility' => array('desktop', 'smartphone', 'tablet')
        );
        
        $result = $this->settings->sanitize_pro_settings($input);
        
        // Should only contain valid devices
        $this->assertCount(2, $result['device_visibility']);
        $this->assertContains('desktop', $result['device_visibility']);
        $this->assertContains('tablet', $result['device_visibility']);
        $this->assertNotContains('smartphone', $result['device_visibility']);
    }
    
    /**
     * Test pro settings sanitization with no devices selected
     */
    public function test_sanitize_pro_settings_no_devices() {
        $input = array(
            'device_visibility' => array()
        );
        
        $result = $this->settings->sanitize_pro_settings($input);
        
        // Should default to all devices
        $this->assertCount(3, $result['device_visibility']);
        $this->assertContains('desktop', $result['device_visibility']);
        $this->assertContains('tablet', $result['device_visibility']);
        $this->assertContains('mobile', $result['device_visibility']);
    }
    
    /**
     * Test phone number validation edge cases
     */
    public function test_phone_number_edge_cases() {
        $test_cases = array(
            // Valid cases
            '+1-555-123-4567' => '+1-555-123-4567',
            '+1-000-000-0000' => '+1-000-000-0000',
            '+1-999-999-9999' => '+1-999-999-9999',
            
            // Invalid cases - should return default
            '555-123-4567' => '+1-234-567-8910', // Missing +1-
            '+1 555 123 4567' => '+1-234-567-8910', // Spaces instead of dashes
            '(555) 123-4567' => '+1-234-567-8910', // Wrong format
            '+15551234567' => '+1-234-567-8910', // No dashes
            '555.123.4567' => '+1-234-567-8910', // Dots instead of dashes
            '' => '+1-234-567-8910', // Empty
        );
        
        foreach ($test_cases as $input_phone => $expected_phone) {
            $input = array('phone_number' => $input_phone);
            $result = $this->settings->sanitize_basic_settings($input);
            $this->assertEquals($expected_phone, $result['phone_number'], "Failed for input: {$input_phone}");
        }
    }
}