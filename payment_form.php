<?php
	
	require_once(__DIR__ . '/../../../config.php');
	require_once($CFG->libdir . '/adminlib.php');
	require_once(__DIR__ . '/portmone_form.php');
	require_once(__DIR__ . '/managerlib.php');

	$api_url = 'https://expert.education/_payallways_backend/public/api/';

	$args = array();
	$baseurl = new \moodle_url('payment_form.php', $args);

	$PAGE->set_url($CFG->wwwroot . '/availability/condition/payallways/payment_form.php', $args);
	try {
		$course_id = (int)$_GET['course_id'];
		$section_id = (int)$_GET['section_id'];

		if($section_id < 1 || $course_id < 1) {
			throw new Exception("invalidcourseid", 1);
		}

	} catch(Exception $e) {
		print_error('invalidcourseid');
	}
	if (!$course = $DB->get_record('course', array('id'=>$course_id))) {
	    print_error('invalidcourseid');
	}
	require_login($course);

	echo $OUTPUT->header();

	// $form = new payallways_portmone_form('manage.php?course_id='.$course->id, array('course_id' => $course->id));
	// if ($form->is_cancelled()) {
	// 	redirect($baseurl);
	// }

	//set your own error handler before the call
	set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context)
	{
	    throw new ErrorException( $err_msg, 0, $err_severity, $err_file, $err_line );
	}, E_WARNING);

	$form = null;

	try {

		// получить секцию, курс, сумму платежа, оплата за секцию или за весь курс, передать ид юзера и сайт, с которого идет оплата
		if(isset($_GET['is_section'])) {
			$table = 'course_sections';
		} else {
			$table = 'course_modules';
		}
		$v = $DB->get_field($table, 'availability', ['id' => $section_id]);
		
		$o = json_decode($v);
		
		if(is_null($o)) {
			throw new Exception(get_string("contactmanager", 'availability_payallways'), 1);
		}

		foreach ($o->c as $type) {
			if($type->type == 'payallways') {
				$obj = $type;
			}
		}

		if(!isset($obj)) {
			throw new Exception("Ошибка обработки формы", 1);
		}

		$course_data = get_local_data($course_id);
		if(!isset($course_data->operating_mode)) {
			$full_access = 1;
		} else {
			$full_access = (isset($course_data->operating_mode) && $course_data->operating_mode == PAYALLWAYS_OPERATING_MODES['coursewide']) ? 1 : 0;
		}
		
		global $USER;
		global $PAGE;
		global $CFG;
	
	    $form = file_get_contents(sprintf($api_url . '%s?course_id=%s&section_id=%s&cost=%s&full_access=%s&user_id=%s&source_url=%s&redirect_url=%s&is_section=%s', 'gateway/getform', $obj->course_id, isset($obj->cm) ? $obj->cm : $obj->section, $obj->access_cost, $full_access, $USER->id, urlencode($PAGE->url->get_host()), $CFG->wwwroot, isset($obj->is_section) ? $obj->is_section : '0'));
	    $form = json_decode($form);
	} catch (Exception $e) {
		echo $e->getMessage();
	    //echo 'Error getting payment form, plase try again later'; 
	}

	if($form) {
		echo $form->response;
	}

	//restore the previous error handler
	restore_error_handler();

// $current = get_local_data($course->id);
// if($current) {
// 	$form->set_data($current);
// }
// $form->display();
// $data = $form->get_data();
// if($data) {
// 	$form->save_changes($data);
// }

echo $OUTPUT->footer();


?>