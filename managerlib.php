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
 * Plugin strings are defined here.
 *
 * @package     availability_payallways
 * @category    string
 * @copyright   <email1>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 //файл для обработки формы плагина - сохранение/чтение карты, режим работы и т.д.
 
// записывает номер карты в бд 

define('INFODB_TABLENAME', 'availability_payallways_ci');
define('PAYALLWAYS_OPERATING_MODES', array('coursewide' => 1, 'persection' => 2));

class Mode {
	public const COURSEWIDE = 'coursewide';
	public const PER_SECTION = 2;
}

class Restriction {
	public const MODE_NULL = 1;
	public const MODE_REMOVE = 2;
	public const MODE_ADD = 3;

	public const TABLE_SECTIONS = 'course_sections';
	public const TABLE_MODULES = 'course_modules';

	public const TYPE_SECTION = 1;
	public const TYPE_MODULE = 2;

	private $current_mode = null;
	private $db = null;
	private $headline_restriction = null;
	private $course_id = null;

	public function __construct($restriction_mode, $course_id) {
		global $DB;

		$this->db = $DB;
		$this->current_mode = $restriction_mode;
		$this->course_id = $course_id;
	}

	public function process_element($element_id, $element_type) {
		$restriction = null;
		if($this->current_mode == Restriction::MODE_NULL || $this->current_mode == Restriction::MODE_REMOVE) {
			$restriction = $this->delete_from_element($element_id, $element_type);
		} else {
			$restriction = $this->add_to_element($element_id, $element_type);
		}

		update_db_restriction($this->get_table($element_type), $restriction, $element_id);
	}

	private function add_to_element($element_id, $element_type) {
		$jsoned = json_decode($this->headline_restriction); // декодируем главную секцию

		$access_cost = $this->get_access_cost($jsoned);  // возьмем оттуда стоимость курса
		$is_section = $element_type == Restriction::TYPE_SECTION ? 1 : 0; //$this->get_is_section($jsoned);

		$current_res = $this->get_restriction($this->get_table($element_type), $element_id);  // получим restriction текущей секции
		if($current_res != null) {
			$j_c = json_decode($current_res);
		} else {
			$j_c = create_restriction($element_id, $this->course_id, $access_cost, $is_section);
		}

		remove_restriction($j_c);
		$restriction = create_restriction($element_id, $this->course_id, $access_cost, $is_section);  // и заново ее закодируем, указав корректную стоимость курса
		add_restriction($j_c, $restriction);


		$rec = $this->get_record($this->get_table($element_type), $element_id);
		$rec->payallways_locked = 1;
		$this->update_record($this->get_table($element_type), $rec);

		//$this->db->update_record(INFODB_TABLENAME, $rec);
		return json_encode($j_c);
	}

	private function get_access_cost($jsoned) {
		if($jsoned) {
			foreach ($jsoned->c as $se) {
				if($se->type == 'payallways') {
					return $se->access_cost;
				}
			}
			return 0; // если не найдено
		}
	}

	private function get_is_section($jsoned) {
		if($jsoned) {
			foreach ($jsoned->c as $se) {
				if($se->type == 'payallways') {
					return isset($se->is_section) ? $se->is_section : false;
				}
			}
			return 0; // если не найдено
		}
	}

	public function set_headline($headline_restriction) {
		$this->headline_restriction = $headline_restriction;
	}

	private function get_table($element_type) {
		if($element_type == Restriction::TYPE_SECTION) {
			return Restriction::TABLE_SECTIONS;
		} else if($element_type == Restriction::TYPE_MODULE) {
			return Restriction::TABLE_MODULES;
		}
	}

	public function process_module($module_id) {
		$restriction = null;
		if($this->current_mode == Restriction::MODE_NULL || $this->current_mode == Restriction::MODE_REMOVE) {
			$restriction = $this->delete_from_module($module_id);
		}

		update_db_restriction(Restriction::TABLE_MODULES, $restriction, $module_id);
	}

	private function delete_from_element($element_id, $element_type) {
		$table = $this->get_table($element_type);

		$element_restriction = $this->get_restriction($table, $element_id);
		$json_restriction = $this->decode_restriction($element_restriction);

		$rec = $this->get_record($this->get_table($element_type), $element_id);
		$rec->payallways_locked = 0;
		$this->update_record($this->get_table($element_type), $rec);
		
		if($json_restriction != false) {
			remove_restriction($json_restriction);
			return json_encode($json_restriction);
		} else {
			return null;
		}

	}


	private function get_restriction($table, $id) {
		return $this->db->get_field($table, 'availability', ['id' => $id]);
	}

	private function get_record($table, $id) {
		return $this->db->get_record($table, ['id' => $id]);
	}
	private function update_record($table, $rec) {
		return $this->db->update_record($table, $rec);
	}

	private function decode_restriction($restriction) {
		return Restriction::do_decode_restriction($restriction);
	}

	public static function do_decode_restriction($restriction) {
		if($restriction == null) {
			return false;
		}

		return json_decode($restriction);
	}
}

class PayAllWaysHelper {
	public const SECTION = 1;
	public const ACTOVITY = 2;
}

function set_local_config($course_id, $card_number, $operating_mode, $passport, $author_name, $iban, $inn) {
	global $DB;
	
    $rec = $DB->get_record(INFODB_TABLENAME, array('course_id' => $course_id)); // есть ли карта для этого курса?
    if($rec && $rec->operating_mode != $operating_mode) {
    	clear_sections($course_id);
    	$rec->course_closed = 0;
    	rebuild_course_cache($course_id, true);
    }

	$insert = false; // режим по умолчанию - замена
    if (empty($rec)) {  // если данных нет,
        $insert = true;  // добавим их
        $rec = new stdClass;
        $rec->course_id = $course_id;
        $rec->card_number = $card_number;
		$rec->operating_mode = $operating_mode;
		$rec->passport = $passport;
		$rec->author_name = $author_name;
		$rec->iban = $iban;
		$rec->inn = $inn;
    } else {
		$rec->card_number = $card_number;  // иначе просто обновим
		$rec->operating_mode = $operating_mode;
		$rec->passport = $passport;
		$rec->author_name = $author_name;
		$rec->iban = $iban;
		$rec->inn = $inn;
	}
    if ($insert) {
        $DB->insert_record(INFODB_TABLENAME, $rec);
    } else {
        $DB->update_record(INFODB_TABLENAME, $rec);
    }
}

function remove_restriction(&$restrictions, $type='payallways') {
	$idx = 0;
	foreach ($restrictions->c as $k) {
		if($k->type == $type) {
			array_splice($restrictions->c, $idx, 1);
			array_splice($restrictions->showc, $idx, 1);
		}
		$idx++;
	}
}

function add_restriction(&$current, $adding) {
	$current->c[] = $adding->c[0];
	$current->showc[] = $adding->showc[0];
}

function create_restriction($sec_num, $course_id, $access_cost, $is_section) {
	return \core_availability\tree::get_root_json(  
								[\availability_payallways\condition::get_json($sec_num, $sec_num, $course_id, $access_cost, $is_section)]
							);
}

// проверяет, содержат ли ограничения нужную подстроку
function contains_restriction($str = 'payallways', $current_restriction) {
	if($current_restriction == null) {
		return false;
	}
	
	if(strpos($current_restriction, $str) !== false) {  // если в текущей вресии
		return true;
	}


	return false;

}

function update_db_restriction($table, $restriction, $section_id) {
	global $DB;
	$DB->set_field($table, 'availability', $restriction, ['id' => $section_id]);
}

function clear_sections($course_id) {
	global $DB;
	global $CFG;

	$modinfo = get_fast_modinfo($course_id);
	$course_sections = $modinfo->get_sections();

	foreach ($course_sections as $sec) {
		foreach ($sec as $cur) {
			$current = $DB->get_field('course_modules', 'availability', ['id' => $cur]);  // получим текущую секцию

			if(contains_restriction('payallways', $current)) {
				$j = json_decode($current);
				remove_restriction($j);
				$enc = json_encode($j);
				update_db_restriction('course_modules', $enc, $cur);
			}
		}
	}


	$sections = $DB->get_records_sql('SELECT id from ' . $CFG->prefix . 'course_sections where availability LIKE "%payallways%" and course = ' . $course_id);
	if($sections) {
		foreach ($sections as $sec) {
			$current = $DB->get_field('course_sections', 'availability', ['id' => $sec->id]);  // получим текущую секцию

			if(contains_restriction('payallways', $current)) {
				$j = json_decode($current);
				remove_restriction($j);
				$enc = json_encode($j);
				update_db_restriction('course_sections', $enc, $sec->id);
			}
		}
	}
}

function update_the_course($course_id, $locked = 0, $section = null, $activity = null, $cm = null, $access_cost = null ) {
	// rebuild_course_cache($course_id, true);
	// die();
	global $DB;
	global $CFG;

	$rec = get_local_data($course_id);
	$lastid = $DB->get_field_sql('SELECT MAX(id) from ' . $CFG->prefix . INFODB_TABLENAME);
	
	if($rec == false) {
		$rec = new stdclass();
		$rec->id = $lastid == null ? 1 : $lastid + 1;
		$rec->course_id = $course_id;
		$rec->operating_mode = 1;
	}
	$rec->course_closed = $locked;
	$rec->section_id = $section;
	$rec->activity_id = $activity;

	if($cm == null) {
		// var_dump('dsdsds');
		// var_dump($course_id);
		// die();
	}
	// echo "<br />";

	// $do = false;

	// if($section) {
	// 	$do = true;
	// }

	// $modinfo = get_fast_modinfo($course_id);
	// $s = $modinfo->get_sections();

	// $go = false;

	// //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	// foreach ($s as $v) {
	// 	foreach ($v as $k) {
	// 		if($go && $do) {
	// 			$restriction = \core_availability\tree::get_root_json( 
	// 				[\availability_payallways\condition::get_json($k, $k, $course_id, $access_cost)]
	// 			);
	// 			$restriction = json_encode($restriction);
	// 			//echo $restriction . "<br />";
	// 		} else {
	// 			$restriction = NULL;
	// 		}
	// 		$DB->set_field('course_modules', 'availability', $restriction, ['id' => $k]);
	// 		if($k == $cm) {
	// 			$go = true;
	// 		}
	// 	}
	// }

	// rebuild_course_cache($course_id, true);
	//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	$DB->update_record(INFODB_TABLENAME, $rec);
}

function course_locked($course_id) {
	$rec = get_local_data($course_id);
	if(!$rec) {
		return false;
	}
	if($rec->course_closed) {
		return true;
	}
	return false;
}

function unlock_course($course_id) {
	global $DB;
	$rec = get_local_data($course_id);
	if(!$rec) {
		return false;
	}
	$rec->course_closed = 0;
	$DB->update_record(INFODB_TABLENAME, $rec);
	return true;
}

function get_price($id, $type) {
	
}

function get_legal_entity($course_id) {
	return ['payment_account' => get_config('availability_payallways', 'legalentitypaymentaccount'),
			'MFO' => get_config('availability_payallways', 'bankmfonum'),
			'edrpu' => get_config('availability_payallways', 'edrpu'),
			'name' => get_config('availability_payallways', 'entity_name'),
			'IBAN' => get_config('availability_payallways', 'iban'),
		];
}

// возвращает номер карты, если она записана в этом курсе
function get_local_data($course_id) {
	global $DB;
	
	return $DB->get_record(INFODB_TABLENAME, array('course_id' => $course_id));
}

function get_current_restriction_options_payallways($course_id) {
	$rec = get_local_data($course_id);
	if($rec->operating_mode == PAYALLWAYS_OPERATING_MODES['coursewide']) {
		return array('1' => 'Yes', '2' => 'No');
	} else if($rec->operating_mode == PAYALLWAYS_OPERATING_MODES['persection']) {
		return array('1' => 'Free', '2' => 'Paid');
	}
}

function get_current_headline($course_id) {
	$rec = get_local_data($course_id);
	if($rec->operating_mode == 1) {
		return get_string('headline_coursewide', 'availability_payallways');
	} if($rec->operating_mode == 2) {
		return get_string('headline_persection', 'availability_payallways');
	}
}

function payallways_add_allowed($course_id) {
	if($rec && $rec->operating_mode == 1) {
		return false;
	}
	return true;
}

// вернет текущий режим работы плагина: блок на весь курс или посекционно, и т.д.
function get_working_mode($course_id) {
	$rec = get_local_data($course_id);
	if(!$rec || $rec->operating_mode == PAYALLWAYS_OPERATING_MODES['coursewide']) {
		return 'coursewide';
	}
	if($rec->operating_mode == PAYALLWAYS_OPERATING_MODES['persection']) {
		return 'persection';
	}
}

function get_max_section_number($course_id) {
	global $DB;
	global $CFG;

	return $DB->get_field_sql('SELECT MAX(section) from ' . $CFG->prefix . 'course_sections where course = ' . $course_id);
}

function get_section_id($section_num, $course_id) {
	global $DB;
	global $CFG;

	return $DB->get_field_sql('SELECT id from ' . $CFG->prefix . 'course_sections where section = ' . $section_num . ' AND course = ' . $course_id);
}

function get_restricted_min_section_number($course_id) {
	global $DB;
	global $CFG;

	return $DB->get_field_sql('SELECT min(section) from ' . $CFG->prefix . 'course_sections where availability LIKE "%payallways%" and course = ' . $course_id);
}

function get_id_by_section_number($headline_section_number, $course_id) {
	global $DB;

	return $DB->get_field('course_sections', 'id', ['section' => $headline_section_number, 'course' => $course_id]);
}

function get_restriction_mode($headline_restriction) {
	$section_contains_restriction = contains_restriction('payallways', $headline_restriction);

	if($headline_restriction == null) { // если у хэдлайна вообще нет ограничений,
		return Restriction::MODE_NULL; // обнуляем
	} else if(!$section_contains_restriction) {  // если нет только payallways
		return Restriction::MODE_REMOVE;
	} else {
		return Restriction::MODE_ADD;
	}
}

function get_section_by_num($section_num, $course_id) {
	global $DB;
	
	return $DB->get_record(Restriction::TABLE_SECTIONS, ['section' => $section_num, 'course' => $course_id]);
}

function get_section_modules($section_id) {
	global $DB;

	$ids = $DB->get_field(Restriction::TABLE_SECTIONS, 'sequence', ['id' => $section_id]);
	if(strlen($ids) != 0) {
		return explode(',', $ids);
	}
	return false;
}

?>