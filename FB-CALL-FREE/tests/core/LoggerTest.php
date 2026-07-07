<?php

namespace FBCallNow\Tests\Core;

use PHPUnit\Framework\TestCase;
use FBCallNow\Core\Logger;

/**
 * Logger class tests
 * 
 * @package FBCallNow\Tests\Core
 */
class LoggerTest extends TestCase {
    
    private $log_file_path;
    
    /**
     * Set up test environment
     */
    public function setUp(): void {
        parent::setUp();
        
        // Mock WordPress functions for testing
        if (!function_exists('wp_upload_dir')) {
            function wp_upload_dir() {
                return array('basedir' => sys_get_temp_dir());
            }
        }
        
        if (!function_exists('current_time')) {
            function current_time($format) {
                return date($format);
            }
        }
        
        $this->log_file_path = Logger::get_log_file_path();
        
        // Clean up any existing log file
        if (file_exists($this->log_file_path)) {
            unlink($this->log_file_path);
        }
    }
    
    /**
     * Clean up after test
     */
    public function tearDown(): void {
        // Clean up log file
        if (file_exists($this->log_file_path)) {
            unlink($this->log_file_path);
        }
        
        parent::tearDown();
    }
    
    /**
     * Test info logging
     */
    public function test_info_logging() {
        $message = 'Test info message';
        Logger::info($message);
        
        $this->assertTrue(file_exists($this->log_file_path));
        
        $log_content = file_get_contents($this->log_file_path);
        $this->assertStringContainsString('INFO: ' . $message, $log_content);
    }
    
    /**
     * Test error logging
     */
    public function test_error_logging() {
        $message = 'Test error message';
        Logger::error($message);
        
        $this->assertTrue(file_exists($this->log_file_path));
        
        $log_content = file_get_contents($this->log_file_path);
        $this->assertStringContainsString('ERROR: ' . $message, $log_content);
    }
    
    /**
     * Test log file path generation
     */
    public function test_log_file_path() {
        $path = Logger::get_log_file_path();
        $this->assertStringEndsWith('fb-call-now.log', $path);
    }
    
    /**
     * Test reading log when file doesn't exist
     */
    public function test_read_log_no_file() {
        $content = Logger::read_log();
        $this->assertStringContainsString('Log file does not exist yet', $content);
    }
    
    /**
     * Test reading log with content
     */
    public function test_read_log_with_content() {
        Logger::info('First message');
        Logger::error('Second message');
        
        $content = Logger::read_log();
        $this->assertStringContainsString('First message', $content);
        $this->assertStringContainsString('Second message', $content);
    }
    
    /**
     * Test clearing log
     */
    public function test_clear_log() {
        Logger::info('Test message');
        $this->assertTrue(file_exists($this->log_file_path));
        
        $result = Logger::clear_log();
        $this->assertTrue($result);
        $this->assertFalse(file_exists($this->log_file_path));
    }
    
    /**
     * Test log file size calculation
     */
    public function test_log_file_size() {
        // When no file exists
        $size = Logger::get_log_file_size();
        $this->assertEquals('0 B', $size);
        
        // After writing some content
        Logger::info('Test message for size calculation');
        $size = Logger::get_log_file_size();
        $this->assertNotEquals('0 B', $size);
        $this->assertStringContainsString('B', $size); // Should contain bytes unit
    }
    
    /**
     * Test multiple log entries
     */
    public function test_multiple_log_entries() {
        Logger::info('Message 1');
        Logger::error('Message 2');
        Logger::info('Message 3');
        
        $content = Logger::read_log();
        $lines = explode("\n", trim($content));
        
        // Should have 3 lines
        $this->assertCount(3, $lines);
        
        // Check chronological order
        $this->assertStringContainsString('Message 1', $lines[0]);
        $this->assertStringContainsString('Message 2', $lines[1]);
        $this->assertStringContainsString('Message 3', $lines[2]);
    }
    
    /**
     * Test log format
     */
    public function test_log_format() {
        Logger::info('Test format message');
        
        $content = Logger::read_log();
        $lines = explode("\n", trim($content));
        $line = $lines[0];
        
        // Should match format: [YYYY-MM-DD HH:MM:SS] LEVEL: Message
        $this->assertMatchesRegularExpression(
            '/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] INFO: Test format message$/',
            $line
        );
    }
}