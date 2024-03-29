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
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'cursive_json' => [
        'classname' => 'cursive_json_func_data',
        'methodname' => 'cursive_json_func',
        'classpath' => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => 'generate JSON',
        'type' => 'write',
        'ajax' => true,
    ],
    'cursive_get_user_list' => [
        'classname' => 'cursive_json_func_data',
        'methodname' => 'get_user_list',
        'classpath' => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => 'get quiz list by course',
        'type' => 'read',
        'ajax' => true,
    ],
    'cursive_get_module_list' => [
        'classname' => 'cursive_json_func_data',
        'methodname' => 'get_module_list',
        'classpath' => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => 'get quiz list by course',
        'type' => 'read',
        'ajax' => true,
    ],
    'cursive_reports' => [
        'classname' => 'cursive_json_func_data',
        'methodname' => 'cursive_reports_func',
        'classpath' => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => 'generate Reports for download',
        'type' => 'write',
        'ajax' => true,
    ],
    'cursive_approve_token' => [
        'classname' => 'cursive_json_func_data',
        'methodname' => 'cursive_approve_token_func',
        'classpath' => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => 'generate Reports for download',
        'type' => 'write',
        'ajax' => true,
    ],
    'cursive_user_comments' => [
        'classname' => 'cursive_json_func_data',
        'methodname' => 'cursive_user_comments_func',
        'classpath' => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => 'User Comments',
        'type' => 'write',
        'ajax' => true,
    ],
    'cursive_get_comment_link' => [
        'classname' => 'cursive_json_func_data',
        'methodname' => 'get_comment_link',
        'classpath' => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => ' Comments Links',
        'type' => 'write',
        'ajax' => true,
    ],
    'cursive_get_assign_comment_link' => [
        'classname' => 'cursive_json_func_data',
        'methodname' => 'get_assign_comment_link',
        'classpath' => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => ' Comments Links',
        'type' => 'write',
        'ajax' => true,
    ],
    'cursive_get_forum_comment_link' => [
        'classname' => 'cursive_json_func_data',
        'methodname' => 'get_forum_comment_link',
        'classpath' => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => ' Comments Links',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'tiny_cursive/cursive:view',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ],
    'cursive_get_assign_grade_comment' => [
        'classname' => 'cursive_json_func_data',
        'methodname' => 'get_assign_grade_comment',
        'classpath' => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => ' Comments Links',
        'type' => 'write',
        'ajax' => true,
    ],
    'cursive_user_list_submission_stats' => [
        'classname' => 'cursive_json_func_data',
        'methodname' => 'get_user_list_submission_stats',
        'classpath' => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => ' Comments Links',
        'type' => 'write',
        'ajax' => true,
    ],

    'cursive_filtered_writing' => [
        'classname' => 'cursive_json_func_data',
        'methodname' => 'cursive_filtered_writing_func',
        'classpath' => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => 'generate Reports for download',
        'type' => 'read',
        'ajax' => true,
    ],
];

// We define the services to install as pre-build services.
// A pre-build service is not editable by administrator.

$services = [
    'cursive_json_service' => [
        'functions' => [
            'cursive_json',
            'cursive_reports',
            'cursive_get_quizlist',
            'cursive_get_module_list',
            'cursive_user_comments',
            'cursive_get_comment_link',
            'cursive_user_list_submission_stats',
            'cursive_approve_token',
            'cursive_get_assign_comment_link',
            'cursive_get_forum_comment_link',
            'cursive_get_assign_grade_comment',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];



