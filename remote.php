<?php
	require_once(__DIR__ . '/../../../config.php');
	require_once($CFG->libdir.'/moodlelib.php');
	require_once(__DIR__ . '/managerlib.php');
	
	$course_id = $_GET['course_id'];
	try {
		$course_id = (int)$course_id;
	} catch (Exception $e) {
		die;
	}

	if( (is_int($course_id)) && ($course_id > 0 && $course_id < 4000000) ) {
		try {
			if(get_config('availability_payallways', 'billingentity') == 'legal_entity') {
				echo json_encode(get_legal_entity($course_id));
			} else {
				echo json_encode(get_local_data($course_id));
			}
		} catch (Exception $e) {
			die;
		}
	}

?>