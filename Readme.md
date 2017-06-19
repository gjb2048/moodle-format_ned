Introduction
============
If you find an issue with the format, please see the 'Reporting Issues' section below.

Required version of Moodle
==========================
This version works with Moodle 3.3 version 2017051500.00 (Build: 20170515) and above within the 3.3 branch until the
next release.

Please ensure that your hardware and software complies with 'Requirements' in 'Installing Moodle' on
'docs.moodle.org/33/en/Installing_Moodle'.

Downloads and documentation
===========================
The primary source for downloading this branch of the format is ****
with 'Select Moodle version:' set at 'Moodle 3.3'.

The secondary source is a tagged version with the v3.3 prefix on https://github.com/fernandooliveira/moodle-format_ned/tags

Documented on ****

Installation
============
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
    format relies on underlying core code that is out of my control.
 2. Put Moodle in 'Maintenance Mode' (docs.moodle.org/en/admin/setting/maintenancemode) so that there are no 
    users using it bar you as the administrator - if you have not already done so.
 3. Copy 'ned' to '/course/format/' if you have not already done so.
 4. Login as an administrator and follow standard the 'plugin' update notification.  If needed, go to
    'Site administration' -> 'Notifications' if this does not happen.
 5. Put Moodle out of Maintenance Mode.

Upgrade Instructions
====================
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
    format relies on underlying core code that is out of my control.
 2. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
 3. In '/course/format/' move old 'ned' directory to a backup folder outside of Moodle.
 4. Follow installation instructions above.
 5. If automatic 'Purge all caches' appears not to work by lack of display etc. then perform a manual 'Purge all caches'
    under 'Home -> Site administration -> Development -> Purge all caches'.
 6. Put Moodle out of Maintenance Mode.

Uninstallation
==============
 1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
 2. It is recommended but not essential to change all of the courses that use the format to another.  If this is
    not done Moodle will pick the last format in your list of formats to use but display in 'Edit settings' of the
    course the first format in the list.  You can then set the desired format.
 3. In '/course/format/' remove the folder 'ned'.
 4. In the database, remove the table 'format_ned_****' along with the entry for 'format_ned'
    ('plugin' attribute) in the table 'config_plugins'.  If using the default prefix this will be
    'mdl_format_ned_****' and 'mdl_config_plugins' respectively.
 5. Put Moodle out of Maintenance Mode.

Reporting Issues
================
Before reporting an issue, please ensure that you are running the latest version for your release of Moodle.  The primary
release area is located on https://moodle.org/plugins/view.php?plugin=format_ned.  It is also essential that you are
operating the required version of Moodle as stated at the top - this is because the format relies on core functionality that
is out of its control.

When reporting an issue you can post in the course format's forum on Moodle.org (currently 'moodle.org/mod/forum/view.php?id=47').

It is essential that you provide as much information as possible, the critical information being the contents of the format's 
version.php file.  Other version information such as specific Moodle version, theme name and version also helps.  A screen shot
can be really useful in visualising the issue along with any files you consider to be relevant.

Version Information
===================
See Changes.md.

Me
==
G J Barnard MSc. BSc(Hons)(Sndw). MBCS. CEng. CITP. PGCE.
Moodle profile: http://moodle.org/user/profile.php?id=442195.
Web profile   : http://about.me/gjbarnard
