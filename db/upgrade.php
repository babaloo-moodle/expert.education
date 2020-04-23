<?php
 
function xmldb_availability_payallways_upgrade($oldversion) {
    global $CFG;
	global $DB;
	
    $result = TRUE;
 
    if ($oldversion < 2019110520) {
		$dbman = $DB->get_manager(); 
		
        // Define table availability_payallways to be created.
        $table = new xmldb_table('availability_payallways');

        // Adding fields to table availability_payallways.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);

        // Adding keys to table availability_payallways.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for availability_payallways.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

		// Define table availability_payallways_ci to be created.
        $table = new xmldb_table('availability_payallways_ci');

        // Adding fields to table availability_payallways_ci.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('card_number', XMLDB_TYPE_CHAR, '16', null, null, null, null);
        $table->add_field('operating_mode', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('course_closed', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');

        // Adding keys to table availability_payallways_ci.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('courseidfk', XMLDB_KEY_FOREIGN, ['course_id'], 'course', ['id']);

        // Conditionally launch create table for availability_payallways_ci.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


        // Payallways savepoint reached.
        upgrade_plugin_savepoint(true, 2019110520, 'availability', 'payallways');
		
    }
	
	if ($oldversion < 2019110720) {
		$dbman = $DB->get_manager();
        
		// Define field course_closed to be added to availability_payallways_ci.
        $table = new xmldb_table('availability_payallways_ci');
        $field = new xmldb_field('course_closed', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'operating_mode');

        // Conditionally launch add field course_closed.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }


        // Payallways savepoint reached.
        upgrade_plugin_savepoint(true, 2019110720, 'availability', 'payallways');
    }

    if ($oldversion < 2019110730) {
        $dbman = $DB->get_manager();
        
        // Define field course_closed to be added to availability_payallways_ci.
        $table = new xmldb_table('availability_payallways_ci');
        $field = new xmldb_field('section_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'course_closed');

        // Conditionally launch add field section_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('availability_payallways_ci');
        $field = new xmldb_field('activity_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'section_id');

        // Conditionally launch add field activity_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }


        // Payallways savepoint reached.
        upgrade_plugin_savepoint(true, 2019110730, 'availability', 'payallways');
    }

    if ($oldversion < 2020012010) {
        $dbman = $DB->get_manager();

        // Define field id to be added to availability_payallways_ci.
        $table = new xmldb_table('availability_payallways_ci');
        $field = new xmldb_field('author_name', XMLDB_TYPE_TEXT, null, null, null, null, null, 'activity_id');

        // Conditionally launch add field author_name.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('availability_payallways_ci');
        $field = new xmldb_field('passport', XMLDB_TYPE_TEXT, null, null, null, null, null, 'author_name');

        // Conditionally launch add field passport.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('availability_payallways_ci');
        $field = new xmldb_field('edrpu', XMLDB_TYPE_TEXT, null, null, null, null, null, 'passport');

        // Conditionally launch add field edrpu.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('availability_payallways_ci');
        $field = new xmldb_field('iban', XMLDB_TYPE_TEXT, null, null, null, null, null, 'edrpu');

        // Conditionally launch add field iban.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Payallways savepoint reached.
        upgrade_plugin_savepoint(true, 2020012010, 'availability', 'payallways');
    }

    if ($oldversion < 2020021080) {
        $dbman = $DB->get_manager();

        // Define field id to be added to availability_payallways_ci.
        $table = new xmldb_table('availability_payallways_ci');
        $field = new xmldb_field('inn', XMLDB_TYPE_TEXT, null, null, null, null, null, 'iban');

        // Conditionally launch add field author_name.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2020021080, 'availability', 'payallways');
    }




 
    return $result;
}
?>