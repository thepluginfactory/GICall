<?php

namespace FBCallNow\Tests\Frontend;

use PHPUnit\Framework\TestCase;
use FBCallNow\Frontend\ButtonRenderer;

/**
 * ButtonRenderer class tests
 * 
 * @package FBCallNow\Tests\Frontend
 */
class ButtonRendererTest extends TestCase {
    
    private $button_renderer;
    private $mock_basic_settings;
    private $mock_pro_settings;
    
    /**
     * Set up test environment
     */
    public function setUp(): void {
        parent::setUp();
        
        // Mock WordPress functions
        if (!function_exists('get_option')) {
            function get_option($option, $default = false) {
                global $mock_options;
                return $mock_options[$option] ?? $default;
            }
        }
        
        if (!function_exists('wp_date')) {
            function wp_date($format, $timestamp = null) {
                return date($format, $timestamp ?? time());
            }
        }
        
        if (!function_exists('__')) {
            function __($text, $domain = 'default') {
                return $text;
            }
        }
        
        if (!function_exists('esc_html')) {
            function esc_html($text) {
                return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
            }
        }
        
        if (!function_exists('esc_attr')) {
            function esc_attr($text) {
                return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
            }
        }
        
        $this->button_renderer = new ButtonRenderer();
        
        // Set up default mock settings
        $this->mock_basic_settings = array(
            'enable' => true,
            'button_text' => 'Call Now',
            'phone_number' => '+1-555-123-4567',
            'button_color' => '#007cba',
            'text_color' => '#ffffff',
            'horizontal_position' => 'right',
            'vertical_position' => 10
        );
        
        $this->mock_pro_settings = array(
            'days_visible' => array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'),
            'start_time' => '00:00',
            'end_time' => '23:00',
            'wrap_to_next_day' => false,
            'device_visibility' => array('desktop', 'tablet', 'mobile')
        );
    }
    
    /**
     * Set mock options for testing
     */
    private function setMockOptions($basic = null, $pro = null) {
        global $mock_options;
        $mock_options = array(
            'fbcn_basic_settings' => $basic ?? $this->mock_basic_settings,
            'fbcn_pro_settings' => $pro ?? $this->mock_pro_settings
        );
    }
    
    /**
     * Test button should display with valid settings
     */
    public function test_should_display_button_valid() {
        $this->setMockOptions();
        
        $reflection = new \ReflectionClass($this->button_renderer);
        $method = $reflection->getMethod('should_display_button');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->button_renderer);
        $this->assertTrue($result);
    }
    
    /**
     * Test button should not display when disabled
     */
    public function test_should_not_display_when_disabled() {
        $basic_settings = $this->mock_basic_settings;
        $basic_settings['enable'] = false;
        
        $this->setMockOptions($basic_settings);
        
        $reflection = new \ReflectionClass($this->button_renderer);
        $method = $reflection->getMethod('should_display_button');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->button_renderer);
        $this->assertFalse($result);
    }
    
    /**
     * Test button should not display with invalid phone number
     */
    public function test_should_not_display_invalid_phone() {
        $basic_settings = $this->mock_basic_settings;
        $basic_settings['phone_number'] = '555-123-4567'; // Invalid format
        
        $this->setMockOptions($basic_settings);
        
        $reflection = new \ReflectionClass($this->button_renderer);
        $method = $reflection->getMethod('should_display_button');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->button_renderer);
        $this->assertFalse($result);
    }
    
    /**
     * Test visibility on different days
     */
    public function test_visibility_different_days() {
        $reflection = new \ReflectionClass($this->button_renderer);
        $method = $reflection->getMethod('is_visible_today');
        $method->setAccessible(true);
        
        // Test visible on Monday
        $pro_settings = array('days_visible' => array('monday', 'wednesday', 'friday'));
        
        // Mock Monday
        if (!function_exists('wp_date')) {
            function wp_date($format) {
                if ($format === 'l') return 'Monday';
                return date($format);
            }
        }
        
        $result = $method->invoke($this->button_renderer, $pro_settings);
        $this->assertTrue($result);
        
        // Test not visible on Tuesday (redeclare function for this test)
        eval('
        function wp_date($format) {
            if ($format === "l") return "Tuesday";
            return date($format);
        }');
        
        $result = $method->invoke($this->button_renderer, $pro_settings);
        $this->assertFalse($result);
    }
    
    /**
     * Test time window visibility
     */
    public function test_time_window_visibility() {
        $reflection = new \ReflectionClass($this->button_renderer);
        $method = $reflection->getMethod('is_visible_now');
        $method->setAccessible(true);
        
        // Test during business hours (9-17)
        $pro_settings = array(
            'start_time' => '09:00',
            'end_time' => '17:00',
            'wrap_to_next_day' => false
        );
        
        // Mock 2 PM (14:00)
        eval('
        function wp_date($format) {
            if ($format === "H:i") return "14:00";
            if ($format === "H") return "14";
            return date($format);
        }');
        
        $result = $method->invoke($this->button_renderer, $pro_settings);
        $this->assertTrue($result);
        
        // Mock 8 AM (before business hours)
        eval('
        function wp_date($format) {
            if ($format === "H:i") return "08:00";
            if ($format === "H") return "8";
            return date($format);
        }');
        
        $result = $method->invoke($this->button_renderer, $pro_settings);
        $this->assertFalse($result);
    }
    
    /**
     * Test wrap to next day functionality
     */
    public function test_wrap_to_next_day() {
        $reflection = new \ReflectionClass($this->button_renderer);
        $method = $reflection->getMethod('is_visible_now');
        $method->setAccessible(true);
        
        // Test wrap to next day enabled
        $pro_settings = array(
            'start_time' => '20:00',
            'end_time' => '23:00',
            'wrap_to_next_day' => true
        );
        
        // Mock midnight (00:00) - should be visible with wrap enabled
        eval('
        function wp_date($format) {
            if ($format === "H:i") return "00:00";
            if ($format === "H") return "0";
            return date($format);
        }');
        
        $result = $method->invoke($this->button_renderer, $pro_settings);
        $this->assertTrue($result);
        
        // Test same settings without wrap
        $pro_settings['wrap_to_next_day'] = false;
        
        $result = $method->invoke($this->button_renderer, $pro_settings);
        $this->assertFalse($result);
    }
    
    /**
     * Test visibility debug info generation
     */
    public function test_visibility_debug_info() {
        $this->setMockOptions();
        
        $debug_info = $this->button_renderer->get_visibility_debug_info();
        
        $this->assertIsArray($debug_info);
        $this->assertArrayHasKey('enabled', $debug_info);
        $this->assertArrayHasKey('phone_valid', $debug_info);
        $this->assertArrayHasKey('visible_today', $debug_info);
        $this->assertArrayHasKey('visible_now', $debug_info);
        $this->assertArrayHasKey('current_time', $debug_info);
        $this->assertArrayHasKey('current_day', $debug_info);
        $this->assertArrayHasKey('should_display', $debug_info);
        
        $this->assertTrue($debug_info['enabled']);
        $this->assertTrue($debug_info['phone_valid']);
    }
    
    /**
     * Test phone number validation in should_display_button
     */
    public function test_phone_validation_in_display_check() {
        $test_cases = array(
            '+1-555-123-4567' => true,  // Valid
            '+1-000-000-0000' => true,  // Valid (all zeros)
            '+1-999-999-9999' => true,  // Valid (all nines)
            '555-123-4567' => false,    // Missing +1-
            '+15551234567' => false,    // No dashes
            '(555) 123-4567' => false,  // Wrong format
            '' => false,                // Empty
            '+1-555-12-34567' => false, // Wrong dash placement
        );
        
        $reflection = new \ReflectionClass($this->button_renderer);
        $method = $reflection->getMethod('should_display_button');
        $method->setAccessible(true);
        
        foreach ($test_cases as $phone => $expected) {
            $basic_settings = $this->mock_basic_settings;
            $basic_settings['phone_number'] = $phone;
            
            $this->setMockOptions($basic_settings);
            
            $result = $method->invoke($this->button_renderer);
            $this->assertEquals($expected, $result, "Failed for phone: {$phone}");
        }
    }
    
    /**
     * Test edge cases for time window validation
     */
    public function test_time_window_edge_cases() {
        $reflection = new \ReflectionClass($this->button_renderer);
        $method = $reflection->getMethod('is_visible_now');
        $method->setAccessible(true);
        
        // Test exact start time
        $pro_settings = array(
            'start_time' => '09:00',
            'end_time' => '17:00',
            'wrap_to_next_day' => false
        );
        
        eval('
        function wp_date($format) {
            if ($format === "H:i") return "09:00";
            if ($format === "H") return "9";
            return date($format);
        }');
        
        $result = $method->invoke($this->button_renderer, $pro_settings);
        $this->assertTrue($result);
        
        // Test exact end time
        eval('
        function wp_date($format) {
            if ($format === "H:i") return "17:00";
            if ($format === "H") return "17";
            return date($format);
        }');
        
        $result = $method->invoke($this->button_renderer, $pro_settings);
        $this->assertTrue($result);
        
        // Test just after end time
        eval('
        function wp_date($format) {
            if ($format === "H:i") return "18:00";
            if ($format === "H") return "18";
            return date($format);
        }');
        
        $result = $method->invoke($this->button_renderer, $pro_settings);
        $this->assertFalse($result);
    }
    
    /**
     * Test all-day visibility (default settings)
     */
    public function test_all_day_visibility() {
        $reflection = new \ReflectionClass($this->button_renderer);
        $method = $reflection->getMethod('is_visible_now');
        $method->setAccessible(true);
        
        // Default pro settings (00:00 to 23:00)
        $pro_settings = $this->mock_pro_settings;
        
        // Test various times throughout the day
        $test_times = array('00:00', '06:00', '12:00', '18:00', '23:00');
        
        foreach ($test_times as $time) {
            $hour = intval(explode(':', $time)[0]);
            
            eval("
            function wp_date(\$format) {
                if (\$format === 'H:i') return '{$time}';
                if (\$format === 'H') return '{$hour}';
                return date(\$format);
            }");
            
            $result = $method->invoke($this->button_renderer, $pro_settings);
            $this->assertTrue($result, "Should be visible at {$time}");
        }
    }
}