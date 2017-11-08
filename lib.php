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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot. '/course/format/lib.php');

class format_ned extends format_base {
    private $settings;  // Course format settings.
    private $sectiondeliverymethoddata;  // JSON decode of 'sectiondeliverymethod' setting.
    private $sectionheaderformatheaders = null; // Array (indexed by section number) of JSON decodes of 'headerformat' section settings.
    private $displaysection = false;
    private $displaysectioncalculated = false;

    /**
     * Creates a new instance of class
     *
     * Please use {@link course_get_format($courseorid)} to get an instance of the format class
     *
     * @param string $format
     * @param int $courseid
     * @return format_ned
     */
    protected function __construct($format, $courseid) {
        if ($courseid === 0) {
            global $COURSE;
            $courseid = $COURSE->id;  // Save lots of global $COURSE as we will never be the site course.
        }
        parent::__construct($format, $courseid);
    }

    /**
     * Returns the format's settings and gets them if they do not exist.
     * @return type The settings as an array.
     */
    public function get_settings() {
        if (empty($this->settings) == true) {
            $this->settings = $this->get_format_options();
            // TODO: Do these (JSON Strings) need to be 'unset' from 'settings' and 'get_setting($name)' updated?
            $this->sectiondeliverymethoddata = json_decode($this->settings['sectiondeliverymethod']);
            if ($this->settings['sectionformat'] == 3) {
                $numsections = $this->get_last_section_number();
                $this->sectionheaderformatheaders = array();
                $section = 1;
                while ($section <= $numsections) {
                    $this->sectionheaderformatheaders[$section] = json_decode($this->get_format_options($section)['headerformat'], true);
                    $section++;
                }
            }
            $this->settings['activitytrackingbackground'] = get_config('format_ned', 'activitytrackingbackground');
            $this->settings['activityresourcemouseover'] = get_config('format_ned', 'activityresourcemouseover');
            $this->settings['locationoftrackingicons'] = get_config('format_ned', 'locationoftrackingicons');
        }
        return $this->settings;
    }

    /**
     * Returns the value of the given setting.
     *
     * @param string $name Name of the setting.
     * @param int $section null or integer section number.
     * @return setting value of any type.
     */
    public function get_setting($name, $section = null) {
        $settings = $this->get_settings();
        if (array_key_exists($name, $settings)) {
            if ($name == 'sectiondeliverymethod') {
                return $this->sectiondeliverymethoddata;
            }
            return $settings[$name];
        } else if ($name == 'sectionheaderformats') { // Needed on the nedsettings_form.php regardless of course section format value.
            return self::get_section_header_formats_setting();
        } else if ($name == 'activitytrackingbackground') {
            return $this->settings['activitytrackingbackground'];
        } else if ($name == 'activityresourcemouseover') {
            return $this->settings['activityresourcemouseover'];
        } else if ($name == 'locationoftrackingicons') {
            return $this->settings['locationoftrackingicons'];
        } else if ($settings['sectionformat'] == 3) {
            if (($name === 'sectionheaderformat') && ($section !== null)) {
                return $this->sectionheaderformatheaders[$section];
            }
        }
        return false;
    }

    public function set_setting($name, $value) {
        $settings = $this->get_settings();
        if (array_key_exists($name, $settings)) {
            $data = new stdClass;
            $data->$name = $value;
            $this->update_course_format_options($data);
            return true;
        }
        // Note 'sectionheaderformats' not set here but by the nedsitesettingheaderformats_form.php indirectly calling 'set_section_header_formats_setting'.
        return false;
    }

    public function get_displaysection() {
        /* This has to be done here instead of format.php because of get_view_url() is used to generate its links
           before format.php is included and 'default section selected' and 'earliest not attempted activity'
           change the way the format displays despite the value of the 'coursedisplay' format setting. */
        if (!$this->displaysectioncalculated) {
            global $PAGE;
            if (!$PAGE->user_is_editing()) {
                $sdmdata = $this->get_setting('sectiondeliverymethod');
                if (!empty($sdmdata)) {
                    // Section delivery method 'section' selected = 1, schedule is 2.
                    if ($sdmdata->sectiondeliverymethod == 1) {
                        $usesectionno = false;
                        $displaysection = optional_param('section', 0, PARAM_INT);
                        // Specify default section selected = 3, Moodle default is 1 and earliest not attempted activity is 2.
                        if ($sdmdata->defaultsection == 3) {
                            if (empty($displaysection)) {
                                $usesectionno = $sdmdata->specifydefaultoptionnumber;
                            } else {
                                $this->displaysection = $displaysection;
                            }
                        } else if ($sdmdata->defaultsection == 2) {
                            if (empty($displaysection)) {
                                $usesectionno = $this->get_earliest_not_attempted_activity();
                            } else {
                                $this->displaysection = $displaysection;
                            }
                        }
                        if (!empty($usesectionno)) {
                            $this->displaysection = $usesectionno;
                        }
                    }
                }
            }
            $this->displaysectioncalculated = true;
        }

        return $this->displaysection;
    }

    /**
     * Returns true if this course format uses sections
     *
     * @return bool
     */
    public function uses_sections() {
        return true;
    }

    /**
     * Returns the display name of the given section that the course prefers.
     *
     * Use section name is specified by user. Otherwise use default ("Section #")
     *
     * @param int|stdClass $section Section object from database or just field section.section
     * @return string Display name that the course format prefers, e.g. "Section 2"
     */
    public function get_section_name($section) {
        $section = $this->get_section($section);

        if (is_object($section)) {
            $sectionnum = $section->section;
        } else {
            $sectionnum = $section;
        }
        if ($sectionnum != 0) {
            $sectionheaderformat = $this->get_setting('sectionheaderformat', $sectionnum);
        } else {
            $sectionheaderformat = false;
        }
        if ($sectionheaderformat) {
            // 0 = Default, 1 = left column, 2 = middle column and 3 = right column.
            if ($sectionheaderformat['navigationname'] > 0) {
                switch($sectionheaderformat['navigationname']) {
                    case 1:
                        $navigationname = $sectionheaderformat['sectionname']['leftcolumn'];
                    break;
                    case 2:
                        $navigationname = $sectionheaderformat['sectionname']['middlecolumn'];
                    break;
                    case 3:
                        $navigationname = $sectionheaderformat['sectionname']['rightcolumn'];
                    break;
                    default:
                        $navigationname = 'Error: Unknown in format_ned/lib.php -> get_section_name()';
                }
                return format_string($navigationname, true,
                    array('context' => context_course::instance($this->courseid)));
            }
        }
        return $this->get_section_name_noshf($section);
    }

    /**
     * Returns the display name of the given section ignoring the 'sectionheaderformat' setting.
     *
     * Use section name is specified by user. Otherwise use default ("Section #")
     *
     * @param int|stdClass $section Section object from database or just field section.section
     * @return string Display name that the course format prefers, e.g. "Section 2"
     */
    public function get_section_name_noshf($section) {
        if (!is_object($section)) {
            $section = $this->get_section($section);
        }

        if ((string)$section->name !== '') {
            return format_string($section->name, true,
                    array('context' => context_course::instance($this->courseid)));
        } else {
            return $this->get_default_section_name($section);
        }
    }

    /**
     * Returns the default section name for the NED course format.
     *
     * If the section number is 0, it will use the string with key = section0name from the course format's lang file.
     * If the section number is not 0, the base implementation of format_base::get_default_section_name which uses
     * the string with the key = 'sectionname' from the course format's lang file + the section number will be used.
     *
     * @param stdClass $section Section object from database or just field course_sections section
     * @return string The default value for the section name.
     */
    public function get_default_section_name($section) {
        if ($section->section == 0) {
            // Return the general section.
            return get_string('section0name', 'format_ned');
        } else {
            $sdmdata = $this->get_setting('sectiondeliverymethod');
            if (!empty($sdmdata)) {
                if ($sdmdata->sectiondeliverymethod == 2) {
                    $dates = $this->get_section_dates($section, false,
                        $sdmdata->scheduleadvanceoptionnumber, $sdmdata->scheduleadvanceoptionunit);

                    // We subtract 24 hours for display purposes.
                    $dates->end = ($dates->end - 86400);

                    $dateformat = get_string('strftimedateshort');
                    $weekday = userdate($dates->start, $dateformat);
                    $endweekday = userdate($dates->end, $dateformat);
                    return $weekday.' - '.$endweekday;
                }
            }
            // Use format_base::get_default_section_name implementation which will display the section name in "Topic n" format.
            return parent::get_default_section_name($section);
        }
    }

    /**
     * Return the start and end date of the passed section
     *
     * @param int|stdClass|section_info $section section to get the dates for
     * @param int $startdate Force course start date, useful when the course is not yet created
     * @param int $scheduleadvanceoptionnumber Interval number of days or weeks given 'scheduleadvanceoptionunit'.
     * @param int $scheduleadvanceoptionunit Days = 1 or Weeks = 2.
     * @return stdClass property start for startdate, property end for enddate
     */
    public function get_section_dates($section, $startdate = false, $scheduleadvanceoptionnumber = false,
        $scheduleadvanceoptionunit = false) {

        if ($startdate === false) {
            $course = $this->get_course();
            $startdate = $course->startdate;
        }

        if (is_object($section)) {
            $sectionnum = $section->section;
        } else {
            $sectionnum = $section;
        }

        if ((!empty($scheduleadvanceoptionnumber)) && (!empty($scheduleadvanceoptionunit))) {
            if ($scheduleadvanceoptionunit == 2) { // Days.
                $durationseconds = 86400 * $scheduleadvanceoptionnumber;  // One day of seconds times the number specified.
            } else if ($scheduleadvanceoptionunit == 1) { // Weeks.
                $durationseconds = 604800 * $scheduleadvanceoptionnumber;  // One week of seconds times the number specified.
            } // else should not happen so leave to code fault if it does.
        } else {
            $durationseconds = 604800;  // One week of seconds.
        }
        // Hack alert. We add 2 hours to avoid possible DST problems. (e.g. we go into daylight
        // savings and the date changes.
        $startdate = $startdate + 7200;

        $dates = new stdClass();
        $dates->start = $startdate + ($durationseconds * ($sectionnum - 1));
        $dates->end = $dates->start + $durationseconds;

        return $dates;
    }

    /**
     * Returns true if the specified week is current
     *
     * @param int|stdClass|section_info $section
     * @return bool
     */
    public function is_section_current($section) {
        if (is_object($section)) {
            $sectionnum = $section->section;
        } else {
            $sectionnum = $section;
        }
        if ($sectionnum < 1) {
            return false;
        }

        $sdmdata = $this->get_setting('sectiondeliverymethod');
        if (!empty($sdmdata)) {
            if ($sdmdata->sectiondeliverymethod == 1) {
                // Section based not day / week based.
                return parent::is_section_current($section);
            }
        }

        $timenow = time();
        $dates = $this->get_section_dates($section);
        return (($timenow >= $dates->start) && ($timenow < $dates->end));
    }

    /**
     * The URL to use for the specified course (with section)
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if omitted the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section has no separate page, the function returns null
     *     'sr' (int) used by multipage formats to specify to which section to return
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
        $course = $this->get_course();
        $url = new moodle_url('/course/view.php', array('id' => $course->id));

        $sr = null;
        if (array_key_exists('sr', $options)) {
            $sr = $options['sr'];
        }
        if (is_object($section)) {
            $sectionno = $section->section;
        } else {
            $sectionno = $section;
        }
        if ($sectionno !== null) {
            $formatdisplaysectionno = $this->get_displaysection();
            if ($sr !== null) {
                if ($sr) {
                    $usercoursedisplay = COURSE_DISPLAY_MULTIPAGE;
                    $sectionno = $sr;
                } else {
                    $usercoursedisplay = COURSE_DISPLAY_SINGLEPAGE;
                }
            } else if ($formatdisplaysectionno) {
                $usercoursedisplay = COURSE_DISPLAY_MULTIPAGE;
            } else {
                $usercoursedisplay = $course->coursedisplay;
            }
            if ($sectionno != 0 && $usercoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $url->param('section', $sectionno);
            } else {
                $url->set_anchor('section-'.$sectionno);
            }
        }
        return $url;
    }

    /**
     * Returns the information about the ajax support in the given source format
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
    }

    /**
     * Returns the section number with the first non-attempted activity.
     *
     * @return bool|int false for none / no completion etc. or the section number
     * of the section containing the first non-attempted activity.
     */
    public function get_earliest_not_attempted_activity() {
        $sectionno = false;
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
        $completioninfo = new completion_info($course);
        foreach ($modinfo->get_section_info_all() as $section) {
            if (empty($modinfo->sections[$section->section])) {
                continue;
            }
            foreach ($modinfo->sections[$section->section] as $cmid) {
                $thismod = $modinfo->cms[$cmid];
                if ($thismod->modname == 'label') {
                    // Labels are special.
                    continue;
                }
                if ($thismod->uservisible) {
                    if ($completioninfo->is_enabled($thismod) != COMPLETION_TRACKING_NONE) {
                        $completiondata = $completioninfo->get_data($thismod, true);
                        if ($completiondata->completionstate == COMPLETION_INCOMPLETE) {
                            // This section is the first.
                            $sectionno = $section->section;
                        }
                    }
                }
                if (!empty($sectionno)) {
                    break;
                }
            }
            if (!empty($sectionno)) {
                break;
            }
        }

        return $sectionno;
    }

    /**
     * Loads all of the course sections into the navigation
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        global $PAGE;
        // If section is specified in course/view.php, make sure it is expanded in navigation.
        if ($navigation->includesectionnum === false) {
            $selectedsection = optional_param('section', null, PARAM_INT);
            if ($selectedsection !== null && (!defined('AJAX_SCRIPT') || AJAX_SCRIPT == '0') &&
                    $PAGE->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {
                $navigation->includesectionnum = $selectedsection;
            }
        }

        // Check if there are callbacks to extend course navigation.
        parent::extend_course_navigation($navigation, $node);

        // We want to remove the general section if it is empty.
        $modinfo = get_fast_modinfo($this->get_course());
        $sections = $modinfo->get_sections();
        if (!isset($sections[0])) {
            // The general section is empty to find the navigation node for it we need to get its ID.
            $section = $modinfo->get_section_info(0);
            $generalsection = $node->get($section->id, navigation_node::TYPE_SECTION);
            if ($generalsection) {
                // We found the node - now remove it.
                $generalsection->remove();
            }
        }
    }

    /**
     * Custom action after section has been moved in AJAX mode
     *
     * Used in course/rest.php
     *
     * @return array This will be passed in ajax respose
     */
    public function ajax_section_move() {
        global $PAGE;
        $titles = array();
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
        $renderer = $this->get_renderer($PAGE);
        if ($renderer && ($sections = $modinfo->get_section_info_all())) {
            foreach ($sections as $number => $section) {
                $titles[$number] = $renderer->section_title($section, $course);
            }
        }
        return array('sectiontitles' => $titles, 'action' => 'move');
    }

    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        return array(
            BLOCK_POS_LEFT => array(),
            BLOCK_POS_RIGHT => array()
        );
    }

    /**
     * Returns the id of the default colour preset.
     *
     * @return int colour preset default id - see nedsettings_form.php.
     */
    private function get_colourpreset_default() {
        return get_config('format_ned', 'defaultcolourpreset');
    }

    /**
     * Definitions of the additional options that this course format uses for section
     *
     * See {@link format_base::course_format_options()} for return array definition.
     *
     * Additionally section format options may have property 'cache' set to true
     * if this option needs to be cached in {@link get_fast_modinfo()}. The 'cache' property
     * is recommended to be set only for fields used in {@link format_base::get_section_name()},
     * {@link format_base::extend_course_navigation()} and {@link format_base::get_view_url()}
     *
     * For better performance cached options are recommended to have 'cachedefault' property
     * Unlike 'default', 'cachedefault' should be static and not access get_config().
     *
     * Regardless of value of 'cache' all options are accessed in the code as
     * $sectioninfo->OPTIONNAME
     * where $sectioninfo is instance of section_info, returned by
     * get_fast_modinfo($course)->get_section_info($sectionnum)
     * or get_fast_modinfo($course)->get_section_info_all()
     *
     * All format options for particular section are returned by calling:
     * $this->get_format_options($section);
     *
     * @param bool $foreditform
     * @return array
     */
    public function section_format_options($foreditform = false) {
        static $sectionformatoptions = false;
        static $headerformatdefault = '{'.
                '"headerformat": 1, '. // 1, 2 or 3 for 'sectionheaderformatone' etc.
                '"navigationname": 0, '. // 0 = Default, 1 = left column, 2 = middle column and 3 = right column.
                '"sectionname": {'.
                    '"leftcolumn": "", '.
                    '"middlecolumn": "", '.
                    '"rightcolumn": ""'.
                '}'.
            '}';

        if ($sectionformatoptions === false) {
            $sectionformatoptions = array(
                'headerformat' => array(
                    'default' => $headerformatdefault, // JSON String for use in array.
                    'type' => PARAM_RAW
                )
            );
        }
        if ($foreditform && !isset($sectionformatoptions['headerformat']['label'])) {
            $sectionformatoptionsedit = array(
                'headerformat' => array(
                    'label' => 'headerformat',
                    'element_type' => 'hidden'
                )
            );
            $sectionformatoptions = array_merge_recursive($sectionformatoptions, $sectionformatoptionsedit);
        }

        if ($this->get_setting('sectionformat') == 3) {
            return $sectionformatoptions;
        } else {
            return array();
        }
    }

    /**
     * Returns the global header section formats setting.
     *
     * If not set in the Moodle installation then set to the default.
     * Static access so can be called from the nedsitesettingheaderformats.php file without needing a course.
     *
     * @return array Multidimensional array represening the header formats.
     */
    public static function get_section_header_formats_setting() {
        static $sectionheaderformatsdefault = '{'.
                '"sectionheaderformatone": {'.
                    '"active": 1, '.
                    '"name": "Lesson", '.
                    '"leftcolumn": '.
                        '{"active": 1, "value": "Lesson number"}, '.
                    '"middlecolumn": '.
                        '{"active": 1, "value": "Title"}, '.
                    '"rightcolumn": '.
                        '{"active": 1, "value": "Time"}, '.
                    '"colourpreset": -1}, '.
                '"sectionheaderformattwo": {'.
                    '"active": 1, '.
                    '"name": "Unit", '.
                    '"leftcolumn": '.
                        '{"active": 0, "value": ""}, '.
                    '"middlecolumn": '.
                        '{"active": 1, "value": "Title"}, '.
                    '"rightcolumn": '.
                        '{"active": 1, "value": "Time"}, '.
                    '"colourpreset": -1},'.
                '"sectionheaderformatthree": {'.
                    '"active": 0, '.
                    '"name": "Other", '.
                    '"leftcolumn": '.
                        '{"active": 0, "value": ""}, '.
                    '"middlecolumn": '.
                        '{"active": 1, "value": "Title"}, '.
                    '"rightcolumn": '.
                        '{"active": 0, "value": ""}, '.
                    '"colourpreset": -1}, '.
                '"shfmclt": 1'.
            '}';

        $sectionheaderformats = get_config('format_ned', 'sectionheaderformats');
        if (!$sectionheaderformats) {
            $sectionheaderformats = $sectionheaderformatsdefault;
            set_config('sectionheaderformats', $sectionheaderformats, 'format_ned');
        }

        return json_decode($sectionheaderformats, true);
    }

    /**
     * Changes the values of the global section header formats setting if they have changed.
     *
     * Static access so can be called from the nedsitesettingheaderformats.php file without needing a course.
     *
     * @param stdClass|array $data array / stdClass containing the multidimensional data to update.
     * @return nothing.
     */
    public static function set_section_header_formats_setting($data) {
        $data = (array)$data;

        // Convert section header formats to JSON for storage.
        $sectionheaderformats = self::get_section_header_formats_setting();
        $shfupdated = false;
        $shfrows = array('sectionheaderformatone', 'sectionheaderformattwo', 'sectionheaderformatthree');
        foreach ($shfrows as $shfrow) {
            // Active.
            if (!empty($data[$shfrow.'active'])) {
                if ($sectionheaderformats[$shfrow]['active'] != 1) {
                    $sectionheaderformats[$shfrow]['active'] = 1;
                    $shfupdated = true;
                }
                unset($data[$shfrow.'active']);
            } else {
                if ($sectionheaderformats[$shfrow]['active'] != 0) {
                    $sectionheaderformats[$shfrow]['active'] = 0;
                    $shfupdated = true;
                }
            }

            // Name.
            if ($data[$shfrow.'name'] !== $sectionheaderformats[$shfrow]['name']) {
                $sectionheaderformats[$shfrow]['name'] = $data[$shfrow.'name'];
                $shfupdated = true;
            }
            unset($data[$shfrow.'name']);

            // Left column active.
            if (!empty($data[$shfrow.'leftcolumnactive'])) {
                if ($sectionheaderformats[$shfrow]['leftcolumn']['active'] != 1) {
                    $sectionheaderformats[$shfrow]['leftcolumn']['active'] = 1;
                    $shfupdated = true;
                }
                unset($data[$shfrow.'leftcolumnactive']);
            } else {
                if ($sectionheaderformats[$shfrow]['leftcolumn']['active'] != 0) {
                    $sectionheaderformats[$shfrow]['leftcolumn']['active'] = 0;
                    $shfupdated = true;
                }
            }

            // Left column value.
            if ($data[$shfrow.'leftcolumnvalue'] !== $sectionheaderformats[$shfrow]['leftcolumn']['value']) {
                $sectionheaderformats[$shfrow]['leftcolumn']['value'] = $data[$shfrow.'leftcolumnvalue'];
                $shfupdated = true;
            }
            unset($data[$shfrow.'leftcolumnvalue']);

            // Middle column active.
            if (!empty($data[$shfrow.'middlecolumnactive'])) {
                if ($sectionheaderformats[$shfrow]['middlecolumn']['active'] != 1) {
                    $sectionheaderformats[$shfrow]['middlecolumn']['active'] = 1;
                    $shfupdated = true;
                }
                unset($data[$shfrow.'middlecolumnactive']);
            } else {
                if ($sectionheaderformats[$shfrow]['middlecolumn']['active'] != 0) {
                    $sectionheaderformats[$shfrow]['middlecolumn']['active'] = 0;
                    $shfupdated = true;
                }
            }

            // Middle column value.
            if ($data[$shfrow.'middlecolumnvalue'] !== $sectionheaderformats[$shfrow]['middlecolumn']['value']) {
                $sectionheaderformats[$shfrow]['middlecolumn']['value'] = $data[$shfrow.'middlecolumnvalue'];
                $shfupdated = true;
            }
            unset($data[$shfrow.'middlecolumnvalue']);

            // Right column active.
            if (!empty($data[$shfrow.'rightcolumnactive'])) {
                if ($sectionheaderformats[$shfrow]['rightcolumn']['active'] != 1) {
                    $sectionheaderformats[$shfrow]['rightcolumn']['active'] = 1;
                    $shfupdated = true;
                }
                unset($data[$shfrow.'rightcolumnactive']);
            } else {
                if ($sectionheaderformats[$shfrow]['rightcolumn']['active'] != 0) {
                    $sectionheaderformats[$shfrow]['rightcolumn']['active'] = 0;
                    $shfupdated = true;
                }
            }

            // Right column value.
            if ($data[$shfrow.'rightcolumnvalue'] !== $sectionheaderformats[$shfrow]['rightcolumn']['value']) {
                $sectionheaderformats[$shfrow]['rightcolumn']['value'] = $data[$shfrow.'rightcolumnvalue'];
                $shfupdated = true;
            }
            unset($data[$shfrow.'rightcolumnvalue']);

            // Colour preset.
            if ($data[$shfrow.'colourpreset'] != $sectionheaderformats[$shfrow]['colourpreset']) {
                $sectionheaderformats[$shfrow]['colourpreset'] = $data[$shfrow.'colourpreset'];
                $shfupdated = true;
            }
            unset($data[$shfrow.'colourpreset']);
        }

        if ($data['shfmclt'] !== $sectionheaderformats['shfmclt']) {
            $sectionheaderformats['shfmclt'] = $data['shfmclt'];
            $shfupdated = true;
        }
        unset($data['shfmclt']);

        if ($shfupdated) {
            // Only update if the data from the course edit form has changed.
            set_config('sectionheaderformats', json_encode($sectionheaderformats), 'format_ned');
        }
    }

    /**
     * Definitions of the additional options that this course format uses for course
     *
     * Topics format uses the following options:
     * - coursedisplay
     * - hiddensections
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        if ($courseformatoptions === false) {
            $courseconfig = get_config('moodlecourse');
            $courseformatoptions = array(
                'hiddensections' => array(
                    'default' => $courseconfig->hiddensections,
                    'type' => PARAM_INT
                ),
                'coursedisplay' => array(
                    'default' => $courseconfig->coursedisplay,
                    'type' => PARAM_INT
                ),
                'sectionformat' => array(
                    'default' => get_config('format_ned', 'defaultsectionformat'),
                    'type' => PARAM_INT
                ),
                'sectionnamelocation' => array(
                    'default' => 1,
                    'type' => PARAM_INT
                ),
                'sectionsummarylocation' => array(
                    'default' => 1,
                    'type' => PARAM_INT
                ),
                'colourpreset' => array(
                    'default' => $this->get_colourpreset_default(),
                    'type' => PARAM_INT
                ),
                'showsection0' => array(
                    'default' => 1,
                    'type' => PARAM_INT
                ),
                'sectioncontentjustification' => array(
                    'default' => 0,
                    'type' => PARAM_INT
                ),
                'progresstooltip' => array(
                    'default' => 0,
                    'type' => PARAM_INT
                ),
                'sectiondeliverymethod' => array(
                    'default' => '{"sectiondeliverymethod": 1, "defaultsection": 1}', // JSON String for use in array.
                    'type' => PARAM_RAW
                )
            );
        }
        if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {
            $courseformatoptionsedit = array(
                'hiddensections' => array(
                    'label' => new lang_string('hiddensections'),
                    'help' => 'hiddensections',
                    'help_component' => 'moodle',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            0 => new lang_string('hiddensectionscollapsed'),
                            1 => new lang_string('hiddensectionsinvisible')
                        )
                    ),
                ),
                'coursedisplay' => array(
                    'label' => new lang_string('coursedisplay'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single'),
                            COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi')
                        )
                    ),
                    'help' => 'coursedisplay',
                    'help_component' => 'moodle',
                )
            );
            // Storage for settings defined on 'ned_settings_form.php' and instantiated with defaults above in 'nedsettings.php'.
            $courseformatoptionsedit['sectionformat'] = array(
                'label' => 'sectionformat', 'element_type' => 'hidden');
            $courseformatoptionsedit['sectionnamelocation'] = array(
                'label' => 'sectionnamelocation', 'element_type' => 'hidden');
            $courseformatoptionsedit['sectionsummarylocation'] = array(
                'label' => 'sectionsummarylocation', 'element_type' => 'hidden');
            $courseformatoptionsedit['colourpreset'] = array(
                'label' => 'colourpreset', 'element_type' => 'hidden');
            $courseformatoptionsedit['showsection0'] = array(
                'label' => 'showsection0', 'element_type' => 'hidden');
            $courseformatoptionsedit['sectioncontentjustification'] = array(
                'label' => 'sectioncontentjustification', 'element_type' => 'hidden');
            $courseformatoptionsedit['progresstooltip'] = array(
                'label' => 'progresstooltip', 'element_type' => 'hidden');
            $courseformatoptionsedit['sectiondeliverymethod'] = array(
                 // Storage for complex element in 'create_edit_form_elements()'.  This is in the course not ned settings.
                'label' => 'sectiondeliverymethod', 'element_type' => 'hidden');
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Adds format options elements to the course/section edit form.
     *
     * This function is called from {@link course_edit_form::definition_after_data()}.
     *
     * @param MoodleQuickForm $mform form the elements are added to.
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form.
     * @return array array of references to the added form elements.
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        global $COURSE;
        $elements = parent::create_edit_form_elements($mform, $forsection);

        if (!$forsection && (empty($COURSE->id) || $COURSE->id == SITEID)) {
            /* Add "numsections" element to the create course form - it will force new course to be prepopulated
               with empty sections.
               The "Number of sections" option is no longer available when editing course, instead teachers should
               delete and add sections when needed. */
            $courseconfig = get_config('moodlecourse');
            $max = (int)$courseconfig->maxsections;
            $element = $mform->addElement('select', 'numsections', get_string('numberweeks'), range(0, $max ?: 52));
            $mform->setType('numsections', PARAM_INT);
            if (is_null($mform->getElementValue('numsections'))) {
                $mform->setDefault('numsections', $courseconfig->numsections);
            }
            array_unshift($elements, $element);
        }

        if (!$forsection) {
            $sectiondeliverymethodgroupdata = $this->get_setting('sectiondeliverymethod');
            $sectiondeliverymethodgroup = array();
            $sectiondeliverymethodgroup[] =& $mform->createElement('checkbox', 'sectiondeliveryoption', null,
                get_string('sectiondeliveryoption', 'format_ned'));

            $sectiondeliverymethodgroup[] =& $mform->createElement('radio', 'sectiondeliveryoptions', null,
                get_string('moodledefaultoption', 'format_ned'), 1);
            $sectiondeliverymethodgroup[] =& $mform->createElement('radio', 'sectiondeliveryoptions', null,
                get_string('sectionnotattemptedoption', 'format_ned'), 2);
            $sectiondeliverymethodgroup[] =& $mform->createElement('radio', 'sectiondeliveryoptions', null,
                get_string('specifydefaultoption', 'format_ned'), 3);
            $sections = array();
            $totalsections = $this->get_last_section_number();
            for ($sectionnum = 1; $sectionnum <= $totalsections; $sectionnum++) {
                $sections[$sectionnum] = ''.$sectionnum;
            }
            $specifydefaultoptionnumber =& $mform->createElement('select', 'specifydefaultoptionnumber', null, $sections,
                array('class' => 'specifydefaultoptionnumber'));
            if (!empty($sectiondeliverymethodgroupdata->specifydefaultoptionnumber)) {
                $specifydefaultoptionnumber->setSelected($sectiondeliverymethodgroupdata->specifydefaultoptionnumber);
            }
            $sectiondeliverymethodgroup[] = $specifydefaultoptionnumber;
            if (!empty($sectiondeliverymethodgroupdata->defaultsection)) {
                $mform->setDefault('sectiondeliveryoptions', $sectiondeliverymethodgroupdata->defaultsection);
            } else {
                $mform->setDefault('sectiondeliveryoptions', 1);
            }

            $sectiondeliverymethodgroup[] =& $mform->createElement('checkbox', 'scheduledeliveryoption', null,
                get_string('scheduledeliveryoption', 'format_ned'));

            $scheduleadvanceoptionnumbers = array();
            for ($opnum = 1; $opnum <= 60; $opnum++) {
                $scheduleadvanceoptionnumbers[$opnum] = ''.$opnum;
            }
            $scheduleadvanceoptionnumber =& $mform->createElement('select', 'scheduleadvanceoptionnumber',
                get_string('scheduleadvanceoption', 'format_ned'), $scheduleadvanceoptionnumbers,
                array('class' => 'scheduleadvanceoptionnumber'));
            if (!empty($sectiondeliverymethodgroupdata->scheduleadvanceoptionnumber)) {
                $scheduleadvanceoptionnumber->setSelected($sectiondeliverymethodgroupdata->scheduleadvanceoptionnumber);
            }
            $sectiondeliverymethodgroup[] = $scheduleadvanceoptionnumber;

            $sectiondeliverymethodgroup[] =& $mform->createElement('select', 'scheduleadvanceoptionunit', '',
                array(1 => get_string('weeks', 'format_ned'), 2 => get_string('days', 'format_ned')));
            if (!empty($sectiondeliverymethodgroupdata->scheduleadvanceoptionunit)) {
                $mform->setDefault('scheduleadvanceoptionunit', $sectiondeliverymethodgroupdata->scheduleadvanceoptionunit);
            }

            if (!empty($sectiondeliverymethodgroupdata->specifydefaultoptionnumber)) {
                if ($sectiondeliverymethodgroupdata->sectiondeliverymethod == 1) {
                    $mform->setDefault('sectiondeliveryoption', 'checked');
                } else if ($sectiondeliverymethodgroupdata->sectiondeliverymethod == 2) {
                    $mform->setDefault('scheduledeliveryoption', 'checked');
                } else {
                    $mform->setDefault('sectiondeliveryoption', 'checked');
                }
            } else {
                $mform->setDefault('sectiondeliveryoption', 'checked');
            }

            $mform->disabledIf('sectiondeliveryoption', 'scheduledeliveryoption', 'checked');
            $mform->disabledIf('scheduledeliveryoption', 'sectiondeliveryoption', 'checked');
            $mform->disabledIf('sectiondeliveryoptions', 'sectiondeliveryoption', 'notchecked');
            $mform->disabledIf('specifydefaultoptionnumber', 'sectiondeliveryoptions', 'neq', 3);
            $mform->disabledIf('specifydefaultoptionnumber', 'sectiondeliveryoption', 'notchecked');
            $mform->disabledIf('scheduleadvanceoptionnumber', 'scheduledeliveryoption', 'notchecked');
            $mform->disabledIf('scheduleadvanceoptionunit', 'scheduledeliveryoption', 'notchecked');

            $elements[] = $mform->addGroup($sectiondeliverymethodgroup, 'sectiondeliverymethodgroup',
                get_string('sectiondeliverymethod', 'format_ned'), array('<br class="nedsep" />'), false);
            $mform->addHelpButton('sectiondeliverymethodgroup', 'sectiondeliverymethod', 'format_ned');
        }

        return $elements;
    }

    /**
     * Resets the colour preset for the course.
     */
    public function reset_colourpreset() {
        $data = array('colourpreset' => $this->get_colourpreset_default());
        $this->update_course_format_options($data);
    }

    /**
     * Updates format options for a course
     *
     * In case if course format was changed to 'topics', we try to copy options
     * 'coursedisplay' and 'hiddensections' from the previous format.
     *
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data
     * @param stdClass $oldcourse if this function is called from {@link update_course()}
     *     this object contains information about the course before update
     * @return bool whether there were any changes to the options values
     */
    public function update_course_format_options($data, $oldcourse = null) {
        $data = (array)$data;

        // Convert section delivery method to JSON for storage.
        $sectiondeliverymethod = array();
        if (!empty($data['sectiondeliveryoption'])) {
            $sectiondeliverymethod['sectiondeliverymethod'] = 1;
            unset($data['sectiondeliveryoption']);
        } else if (!empty($data['scheduledeliveryoption'])) {
            $sectiondeliverymethod['sectiondeliverymethod'] = 2;
            unset($data['scheduledeliveryoption']);
        }
        if (!empty($data['sectiondeliveryoptions'])) {
            $sectiondeliverymethod['defaultsection'] = $data['sectiondeliveryoptions'];
            unset($data['sectiondeliveryoptions']);
        }
        if (!empty($data['specifydefaultoptionnumber'])) {
            $sectiondeliverymethod['specifydefaultoptionnumber'] = $data['specifydefaultoptionnumber'];
            unset($data['specifydefaultoptionnumber']);
        }
        if (!empty($data['scheduleadvanceoptionnumber'])) {
            $sectiondeliverymethod['scheduleadvanceoptionnumber'] = $data['scheduleadvanceoptionnumber'];
            unset($data['scheduleadvanceoptionnumber']);
        }
        if (!empty($data['scheduleadvanceoptionunit'])) {
            $sectiondeliverymethod['scheduleadvanceoptionunit'] = $data['scheduleadvanceoptionunit'];
            unset($data['scheduleadvanceoptionunit']);
        }
        if (!empty($sectiondeliverymethod)) {
            // Only update if we have been parsed a set of data to update by the course edit form.
            $data['sectiondeliverymethod'] = json_encode($sectiondeliverymethod);
        }

        if ($oldcourse !== null) {
            $oldcourse = (array)$oldcourse;
            $options = $this->course_format_options();
            foreach ($options as $key => $unused) {
                if (!array_key_exists($key, $data)) {
                    if (array_key_exists($key, $oldcourse)) {
                        $data[$key] = $oldcourse[$key];
                    }
                }
            }
        }
        return $this->update_format_options($data);
    }

    /**
     * Updates format options for a course or section
     *
     * If $data does not contain property with the option name, the option will not be updated
     *
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data
     * @param null|int null if these are options for course or section id (course_sections.id)
     *     if these are options for section
     * @return bool whether there were any changes to the options values
     */
    protected function update_format_options($data, $sectionid = null) {
        $changed = parent::update_format_options($data, $sectionid);
        if ($changed) {
            // Settings have changed so clear our member attribute.
            unset($this->settings);
        }
    }

    /**
     * Updates format options for a section.
     *
     * Section id is expected in $data->id (or $data['id']).  Not the same as section number,
     * this is in $data['sectionno'] - an addition to nededitsection_form.php.
     * If $data does not contain property with the option name, the option will not be updated.
     *
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data.
     * @return bool whether there were any changes to the options values.
     */
    public function update_section_format_options($data) {
        $data = (array)$data;

        // Convert form data into section format option setting.
        // If no 'sectionno' then not our custom 'nededitsection_form' + not used on section 0.
        if (($this->get_setting('sectionformat') == 3) && (!empty($data['sectionno']))) {
            $changeddata = false;
            if ($this->sectionheaderformatheaders[$data['sectionno']]['headerformat'] != $data['sectionheaderformat']) {
                $this->sectionheaderformatheaders[$data['sectionno']]['headerformat'] = $data['sectionheaderformat'];
                $changeddata = true;
            }
            unset($data['sectionheaderformat']);

            if ($data['navigationname'] != $this->sectionheaderformatheaders[$data['sectionno']]['navigationname']) {
                $this->sectionheaderformatheaders[$data['sectionno']]['navigationname'] = $data['navigationname'];
                $changeddata = true;
            }
            unset($data['navigationname']);

            if (!empty($data['shfcleftcolumn'])) { // Tick is ticked.
                if ($this->sectionheaderformatheaders[$data['sectionno']]['sectionname']['leftcolumn'] !== $data['shfvleftcolumn']) {
                    $this->sectionheaderformatheaders[$data['sectionno']]['sectionname']['leftcolumn'] = $data['shfvleftcolumn'];
                    $changeddata = true;
                }
                unset($data['shfcleftcolumn']);
            }
            unset($data['shfvleftcolumn']);

            if (!empty($data['shfcmiddlecolumn'])) { // Tick is ticked.
                if ($this->sectionheaderformatheaders[$data['sectionno']]['sectionname']['middlecolumn'] !== $data['shfvmiddlecolumn']) {
                    $this->sectionheaderformatheaders[$data['sectionno']]['sectionname']['middlecolumn'] = $data['shfvmiddlecolumn'];
                    $changeddata = true;
                }
                unset($data['shfcmiddlecolumn']);
            }
            unset($data['shfvmiddlecolumn']);

            if (!empty($data['shfcrightcolumn'])) { // Tick is ticked.
                if ($this->sectionheaderformatheaders[$data['sectionno']]['sectionname']['rightcolumn'] !== $data['shfvrightcolumn']) {
                    $this->sectionheaderformatheaders[$data['sectionno']]['sectionname']['rightcolumn'] = $data['shfvrightcolumn'];
                    $changeddata = true;
                }
                unset($data['shfcrightcolumn']);
            }
            unset($data['shfvrightcolumn']);

            if ($changeddata) {
                $data['headerformat'] = json_encode($this->sectionheaderformatheaders[$data['sectionno']]);
            } else {
                unset($data['headerformat']);
            }
        }

        return $this->update_format_options($data, $data['id']);
    }

    /**
     * Override if you need to perform some extra validation of the format options
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @param array $errors errors already discovered in edit form validation
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     *         Do not repeat errors from $errors param here
     */
    public function edit_form_validation($data, $files, $errors) {
        $retr = array();

        if ((empty($data['sectiondeliveryoption'])) && (empty($data['scheduledeliveryoption']))) {
            $retr['sectiondeliverymethodgroup'] = get_string('sectionscheduleerror', 'format_ned');
        }
        return $retr;
    }

    /**
     * Return an instance of moodleform to edit a specified section
     *
     * Default implementation returns instance of editsection_form that automatically adds
     * additional fields defined in {@link format_base::section_format_options()}
     *
     * This is so we can override the layout of the edit section page and have the extra
     * section header format elements.
     *
     * @param mixed $action the action attribute for the form. If empty defaults to auto detect the
     *              current url. If a moodle_url object then outputs params as hidden variables.
     * @param array $customdata the array with custom data to be passed to the form
     *     /course/editsection.php passes section_info object in 'cs' field
     *     for filling availability fields
     * @return moodleform
     */
    public function editsection_form($action, $customdata = array()) {
        // Framed sections with Formatted headers and not section 0.
        if (($this->get_setting('sectionformat') == 3) && ($customdata['cs']->section != 0)) {
            global $CFG;
            require_once($CFG->dirroot. '/course/format/ned/nededitsection_form.php');
            $context = context_course::instance($this->courseid);
            if (!array_key_exists('course', $customdata)) {
                $customdata['course'] = $this->get_course();
            }
            return new nededitsection_form($action, $customdata);
        } else {
            return parent::editsection_form($action, $customdata);
        }
    }

    /**
     * Whether this format allows to delete sections
     *
     * Do not call this function directly, instead use {@link course_can_delete_section()}
     *
     * @param int|stdClass|section_info $section
     * @return bool
     */
    public function can_delete_section($section) {
        return true;
    }

    /**
     * Prepares the templateable object to display section name
     *
     * @param \section_info|\stdClass $section
     * @param bool $linkifneeded
     * @param bool $editable
     * @param null|lang_string|string $edithint
     * @param null|lang_string|string $editlabel
     * @return \core\output\inplace_editable
     */
    public function inplace_editable_render_section_name($section, $linkifneeded = true,
                                                         $editable = null, $edithint = null, $editlabel = null) {
        if (empty($edithint)) {
            $edithint = new lang_string('editsectionname', 'format_ned');
        }
        if (empty($editlabel)) {
            $title = get_section_name($section->course, $section);
            $editlabel = new lang_string('newsectionname', 'format_ned', $title);
        }
        return parent::inplace_editable_render_section_name($section, $linkifneeded, $editable, $edithint, $editlabel);
    }

    /**
     * Indicates whether the course format supports the creation of a news forum.
     *
     * @return bool
     */
    public function supports_news() {
        return true;
    }

    /**
     * Returns whether this course format allows the activity to
     * have "triple visibility state" - visible always, hidden on course page but available, hidden.
     *
     * @param stdClass|cm_info $cm course module (may be null if we are displaying a form for adding a module)
     * @param stdClass|section_info $section section where this module is located or will be added to
     * @return bool
     */
    public function allow_stealth_module_visibility($cm, $section) {
        // Allow the third visibility state inside visible sections or in section 0.
        return !$section->section || $section->visible;
    }

    public function section_action($section, $action, $sr) {
        global $PAGE;

        if ($section->section && ($action === 'setmarker' || $action === 'removemarker')) {
            // Format 'ned' allows to set and remove markers in addition to common section actions.
            require_capability('moodle/course:setcurrentsection', context_course::instance($this->courseid));
            course_set_marker($this->courseid, ($action === 'setmarker') ? $section->section : 0);
            return null;
        }

        // For show/hide actions call the parent method and return the new content for .section_availability element.
        $rv = parent::section_action($section, $action, $sr);
        $renderer = $PAGE->get_renderer('format_ned');
        $rv['section_availability'] = $renderer->section_availability($this->get_section($section));
        return $rv;
    }
}

/**
 * Implements callback inplace_editable() allowing to edit values in-place
 *
 * @param string $itemtype
 * @param int $itemid
 * @param mixed $newvalue
 * @return \core\output\inplace_editable
 */
function format_ned_inplace_editable($itemtype, $itemid, $newvalue) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
        $section = $DB->get_record_sql(
            'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
            array($itemid, 'ned'), MUST_EXIST);
        return course_get_format($section->course)->inplace_editable_update_section_name($section, $itemtype, $newvalue);
    }
}

function format_ned_extend_navigation_course(navigation_node $parentnode, stdClass $course, context_course $context) {
    if (($course->format == 'ned') && (has_capability('moodle/course:update', $context))) {
        $node = navigation_node::create(get_string('editnedformatsettings', 'format_ned'),
            new moodle_url('/course/format/ned/nedsettings.php', array('id' => $course->id)),
            navigation_node::TYPE_SETTING, null, null, new pix_icon('ned_icon',
            get_string('editnedformatsettings', 'format_ned'), 'format_ned'));
        $node->showinflatnavigation = true;
        $parentnode->add_node($node);
    }
}
