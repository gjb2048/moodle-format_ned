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

class ned_admin_setting_headerformats extends admin_setting_configtext {

    /** @var string Error message to show if validation fails */
    public $error;

    /**
     * NED admin setting header formats constructor.
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in
     * config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param int $error Error message to show if validation fails.
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $error) {
        $this->error = $error;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    public function write_setting($data) {
        if (!is_array($data)) {
            return ''; // ignore it
        }

        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }

        //error_log(print_r($data, true));

        // return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
        return true;
    }

    /**
     * Validate data before storage
     * @param string data
     * @return mixed true if ok string if error found
     */
    public function validate($data) {
        //$validated = parent::validate($data); // Pass parent validation first.
        $validated = true;

        return $validated;
    }

    /**
     * Return an XHTML string for the setting
     * @return string Returns an XHTML string
     */
    public function output_html($data, $query='') {
        global $OUTPUT;
error_log('output_html: '.print_r($data, true));
$data['a'] = 'One';
$data['b'] = 'Two';

        $default = $this->get_defaultsetting();
        $context = (object) [
            'size' => $this->size,
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value_a' => $data['a'],
            'value_b' => $data['b'],
            'forceltr' => $this->get_force_ltr(),
        ];
        $element = $OUTPUT->render_from_template('format_ned/admin_setting_headerformats', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }
}
