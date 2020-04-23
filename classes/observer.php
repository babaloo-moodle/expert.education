<?php
	defined('MOODLE_INTERNAL') || die();
	require_once(__DIR__ . '/../managerlib.php');

	class availability_payallways_observer {
		public static function ttest(\core\event\course_module_updated $event) {
			if(get_working_mode($event->courseid) != 'coursewide') {
				return true;
			}

			$course_id = $event->courseid;
			$section_id = $event->objectid;

			global $DB;
			global $CFG;

			$info = get_fast_modinfo($course_id);
			$modules = $info->get_sections();

			$first_module_id = null;

			$f = false; //found

			foreach ($modules as $mod) {
				if($f) {
					break;
				}
				foreach ($mod as $cur) {
					$mod_info = $DB->get_record('course_modules', ['id' => $cur]);
					if(contains_restriction('payallways', $mod_info->availability)) {
						$first_module_id = $cur;
						$f = true;
						break;
					}
					if($cur == $section_id) {
						$first_module_id = $cur;
						$f = true;
						break;
					}
				}
			}

			$headline_module = $DB->get_record('course_modules', ['id' => $first_module_id]);
			$sec_id = $headline_module->section;
			$headline_restriction = $DB->get_field('course_modules', 'availability', ['id' => $first_module_id]);
			$restriction_mode = get_restriction_mode($headline_module->availability);
			if($mod_info->payallways_locked != 1 && $restriction_mode == Restriction::MODE_NULL) {
				return false;
			}
			$section = $DB->get_record('course_sections', ['id' => $sec_id]);
			
			$ids = get_section_modules($section->id);
			$found = false;
			foreach ($ids as $id) {
				if($id == $first_module_id) {
					$found = true;
				}
				if($found) {
					$restriction = new Restriction($restriction_mode, $course_id);
					$restriction->set_headline($headline_restriction);
					$restriction->process_element($id, Restriction::TYPE_MODULE);
				}
			}

			if($found) {
				$next_section_id = get_section_id($section->section+1, $course_id);
				$restriction->process_element($next_section_id, Restriction::TYPE_SECTION);
				availability_payallways_observer::block_themes($next_section_id, $course_id);

				if($restriction_mode == Restriction::MODE_NULL) {
					unlock_course($course_id);
				}
			}

			rebuild_course_cache($course_id, true);

			return true;
		}

		public static function ttest1(\core\event\course_section_updated $event) {
			if(get_working_mode($event->courseid) != 'coursewide') {
				return true;
			}

			$course_id = $event->courseid;
			$theme_id = $event->objectid;
			
			availability_payallways_observer::block_themes($theme_id, $course_id);		

			return true;
		}		

		public static function block_themes($startid, $course_id, $restriction_outer = null) {
			global $DB;
			global $CFG;

			$current_theme = $DB->get_record('course_sections', ['id' => $startid]);
			
			if($current_theme == null){
				$current_theme = $DB->get_record('course_sections', ['id' => $startid - 1]);
			}

			$last_section_number = get_max_section_number($course_id);
			
			$headline_section_number = get_restricted_min_section_number($course_id);

			if($headline_section_number < $current_theme->section) {
				$headline_id = get_id_by_section_number($headline_section_number, $course_id);
			} else {
				$headline_id = $startid;
			}
			$headline_restriction = $DB->get_field('course_sections', 'availability', ['id' => $headline_id]);
			
			
			$restriction_mode = get_restriction_mode($headline_restriction);

			for($i = $current_theme->section; $i <= $last_section_number; $i++) {
				$c = get_section_by_num($i, $course_id);
				
				$restriction = new Restriction($restriction_mode, $course_id);

				$restriction->set_headline($headline_restriction);
				$restriction->process_element($c->id, Restriction::TYPE_SECTION);

				$ids = get_section_modules($c->id);
				if($ids !== false) {
					foreach ($ids as $id) {
						$restriction->process_element($id, Restriction::TYPE_MODULE);
					}
				}

				if($restriction_mode == Restriction::MODE_NULL) {
					unlock_course($course_id);
				}
			}
			rebuild_course_cache($course_id, true);

		}
	
		public static function module_created(\core\event\course_module_created $event) {
			$course_id = $event->courseid;
			$section_id = $event->objectid;

			// если режим посекционный, то ничего не делаем
			if(get_working_mode($event->courseid) != Mode::COURSEWIDE) {
				return true;
			}

			global $DB;
			global $CFG;

			// если блок на весь курс, то надо узнать, попадает ли активность в заблокированную секцию
			// т.е. если она идет после любой заблокированной активности, значит, она заблочена

			$info = get_fast_modinfo($course_id);
			$modules = $info->get_sections();

			$first_module_id = null;

			$found = false; //found
			$shold_be_blocked = false;

			foreach ($modules as $mod) {
				if($found) {
					break;
				}
				foreach ($mod as $cur) {
					$mod_info = $DB->get_record('course_modules', ['id' => $cur]);
					if(contains_restriction('payallways', $mod_info->availability)) {
						$first_module_id = $cur;
						$found = true;
						$shold_be_blocked = true;
						break;
					}
					if($cur == $section_id) {
						$first_module_id = $cur;
						$found = true;
						break;
					}
				}
			}

			if($found && $shold_be_blocked) {
				$mod_info = $DB->get_record('course_modules', ['id' => $first_module_id]);
				if(contains_restriction('payallways', $mod_info->availability)) {
					$restriction = new Restriction(Restriction::MODE_ADD, $course_id);
					$headline_restriction = $DB->get_field('course_modules', 'availability', ['id' => $first_module_id]);
					$restriction->set_headline($headline_restriction);
					$restriction->process_element($section_id, Restriction::TYPE_MODULE);
				}
			}

			rebuild_course_cache($course_id, true);

		}

		public static function section_created(\core\event\course_section_created $event) {
			$course_id = $event->courseid;
			$section_id = $event->objectid;

			// если режим посекционный, то ничего не делаем
			if(get_working_mode($event->courseid) != Mode::COURSEWIDE) {
				return true;
			}

			global $DB;
			global $CFG;

			$current_theme = $DB->get_record('course_sections', ['id' => $section_id]);
			$headline_section_number = get_restricted_min_section_number($course_id);

			if($headline_section_number < $current_theme->section) {
				$headline_id = get_id_by_section_number($headline_section_number, $course_id);

				$headline_restriction = $DB->get_field('course_sections', 'availability', ['id' => $headline_id]);
				if($headline_restriction) {
					$restriction_mode = Restriction::MODE_ADD;
					$restriction = new Restriction($restriction_mode, $course_id);
					$restriction->set_headline($headline_restriction);
					$restriction->process_element($section_id, Restriction::TYPE_SECTION);
				}
				rebuild_course_cache($course_id, true);

			} 
		}
	
	}
?>
