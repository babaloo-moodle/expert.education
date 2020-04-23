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
 * Code that is executed before the tables and data are dropped during the plugin uninstallation.
 *
 * @package     availability_payallways
 * @category    upgrade
 * @copyright   <емейл1>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../managerlib.php');

/**
 * Custom uninstallation procedure.
 */
function xmldb_availability_payallways_uninstall() {
	global $DB;
	global $CFG;

	$sections = $DB->get_records_sql('SELECT * from ' . $CFG->prefix . 'course_sections where availability LIKE "%payallways%"');
	if($sections) {
		foreach ($sections as $sec) {
			if(contains_restriction('payallways', $sec->availability)) {
				$j = Restriction::do_decode_restriction($sec->availability);
				remove_restriction($j);
				$restriction = json_encode($j);
				update_db_restriction('course_sections', $restriction, $sec->id);
			}
		}
	}

	$modules = $DB->get_records_sql('SELECT * from ' . $CFG->prefix . 'course_modules where availability LIKE "%payallways%"');
	if($modules) {
		foreach ($modules as $mod) {
			if(contains_restriction('payallways', $mod->availability)) {
				$j = Restriction::do_decode_restriction($mod->availability);
				remove_restriction($j);
				$restriction = json_encode($j);
				update_db_restriction('course_sections', $restriction, $mod->id);
			}
		}
	}

	$dbman = $DB->get_manager();
    // Define field payallways_locked to be dropped from course_modules.
    $table = new xmldb_table('course_modules');
    $field = new xmldb_field('payallways_locked');

    // Conditionally launch drop field payallways_locked.
    if ($dbman->field_exists($table, $field)) {
        $dbman->drop_field($table, $field);
    }

    $table = new xmldb_table('course_sections');
    $field = new xmldb_field('payallways_locked');

    // Conditionally launch drop field payallways_locked.
    if ($dbman->field_exists($table, $field)) {
        $dbman->drop_field($table, $field);
    }

    return true;
}
