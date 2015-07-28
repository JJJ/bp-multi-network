# BP Multi Network

A helper class for routing multiple BuddyPress networks in a WordPress Multisite environment.

This plugin segregates BuddyPress networks in a WordPress multi-network installation so that each WordPress network has a different BuddyPress social network. The user-base is still shared across the WordPress installation.

(Multiple WordPress networks can be created with the WP Multi Network plugin.)

# Installation

* Download and install into your `mu-plugins` directory.

# FAQ

### Can I have separate domains?

Yes you can, using the WP Multi Network plugin.

### Will this work on standard WordPress?

You need to have multi-site functionality enabled before using this plugin. https://codex.wordpress.org/Create_A_Network

### Where can I get support?

The WordPress support forums: https://wordpress.org/tags/bp-multi-network/

### What's up with uploads?

WP Multi Network needs to be running to set the upload path for new sites. As such, all new networks created with this plugin will have it network activated. If you do disable it on one of your networks, any new site on that network will upload files to that network's root site, effectively causing them to be broken.

(TL;DR - Leave that plugin activated and it will make sure uploads go where they should.)

### Can I contribute?

Please! The number of users needing multiple WordPress and BuddyPress networks is increasing all the time. Having an easy-to-use interface and powerful set of functions is critical to managing complex WordPress installations. If this is your thing, please help us out!
