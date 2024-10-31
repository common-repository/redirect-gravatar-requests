=== Redirect Gravatar requests ===

Contributors:      spartelfant
Donate link:       https://paypal.me/bartkuijper
Tags:              gravatar,avatar,block,disable,redirect,local
Requires at least: 4.6
Tested up to:      6.0
Requires PHP:      5.6.20
Stable tag:        2.0.0
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

All requests to load an avatar from gravatar.com are redirected to a local image, preventing Gravatar from potentially gathering data about your site visitors.

== Description ==

When displaying a post or comment with avatars enabled, WordPress will always check for the existence of a Gravatar. (Note: even the default avatars 'mystery person' and 'blank' are in fact Gravatars served from gravatar.com.) WordPress does this by sending an MD5 hash of every displayed post or comment author's email address to gravatar.com. Even with many plugins that introduce locally stored default or user avatars, this check still happens. On top of that, some themes or plugins force (Gr)avatars to load even if the display of avatars is disabled completely in WordPress' settings. If for whatever reason you do not wish for Gravatar to receive these requests (which some people have voiced GDPR concerns about), this plugin is for you.

The way it works is every time WordPress attempts to display an avatar, this plugin first checks if the image is about to be retrieved from gravatar.com. If it is, the URL is changed to the locally stored 'mystery person' image (included with this plugin). If the avatar has any other source, this plugin doesn't interfere.

Gravatars are also removed from the Discussion page in Settings and replaced with the locally stored 'mystery person' image. Again, any non-Gravatar images are left alone.

Upon activation of this plugin, if a Gravatar is selected as the default avatar, that setting is changed to the locally stored 'mystery person' image. If any non-Gravatar avatar is selected, that setting isn't changed.

Upon deactivation of this plugin, if the locally stored 'mystery person' image is selected as the default avatar, that setting is changed to the Gravatar logo. If any non-Gravatar avatar is selected, that setting isn't changed.

== Installation ==

This plugin can be installed the usual way through WordPress's interface. If you want to manually install this plugin:

1. Upload the plugin files to `/wp-content/plugins/redirect-gravatar-requests/`.
1. Activate the plugin through the 'Plugins' menu in WordPress.

That's it. You can (de)activate the plugin as often as you like. You can remove it completely either by deleting it in WordPress or through FTP. This plugin doesn't modify any files nor does it add anything new to the database, once removed there is no trace of it.

== Frequently Asked Questions ==

= Can I change the local image? =
Not through the WordPress Dashboard. You would have to replace `/wp-content/plugins/redirect-gravatar-requests/mystery.jpg`. You would also have to repeat this after each plugin update.

The reason for this choice is simple: if you enable the display of avatars, but use this plugin to block Gravatars, then you most likely are already using another plugin for custom avatars.

= How to configure this plugin? =
There is nothing to be configured about this plugin. As soon as it's activated, it will redirect all attempts to load a Gravatar from gravatar.com to the locally stored 'mystery person' image included with this plugin.

You can verify that the plugin is working on a page displaying (Gr)avatars in Chrome by opening the developer console (default hotkey F12), selecting the 'Network' tab at the top, typing 'gravatar' in the console's search box and then reloading the page (by pressing CTRL+R). With the plugin deactivated, you will see requests going out to gravatar.com. With the plugin activated, you will see those requests going to this plugin's locally stored image instead.

= Gravatars are still showing when using a particular theme or plugin, why?
This plugin filters the WordPress `get_avatar` function. However some theme and plugin authors use their own code to load (Gr)avatars, in some cases even if the display of avatars is disabled completely in WordPress' settings. In order for this plugin to be able to intercept Gravatars, the offending theme or plugin has to either be making use of the `get_avatar` function or at least apply the `get_avatar` filter in their code.

If you come across such a theme or plugin, feel free to open a [support ticket](https://wordpress.org/support/plugin/redirect-gravatar-requests/) and I'll be happy to see if there's a way to deal with it.

= Is this plugin compatible with plugins that add other avatars? =
It should be, since this plugin specifically targets only Gravatars. If you do run into problems, please let me know.

== Screenshots ==

1. Plugin deactivated, Gravatars are loaded.
2. Plugin activated, Gravatars are redirected to the locally stored image.
3. Plugin deactivated, Gravatars are loaded.
4. Plugin activated, Gravatars are redirected to the locally stored image.

== Changelog ==

= 2.0.0 =
* Changed filter from `get_avatar_url` to `get_avatar` to also intercept Gravatars that were forcefully displayed by an unruly plugin.
* All code refactored.
* Tested on WordPress version 5.4, 5.5, 5.6, 5.7, 5.8, 5.9, and 6.0.

= 1.0.8 =
* Fixed a bug where the plugin would generate an error instead of dying when directly accessed.

= 1.0.7 =
* Tested on WordPress version 5.3.

= 1.0.6 =
* Tested on WordPress version 5.2.
* Increased required PHP version to 5.6.20 in line with the minimum required version for WP 5.2.

= 1.0.5 =
* Added translation to Dutch (nl_NL).
* Fixed untranslatable string.
* Reincluded translation template (`/languages/redirect-gravatar-requests.pot`).
* Some minor touch-ups on the readme.txt.

= 1.0.4 =
* Refactored code to conform to WordPress coding standards.
* Added FAQ link to plugin page.

= 1.0.3 =
* Added support and review links to plugin page.

= 1.0.2 =
* Tested on WordPress version 5.1.
* Increased required PHP version to 5.6 in line with the minimum required version for WP 5.1.

= 1.0.1 =
* Some corrections to the readme.txt to make it more consistent.
* Removed translation template (`/languages/redirect-gravatar-requests.pot`), because it's ignored and a new one is automatically generatad.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 2.0.0 =
Improved Gravatar filtering, updating is recommended.

= 1.0.8 =
Fixed a bug, updating is recommended.

= 1.0.7 =
No functional changes, upgrading to this version is optional.

= 1.0.6 =
No functional changes, upgrading to this version is optional.

= 1.0.5 =
No functional changes, added translation to Dutch (nl_NL), upgrading to this version is optional.

= 1.0.4 =
No functional changes, upgrading to this version is optional.

= 1.0.3 =
No functional changes, upgrading to this version is optional.

= 1.0.2 =
No functional changes, upgrading to this version is optional.

= 1.0.1 =
No functional changes, upgrading to this version is optional.

= 1.0.0 =
Initial release.
