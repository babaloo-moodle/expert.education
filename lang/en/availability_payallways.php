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

defined('MOODLE_INTERNAL') || die();

$string['1'] = 'en';
$string['description'] = 'Control paid access. Students are not able to move further until they pay for the access.';
$string['title'] = 'PayAllWays';
$string['pluginname'] = 'PayAllWays';
$string['pluginname_desc'] = 'The PayAllWays module allows you to set up paid activities or courses.';
$string['legalentity'] = 'Legal entity';
$string['fop'] = 'Self employed';
$string['billingentity'] = 'Billing entity';
$string['billingentity_help'] = 'Select a billing scheme: "legal entity" is set for the plugin globally while "self employed" allows coursemanager accept payments individually';
$string['paymentaccount'] = 'Corporate card number';
$string['paymentaccount_help'] = 'Payment account where funds will be sent';
$string['paymentnote'] = 'Payment note';
$string['cardnum'] = 'Card number';
$string['card_details'] = 'Edit card details';
$string['card_section_heading'] = 'Card info';
$string['getdescription'] = 'User has paid the access';
$string['getdescriptionnot'] = 'The section is free';
$string['makepaymentnow'] = 'Make payment now';
$string['payallways_settings'] = 'Edit monetization settings';
$string['plugin_oprtating_mode'] = 'Plugin operating mode';
$string['mode_course'] = 'Cousrse-wide restriction';
$string['mode_activity'] = 'Per activity restricton';
$string['section_block_headline'] = 'Premium section';
$string['bankmfo'] = 'Bank MFO';
$string['access_cost'] = 'Access cost';
$string['contactmanager'] = 'Error getting course data, please contact manager or course creator';
$string['edrpu'] = 'ERDPU';
$string['entity_name'] = 'Entity name';
$string['passport'] = 'Passport number';
$string['fio'] = 'Full name';
$string['inn'] = 'ІTN';
$string['data_valid'] = 'I confirm that all data entered is checked and valid';
$string['rules'] = 'By using the PayAllWays plugin you accept the <a href="https://expert.education/mod/page/view.php?id=113&forceview=1" target="_blank">rules</a>';

//errors
$string['nopermissionseditcourse'] = 'Sorry, you don\'t have permission to edit this course';
$string['paidinvalidparam'] = 'is_paid parameter must be bool';
$string['cost_error'] = 'Course cost must be numeric and greater than 0';
$string['empty_field'] = 'Field cannot be empty';
$string['account_error'] = 'Invalid bank card number (16 symbols, no spaces)';
$string['mfo_error'] = 'Incorrect MFO number (6 digits without spaces)';
$string['edrpu_error'] = 'ERDPU is invalid';
$string['iban_error'] = 'IBAN format: 2 letters + 27 digits without spaces';