=== Trulia ===
Contributors: katzwebdesign
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=zackkatz%40gmail%2ecom&item_name=Trulia%20Plustin&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: trulia, trulia.com, widget, shortcode,housing, zillow, zip realy, redfin,  real estate, houses, house, TruliaMap
Requires at least: 2.8
Tested up to: 3.5
Stable tag: trunk

Easily add Trulia maps to your sidebar or embed Trulia.com real estate maps in your content.

== Description ==

> <strong>Easily add TruliaMaps to your website!</strong>
> Add a Trulia maps widget showing homes in your area, <em>without touching any HTML or code</em>. This plugin allows you to configure tons of options; check it out today!

###The Trulia real estate plugin includes options for:

* Map size
* Map, Satellite or Hybrid map type
* Slideshow - automatically cycle through houses. Choose how quickly the slideshow displays, or to turn it off.
* Map Background and Text colors

####Easily add a real estate map to your page or post using "shortcodes":

`[trulia city="Bethesda" state="MD"]` will show a map for Bethesda, MD real estate.

`[trulia zip=90210 rotate=10 size=panorama]` will show a wide map of the 90210 real estate for sale, and will rotate showing listings every 10 seconds.

Learn more on the official <strong>[Trulia Plugin](http://www.seodenver.com/trulia/)</strong> page.

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
1. Activate the plugin
1. Add the Trulia map widget by going to the Widgets page (under Appearance > Widgets)
1. Drag the Trulia widget to the sidebar of your choice, and configure.


== Frequently Asked Questions ==

= How do I remove the "Real Estate" link from the map? =
Add the following code to your theme's `functions.php` file:

`add_filter('trulia_link', create_function('$a', 'return false;'));`

= How do I remove the Trulia logo from the map? =

Add the following code to your theme's `functions.php` file:

`add_filter('trulia_logo', create_function('$a', 'return false;'));`


= Do I need a Trulia account for this widget? =
No, this plugin is free. You must agree to the Trulia terms of use, however.

= Is this plugin created by Trulia = 
No, this plugin is not created by or endorsed by Trulia, Inc. Its purpose is to facilitate incorporating features from the <a href="http://www.trulia.com/tools/map/" rel="nofollow">TruliaMap</a> feature into WordPress.

= What is the license for this plugin? = 

This plugin is released under a GPL license, but users of the plugin must agree to the <a href="http://www.trulia.com/terms">Trulia Terms & Conditions</a>.

== Screenshots ==

1. How the widget appears in the Widgets panel 

== Changelog ==

= 1.0.1 = 
*Fixed error calling for `footer` method

= 1.0 =

* Launched Trulia real estate map plugin.

== Upgrade Notice ==

= 1.0.1 = 
*Fixed error calling for `footer` method

= 1.0 =

* Launched Trulia real estate map plugin.
