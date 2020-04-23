<?php

require_once('../../../config.php');
require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->libdir .'/filelib.php');

global $DB;

var_dump($DB->get_record('course_modules', array('id' => 10), '*', MUST_EXIST)); //->get_record('course_modules', array('course' => 2, 'section' => 3, 'id' => 10), '*', MUST_EXIST)););
echo 'a';

?>