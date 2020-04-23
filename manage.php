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
 * Plugin administration pages are defined here.
 *
 * @package     availability_payallways
 * @category    admin
 * @copyright   <емейл1>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// импорты
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/manager_form.php');
require_once(__DIR__ . '/managerlib.php');

$id = required_param('course_id', PARAM_INT); // id курса
$args = array('course_id'=>$id);
$baseurl = new moodle_url($CFG->wwwroot . '/availability/condition/payallways/manage.php', $args);
$returnurl = new moodle_url($CFG->wwwroot . '/course/view.php?id=' . $id);

$PAGE->set_url($baseurl, $args);

if (!$id) {
    require_login();
    print_error('needcourseid');
}

if($id == SITEID){
    // Don't allow editing of 'site course' using this form.
    print_error('cannoteditsiteform');
}

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error('invalidcourseid');
}
require_login($course);
$context = context_course::instance($course->id);

if (!has_capability('moodle/course:update', $context)) {
    print_error('nopermissionseditcourse', 'availability_payallways');
}


$PAGE->set_course($course);
$PAGE->set_url('/availability/condition/payallways/manage.php', array('course_id' => $course->id));
$PAGE->set_title($course->shortname);
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('admin');

// Print the form.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('card_details', 'availability_payallways'));
$form = new payallways_manager_form('manage.php?course_id='.$id, array('course_id' => $course->id));
if ($form->is_cancelled()) {
	redirect($returnurl);
}


$data = $form->get_data();
if($data) {
	$form->save_changes($data);
	redirect($returnurl);
}

$current = get_local_data($course->id);
if($current) {
	$form->set_data($current);
}

$form->display();


echo $OUTPUT->footer();
