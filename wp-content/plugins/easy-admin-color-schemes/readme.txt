=== Easy Admin Color Schemes ===
Contributors: Jick
Donate link: http://jamesdimick.com/support-my-work
Tags: schemes, color schemes, admin color schemes, colors, admin, administration, easy, simple, control, custom, css, design, designs, edit, interface, panel, preview, profile, style, styles
Requires at least: 3.6
Tested up to: 3.6
Stable tag: 4.2

The Easy Admin Color Schemes plugin allows users to easily customize the colors of the administration interface for WordPress.

== Description ==

The Easy Admin Color Schemes plugin allows users to easily customize the colors of the administration interface for WordPress. It works by adding a new interface to the WordPress admin that allows you to add, edit, import, and export custom admin color schemes. Without the plugin you would need to know how to create your own WordPress plugins or customize WordPress code in order to add your own custom admin color schemes.

Get more information and support at [the official Easy Admin Color Schemes page](http://jamesdimick.com/easy-admin-color-schemes).

== Installation ==

Either [install the plugin automatically through WordPress](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins) or, follow these steps:

1. Extract all files from the ZIP archive, making sure to keep the file structure intact.
2. Upload the `easy-admin-color-schemes` folder to the WordPress plugins directory.
3. Activate the plugin through the `Plugins` menu in WordPress.
4. Have fun creating new color schemes!

== Frequently Asked Questions ==

= I get "____" error =

Please report any errors you get to me. You can contact me [through my website](http://jamesdimick.com).

= This plugin rocks! Can I donate to you? =

Well, I'm glad you like it. I do appreciate all contributions. Please [see here](http://jamesdimick.com/support-my-work).

= What if I have questions that are not covered here? =

The best way to get in contact with me is [through my website](http://jamesdimick.com). Please contact me any time if you have questions, comments, etc.

== Screenshots ==

1. The All Admin Color Schemes page
2. The Add/Edit Admin Color Scheme page
3. The Import Admin Color Scheme page
4. The Export Admin Color Scheme dialog
5. The Admin Color Schemes Settings page

== Changelog ==

= 4.2 =
* Many bug fixes and better compatibility with newer versions of WordPress.
* Updated all default color schemes that come packaged with the plugin. (You will need to delete all old versions of the default schemes and then deactivate and reactivate the plugin in order to get the new versions)
* Added a new "Orange" default color scheme.
* Added minified versions of all admin scripts and styles used by the plugin to improve speed. Can be set to use non-minified versions by changing the `EACS_DEBUG` flag in the main plugin file.

= 4.1 =
* Went back to prefixed function names instead of PHP 5.3 namespaces because of many complaints. Maybe we can try again some time in the future.

= 4.0 =
* Version 4.0 is a complete rewrite from the ground up. Everything has been restructured and improved.
* Schemes have been moved from the file system to the database to prevent various file permissions issues the old versions suffered from.
* Simplified a lot of the code thanks to using a custom post type in the background.
* Old scheme exports are not compatible with the new version. However, a simple copy-paste procedure from the old scheme file into the Add Scheme page should work fine.

= 3.2 =
* Fixed (hopefully) the image display problems in the lower preview pane when adding/editing a color scheme
* Fixed a bug in the last modified times function
* Replaced some of the old JavaScript with jQuery code of similar functionality
* Added a new Preview function which opens a preview of the selected color scheme in a lightbox
* Added some more in-depth permissions functionality
* Added the ability for the primary colors to actually affect the color scheme
* Added the ability to set a default color scheme which will affect all new users
* Added the ability to force a certain color scheme on all users regardless of what they choose
* Updated the included readme, screenshot, and .POT file to include the new changes

= 3.1 =
* Fixed an issue with the last modified times
* Added Russian translation thanks to fatcow (http://www.fatcow.com/)

= 3.0 =
* Updated the plugin interface to fit in better with the new WordPress 2.7 look and be more intuitive
* Added collapsible sections to help with overall plugin ease of use
* Added a toggle button for the live preview section
* Removed the Washedout color scheme because it is obsolete with the new default gray color scheme of 2.7
* Removed the For the Love of 2.3 scheme. It is too difficult to maintain with the constant interface changes in WordPress
* Added two new color schemes called Red and Green which are variations of the default scheme of 2.7
* Now allowing more special characters in the color scheme names and also scheme names up to 200 characters
* Also now allowing editing of color scheme names
* Added a Copy action which will copy the selected color scheme into the Create a Color Scheme form
* Can now export the default WordPress color schemes as well
* Now using a new and improved color picker
* Completely removed previously commented-out code to save on the overall file size
* Fixed a few small bugs here and there
* Updated the included readme, screenshot, and .POT file to include the new changes

= 2.7 =
* Fixed (really this time) the issue with exporting color schemes with Method 2 of the export functionality

= 2.6 =
* Fixed an issue with exporting color schemes with Method 2 of the export functionality
* Fixed a few minor issues with the localization parts of the plugin
* Also added a small bit to the Right Now section on the dashboard

= 2.5 =
* Fixed some issues caused by WordPress 2.6 including an issue with the For the Love of 2.3 scheme
* Also updated the Washedout color scheme a bit

= 2.4 =
* Fixed (hopefully) the issue with the last modified dates

= 2.3 =
* Fixed an issue with the new For the Love of 2.3 color scheme
* Moved the update preview button to just above the preview window
* Added a note just below the primary colors area in an attempt to relieve some confusion many have been having
* Updated the included screenshot to include the new changes

= 2.2 =
* Removed the link on the user profile page until it can be done more reliably
* Also added a new scheme called For the Love of 2.3 which attempts to bring back some of the old WordPress 2.3 styles

= 2.1 =
* Fixed the major issue some people were having with setting their current scheme from the plugin page
* As a positive side-effect, setting the scheme from the built-in scheme picker on the profile page now works correctly

= 2.0 =
* Added a new export feature which allows users to export color schemes in a couple different formats
* Also added an upload feature so color schemes that have been exported can be imported back in
* Fixed some issues with setting the current scheme from the plugin page
* Fixed a few spelling errors in various parts of the plugin
* Fixed some problems with the JavaScript
* Fixed a few issues with localization
* Improved the error reporting functions

= 1.8 =
* Corrected some things to allow for better localization

= 1.7 =
* Fixed the way the new link on the profile page works so only users with proper permissions can see it

= 1.6 =
* Added a link to the user profile page right by the Admin Color Scheme selector which links to the plugin

= 1.5 =
* Added error codes to aid in debugging and fixed some image issues with the new Washedout color scheme

= 1.4 =
* Added a better-looking default color scheme called Washedout

= 1.3 =
* Fixed an issue with slashes in the CSS content when you save a scheme

= 1.2 =
* Changed the way Last Modified dates are handled so the plugin still works instead of erroring out

= 1.1 =
* Changed some URL query variable names in an attempt to prevent clashing with other plugins

= 1.0 =
* The first version

== Upgrade Notice ==

= 4.1 =
* Version 4.1 is now compatible with older versions of PHP. It still requires WordPress 3.3+ however.

= 4.0 =
* Easy Admin Color Schemes 4.0+ is a complete rewrite of the plugin. Pretty much everything is improved. Everyone should definitely upgrade.
