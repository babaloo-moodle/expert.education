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

class payallways_portmone_form extends moodleform {
	
	public function __construct($submiturl, $course_id) {
        $this->course_id = $course_id['course_id'];
		$this->card_number = '';
		parent::__construct($submiturl);
    }
	
	public function definition() {
        $mform = $this->_form;
		// $label = get_string('card_section_heading', 'availability_payallways');
        $mform->addElement('header', 'header', 'Оплата курса');
        
		$mform->addElement('hidden', 'course_id');
        $mform->setType('course_id', PARAM_INT);
        $mform->setDefault('course_id', $this->course_id);
		
		// if(get_config('availability_payallways', 'billingentity') != 'legal_entity') {
		$mform->addElement('text', 'card_number', get_string('cardnum', 'availability_payallways'), 'maxlength="16"');
	    $mform->setType('card_number', PARAM_TEXT);
	    $mform->setDefault('card_number', '');

	    $mform->addElement('text', 'cvv', 'cvv', 'maxlength="4"');
	    $mform->setType('cvv', PARAM_TEXT);

	    $mform->addElement('text', 'cvv', 'Срок действия карты');
	    $mform->setType('exp_date', PARAM_TEXT);

		// }
        
		

        $this->add_action_buttons();
    }
	
	private function get_operating_modes() {
		return array('1' => get_string('mode_course', 'availability_payallways'), '2' => get_string('mode_activity', 'availability_payallways'));
	}

	public function validation($data, $files) {
		if(array_key_exists('card_number', $data)) {
			$card = $data['card_number'];
			if(!is_numeric($card) || strlen($card) != 16) {
				return array('card_number' => 'Номер карты некорректен или содержит не 16 цифр');
			}
		}
	}
	
	public function save_changes($data) {
	    $data = (array) $data;
        if(array_key_exists('card_number', $data)) {
        	$card_number = $data['card_number'];
        } else {
        	$card_number = $data['card_number'] = '';
        }
		set_local_config($data['course_id'], $card_number, $data['operating_mode']);
    }
}
 
?>