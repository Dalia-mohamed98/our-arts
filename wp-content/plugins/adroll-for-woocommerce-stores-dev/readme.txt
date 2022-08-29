=== AdRoll for WooCommerce Stores ===
Contributors: AdRoll, aaj2006, nextrollandersen
License: MIT
License URI: https://opensource.org/licenses/MIT
Requires at least: 4.4
Tested up to: 5.6.0
Requires PHP: 5.6.20
Stable tag: 2.2.9
Tags: retargeting, adroll, nextroll, ads

Connect your WooCommerce store to AdRoll and start serving retargeted ads across the web and Facebook.

== Description ==

**Plugin Features**

* The plugin places the AdRoll pixel - no need to place any code on your site.

* We automatically create a cart segment and a checkout segment, so you can set up a cart abandonment campaign in a flash.

* Track the number of sales and amount of revenue driven by the AdRoll campaign using an automatically-created conversion segment that passes back exact conversion values

**AdRoll Features**

* Access to all of the major display networks and Facebook

* Design Squad: we’ll make ads for you!

* A delightful support team: we’ll provide technical support and advice on how to set up an effective campaign

* SendRoll: send dynamic product emails triggered by customer behavior on your site.

* Integrations with other platforms

 * Easily connect your MailChimp or Campaign Monitor account to retarget your email lists with ads

 * Use Zapier to zap customer emails into retargeting segments

 * Use AdRoll’s pixel as a container and flip on integrations with Optimizely, Customer.io, Heap, and more, without placing additional code

== Installation ==

**Via AdRoll (Recommended)**
**The easiest way to use the plugin is by going through the AdRoll signup flow.**

1. Sign up for an AdRoll account at https://app.adroll.com/welcome/signup. Upon signup, AdRoll will check the URL you entered for a WooCommerce store.

2. Click on “Connect with WooCommerce” and authorize AdRoll to connect with your store.

3. Click on “Install WordPress Plugin” and install the plugin - the install button is at the bottom of the page.

4. IMPORTANT: once you’re redirected to the plugin page, click on the “Activate Plugin” link.

5. Your WooCommerce Store is now connected! The cart, checkout, and conversion segments are already created and ready for you to target.

**Via WooCommerce Plugin portal**

1. Go to plugins -> Add new.

2. Search for “AdRoll for WooCommerce Stores”

3. Click on “Install Now”

4. Click on “Activate Plugin”

== Frequently Asked Questions ==
n/a

== Changelog ==
=2.2.9=
* Adds support for WordPress 5.6.0 and Woocommerce 4.8.0
* Improved onboarding experience
* fixed a bug in search tracking
=2.2.8=
* Adds support for WordPress 5.5.1 and Woocommerce 4.5.2
=2.2.7=
* General improvements
=2.2.6=
* Security update
* Added extra pixel customization hooks
=2.2.5=
* Fixed checkout value tracking issues
=2.2.4=
* Fixed incompatibility with some CDN plugins
=2.2.3=
* Added support for WP 5.3.2
=2.2.2=
* Fixed some optimizers removing the script
=2.2.1=
* Added support for WP 5.2
=2.2.0=
* Improved installation flow by using generic return url
* Added admin notice when plugin setup is incomplete
=2.1.0=
* Added plugin activation/deactivation callbacks
=2.0.0=
* Overhauled installation flow
* Implemented updated version of AdRoll tracking pixel for improved performance
* Added tracking for products viewed
* Added tracking for product search
* Added tracking for products added to cart
* Added time limit to conversion tracking to prevent duplicates
* Standardized and improved the level of detail of tracked data
=1.0.5=
* Added version requirements for WordPress/PHP in readme
=1.0.4=
* Add support for WooCommerce 2.x
=1.0.3=
* Get rid of PHP Notices when debug mode is enabled
=1.0.2=
* Replace redirects with admin notices
=1.0.1=
* Use array() instead of shorthand, which only works with certain versions of PHP
=1.0.0=
* Stop checking adroll servers for eids after ten attempts per day and ten days total
* Hide options settings page
* Add teardown of db values when deactivated
* Add dynamic ads pixel code outside of plugin
=0.1.2=
* Introduce support for product collection
=0.1.1=
=0.1=

==Upgrade Notice ==
n/a

== Screenshots ==
n/a
