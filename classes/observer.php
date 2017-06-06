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
 * @package    course/format
 * @subpackage ned
 * @copyright  &copy; 2017 G J Barnard.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Event observers supported by this format.
 */
class format_ned_observer {

    /**
     * Observer for the event course_content_deleted.
     *
     * Deletes the user preference entries for the given course upon course deletion.
     * CONTRIB-3520.
     *
     * @param \core\event\course_content_deleted $event
     */
    public static function course_content_deleted(\core\event\course_content_deleted $event) {
        global $DB;
        $DB->delete_records("format_ned_config", array('courseid' => $event->objectid)); // This is the $courseid.
        $DB->delete_records("format_ned_colour", array('courseid' => $event->objectid)); // This is the $courseid.
        $DB->delete_records("format_ned_cm", array('courseid' => $event->objectid)); // This is the $courseid.
    }
}
