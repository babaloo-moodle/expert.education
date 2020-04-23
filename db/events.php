<?php

    $observers = array(
 
            array(
                'eventname'   => '\core\event\course_module_updated',
                'callback'    => 'availability_payallways_observer::ttest',
            ),
            array(
                'eventname'   => '\core\event\course_section_updated',
                'callback'    => 'availability_payallways_observer::ttest1',
            ),
            array(
                'eventname'   => '\core\event\course_module_created',
                'callback'    => 'availability_payallways_observer::module_created',
            ),
            array(
                'eventname'   => '\core\event\course_section_created',
                'callback'    => 'availability_payallways_observer::section_created',
            ),
         
        );
