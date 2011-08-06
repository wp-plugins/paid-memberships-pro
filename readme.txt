=== Paid Memberships Pro ===
Contributors: strangerstudios
Tags: memberships, ecommerce, authorize.net, paypal
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 1.1.11

An infinitely customizable Membership Plugin for WordPress integrated with Authorize.net or PayPal(r) for recurring payments, flexible content control, themed registration, checkout, and more ...

== Description ==

Paid Memberships Pro is a WordPress Plugin and support community for membership site curators. PMPro's rich feature set allows you to add a new revenue source to your new or current blog or website and is flexible enough to fit the needs of almost all online and offline businesses.

== Installation ==

1. Upload the `paid-memberships-pro` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Follow the instructions here to setup your memberships: http://www.paidmembershipspro.com/support/initial-plugin-setup/

== Changelog ==
= 1.1.11 =
* Removed some debug code from the invoice page that might have shown on error.
* Added check to recaptcha library code incase it is already installed. (Let's hope other plugin developers are doing the same.)
* Removed the TinyMCE editor from the description field on the edit membership level page. It was a little buggy. Might bring it back later.

= 1.1.10 =
* added a hook/filter "pmpro_rss_text_filter"
* added a hook/filter "pmpro_non_member_text_filter"
* added a hook/filter "pmpro_not_logged_in_text_filter"
* adjusted the pmpro_has_membership_access() function
* added a hook/filter "pmpro_has_membership_access_filter"
* updated the hook/filter "pmpro_has_membership_access_filter_{post-type}"
* removed the "pmpro_has_membership_access_action_{post-type}" hook/action
* update invoice page to handle case where no invoice is found

= 1.1.9 =
* You can now set individual posts to require membership without assigning them to a category.
* Fixed bug with the confirmation email during signup.
* Fixed a CSS bug on the cancel membership page.

= 1.1.8 =
* Fix for login/registration URL rerouting.
* Added members list to admin bar menu.
* Added warning/error when trying to checkout before the payment gateway is setup.
* Fixed some error handling in the order class.
* Fixed a bug that occurred when processing amounts less than $1.

= 1.1.7 =
* Fixed bugs with http to https redirects and visa versa.
* Fixed redirect bugs for sites installed in a subdomain.

= 1.1.6 =
* Fixed MySQL bug showing up on some users add membership level page.

= 1.1.5 =
* Required fix for PayPal Website Payments Pro processing. Please update.
* Fixed bug with pagination on members list.
* Fixed bugs with errors thrown by MemberOrder class.
* Updated login/registration URL rerouting.

= 1.1.4 =
* Custom Post Types default to allowing access
* Fixed login_redirect code.
* Added pmpro_login_redirect filter for when members login.

= 1.1.3 =
* Getting ready for the WP plugin repository
* License text update.

= 1.1.2 =
* Added hooks to checkout page for customizing registration fields.
* Fixed bug in pmpro_getLevelCost();
* Another CCV/CVV fix for Authorize.net.
* License text update.
* Admin notices are loaded via Ajax now.

= 1.1.1 =
* Added honeypot to signup page.
* Updated pmpro_add_pages to use capabilities instead of user levels
* Fixed checkboxes in admin screens.
* Now checking that passwords match on signup.
* Properly sending CCV/CVV codes to Authorize.net.

= 1.0 =
* This is the launch version. No changes yet.
