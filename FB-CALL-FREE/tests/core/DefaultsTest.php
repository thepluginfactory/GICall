<?php

namespace FBCallNow\Tests\Core;

use PHPUnit\Framework\TestCase;
use FBCallNow\Core\Defaults;

/**
 * Defaults class tests
 * 
 * @package FBCallNow\Tests\Core
 */
class DefaultsTest extends TestCase {
    
    /**
     * Test basic settings defaults
     */
    public function test_get_basic_settings() {
        // Mock WordPress function if not available
        if (!function_exists('__')) {
            function __($text, $domain) {
                return $text;
            }
        }
        
        $defaults = Defaults::get_basic_settings();
        
        $this->assertIsArray($defaults);
        $this->assertArrayHasKey('enable', $defaults);
        $this->assertArrayHasKey('button_text', $defaults);
        $this->assertArrayHasKey('phone_number', $defaults);
        $this->assertArrayHasKey('button_color', $defaults);
        $this->assertArrayHasKey('text_color', $defaults);
        $this->assertArrayHasKey('horizontal_position', $defaults);
        $this->assertArrayHasKey('vertical_position', $defaults);
        $this->assertArrayHasKey('delete_data_on_uninstall', $defaults);
        
        // Test specific default values
        $this->assertTrue($defaults['enable']);
        $this->assertEquals('Call Now', $defaults['button_text']);
        $this->assertEquals('+1-234-567-8910', $defaults['phone_number']);
        $this->assertEquals('#007cba', $defaults['button_color']);
        $this->assertEquals('#ffffff', $defaults['text_color']);
        $this->assertEquals('right', $defaults['horizontal_position']);
        $this->assertEquals(10, $defaults['vertical_position']);
        $this->assertFalse($defaults['delete_data_on_uninstall']);
    }
    
    /**
     * Test pro settings defaults
     */
    public function test_get_pro_settings() {
        $defaults = Defaults::get_pro_settings();
        
        $this->assertIsArray($defaults);
        $this->assertArrayHasKey('days_visible', $defaults);
        $this->assertArrayHasKey('start_time', $defaults);
        $this->assertArrayHasKey('end_time', $defaults);
        $this->assertArrayHasKey('wrap_to_next_day', $defaults);
        $this->assertArrayHasKey('device_visibility', $defaults);
        $this->assertArrayHasKey('debug_logging', $defaults);
        
        // Test specific default values
        $expected_days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
        $this->assertEquals($expected_days, $defaults['days_visible']);
        $this->assertEquals('00:00', $defaults['start_time']);
        $this->assertEquals('23:00', $defaults['end_time']);
        $this->assertFalse($defaults['wrap_to_next_day']);
        
        $expected_devices = array('desktop', 'tablet', 'mobile');
        $this->assertEquals($expected_devices, $defaults['device_visibility']);
        $this->assertFalse($defaults['debug_logging']);
    }
    
    /**
     * Test combined settings
     */
    public function test_get_all_settings() {
        $all_settings = Defaults::get_all_settings();
        
        $this->assertIsArray($all_settings);
        $this->assertArrayHasKey('basic', $all_settings);
        $this->assertArrayHasKey('pro', $all_settings);
        
        // Verify it returns the same data as individual methods
        $this->assertEquals(Defaults::get_basic_settings(), $all_settings['basic']);
        $this->assertEquals(Defaults::get_pro_settings(), $all_settings['pro']);
    }
    
    /**
     * Test that defaults are consistent across calls
     */
    public function test_defaults_consistency() {
        $basic1 = Defaults::get_basic_settings();
        $basic2 = Defaults::get_basic_settings();
        $this->assertEquals($basic1, $basic2);
        
        $pro1 = Defaults::get_pro_settings();
        $pro2 = Defaults::get_pro_settings();
        $this->assertEquals($pro1, $pro2);
    }
    
    /**
     * Test default array structure integrity
     */
    public function test_array_structure_integrity() {
        $basic = Defaults::get_basic_settings();
        $pro = Defaults::get_pro_settings();
        
        // Ensure no null values in basic settings
        foreach ($basic as $key => $value) {
            $this->assertNotNull($value, "Basic setting '{$key}' should not be null");
        }
        
        // Ensure no null values in pro settings
        foreach ($pro as $key => $value) {
            $this->assertNotNull($value, "Pro setting '{$key}' should not be null");
        }
        
        // Test array values are proper arrays
        $this->assertIsArray($pro['days_visible']);
        $this->assertIsArray($pro['device_visibility']);
        $this->assertNotEmpty($pro['days_visible']);
        $this->assertNotEmpty($pro['device_visibility']);
    }
}