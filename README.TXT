=== Spotler Mail+ Forms ===
Contributors: spotler
Donate link: https://www.spotler.com/software
Tags: forms, spotler, spotler mail+ forms, mailplus
Requires at least: 3.3
Tested up to: 6.5.2
Requires PHP: 7.2
Stable tag: 1.2.6
License: Modified BSD License

With the Spotler Mail+ Forms Plugin you can easily integrate web forms or surveys created in Spotler Mail+ (www.spotler.com) without technical knowledge.

== Description ==

With the Spotler Mail+ Forms Plugin web masters can easily integrate web forms or online surveys created in [Spotler](http://www.spotler.com) on pages and posts without any technical knowledge. Spotler Mail+ is an online marketing platform which contains a user-friendly form editor with a lot of features. For example, matrix questions, conditional questions, skip logic/branching, multi-paging, extensive features for validating answers from respondents, great e-mail confirmation possibilities and much more.
 
== Installation ==

1. Install the plugin.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enter the Consumer Key and Consumer Secret through the 'Spotler Mail+ Forms' option in the 'Settings' menu in Wordpress.
4. Add a form in a post or a page through the "Add Spotler Mail+ form" button in the HTML editor (TinyMCE)

== Changelog ==

= 1.2.6 =
* Fixed: Security issue in mail-plus forms.
* Updated composer packages.

= 1.2.5 =
* Changed: Naming convention from "Spotler Forms" to "Spotler Mail+ Forms".
* Added: Gutenberg support by adding a Gutenberg block.
* Fixed: Refreshing unique id dynamically now also works for Table forms.

= 1.2.4 =
* Sorting the forms within the TinyMCE element.
* Adding function that retrieves form dynamically.
* Making the abInfo unique for every page view(even when cached).
* Updated composer packages.

= 1.2.3 =
* Updated settings page url in plugin description(plugins page).
* Updated the icon that is used within the toolbar for the TinyMCE editor.

= 1.2.2 =
* Enhanced allowing only certain HTML tags when using echo on an HTML string.
* Updated translation strings.

= 1.2.1 =
* Enhanced escaping in form fields.
* Changed the text domain mentioning from a constant to hardcoded strings.

= 1.2.0 =
* Complete refactor of plugin
* Fix security issues regarding escaping values.
* Changed naming convention from MailPlus Forms to Spotler Forms.
* Added Dutch translations.
* Added exceptions.

= 1.1.0 =
* Fix iconv_set_encoding deprecated warning
* Upgrade TinyMCE plugin to TinyMCE v4. TinyMCE v3 is no longer supported.
* Move all PHP logic the plugin core which fixes the plugin running on Wordpress installations with changed directories (e.d. Roots' Bedrock)

= 1.0.6 =
* Fix warnings while submitting a form

= 1.0.5 =
* Fix for supporting HTTPS completely

= 1.0.4 =
* Support external URL's as last form page. This has been an option in MailPlus for quite a while and was not supported until now.
* Support for prefilling forms inside Wordpress. When linking to a page with a form add "?encId={encId}" to the URL in MailPlus.

= 1.0.3 =
* Unset $_POST['formEncId'] after handling the post to prevent the handling of posted values more than once when multiple forms are used in one page

= 1.0.2 =
* Changed \_\_DIR\_\_ to dirname(\_\_FILE\_\_) so the plugin works on PHP < 5.3.1

= 1.0.1 =
* Plugin directory is "mailplus-forms", not "mailplusforms"

= 1.0 =
* Initial version released

== Upgrade Notice ==

