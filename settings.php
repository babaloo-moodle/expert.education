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

include_once 'extended_settings_my.php';

if ($ADMIN->fulltree) {
   //--- settings ------------------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('availability_payallways_settings', '', get_string('pluginname_desc', 'availability_payallways')));
	$options = array(
        'legal_entity' => get_string('legalentity', 'availability_payallways'),
        'fop' => get_string('fop', 'availability_payallways')
    );
    $settings->add(new admin_setting_configselect('availability_payallways/billingentity', get_string('billingentity', 'availability_payallways'), get_string('billingentity_help', 'availability_payallways'), $options['legal_entity'], $options));
	
	if(get_config('availability_payallways', 'billingentity') == 'legal_entity') {
		$settings->add(new admin_setting_configtext_my('availability_payallways/legalentitypaymentaccount', get_string('paymentaccount', 'availability_payallways'), '', '', '/\d[5,15]/'));

		$settings->add(new admin_setting_configtext_my('availability_payallways/bankmfonum', get_string('bankmfo', 'availability_payallways'), '', '', '/\d[6]/'));

		$settings->add(new admin_setting_configtext_my('availability_payallways/edrpu', get_string('edrpu', 'availability_payallways'), '', ''));

		$settings->add(new admin_setting_configtext_my('availability_payallways/entity_name', get_string('entity_name', 'availability_payallways'), '', ''));

		$settings->add(new admin_setting_configtext_my('availability_payallways/iban', "IBAN", '', ''));

	}
	
	$settings->add(new admin_setting_configtext('availability_payallways/paymentnote', get_string('paymentnote', 'availability_payallways'), '', '', 0));

	$settings->add(new admin_setting_description('availability_payallways/valid', "", 'Используя плагин PayAllWays вы соглашаетесь с <a href="https://expert.education/mod/page/view.php?id=113&forceview=1" target="_blank">правилами</a>'));
}
