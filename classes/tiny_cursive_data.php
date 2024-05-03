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

namespace tiny_cursive;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/config.php');
require_login();
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/attemptlib.php');


use quiz;
use stdClass;

class tiny_cursive_data {

    public static function get_courses_users($params) {
        global $DB;
        $allusers = new stdClass();
        $allusers->userlist = [];
        $udetail = [];
        $udetail2 = [];
        $courseid = (int)$params['courseid'];
        $quizid = (int)$params['quizid'];
        $users = $DB->get_records_sql("SELECT ue.id as enrolid,u.id as id,u.firstname,u.lastname FROM {enrol} e
        INNER JOIN {user_enrolments} ue ON e.id = ue.enrolid
        INNER JOIN {user} u ON u.id = ue.userid WHERE e.courseid = :courseid AND u.id != 1", ['courseid' => $courseid]);
        $udetail2['id'] = 0;
        $udetail2['name'] = 'All Users';
        $allusers->userlist[] = $udetail2;
        foreach ($users as $user) {
            $udetail['id'] = $user->id;

            $udetail['name'] = $user->firstname . ' ' . $user->lastname;

            $allusers->userlist[] = $udetail;

        }
        return $allusers;
    }

    public static function get_courses_modules($params) {

        global $DB;
        $allusers = new stdClass();
        $allusers->userlist = [];

        $udetail = [];
        $udetail2 = [];

        $courseid = (int)$params['courseid'];

        $udetail2['id'] = 0;
        $udetail2['name'] = 'All Modules';
        $allusers->userlist[] = $udetail2;
        $modules = $DB->get_records_sql("SELECT id, instance  FROM {course_modules}
                     WHERE course = :courseid", ['courseid' => $courseid]);
        foreach ($modules as $cm) {
            $modinfo = get_fast_modinfo($courseid);
            $cm = $modinfo->get_cm($cm->id);
            $getmodulename = get_coursemodule_from_id($cm->modname, $cm->id, 0, false, MUST_EXIST);
            $udetail['id'] = $cm->id;
            $udetail['name'] = $getmodulename->name;
            $allusers->userlist[] = $udetail;
        }
        return $allusers;
    }
}
