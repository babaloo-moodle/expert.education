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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once(__DIR__ . '/managerlib.php');

class payallways_manager_form extends moodleform {
	
	public function __construct($submiturl, $course_id) {
        $this->course_id = $course_id['course_id'];
		$this->card_number = '';
		parent::__construct($submiturl);
    }
	
	public function definition() {
        $mform = $this->_form;
		$label = get_string('card_section_heading', 'availability_payallways');
        $mform->addElement('header', 'header', $label);
        
		$mform->addElement('hidden', 'course_id');
        $mform->setType('course_id', PARAM_INT);
        $mform->setDefault('course_id', $this->course_id);
		

		if(get_config('availability_payallways', 'billingentity') != 'legal_entity') {
			/*номер карты*/
			$mform->addElement('text', 'card_number', get_string('cardnum', 'availability_payallways'), 'maxlength="16"');
	        $mform->setType('card_number', PARAM_TEXT);
	        $mform->setDefault('card_number', $this->card_number);
	        /*end номер карты*/

			/*паспорт*/
			$mform->addElement('text', 'passport', get_string('passport', 'availability_payallways'));
			$mform->setType('passport', PARAM_TEXT);
			/*end паспорт*/

			/*ФИО*/
			$mform->addElement('text', 'author_name', get_string('fio', 'availability_payallways'));
			$mform->setType('author_name', PARAM_TEXT);
			/*end ФИО*/

			/*IBAN*/
			$mform->addElement('text', 'iban', "IBAN");
			$mform->setType('iban', PARAM_TEXT);
			/*end IBAN*/

			/*ИНН*/
			$mform->addElement('text', 'inn', get_string('inn', 'availability_payallways'));
			$mform->setType('inn', PARAM_TEXT);
			/*end IBAN*/
		}


		$mform->addElement('select', 'operating_mode', get_string('plugin_oprtating_mode', 'availability_payallways'), $this->get_operating_modes());

        //$mform->addElement('hidden', 'filter');
        //$mform->setType('filter', PARAM_SAFEPATH);
        //$mform->setDefault('filter', $this->filter);
		$mform->addElement('advcheckbox', 'checkbox_valid', '', get_string('data_valid', 'availability_payallways'));
		$mform->setDefault('checkbox_valid', 1);

		$mform->setType('chkbx', PARAM_TEXT);
		$mform->addElement('static', 'rules', '', get_string('rules', 'availability_payallways'));
		$mform->setType('rules', PARAM_TEXT);

		$this->add_action_buttons();
    }
	
	private function get_operating_modes() {
		return array('1' => get_string('mode_course', 'availability_payallways'), '2' => get_string('mode_activity', 'availability_payallways'));
	}

	public function validation($data, $files) {
		if(!array_key_exists('checkbox_valid', $data)) {
			return array('checkbox_valid' => 'Проверьте Ваши реквизиты и отметьте это поле');
		}

		if(array_key_exists('card_number', $data)) {
			$card = $data['card_number'];
			if(!is_numeric($card) || strlen($card) != 16) {
				return array('card_number' => 'Номер карты некорректен или содержит не 16 цифр');
			}
		}

		if(array_key_exists('passport', $data)) {
			$passport = $data['passport'];
			$regex1 = '/^[а-я]{2}\d{6}$/iu';
			$regex2 = '/^\d{9}$/';
			
			if(!preg_match($regex1, $passport) && !preg_match($regex2, $passport)) {
				return array('passport' => 'Паспорт указан неверно');
			}
		}

		if(array_key_exists('iban', $data)) {
			$iban = $data['iban'];
			$regex_iban = '/^[a-z]{2}\d{27}$/i';
			if(strlen($iban) > 29 || !preg_match($regex_iban, $iban)) {
				return array('iban' => 'IBAN-номер указан некорректно');
			}
		}	
	}
	
	public function save_changes($data) {
	    //save data entered to the db
	    $data = (array) $data;

	    //card details
        if(array_key_exists('card_number', $data)) {
        	$card_number = $data['card_number'];
        } else {
        	$card_number = $data['card_number'] = '';
        }

        //passport/id info
        if(array_key_exists('passport', $data)) {
        	$passport = $data['passport'];
        } else {
        	$passport = $data['passport'] = '';
        }

        //author full name
        if(array_key_exists('author_name', $data)) {
        	$author_name = $data['author_name'];
        } else {
        	$author_name = $data['author_name'] = '';
        }


        //IBAN num
        if(array_key_exists('iban', $data)) {
        	$iban = $data['iban'];
        } else {
        	$iban = $data['iban'] = '';
        }

        //инн
        if(array_key_exists('inn', $data)) {
        	$inn = $data['inn'];
        } else {
        	$inn = $data['inn'] = '';
        }
		set_local_config($data['course_id'], $card_number, $data['operating_mode'], $passport, $author_name, $iban, $inn);
    }
}
 
?>