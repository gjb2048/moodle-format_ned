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

$PAGE->set_pagelayout('admin');

// Paging options.
$page      = optional_param('page', 0, PARAM_INT);
$perpage   = optional_param('perpage', 20, PARAM_INT);
$sort      = optional_param('sort', 'name', PARAM_ALPHANUM);
$dir       = optional_param('dir', 'ASC', PARAM_ALPHA);
// Action.
$action    = optional_param('action', false, PARAM_ALPHA);
$search    = optional_param('search', '', PARAM_TEXT);

$thispageurl = new moodle_url('/course/format/ned/colourpreset.php');

$PAGE->set_url($thispageurl);
$PAGE->set_pagelayout('admin');

$name = get_string('colourpresets', 'format_ned');
$title = get_string('colourpresets', 'format_ned');
$heading = $SITE->fullname;

// Breadcrumb.
$PAGE->navbar->add(get_string('pluginname', 'format_ned'));
$PAGE->navbar->add(get_string('settings'));
$PAGE->navbar->add($name);

$PAGE->set_title($title);
$PAGE->set_heading($heading);

$datacolumns = array(
    'id' => 'tc.id',
    'name' => 'tc.name',
    'predefined' => 'tc.predefined',
    'timecreated' => 'tc.timecreated',
    'timemodified' => 'tc.timemodified'
);

// Filter.
$where = '';
if ($search) {
    $where = " WHERE ".$datacolumns['name']." LIKE '%$search%'";
}

// Sort.
$order = '';
if ($sort) {
    $order = " ORDER BY $datacolumns[$sort] $dir";
}

// Table columns.
$columns = array(
    'rowcount',
    'name',
    'predefined',
    'timecreated',
    'timemodified',
    'action'
);

$sql = "SELECT tc.*
          FROM {format_ned_colour} tc
               $where
               $order";

foreach ($columns as $column) {
    $string[$column] = get_string($column, 'format_ned');
    if (($column == 'rowcount') || ($column == 'action')) {
        $$column = $string[$column];
    } else {
        $columndirstr = ($dir == "ASC") ? get_string('sortdesc', 'format_ned') : get_string('sortasc', 'format_ned');
        if ($sort != $column) {
            $columnicon = '';
            if ($column == "name") {
                $columndir = "ASC";
            } else {
                $columndir = "ASC";
            }
        } else {
            $columndir = ($dir == "ASC") ? "DESC" : "ASC";
            $columnicon = ($dir == "ASC") ? "sort_asc" : "sort_desc";
            $columnicon = $OUTPUT->pix_icon('t/'.$columnicon, $columndirstr, '', array('class' => 'iconsort'));
        }
        $sorturl = $thispageurl;
        $sorturl->param('perpage', $perpage);
        $sorturl->param('sort', $column);
        $sorturl->param('dir', $columndir);
        $sorturl->param('search', $search);

        $$column = html_writer::link($sorturl->out(false), $string[$column].$columnicon, array('title' => $columndirstr));
    }
}

// TODO: Make responsive grid?
$table = new html_table();

$table->head = array();
$table->wrap = array();
foreach ($columns as $column) {
    $table->head[$column] = $$column;
    $table->wrap[$column] = '';
}

// Override cell wrap.
$table->wrap['action'] = 'nowrap';

$counter = ($page * $perpage);
$tablerows = $DB->get_records_sql($sql, null, $counter, $perpage);
// Count records for paging.
$totalcount = count($tablerows);

foreach ($tablerows as $tablerow) {
    $row = new html_table_row();
    $actionlinks = '';
    foreach ($columns as $column) {
        $varname = 'cell'.$column;

        switch ($column) {
            case 'rowcount':
                $$varname = ++$counter;
                break;
            case 'timecreated':
            case 'timemodified':
                $$varname = '-';
                if ($tablerow->$column > 0) {
                    $$varname = new html_table_cell(date("m/d/Y g:i A", $tablerow->$column));
                }
                break;
            case 'predefined':
                if ($tablerow->$column > 0) {
                    $$varname = new html_table_cell(get_string('yes'));
                } else {
                    $$varname = new html_table_cell('-');
                }
                break;
            case 'action':
                // Duplicate.
                $actionurl = new moodle_url('/course/format/ned/colourpreset_edit.php',
                    array('duplicate' => $tablerow->id )
                );
                $actionicontext = get_string('duplicate', 'format_ned');
                $actionicon = $OUTPUT->pix_icon('t/copy', $actionicontext);
                $actionlinks .= html_writer::link($actionurl->out(false), $actionicon, array(
                    'class' => 'actionlink',
                    'title' => $actionicontext));
                // Edit.
                if (!$tablerow->predefined) {
                    $actionurl = new moodle_url('/course/format/ned/colourpreset_edit.php',
                        array('edit' => $tablerow->id )
                    );
                    $actionicontext = get_string('edit');
                    $actionicon = $OUTPUT->pix_icon('t/edit', $actionicontext);
                    $actionlinks .= html_writer::link($actionurl->out(false), $actionicon, array(
                            'class' => 'actionlink',
                            'title' => $actionicontext));
                }
                // Delete.
                if (!$tablerow->predefined) {
                    $actionurl = new moodle_url('/course/format/ned/colourpreset_delete.php',
                        array('delete' => $tablerow->id )
                    );
                    $actionicontext = get_string('delete');
                    $actionicon = $OUTPUT->pix_icon('t/delete', $actionicontext);
                    $actionlinks .= html_writer::link($actionurl->out(false), $actionicon, array(
                            'class' => 'actionlink',
                            'title' => $actionicontext));
                }

                $$varname = new html_table_cell($actionlinks);
                break;
            default:
                $$varname = new html_table_cell($tablerow->$column);
        }
    }

    $row->cells = array();
    foreach ($columns as $column) {
        $varname = 'cell' . $column;
        $row->cells[$column] = $$varname;
    }
    $table->data[] = $row;

}

echo $OUTPUT->header();
echo html_writer::start_div('page-content-wrapper', array('id' => 'page-content'));
echo html_writer::tag('h1', $title, array('class' => 'page-title'));

// The view options.
$searchformurl = new moodle_url('/course/format/ned/colourpreset.php');

$searchform = html_writer::tag('form',
    html_writer::empty_tag('input', array(
        'type' => 'hidden',
        'name' => 'sesskey',
        'value' => sesskey(),
    )).
    html_writer::empty_tag('input', array(
        'type' => 'hidden',
        'name' => 'perpage',
        'value' => $perpage,
    )).
    html_writer::empty_tag('input', array(
        'type' => 'hidden',
        'name' => 'sort',
        'value' => $sort,
    )).
    html_writer::empty_tag('input', array(
        'type' => 'hidden',
        'name' => 'dir',
        'value' => $dir,
    )).
    html_writer::empty_tag('input', array(
        'type' => 'text',
        'name' => 'search',
        'value' => $search,
        'class' => 'search-textbox',
    )).
    html_writer::empty_tag('input', array(
        'type' => 'submit',
        'value' => 'Search',
        'class' => 'search-submit-btn',
    )),
    array(
        'action' => $searchformurl->out(),
        'method' => 'post',
        'autocomplete' => 'off'
    )
);
echo html_writer::div($searchform, 'search-form-wrapper', array('id' => 'search-form'));

$pagingurl = new moodle_url('/course/format/ned/colourpreset.php?',
    array(
        'perpage' => $perpage,
        'sort' => $sort,
        'dir' => $dir,
        'search' => $search
    )
);

$pagingbar = new paging_bar($totalcount, $page, $perpage, $pagingurl, 'page');

echo $OUTPUT->render($pagingbar);
echo html_writer::table($table);
echo $OUTPUT->render($pagingbar);

// Add record form.
$formurl = new moodle_url('/course/format/ned/colourpreset_edit.php',
    array('add' => '1')
);
$submitbutton  = html_writer::tag('button', get_string('add'), array(
    'class' => 'spark-add-record-btn',
    'type' => 'submit',
    'value' => 'submit',
));
$form = html_writer::tag('form', $submitbutton, array(
    'action' => $formurl->out(false),
    'method' => 'post',
    'style' => 'float: left;',
    'autocomplete' => 'off'
));

$formurlclose = new moodle_url('/admin/settings.php?section=formatsettingned');
$submitbuttonclose  = html_writer::tag('button', get_string('close', 'format_ned'), array(
    'class' => 'spark-close-record-btn',
    'type' => 'submit',
    'value' => 'submit',
));
$formclose = html_writer::tag('form', $submitbuttonclose, array(
    'action' => $formurlclose->out(false),
    'method' => 'post',
    'style' => 'float: left;',
    'autocomplete' => 'off'
));
echo html_writer::div($form.' '.$formclose, 'add-record-btn-wrapper', array('id' => 'add-record-btn'));

echo html_writer::end_div(); // Main wrapper.
echo $OUTPUT->footer();
