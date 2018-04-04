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

    if (file_exists("{$CFG->dirroot}/course/format/ned/ned_admin_setting_configselect.php")) {
        require_once($CFG->dirroot . '/course/format/ned/ned_admin_setting_configselect.php');
    }

    // Format settings.
    $settings->add(new admin_setting_heading('format_net_formatsettings',
        get_string('format', 'format_ned'), ''));

    // Default section format.
    $name = 'format_ned/defaultsectionformat';
    $title = get_string('defaultsectionformat', 'format_ned');
    $description = get_string('defaultsectionformat_desc', 'format_ned');
    $default = 1;
    $defaultsectionformatoptions = array(
        0 => get_string('sectionformatmoodle', 'format_ned'),
        1 => get_string('sectionformatframed', 'format_ned'),
        2 => get_string('sectionformatframedcustom', 'format_ned'),
        3 => get_string('sectionformatframedformatted', 'format_ned')
    );
    $setting = new ned_admin_setting_configselect($name, $title, $description, $default, $defaultsectionformatoptions);
    $settings->add($setting);
    $PAGE->requires->js_call_amd('format_ned/nedsitesettingsform', 'init',
        array('data' => array(
            'defaultsectionformatoptionsdata' => $defaultsectionformatoptions,
            'defaultsectionformatdefaultdata' => $default
        ))
    );
    unset($defaultsectionformatoptions);

    // Default colour preset.
    // List of colour presets.
    global $DB;
    $defaultcolourpresetitems = array(0 => get_string('colourpresetformattheme', 'format_ned'));
    if ($presets = $DB->get_records('format_ned_colour', null, null, 'id,name')) {
        foreach ($presets as $preset) {
            $defaultcolourpresetitems[$preset->id] = $preset->name;
        }
    } else {
        $defaultcolourpresetitems[1] = 'Embassy Green';
        $defaultcolourpresetitems[2] = 'Blues on Whyte';
        $defaultcolourpresetitems[3] = 'Grey Skies';
    }
    $name = 'format_ned/defaultcolourpreset';
    $title = get_string('defaultcolourpreset', 'format_ned');
    $description = get_string('defaultcolourpreset_desc', 'format_ned');
    $default = 2;
    $setting = new ned_admin_setting_configselect($name, $title, $description, $default, $defaultcolourpresetitems);
    $settings->add($setting);
    unset($defaultcolourpresetitems);


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

    $settings->add($setting);
    $name = 'format_ned/compressedsections';
    $title = get_string('compressedsections', 'format_ned');
    $description = get_string('compressedsections_desc', 'format_ned');
    $default = 1;
    $setting = new ned_admin_setting_configselect($name, $title, $description, $default,
        array(
            0 => get_string('hide'),
            1 => get_string('show')
        )
    );
    $settings->add($setting);

    $settings->add($setting);
    $name = 'format_ned/compressedmodeview';
    $title = get_string('compressedmodeview', 'format_ned');
    $description = get_string('compressedmodeview_desc', 'format_ned');
    $default = 0;
    $setting = new ned_admin_setting_configselect($name, $title, $description, $default,
        array(
            0 => get_string('hidesummarysection', 'format_ned'),
            1 => get_string('showsummarysection', 'format_ned')
        )
    );
    $settings->add($setting);

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

    // Location of tracking icons.
    $name = 'format_ned/locationoftrackingicons';
    $title = get_string('locationoftrackingicons', 'format_ned');
    $description = get_string('locationoftrackingicons_desc', 'format_ned');
    $default = 'nediconsleft';
    $locationoftrackingiconsoptions = array(
        \format_ned\toolbox::$moodleicons => get_string('moodleicons', 'format_ned'),
        \format_ned\toolbox::$nediconsleft => get_string('nediconsleft', 'format_ned'),
        \format_ned\toolbox::$nediconsright => get_string('nediconsright', 'format_ned')
    );
    $setting = new ned_admin_setting_configselect($name, $title, $description, $default, $locationoftrackingiconsoptions);
    $settings->add($setting);
    unset($locationoftrackingiconsoptions);

    $name = 'format_ned/viewjumptomenu';
    $title = get_string('viewjumptomenu', 'format_ned');
    $description = get_string('viewjumptomenu_desc', 'format_ned');
    $default = 2;
    $setting = new ned_admin_setting_configselect($name, $title, $description, $default,
        array(
            0 => get_string('everyone', 'format_ned'),
            1 => get_string('courseeditors', 'format_ned'),
            2 => get_string('nobody', 'format_ned')
        )
    );
    $settings->add($setting);

    $name = 'format_ned/viewsectionforwardbacklinks';
    $title = get_string('viewsectionforwardbacklinks', 'format_ned');
    $description = get_string('viewsectionforwardbacklinks_desc', 'format_ned');
    $default = 2;
    $setting = new ned_admin_setting_configselect($name, $title, $description, $default,
        array(
            0 => get_string('everyone', 'format_ned'),
            1 => get_string('courseeditors', 'format_ned'),
            2 => get_string('nobody', 'format_ned')
        )
    );
    $settings->add($setting);

    $name = 'format_ned/progresstooltip';
    $title = get_string('progresstooltip', 'format_ned');
    $description = get_string('progresstooltip_desc', 'format_ned');
    $default = 0;
    $setting = new ned_admin_setting_configselect($name, $title, $description, $default,
        array(
            0 => get_string('hideicon', 'format_ned'),
            1 => get_string('hidemanualcompletion', 'format_ned'),
            2 => get_string('showalldescriptions', 'format_ned')
        )
    );
    $settings->add($setting);

    // Experimental settings.
    $settings->add(new admin_setting_heading('format_net_experimentalsettings',
        get_string('experimentalsettings', 'format_ned'), ''));

    $name = 'format_ned/activityresourcemouseover';
    $title = get_string('activityresourcemouseover', 'format_ned');
    $description = get_string('activityresourcemouseover_desc', 'format_ned');
    $default = 0;
    $setting = new ned_admin_setting_configselect($name, $title, $description, $default,
        array(
            0 => get_string('hide'),
            1 => get_string('show')
        )
    );
    $settings->add($setting);

    $name = 'format_ned/framedsectionscustomheader';
    $title = get_string('sectionformatframedcustom', 'format_ned');
    $description = get_string('sectionformatframedcustom_desc', 'format_ned');
    $default = 0;
    $setting = new ned_admin_setting_configselect($name, $title, $description, $default,
        array(
            0 => get_string('hide'),
            1 => get_string('show')
        )
    );
    $settings->add($setting);

    $name = 'format_ned/relocateactivitydescription';
    $title = get_string('relocateactivitydescription', 'format_ned');
    $description = get_string('relocateactivitydescription_desc', 'format_ned');
    $default = 0;
    $setting = new ned_admin_setting_configselect($name, $title, $description, $default,
        array(
            0 => get_string('nochange'),
            1 => get_string('abovetext', 'format_ned'),
            2 => get_string('aboveicon', 'format_ned')
        )
    );
    $settings->add($setting);
}
