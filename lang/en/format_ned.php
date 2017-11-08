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

$string['addsections'] = 'Add section';
$string['currentsection'] = 'This section';
$string['editsection'] = 'Edit section';
$string['editsectionname'] = 'Edit section name';
$string['deletesection'] = 'Delete section';
$string['newsectionname'] = 'New name for section {$a}';
$string['sectionname'] = 'Section';
$string['pluginname'] = 'NED Format';
$string['section0name'] = 'General';
$string['page-course-view-ned'] = 'Any course main page in ned format';
$string['page-course-view-ned-x'] = 'Any course page in ned format';
$string['hidefromothers'] = 'Hide section';
$string['showfromothers'] = 'Show section';
$string['addsectionbelow'] = 'Add section below';

// Course Settings form.
$string['sectiondeliverymethod'] = 'Section delivery method';
$string['sectiondeliverymethod_help'] = 'Section delivery method help';
$string['sectiondeliveryoption'] = 'Section (do not advance sections automatically)<br>Default section:';
$string['moodledefaultoption'] = 'Moodle default.';
$string['sectionnotattemptedoption'] = 'Section that contains the earliest "not attempted" activity.';
$string['specifydefaultoption'] = 'Specify default section: ';
$string['scheduledeliveryoption'] = 'Schedule:';
$string['scheduleadvanceoption'] = 'Advance section every';
$string['days'] = 'Day(s)';
$string['weeks'] = 'Week(s)';
$string['sectionscheduleerror'] = 'Please state either section or schedule delivery method.';

// NED Settings form.
$string['format'] = 'Format';
$string['formatinfo'] = 'Format info';

$string['sectionformat'] = 'Section format';
$string['sectionformatmoodle'] = 'Moodle default';
$string['sectionformatframed'] = 'Framed sections';
$string['sectionformatframedcustom'] = 'Framed sections + Custom header';
$string['sectionformatframedformatted'] = 'Framed sections + Formatted header';
$string['sectionnamelocation'] = 'Section name location';
$string['showsectionheader'] = 'Show in section header';
$string['showsectionbody'] = 'Show in section body';
$string['sectionsummarylocation'] = 'Section summary location';

$string['sectionheaderformat'] = 'Header format';
$string['shfldefault'] = 'NED default';
$string['shflname'] = 'Name';
$string['shflnavigationname'] = 'Set section name in Navigation block';
$string['shfllc'] = 'Left column';
$string['shflmc'] = 'Middle column';
$string['shflrc'] = 'Right column';
$string['shfmclt'] = 'Show larger text in middle column?';

$string['colourpreset'] = 'Colour preset';
$string['colourpresetmoodle'] = 'Moodle default';

$string['othersettings'] = 'Other settings';
$string['showsection0'] = 'Show section 0';
$string['showonlysection0'] = 'Show only section 0';
$string['moodleicons'] = 'Moodle icons';
$string['nediconsleft'] = 'NED icons left';
$string['nediconsright'] = 'NED icons right';
$string['sectioncontentjustification'] = 'Center and left jusfify content in sections';
$string['editnedformatsettings'] = 'Edit Ned Format settings';
$string['viewjumptomenu'] = "View 'Jump to...' menu";
$string['viewsectionforwardbacklinks'] = 'View section forward/back links';
$string['courseeditors'] = 'Course editors';
$string['everyone'] = 'Everyone';
$string['nobody'] = 'Nobody';
$string['progresstooltip'] = 'Progress tooltip';
$string['hideicon'] = 'Hide icon';
$string['hidemanualcompletion'] = 'Hide manual completion description';
$string['showalldescriptions'] = 'Show all descriptions';

// NED Site settings.
$string['activityresourcemouseover'] = 'Activity resource mouseover effect';
$string['activityresourcemouseover_desc'] = 'Set the default activity resource mouseover effect';
$string['activitytrackingbackground'] = 'Activity tracking background';
$string['activitytrackingbackground_desc'] = 'Show or hide the activity tracking background.';
$string['defaultcolourpreset'] = 'Default colour preset';
$string['defaultcolourpreset_desc'] = 'Set the default colour preset used when creating a course or a preset gets deleted that is being used by a course.';
$string['defaultsectionformat'] = 'Section format';
$string['defaultsectionformat_desc'] = 'Set the default section format used when creating a course.';
$string['experimentalsettings'] = 'Experimental settings';
$string['locationoftrackingicons'] = 'Location of tracking icons';
$string['locationoftrackingicons_desc'] = 'Set the location of the tracking icons.';
$string['sectionformatframedcustom_desc'] = 'Allow the \'Framed sections + Custom header\' option in the \'Section formats\'.  Note: This may change the value of the \'Section format\' setting above.';
$string['sectionheaderformats'] = 'Header formats';
$string['sectionheaderformats_desc'] = 'Header formats for use when the section format is \'Framed sections + Formatted header\'.';

// Colour presets.
$string['action'] = 'Action';
$string['addedit'] = 'Add/Edit';
$string['appliesto'] = 'Applies to: ';
$string['appliestoone'] = '{$a->one}.';
$string['appliestotwo'] = '{$a->one} and {$a->two}.';
$string['appliestothree'] = '{$a->one}, {$a->two} and {$a->three}.';
$string['close'] = 'Close';
$string['colourpreset'] = 'Colour preset';
$string['colourpresets'] = 'Colour presets';
$string['deletecolourpreset'] = 'Delete this colour preset';
$string['deleteconfirmmsg'] = 'Preset will be deleted permanently. Do you want to continue?';
$string['duplicate'] = 'Duplicate';
$string['duplicatewithbrackets'] = '[duplicate]';
$string['framedsectionbgcolour'] = 'Background colour - A';
$string['framedsectionborderwidth'] = 'Border width - C';
$string['framedsectionheadertxtcolour'] = 'Section header text - B';
$string['managecolourpresets'] = 'Manage colour presets';
$string['managecolourpresets_desc'] = 'Manage the colour presets';
$string['name'] = 'Name';
$string['predefined'] = 'Predefined';
$string['rowcount'] = 'Row';
$string['sortasc'] = 'Sort ascending';
$string['sortdesc'] = 'Sort decending';
$string['successful'] = 'Successful';
$string['timecreated'] = 'Time created';
$string['timemodified'] = 'Time modified';

// Completion icon.
$string['completioniconsnomanual'] = 'Completion tick boxes';
$string['completioniconsnomanual_help'] = 'A tick next to an activity name may be used to indicate when the activity is complete.

If a box with a dotted border is shown, a tick will appear automatically when you have completed the activity according to conditions set by the teacher.';
$string['completion-alt-saved'] = 'Saved: {$a}';
$string['completion-alt-submitted'] = 'Submitted: {$a}';
$string['completion-alt-waitinggrade'] = 'Waiting for grade: {$a}';
