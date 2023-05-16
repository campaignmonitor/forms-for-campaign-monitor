=== Campaign Monitor for WordPress ===
Contributors: vibhorchhabra
Donate link: N/A
Tags: Campaign Monitor, Email Marketing, Sign-Up Forms, Sign Up Forms
Requires at least: 3.9
Requires PHP: 5.3
Tested up to: 6.2
Stable tag: 2.8.11
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Make it easy for customers to subscribe to your Campaign Monitor mailing lists using any of the 5 elegant sign-up forms.

== Description ==
Campaign Monitor for Wordpress allows your visitors to sign up to lists in your [campaignmonitor.com](https://www.campaignmonitor.com?utm_source=wordpress-plugin&utm_medium=referral) account, so you can create, send and measure the impact of your email marketing campaigns.
 
With our plugin, you can create and customize subscribe forms from your WordPress dashboard, decide when and where to show them, and A/B test which one attracts more subscribers.
 
* Slide-Out — Position a floating tab at the top, bottom, left or right of the screen. Clicking it will slide out a subscribe form.
* Lightbox — Overlay a subscribe form while dimming the background of the site. You can show it immediately, after a specific amount of time, or after a user has scrolled a specific amount of pixels or percentage of the page.
* Bar — A strap along the top or bottom of a page, that contains all the fields needed to sign up.
* Button — Generate a button shortcode and insert it in a page or post. The form will appear as a Lightbox.
* Embedded — Generate a shortcode and add your signup form in a page or post.

== What is Campaign Monitor? ==
Campaign Monitor makes it radically easy to create, send and measure the impact of your email marketing campaigns. **Don’t have a Campaign Monitor account? [Sign up for free](https://www.campaignmonitor.com/signup?utm_source=wordpress-plugin&utm_medium=referral)**.

== Installation ==
1. Log in to your WordPress Admin and go to the Plugins section.
2. Click “Add New” and search for “Campaign Monitor for WordPress”.
3. From the search results, install and activate our plugin.
4. In the sidebar, click “Subscribe forms” and then “Connect”.
5. Follow the steps and connect it to your Campaign Monitor account.
6. Once connected, you will return to our plugin settings page. We recommend you setting up reCAPTCHA to protect your lists against spambots (abusive computer programs that sign up a large number of real or fake email addresses). Please note that our plugin currently supports reCAPTCHA v2.
 
= Updating from 2.7+ to 2.8 =
PHP 7 introduced connectivity issues between our plugin and Campaign Monitor. Follow these steps to solve this problem:
 
1. Log in to your WordPress Admin and go to the Plugins section.
2. Update our plugin from your list of pending updates.
3. In the sidebar, click “Campaign Monitor / Settings” and then “Disconnect”.
4. **Connect it again to your Campaign Monitor account**.
 
= Updating from 1.x to 2.7+ =
When we launched 2.0, we improved how our plugin saves forms in the WordPress database, but the ability to upgrade the database was last available in 2.6. Follow these steps to update:
 
1. Log in to your WordPress Admin and disable the 1.x version of our plugin.
2. [Download the 2.6.2 version](https://wordpress.org/plugins/forms-for-campaign-monitor/advanced/) to your computer.
3. Go back to the Plugins section of your WordPress Admin, click “Add New” and then “Upload Plugin”. Select the file you downloaded in the previous step, click “Install Now” and activate the plugin.
4. In the sidebar, click “Campaign Monitor” and then follow the instructions to upgrade your database.
5. Only when you complete the previous step, update our plugin to the most recent version from your list of pending updates in the Plugins section.

== Frequently Asked Questions ==

== Screenshots ==
1. Bar forms for the top/bottom of your site.
2. Lightbox can be popped up after a certain amount of time, or once the user has scrolled down a certain percentage of the page.
3. Slide-Out tabs. Users can click on the tab to see the entire form.
4. Embedded forms so users can fill information without any clicks, or disruption.
5. Button forms for one click forms without taking too much space on your site.
6. Compare results from A/B tests.
7. Easy to add a new form. Just select the form type, choose the Campaign Monitor List where  data will be collected, and you are done.

== Changelog ==
= 2.8.11 =

= 2.8.10 =

= 2.8.9 =

= 2.8.8 =
* Minor fixes for Wordpress 5.8.
* Minor fixes in handling some JQuery deprecation

= 2.8.7 =
* Minor fixes for PHP 8.

= 2.8.6 =
* Minor fixes for Wordpress 5.6.

= 2.8.5 =
* Minor fixes for Wordpress 5.5.

= 2.8.4 =
* Fix where form is targetting more than specified page(s).

= 2.8.3 =
* Minor UI fixes for WordPress 5.4.

= 2.8.2 =
* Further improvements to address connectivity issues.
* Minor UI changes for WordPress 5.3.

= 2.8.1 =
* Admin navigation item renamed to "Subscribe forms".
* Minor fixes to A/B tests.
* Improved security.

= 2.8.0 =
* Addressed connection issues with PHP7
* Support for WordPress 5.2.2 
* Minor issue fixes

= 2.7.0 =

* Address issues with serialize/unserialize functions
* Support for php 7.1 and up

= 2.5.8 =

* Address some issues where jquery is not available making it possible to subscribe people without it.

= 2.5.7 =

* Minor maintenance

= 2.5.6 =

* Fixes some jquery conflict.
* You can now see debug information on settings page.


= 2.5.5 =

* Fixes problems where you weren't able to see all the available pages and posts.
* Fixes problem in which jquery was already in include in front end of website.
* Added debug options to help troubleshoot problems.
* Added the ability to reset form font to default.

= 2.5.4 =




= 2.5.3 =

* You can now change the success message of the form
* minor jquery fixes.

= 2.5.2 =

* Fixes jquery conflicts.
* You can now choose from a variety of custom google fonts to use in your forms


= 2.5.1 =

* Fixes some problems with html height.

= 2.4.1 =

* Fixes some conflict with jquery in cases where other plugin authors have errors in their plugins.

= 2.3.1 =
* Fixes some issues with embedded form
* Fixes some conflict with jquery.

= 2.1.1 =
* Fixes some conflict with jquery.

= 2.0.0 =
* Completely customize each form's colors and styling.
* Add captcha to your forms to prevent spam.
* Connect Campaign Monitor and Wordpress using <a href="https://en.wikipedia.org/wiki/OAuth">OAuth</a> for better security.
* Performance improvements.
* Tons of bug fixes.

= 1.5.5 =
* remove library

= 1.5.2 =
* Recoded to provide PHP 5.3 backwards compatibility. Please be advised that every PHP version before 5.4.24 contains security issues.

= 1.0 =
* Launched Campaign Monitor for WordPress plugin.
* Use sign-up forms to capture visitor information and send them beautifully designed, and personalized emails.
* A/B test sign-up forms to see which perform better with your customers.


== Upgrade Notice ==
= 2.0.0 =
* Completely customize each form's colors and styling.
* Add captcha to your forms to prevent spam.
* Connect Campaign Monitor and Wordpress using <a href="https://en.wikipedia.org/wiki/OAuth">OAuth</a> for better security.
* Performance improvements.
* Tons of bug fixes.
= 1.4 =
Fixed some errors, added show campaign monitor badge option.
= 1.2 =
fixed typo on sanitize function
= 1.1 =
includes all files needed
= 1.0 =
Fixes image rendering
