=== LH Buddypress Email or Message Group Members ===
Author: shawfactor
Contributors: shawfactor
Donate link: http://lhero.org/portfolio/lh-buddypress-email-or-message-group-members/
Tags: buddypress, groups, email, members, mass
Requires at least: 5.0
Tested up to: 5.7
Requires at least: 5.0
Stable tag: 1.01
License: GPLv2 or later

Allows Buddypress group Admins to send email and/or a private message to all group members .


== Description ==

Allows Buddypress group admins to send email and private messages to all group members from the group admin/manage section.

This plugin sends each message (PM or email) individually to each group member. And supports merge tags to personalise the message for each recipient.

**Like this plugin? Please consider [leaving a 5-star review](https://wordpress.org/support/view/plugin-reviews/lh-buddypress-email-or-message-group-members/).**

**Love this plugin or want to help the LocalHero Project? Please consider [making a donation](https://lhero.org/portfolio/lh-buddypress-email-or-message-group-members/).**

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to configure the plugin


== Frequently Asked Questions ==

= What is is this plugin for? =

To send email to all members of you group.

= How to send mail? =

Just go to group admin->manage ->message members tab. Fill the the in subject and send the email and/or message.

= What if I want additional people to access this functionality? =

By default this "E-Mail Members" section is available to admins.
To change it a filter is available and you can change the access like this:

E.g. to allow this option to mods as well as admins:

`add_filter('lh_beomgm_auhority',function(){
	return 'mod';		
});`

= What merge tags are available? =

The tags are recipient.first_name, recipient.last_name, recipient.display_name, group.name, group.description, and group.url. Used in the same way as buddypress emails. More will be added in future releases.

= What if something does not work?  =

LH Buddypress Email or Message Group Members, and all [https://lhero.org](LocalHero) plugins are made to WordPress standards. Therefore they should work with all well coded plugins and themes. However not all plugins and themes are well coded (and this includes many popular ones). 

If something does not work properly, firstly deactivate ALL other plugins and switch to one of the themes that come with core, e.g. twentyfifteen, twentysixteen etc.

If the problem persists please leave a post in the support forum: [https://wordpress.org/support/plugin/lh-buddypress-email-or-message-group-members/](https://wordpress.org/support/plugin/lh-buddypress-email-or-message-group-members/). I look there regularly and resolve most queries.

= What if I need a feature that is not in the plugin?  =

Please contact me for custom work and enhancements here: [https://shawfactor.com/contact/](https://shawfactor.com/contact/)


== Changelog ==

= 1.00 - June 01, 2021 =
* Initial Release

= 1.01 - June 02, 2021 =
* remove debug code