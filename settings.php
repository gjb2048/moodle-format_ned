<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    format_ned
 * @subpackage NED
 * @copyright  NED {@link http://ned.ca}
 * @author     NED {@link http://ned.ca}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @developer  G J Barnard - {@link http://about.me/gjbarnard} and
 *                           {@link http://moodle.org/user/profile.php?id=442195}
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/format/ned/lib.php'); // For format_ned static constants.

if ($ADMIN->fulltree) {
    global $CFG;
    if (file_exists("{$CFG->dirroot}/course/format/ned/ned_admin_setting_button.php")) {
        require_once($CFG->dirroot . '/course/format/ned/ned_admin_setting_button.php');
    }

    // Header formats.
    $name = 'format_ned/sectionheaderformats';
    $title = get_string('sectionheaderformats', 'format_ned');
    $description = get_string('sectionheaderformats_desc', 'format_ned');
    $settings->add(new ned_admin_setting_button($name, $title, $description, 'nedsitesettingheaderformats'));

    // Colour preset.
    $name = 'format_ned/managecolourpresets';
    $title = get_string('managecolourpresets', 'format_ned');
    $description = get_string('managecolourpresets_desc', 'format_ned');
    $settings->add(new ned_admin_setting_button($name, $title, $description, 'colourpreset'));

    if (file_exists("{$CFG->dirroot}/course/format/ned/ned_admin_setting_configselect.php")) {
        require_once($CFG->dirroot . '/course/format/ned/ned_admin_setting_configselect.php');
    }

    // Other settings.
    $settings->add(new admin_setting_heading('format_net_othersettings',
        get_string('othersettings', 'format_ned'), ''));

    // Activity tracking background.
    $name = 'format_ned/activitytrackingbackground';
    $title = get_string('activitytrackingbackground', 'format_ned');
    $description = get_string('activitytrackingbackground_desc', 'format_ned');
    $default = 1;
    $setting = new ned_admin_setting_configselect($name, $title, $description, $default,
        array(
            0 => get_string('hide'),
            1 => get_string('show')
        )
    );
    $settings->add($setting);
}