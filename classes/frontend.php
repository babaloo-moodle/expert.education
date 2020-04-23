<?php
	namespace availability_payallways;
	defined('MOODLE_INTERNAL') || die();
	 
	require_once(__DIR__ . '/../managerlib.php');

	class frontend extends \core_availability\frontend {
	 
		protected function get_javascript_strings() {
			// вернет строки из языковых файлов
			return array('access_cost', 'section_block_headline', 'cost_error');
		}
	 
		protected function get_javascript_init_params($course, \cm_info $cm = null, \section_info $section = null) {			
			update_the_course($course->id, 0, 0, 0); // принудительно удаляем данные о блоке курса, т.к. в Мудле нет адекватного мехаизма проверить это
			
			// получение значений - заголовок блока, секция, цена блока
			$ret = array('headline' => get_string('section_block_headline', 'availability_payallways'));
			if(!isset($cm->module)) {
				$ret['is_section'] = true;
			}
			
			if($cm != null) {
				$ret['cm'] = $cm->id;
				$ret['section'] = $cm->__get('section');
				//$ret['access_cost'] = get_price($cm->id, PayAllWaysHelper::ACTIVITY);
			}
			if($section != null) {
				$ret['section'] = $section->id;
				//$ret['access_cost'] = get_price($cm->id, PayAllWaysHelper::SECTION);
			}

			$ret['access_cost'] = 0;
			
			$ret['course_id'] = $course->id;

			return [(object)$ret];
		}
	 	

	 	// разрешено ли добавить ограничение на секцию
		protected function allow_add($course, \cm_info $cm = null,
				\section_info $section = null) {

			if(get_working_mode($course->id) == 'coursewide') {
				if(course_locked($course->id)) {
					return false;
				}
			}

			// $info = new \core_availability\info_module($cm);
			// echo "<pre>";
			// $modinfo = get_fast_modinfo($course);
			// //$d = $modinfo->get_cms();
			// $c = get_course($course->id);
			// // var_dump($modinfo->get_sections());
			// //var_dump(get_course_mods($course->id));
			// // var_dump($cm->__get('section'));
			
			// // var_dump(get_array_of_activities($course->id));

			// //cm->id & $cm->__get('section')
			// //var_dump();
			// var_dump($cm->__get('section'));

			// echo "</pre>";
			// die();
			// // This function lets you control whether the 'add' button for your
			// // plugin appears. For example, the grouping plugin does not appear
			// // if there are no groupings on the course. This helps to simplify
			// // the user interface. If you don't include this function, it will
			// // appear.

			// if(!payallways_add_allowed($course->id)) {
			// 	return false;
			// }

			return true;
		}

	}
?>