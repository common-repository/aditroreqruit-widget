=== Aditrorecruit Widget ===
Tags: aditroreqruit, aditro, widget, shortcode, rss
Requires at least: 3.2
Tested up to:5.0.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Aditrorecruit job listing widget for WordPress made by Hultsfreds kommun.

== Description ==

This plugin uses Aditro reqruit (aditroreqruit.com) to list available jobs.

Widget and shortcode to show jobs available from the service Aditro recruit.

Very simple shortcode [aditrorecruit] to show all available jobs with description.

All settings are in settings page, check screenshot for details. Some widget specific settings in Widget settings.

Contact Jonas Hjalmarsson, Hultsfreds kommun on Twitter @hjalmarsson for questions. Or find my contact on http://www.hultsfred.se.


== Installation ==

Download to your WordPress-installation or download and unzip manually to the plugins-folder.

Activate in Plugins section.

Drag and drop the widget to your widget-area in the Widgets section, all settings is placed there.

Add the shortcode [aditroreqruit] where you want the full list of jobs with description.

== Screenshots ==

1. Settings view for the widget.
2. Widget and shortcode settings page.
3. Sample of a live widget.

== Changelog ==

= 1.3 =
Force generate cache added to settings page. Tested with Wordpress 4.6.

= 1.2.2 =
Settings page cleanup.

= 1.2.1 =
Cron bugfix.

= 1.2 =
New settings page added to fix that shortcode is not depending of the widget settings from now on. <b>Important: The cron setting is changed and needs to be set and saved again if used</b>.

= 1.1 =
Log added and is checked to work with updated Aditro feed and new wordpress

= 1.0.6 =
ApplicationEndDate bugfix

= 1.0.5 =
Minor bugfix more_link_text

= 1.0.4 =
Added link to "there are num available jobs" if link exist.

= 1.0.3 =
Bugfix, don't show widget if no jobs available.

= 1.0.2 =
Added settings to add show more link. Now also just hides elements if there are more then specified (to be able to make js-feed-rotations)

= 1.0.1 =
Added full description setting for shortcode.

= 1.0 =
Icon removed, now html is allowed in title instead.
Changed description switch to be on or off. (Used to always show description if 2 or less jobs)

= 0.9.10 =
Icon now is html field. Spelling bugfix

= 0.9.9 =
Support for inuit.css icon.

= 0.9.8 =
Job list is now a ul li list instead of divs.

= 0.9.7 =
Bugfix - use before_widget and before_title

= 0.9.6 =
More detail fixes with the date

= 0.9.5 =
span with class time is now wrapping date after title.

= 0.9.4 =
Date added after job title.

= 0.9.3 =
Incorrect widget header was removed in shortcode. Added div with class "entry-content" wrapping the description.

= 0.9.2 =
Simple shortcode functionality added.

= 0.9.1 =
First public version.