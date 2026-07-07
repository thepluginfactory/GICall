<?php

namespace FBCallNow\Core;

/**
 * Debug logging system for FB Call Now plugin
 * 
 * @package FBCallNow\Core
 * @since 3.0.0
 */
class Logger {
    
    /**
     * Log file name
     */
    const LOG_FILE = 'fb-call-now.log';
    
    /**
     * Log an info message
     * 
     * @param string $message The message to log
     */
    public static function info(string $message) {
        self::write_log('INFO', $message);
    }
    
    /**
     * Log an error message
     * 
     * @param string $message The message to log
     */
    public static function error(string $message) {
        self::write_log('ERROR', $message);
    }
    
    /**
     * Write message to log file
     * 
     * @param string $level Log level (INFO, ERROR, etc.)
     * @param string $message The message to log
     */
    private static function write_log(string $level, string $message) {
        // Check if debug logging is enabled in Pro Settings
        $pro_settings = get_option('fbcn_pro_settings', array());
        if (empty($pro_settings['debug_logging'])) {
            return; // Debug logging is disabled
        }
        
        $log_file = self::get_log_file_path();
        
        // Create uploads directory if it doesn't exist
        $upload_dir = wp_upload_dir();
        if (!file_exists($upload_dir['basedir'])) {
            wp_mkdir_p($upload_dir['basedir']);
        }
        
        // Format the log entry
        $timestamp = current_time('Y-m-d H:i:s');
        $log_entry = sprintf("[%s] %s: %s\n", $timestamp, $level, $message);
        
        // Write to file (append mode)
        $result = file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
        
        // If writing failed, try to create the file first
        if ($result === false && !file_exists($log_file)) {
            touch($log_file);
            chmod($log_file, 0644);
            file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
        }
    }
    
    /**
     * Get the full path to the log file
     * 
     * @return string Full path to log file
     */
    public static function get_log_file_path() {
        $upload_dir = wp_upload_dir();
        return trailingslashit($upload_dir['basedir']) . self::LOG_FILE;
    }
    
    /**
     * Read log file contents
     * 
     * @param int $lines Number of lines to read from end of file
     * @return string Log file contents
     */
    public static function read_log(int $lines = 200) {
        $log_file = self::get_log_file_path();
        
        if (!file_exists($log_file)) {
            return __('Log file does not exist yet. Activate the plugin and perform some actions to generate log entries.', 'fb-call-now');
        }
        
        if (!is_readable($log_file)) {
            return __('Log file exists but is not readable. Please check file permissions.', 'fb-call-now');
        }
        
        // Read the last N lines efficiently
        $content = self::tail($log_file, $lines);
        
        if (empty($content)) {
            return __('Log file is empty.', 'fb-call-now');
        }
        
        return $content;
    }
    
    /**
     * Get the last N lines of a file efficiently
     * 
     * @param string $file Path to file
     * @param int $lines Number of lines to retrieve
     * @return string File content
     */
    private static function tail(string $file, int $lines = 200) {
        $handle = fopen($file, 'r');
        if (!$handle) {
            return '';
        }
        
        $line_count = 0;
        $lines_array = array();
        
        // Start from the end of the file
        fseek($handle, -1, SEEK_END);
        
        // Read backwards
        $position = ftell($handle);
        $line = '';
        
        while ($position > 0 && $line_count < $lines) {
            $char = fgetc($handle);
            
            if ($char === "\n") {
                $lines_array[] = strrev($line);
                $line = '';
                $line_count++;
            } else {
                $line .= $char;
            }
            
            fseek($handle, --$position);
        }
        
        // Don't forget the last line
        if (!empty($line) && $line_count < $lines) {
            $lines_array[] = strrev($line);
        }
        
        fclose($handle);
        
        // Reverse the array to get chronological order
        return implode("\n", array_reverse($lines_array));
    }
    
    /**
     * Clear the log file
     * 
     * @return bool Success status
     */
    public static function clear_log() {
        $log_file = self::get_log_file_path();
        
        if (file_exists($log_file)) {
            $result = unlink($log_file);
            if ($result) {
                self::info('Log file cleared by user');
            }
            return $result;
        }
        
        return true; // Already doesn't exist
    }
    
    /**
     * Get log file size
     * 
     * @return string Human readable file size
     */
    public static function get_log_file_size() {
        $log_file = self::get_log_file_path();
        
        if (!file_exists($log_file)) {
            return '0 B';
        }
        
        $bytes = filesize($log_file);
        $units = array('B', 'KB', 'MB', 'GB');
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}