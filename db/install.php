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

function xmldb_format_ned_install() {
    global $DB;

    $rec = new stdClass();
    $rec->name = 'Embassy Green';
    $rec->framedsectionbgcolour = '9DBB61';
    $rec->framedsectionheadertxtcolour = 'FFFF33';
    $rec->predefined = 1;
    $rec->timecreated = time();
    $rec->timemodified = time();
    $DB->insert_record('format_ned_colour', $rec);

    $rec = new stdClass();
    $rec->name = 'Blues on Whyte';
    $rec->framedsectionbgcolour = 'FFFFFF';
    $rec->framedsectionheadertxtcolour = '7CAAFE';
    $rec->predefined = 1;
    $rec->timecreated = time();
    $rec->timemodified = time();
    $DB->insert_record('format_ned_colour', $rec);

    return true;
}