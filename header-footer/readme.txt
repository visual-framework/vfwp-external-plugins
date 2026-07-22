=== Head, Footer and Post Injections ===
Tags: header, footer, ads, analytics, amp
Tested up to: 6.9
Stable tag: 3.3.6
Donate link: https://www.paypal.com/donate/?hosted_button_id=5PHGDGNHAYLJ8
Contributors: satollo
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Head and Footer plugin lets you to add HTML code to the head and footer sections of your site pages, inside posts... and more!

== Description ==

Why you have to install 10 plugins to add Google Analytics, Facebook Pixel, custom
tracking code, Google DFP code, Google Webmaster/Alexa/Bing/Tradedoubler verification code and so on...

With Header and Footer plugin you can just copy the code those services give you
in a centralized point to manage them all. And theme independent: you can change your theme
without loosing the code injected!

= Injection points and features =

* in the <head> page section where most if the codes are usually added
* just after the <body> tag as required by some JavaScript SDK (like Facebook)
* in the page footer (just before the </body> tag)
* recognize and execute PHP code to add logic to your injections
* distinct desktop and mobile injections

= AMP =

A new AMP dedicated section compatible with [AMP plugin](https://wordpress.org/plugins/amp) lets you to inject specific codes in
AMP pages. Should be ok even with other AMP plugins.

= Post Top and Bottom Codes =

Do you need to inject a banner over the post content or after it? No problem. With Header and
Footer you can:

* Add codes on _top_, _bottom_ and in the _middle_ of posts and pages
* Differentiate between _mobile_ and _desktop_ (you don't display the same ad format on both, true?)
* Separate post and page configuration
* Native PHP code enabled
* Shortcodes enabled

= Special Injections =

* Just after the opening BODY tag
* In the middle of post content (using configurable rules)
* Everywhere on template (using placeholders)

= bbPress =

The specific bbPress injections are going to be removed. Switch to my
[Ads for bbPress](https://wordpress.org/ads-bbpress), which is more flexible and complete.

= Limits =

This plugin cannot change the menu or the footer layout, those features must be covered by your theme!

Official page: [Header and Footer](https://www.satollo.net/plugins/header-footer).

Other plugins by Stefano Lissa:

* [Monitor](https://www.satollo.net/plugins/monitor)
* [Hyper Cache](https://www.satollo.net/plugins/hyper-cache)
* [Newsletter](https://www.thenewsletterplugin.com)
* [Include Me](https://www.satollo.net/plugins/include-me)
* [Thumbnails](https://www.satollo.net/plugins/thumbnails)
* [Ads for bbPress](https://wordpress.org/plugins/ads-bbpress/)

= Translation =

You can contribute to translate this plugin in your language on [WordPress Translate](https://translate.wordpress.org)

== Frequently Asked Questions ==

FAQs are answered on [Header and Footer](https://www.satollo.net/plugins/header-footer) page.

== Changelog ==

= 3.3.6 =

* Version number fix

= 3.3.5 =

* Fixed not visible options on the admin side

= 3.3.4 =

* Improved the after "body" tag injection (Markus Sandelin)
* Not completely removed the page buggering due to the possible use of "generic tags" (probably no one use)
* Fixed a int conversion PHP warning
* Started an admin page refresh

= 3.3.3 =

* WP 6.9 check
* PCP Check

= 3.3.1, 3.3.2 =

Breaking changes (see below)

* Disabled by default PHP on multisite installations
* Added constant HEADER_FOOTER_MULTISITE_ALLOW_PHP to be used on wp-config.php to enable PHP on multisite installations
* Added constant HEADER_FOOTER_ALLOW_PHP to be used on wp-config.php to enable PHP (true by default for compatibility)

== Privacy and GDPR ==

This plugin does not collect or process any personal user data.
