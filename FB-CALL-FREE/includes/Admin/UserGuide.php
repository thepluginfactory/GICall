<?php

namespace FBCallNow\Admin;

/**
 * User Guide admin page
 * 
 * @package FBCallNow\Admin
 * @since 3.0.0
 */
class UserGuide {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Menu registration is handled in Settings.php
    }
    
    /**
     * User guide page
     */
    public function user_guide_page() {
        ?>
        <div class="wrap fbcn-settings-page">
            <!-- SaaS Header -->
            <div class="fbcn-saas-header">
                <div class="fbcn-brand">
                    <div class="fbcn-logo-icon">
                        <span class="dashicons dashicons-book"></span>
                    </div>
                    <div class="fbcn-brand-text">
                        <h1><?php _e('User Guide & Documentation', 'fb-call-now'); ?></h1>
                        <span class="fbcn-byline"><?php _e('Master your call button settings', 'fb-call-now'); ?></span>
                    </div>
                </div>
                <div class="fbcn-meta">
                    <a href="<?php echo admin_url('admin.php?page=fbcn_basic_settings'); ?>" class="button button-primary">
                        <?php _e('← Back to Settings', 'fb-call-now'); ?>
                    </a>
                </div>
            </div>

            <div class="fbcn-guide-grid">
                <!-- Basic Settings Card -->
                <div class="fbcn-guide-card">
                    <div class="fbcn-guide-header">
                        <div class="fbcn-guide-icon"><span class="dashicons dashicons-admin-generic"></span></div>
                        <h2><?php _e('Basic Settings', 'fb-call-now'); ?></h2>
                    </div>
                    <p class="fbcn-card-intro"><?php _e('The Basic Settings control the core functionality and appearance of your floating call button.', 'fb-call-now'); ?></p>
                    <ul class="fbcn-guide-list">
                        <li>
                            <strong><?php _e('Enable Button:', 'fb-call-now'); ?></strong>
                            <?php _e('Master on/off switch for the entire plugin. When disabled, the call button will not appear on your website.', 'fb-call-now'); ?>
                        </li>
                        <li>
                            <strong><?php _e('Button Text:', 'fb-call-now'); ?></strong>
                            <?php _e('The text displayed on the floating button. Default is "Call Now". Font size is automatically adjusted: 20px on desktop/tablet, 17px on mobile devices.', 'fb-call-now'); ?>
                        </li>
                        <li>
                            <strong><?php _e('Telephone Number:', 'fb-call-now'); ?></strong>
                            <?php _e('The phone number that will be dialed when visitors click the button. Must be in +1-XXX-XXX-XXXX format. The default example is +1-234-567-8910.', 'fb-call-now'); ?>
                        </li>
                        <li>
                            <strong><?php _e('Button & Text Color:', 'fb-call-now'); ?></strong>
                            <?php _e('Customize the background and text colors to match your brand. Defaults are WordPress blue (#007cba) and white (#ffffff).', 'fb-call-now'); ?>
                        </li>
                        <li>
                            <strong><?php _e('Positioning:', 'fb-call-now'); ?></strong>
                            <?php _e('Choose Left/Right horizontal alignment and a Vertical Position (1-10) to control height tailored to your layout.', 'fb-call-now'); ?>
                        </li>
                        <li>
                            <strong><?php _e('Delete Data on Uninstall:', 'fb-call-now'); ?></strong>
                            <?php _e('When checked, all plugin settings will be permanently removed when you uninstall the plugin. Default is unchecked to preserve your settings.', 'fb-call-now'); ?>
                        </li>
                    </ul>
                </div>

                <!-- Pro Settings Card -->
                <div class="fbcn-guide-card">
                    <div class="fbcn-guide-header">
                        <div class="fbcn-guide-icon"><span class="dashicons dashicons-star-filled"></span></div>
                        <h2><?php _e('Pro Settings', 'fb-call-now'); ?></h2>
                    </div>
                    <p class="fbcn-card-intro"><?php _e('Pro Settings provide advanced visibility controls to show the call button only when appropriate for your business.', 'fb-call-now'); ?></p>
                    <ul class="fbcn-guide-list">
                        <li>
                            <strong><?php _e('Day-of-Week Visibility:', 'fb-call-now'); ?></strong>
                            <?php _e('Select which days of the week the button should appear. By default, all days are selected. Uncheck days when your business is closed.', 'fb-call-now'); ?>
                        </li>
                        <li>
                            <strong><?php _e('Time Window:', 'fb-call-now'); ?></strong>
                            <?php _e('Set specific hours when the button should be visible. Start/End Time use 24-hour format. Default is 00:00 to 23:00 (all day).', 'fb-call-now'); ?>
                        </li>
                        <li>
                            <strong><?php _e('Wrap to Next Day:', 'fb-call-now'); ?></strong>
                            <?php _e('Enable this option if your business hours extend past midnight. (e.g., Open until 02:00 AM next day).', 'fb-call-now'); ?>
                        </li>
                        <li>
                            <strong><?php _e('Device Visibility:', 'fb-call-now'); ?></strong>
                            <?php _e('Choose which device types should display the button based on screen width:', 'fb-call-now'); ?>
                            <ul style="margin-top: 8px; margin-left:15px; list-style-type:circle;">
                                <li><?php _e('Desktop: Screens 992px+', 'fb-call-now'); ?></li>
                                <li><?php _e('Tablet: Screens 768px - 991px', 'fb-call-now'); ?></li>
                                <li><?php _e('Mobile: Screens < 768px', 'fb-call-now'); ?></li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <!-- Technical Information Card -->
                <div class="fbcn-guide-card">
                    <div class="fbcn-guide-header">
                        <div class="fbcn-guide-icon"><span class="dashicons dashicons-hammer"></span></div>
                        <h2><?php _e('Technical Information', 'fb-call-now'); ?></h2>
                    </div>
                    
                    <h3><?php _e('How It Works:', 'fb-call-now'); ?></h3>
                    <ul class="fbcn-guide-list" style="margin-bottom: 20px;">
                        <li>
                            <strong><?php _e('Button Markup:', 'fb-call-now'); ?></strong>
                            <?php _e('Generates a semantic HTML link with ARIA labels: &lt;a href="tel:+1..." role="button" aria-label="Call Now"&gt;', 'fb-call-now'); ?>
                        </li>
                        <li>
                            <strong><?php _e('Positioning:', 'fb-call-now'); ?></strong>
                            <?php _e('Uses CSS fixed positioning. Vertical position is calculated as: (position-1)/9 × 100% from top.', 'fb-call-now'); ?>
                        </li>
                        <li>
                            <strong><?php _e('Timezone Handling:', 'fb-call-now'); ?></strong>
                            <?php _e('All time-based visibility rules respect your WordPress site\'s configured timezone (Settings > General).', 'fb-call-now'); ?>
                        </li>
                        <li>
                            <strong><?php _e('Performance:', 'fb-call-now'); ?></strong>
                            <?php _e('Lightweight implementation with minimal CSS/JS. Assets are loaded globally but unminified for debugging.', 'fb-call-now'); ?>
                        </li>
                    </ul>

                     <h3><?php _e('Troubleshooting:', 'fb-call-now'); ?></h3>
                    <ul class="fbcn-guide-list">
                        <li>
                            <strong><?php _e('Button Not Appearing:', 'fb-call-now'); ?></strong>
                            <?php _e('Check "Enable Button" is ON. Check Pro Settings for Day/Time/Device restrictions.', 'fb-call-now'); ?>
                        </li>
                        <li>
                            <strong><?php _e('Phone Number Issues:', 'fb-call-now'); ?></strong>
                            <?php _e('Ensure strict format +1-XXX-XXX-XXXX. No spaces or extra chars allowed.', 'fb-call-now'); ?>
                        </li>
                        <li>
                            <strong><?php _e('Time Window Not Working:', 'fb-call-now'); ?></strong>
                            <?php _e('Check WP Timezone settings. If crossing midnight, enable "Wrap to Next Day".', 'fb-call-now'); ?>
                        </li>
                    </ul>
                </div>

                <!-- Best Practices Card -->
                <div class="fbcn-guide-card">
                    <div class="fbcn-guide-header">
                        <div class="fbcn-guide-icon"><span class="dashicons dashicons-thumbs-up"></span></div>
                        <h2><?php _e('Best Practices', 'fb-call-now'); ?></h2>
                    </div>
                    <ul class="fbcn-guide-list">
                        <li><?php _e('Use a local phone number for better trust and conversion.', 'fb-call-now'); ?></li>
                        <li><?php _e('Set realistic business hours to avoid missing calls.', 'fb-call-now'); ?></li>
                        <li><?php _e('Contrast is key: Choose button colors that stand out against your background.', 'fb-call-now'); ?></li>
                        <li><?php _e('Test on actual mobile devices to ensure clickability.', 'fb-call-now'); ?></li>
                        <li><?php _e('Consider hiding on Desktop if your traffic is primarily Mobile.', 'fb-call-now'); ?></li>
                        <li><?php _e('Keep text short: "Call Now" or "Call Us" work best.', 'fb-call-now'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }
}