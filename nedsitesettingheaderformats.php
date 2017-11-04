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

require_once('../../../config.php');

defined('MOODLE_INTERNAL') || die;

if (!is_siteadmin()) {
    print_error(get_string('adminonly', 'badges'));
    die();
}

$PAGE->set_context(context_system::instance());

require_once($CFG->dirroot.'/course/format/ned/nedsitesettingheaderformats_form.php');
require_once($CFG->dirroot.'/course/format/ned/lib.php');

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/course/format/ned/nedsitesettingheaderformats.php', array());

// First create the form.
$sectionheaderformatsdata = format_ned::get_section_header_formats_setting();
$editform = new course_ned_sitesettingheaderformats_form(null,
    array('sectionheaderformats' => $sectionheaderformatsdata), 'post', '', array('class' => 'ned_settings')
);

if ($editform->is_cancelled()) {
    redirect(new moodle_url('/admin/settings.php?section=formatsettingned'));
} else if ($data = $editform->get_data()) {
    // Remove form data that is not setting data.
    unset($data->submitbutton);
    format_ned::set_section_header_formats_setting($data);
    redirect(new moodle_url('/admin/settings.php?section=formatsettingned'));
}

// Print the form.
$streditsettings = get_string('sectionheaderformats', 'format_ned');

// Breadcrumb.
$PAGE->navbar->add(get_string('pluginname', 'format_ned'));
$PAGE->navbar->add(get_string('settings'));
$PAGE->navbar->add($streditsettings);

$PAGE->set_title($streditsettings);
$PAGE->set_heading($streditsettings);

echo $OUTPUT->header();

$editform->display();
echo $OUTPUT->footer();
