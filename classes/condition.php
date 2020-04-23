<?php
	// this code decides if a user has access to activity (https://docs.moodle.org/dev/Availability_conditions#classes.2Ffrontend.php)
	
	namespace availability_payallways;
	
	defined('MOODLE_INTERNAL') || die();
	
	require_once(__DIR__ . '/../managerlib.php');

	global $allow_remote;
	$allow_remote = null;

	class condition extends \core_availability\condition {
		protected $allow;
		protected $cm = null;
		protected $section = null;
		protected $course_id = 0;
		protected $access_cost = 0;
		protected $is_section = false;

		public function __construct($structure) {
			$this->is_paid = true;
			if (property_exists($structure, 'section')) {
				$this->section = $structure->section;
			}
			if (property_exists($structure, 'cm')) {
				$this->cm = $structure->cm;
			}
			if (property_exists($structure, 'course_id')) {
				$this->course_id = $structure->course_id;
			}
			if(!property_exists($structure, 'access_cost')) {
				$this->access_cost = 0;
			} else {
				$this->access_cost = $structure->access_cost;
			}

			if(property_exists($structure, 'is_section')) {
				$this->is_section = $structure->is_section;
			} else {
				$this->is_section = false;
			}

			update_the_course($this->course_id, 1, $this->section, $this->cm, $this->cm, $this->access_cost);
			$d = get_local_data($this->course_id);
			// var_dump($this);
			// die;
			// It is also a good idea to check for invalid values here and
			// throw a coding_exception if the structure is wrong.

			global $PAGE;
			global $USER;
			global $allow_remote;
			
			if($allow_remote == null) {
				$api_url = 'https://expert.education/_payallways_backend/public/api/get_full_data/%s/%s/%s';
				
				try {
					$allow_remote = file_get_contents(sprintf($api_url, $this->course_id, $USER->id, $PAGE->url->get_host()));
				} catch(Exception $e) {
					$allow_remote = false;
				}
				
				try {
					$allow_remote = json_decode($allow_remote);
				} catch(Exception $e) {
					$allow_remote = false;
				}
			}
		}
	 
		public function save() {
			$result = (object)['type' => 'payallways'];
			if ($this->is_paid) {
				$result->is_paid = $this->is_paid;
			}
			$result->cm = $this->cm;
			$result->section = $this->section;
			$result->course_id = $this->course_id;
			$result->is_section = $this->is_section;
			if($this->access_cost == null) {
				$result->access_cost = 0;
			} else {
				$result->access_cost = 111;
				//$result->access_cost = $this->access_cost;
			}
			return (object)array('type' => 'payallways', 'allow' => $this->allow, 'cm' => $this->cm, 'section' => $this->section, 'course_id' => $this->course_id, 'access_cost' => $this->access_cost, 'is_section' => $this->is_section);
		}

		public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
			global $CFG, $DB, $USER, $PAGE;
			global $allow_remote;
			
			if($allow_remote == false || $allow_remote->allow == false) {
				return false;
			}

			foreach ($allow_remote->allow as $row) {
				if($row->course_id == $this->course_id && $row->section_id == 0) { // если section_id == 0, значит, оплачен весь курс и дальше проверять нет смысла, вернем true
					return true;
				}

				//иначе проверяем каждый блок отдельно
				if($row->course_id == $this->course_id && $row->section_id == $this->section) {
					if($this->is_section && $row->is_section) {
						return (bool)$row->allowed;
					} else {
						return (bool)$row->allowed;
					}
				}
			}

			// $allow = !$this->is_paid;
			
			
			
			// if ($not) {
			// 	$allow = !$allow;
			// }
			// $course_id = $info->get_course()->id;
			// $section = $this->cm == null ? $this->section : $this->cm;
	
			// $api_url = 'https://expert.education/_payallways_backend/public/api/user_allowed/%s/%s/%s/%s/%s';
			// $allow = file_get_contents(sprintf($api_url, $course_id, $section, $USER->id, $PAGE->url->get_host(), $this->is_section ? '1' : '0'));

			// try{
			// 	$j = json_decode($allow);
			// } catch(Exception $e) {
			// 	return false;
			// }

			// $allow = $j->allow;

			//return $allow;
		}
		
		public static function get_json($cm, $section, $course_id, $access_cost, $is_section) {
			return (object)['type' => 'payallways', 'allow' => false, 'cm' => $cm, 'section' => $section, 'course_id' => $course_id, 'access_cost' => $access_cost, 'is_section' => $is_section];
		}
		
		public function get_description($full, $not, \core_availability\info $info) {			
			global $USER;
			
			if ($not || !$this->is_paid) {
                return get_string('getdescriptionnot', 'availability_payallways');
            }
			
			if(!$this->is_available(false, $info, true, $USER->id)) {
				$fstr = get_string('getdescription', 'availability_payallways'); // full string
				$str = get_string('makepaymentnow', 'availability_payallways');
				//$url = new \moodle_url('#');
				$format = '/availability/condition/payallways/payment_form.php?course_id=%s&section_id=%s';
				$url = new \moodle_url(sprintf($format, $this->course_id, (isset($this->section) && $this->is_section) == true ? $this->section . '&is_section=1' : $this->cm));
		        $str = \html_writer::link($url, $str);
		        return $fstr . ' '  . $str;
		    }
		}
	 
		protected function get_debug_string() {
			// This function is only normally used for unit testing and
			// stuff like that. Just make a short string representation
			// of the values of the condition, suitable for developers.
			return $this->allow ? 'YES' : 'NO';
		}
	}
?>