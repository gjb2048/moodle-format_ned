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

class ned_admin_setting_button extends admin_setting { // Like admin_setting_heading in that no actual direct setting.
    protected $buttonfile;

    /**
     * Not a setting, just a button.
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $heading heading.
     * @param string $information text in box.
     * @param string $buttonfile filename without '.php' that the button links to.
     */
    public function __construct($name, $heading, $information, $buttonfile) {
        $this->nosave = true;
        $this->buttonfile = $buttonfile;
        parent::__construct($name, $heading, $information, '');
    }

    /**
     * Always returns true
     * @return bool Always returns true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true
     * @return bool Always returns true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Never write settings
     * @return string Always returns an empty string
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Returns an HTML string
     * @return string Returns an HTML string
     */
    public function output_html($data, $query='') {
        global $OUTPUT, $CFG;

        $context = new stdClass();
        $context->title = $this->visiblename;
        if (file_exists("{$CFG->dirroot}/course/format/ned/{$this->buttonfile}.php")) {
            $context->formlink = new moodle_url('/course/format/ned/'.$this->buttonfile.'.php');
        }
        return $OUTPUT->render_from_template('format_ned/admin_setting_nedbutton', $context);
    }
}
