=== FB-Call Now ===
Contributors: johnrobins
Tags: call button, floating button, phone, contact, click to call
Requires at least: 5.8
Tested up to: 6.3
Requires PHP: 7.4
Stable tag: 3.0.1
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Renders a floating circular "Call Now" button on the front end, which dials a user-configured telephone number when clicked. Includes both Basic and Pro visibility controls.

== Description ==

FB-Call Now creates a floating call button that appears on your website, making it easy for visitors to contact you with just one click. The plugin offers comprehensive customization options for both appearance and visibility.

**Key Features:**

* **Easy Setup**: Configure your phone number and customize the button appearance in minutes
* **Responsive Design**: Automatically adjusts for desktop, tablet, and mobile devices
* **Smart Visibility**: Show the button only during business hours and on specific days
* **Full Customization**: Choose colors, position, and text to match your brand
* **Accessibility Ready**: Includes proper ARIA labels and keyboard navigation support
* **Debug Logging**: Built-in logging system for easy troubleshooting

**Basic Settings:**
* Enable/disable the call button
* Customize button text and colors
* Set horizontal and vertical positioning
* Configure phone number in +1-XXX-XXX-XXXX format

**Pro Settings:**
* Day-of-week visibility controls
* Time window restrictions with timezone support
* Device-specific visibility (desktop/tablet/mobile)
* Wrap-to-next-day functionality for late hours

**Technical Features:**
* Lightweight and performance-optimized
* Uses CSS media queries for responsive behavior
* Server-side PHP rendering for better performance
* Comprehensive debug logging system
* Clean uninstall option

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/fb-call-now` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the FB-Call Now -> Basic Settings screen to configure your phone number and appearance.
4. Optionally configure Pro Settings for advanced visibility controls.

== Frequently Asked Questions ==

= What phone number format should I use? =

Use the format +1-XXX-XXX-XXXX (including the +1 country code and dashes). For example: +1-234-567-8910.

= Can I hide the button on certain devices? =

Yes! Use the Pro Settings to control visibility on desktop (≥992px), tablet (768px-991px), and mobile (<768px) devices.

= How do I set business hours for the button? =

In Pro Settings, use the Time Window controls to set start and end times. Enable "Wrap to Next Day" if your hours extend past midnight.

= The button isn't appearing. How do I troubleshoot? =

1. Check that "Enable Button" is checked in Basic Settings
2. Verify your phone number is in the correct format
3. Check Pro Settings to ensure current day/time/device are allowed
4. Visit the Debug Log page for detailed troubleshooting information

= Can I customize the button position? =

Yes! You can choose left or right horizontal positioning and select from 10 vertical positions (1=top, 10=bottom).

= Does the plugin work with caching plugins? =

Yes, the plugin is designed to work with caching plugins. The button is rendered server-side and uses efficient CSS for styling.

= How do I completely remove all plugin data? =

Check "Delete Data on Uninstall" in Basic Settings before uninstalling the plugin. This will remove all settings and the debug log.

== Screenshots ==

1. Basic Settings page with live preview
2. Pro Settings for advanced visibility controls
3. Debug Log page for troubleshooting
4. Example of floating call button on website
5. User Guide with detailed documentation

== Changelog ==

= 3.0.1 =
* Code quality improvements and optimizations
* Enhanced cache busting strategy for better performance
* Refactored settings validation for improved maintainability
* Moved inline styles/scripts to external files
* Added centralized default settings management
* Improved configuration files with development tools
* Enhanced test coverage and documentation

= 3.0.0 =
* Initial release
* Basic call button functionality with customizable appearance
* Pro visibility controls for days, times, and devices
* Comprehensive debug logging system
* Responsive design with device-specific visibility
* Accessibility features with ARIA labels
* Clean uninstall option
* Live preview in admin settings
* User guide with detailed documentation

== Upgrade Notice ==

= 3.0.1 =
Improved code quality, performance optimizations, and better maintainability. Recommended update for all users.

= 3.0.0 =
Initial release of FB-Call Now plugin. Start making it easy for customers to call you with just one click!

== Support ==

For support and questions, please visit our support forum or contact us through the plugin's admin pages. The built-in Debug Log page provides detailed information that can help diagnose any issues quickly.

== Privacy ==

This plugin does not collect, store, or transmit any personal data from your website visitors. The phone number you configure is only used to generate the call link and is stored locally in your WordPress database.

== Credits ==

Developed by John Robins. Built with WordPress coding standards and accessibility best practices in mind.