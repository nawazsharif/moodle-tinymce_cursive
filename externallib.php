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

use tiny_cursive\tiny_cursive_data;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once('locallib.php');



class cursive_json_func_data extends external_api {
    public static function cursive_json_func_is_allowed_from_ajax() {
        return true;
    }

    public static function cursive_reports_func_is_allowed_from_ajax() {
        return true;
    }

    // Service for quizzes list.
    public static function get_user_list_is_allowed_from_ajax() {
        return true;
    }
    public static function get_user_list_returns() {
        return new external_value(PARAM_RAW, 'All quizzes');
    }

    public static function get_user_list_parameters() {
        return new external_function_parameters(
            [
                'page' => new external_value(PARAM_INT, '', false),
                'courseid' => new external_value(PARAM_INT, 'Course id', false, 'course_detail'),
            ]
        );
    }

    public static function get_user_list($page, $courseid) {
        require_login();
        $params = self::validate_parameters(
            self::get_user_list_parameters(),
            [
                'page' => $page,
                'courseid' => $courseid,
            ]
        );
        return json_encode(tiny_cursive_data::get_courses_users($params));
    }

    // Service for quizzes list.
    public static function get_module_list_is_allowed_from_ajax() {
        return true;
    }

    public static function get_module_list_returns() {
        return new external_value(PARAM_RAW, 'All quizzes');
    }

    public static function get_module_list_parameters() {
        return new external_function_parameters(
            [
                'page' => new external_value(PARAM_INT, '', false),
                'courseid' => new external_value(PARAM_INT, 'Course id', false, 'course_detail'),
            ]
        );
    }

    public static function get_module_list($page, $courseid) {
        require_login();
        $params = self::validate_parameters(
            self::get_user_list_parameters(),
            [
                'page' => $page,
                'courseid' => $courseid,
            ]
        );
        return json_encode(tiny_cursive_data::get_courses_modules($params));
    }


    public static function cursive_json_func_returns() {
        return new external_value(PARAM_RAW, 'result');
    }

    public static function cursive_reports_func_returns() {
        return new external_value(PARAM_RAW, 'result');
    }

    public static function cursive_json_func_parameters() {
        return new external_function_parameters(
            [
                'resourceId' => new external_value(PARAM_INT, 0, 'resourceId'),
                'key' => new external_value(PARAM_RAW, 'key detail', false, 'key'),
                'keyCode' => new external_value(PARAM_RAW, 'key code ', false, 'keycode'),
                'event' => new external_value(PARAM_RAW, 'event', false, 'event'),
                'cmid' => new external_value(PARAM_INT, 0, 'cmid'),
                'modulename' => new external_value(PARAM_RAW, 'quiz', 'modulename'),
                'editorid' => new external_value(PARAM_TEXT, 'editorid', false, 'editorid'),
            ]
        );
    }

    public static function cursive_reports_func_parameters() {
        return new external_function_parameters(
            [
                'coursename' => new external_value(PARAM_INT, 0, 'coursename'),
                'quizname' => new external_value(PARAM_RAW, 'quizname detail', false, 'quizname'),
                'username' => new external_value(PARAM_RAW, 'username detail ', false, 'username'),
            ]
        );
    }

    public static function cursive_json_func(
        $resourceid = 0,
        $key = null,
        $keycode = null,
        $event = 'keyUp',
        $cmid = 0,
        $modulename = 'quiz',
        $editorid = null
    ) {
        require_login();

        global $USER, $SESSION, $DB, $CFG;
        require_once($CFG->libdir. '/filestorage/file_storage.php');

        $params = self::validate_parameters(
            self::cursive_json_func_parameters(),
            [
                'resourceId' => $resourceid,
                'key' => $key,
                'keyCode' => $keycode,
                'event' => $event,
                'cmid' => $cmid,
                'modulename' => $modulename,
                'editorid' => $editorid,
            ]
        );
        $courseid = 0;
        if ($resourceid == 0) {
            $resourceid = $cmid;
        }
        $userdata = ['resourceId' => $resourceid, 'key' => $key, 'keyCode' => $keycode, 'event' => $event];
        if ($cmid) {
            $cm = $DB->get_record('course_modules', ['id' => $cmid]);
            $userdata["courseId"] = $cm->course;
            $courseid = $cm->course;
        } else {
            $userdata["courseId"] = 0;
        }

        $timearr = explode('.', microtime("now") * 1000);
        $timestampinmilliseconds = $timearr[0];
        $userdata['unixTimestamp'] = $timestampinmilliseconds;
        $userdata["clientId"] = $CFG->wwwroot;
        $userdata["personId"] = $USER->id;
        $editoridarr = explode(':', $editorid);
        if (count($editoridarr) > 1) {
            $uniqueid = substr($editoridarr[0] . "\n", 1);
            $slot = substr($editoridarr[1] . "\n", 0, -11);
            $quba = question_engine::load_questions_usage_by_activity($uniqueid);
            $question = $quba->get_question($slot, false);
            $questionid = $question->id;
        }
        $dirname = __DIR__ . '/userdata/';
        $fname = $USER->id . '_' . $resourceid . '_' . $cmid . '_attempt' . '.json';
        if ($questionid) {
            $fname = $USER->id . '_' . $resourceid . '_' . $cmid . '_' . $questionid . '_attempt' . '.json';
        }
        $filename = __DIR__ . '/userdata/' . $fname;
        // Insert in database.

        $table = 'tiny_cursive_files';

        if (!file_exists($dirname)) {
            mkdir($dirname, 0755);
        }
        $inp = file_get_contents($filename);
        $temparray = null;
        if ($inp) {
            $temparray = json_decode($inp, true);
            array_push($temparray, $userdata);
            $filerec = $DB->get_record($table, ['cmid' => $cmid, 'modulename' => $modulename, 'userid' => $USER->id]);
            if ($questionid) {
                $filerec = $DB->get_record($table, [
                    'cmid' => $cmid,
                    'modulename' => $modulename,
                    'userid' => $USER->id,
                    'questionid' => $questionid,
                ]);
            }
            $filerec->uploaded = 0;
            $DB->update_record($table, $filerec);
        } else {
            $temparray[] = $userdata;
            $dataobj = new stdClass();
            $dataobj->userid = $USER->id;
            $dataobj->resourceid = $resourceid;
            $dataobj->cmid = $cmid;
            $dataobj->modulename = $modulename;
            $dataobj->courseid = $courseid;
            $dataobj->timemodified = time();
            $dataobj->filename = $fname;
            $dataobj->questionid = $questionid ?? 0;
            $dataobj->uploaded = 0;
            $DB->insert_record($table, $dataobj);
        }

        $jsondata = json_encode($temparray);

        if (is_array($temparray)) {
            file_put_contents($filename, $jsondata);
        }
        return $filename;
    }

    public static function cursive_reports_func($coursename = 0, $quizname = null, $username = 'keyUp') {
        require_login();
        global $USER, $SESSION, $DB;
        $params = self::validate_parameters(
            self::cursive_reports_func_parameters(),
            [
                'resourceId' => $coursename,
                'key' => $quizname,
                'keyCode' => $username,
            ]
        );
        return "cursive reports";
    }

    // User comments store.
    public static function cursive_user_comments_func_is_allowed_from_ajax() {
        return true;
    }

    public static function cursive_user_comments_func_returns() {
        return new external_value(PARAM_RAW, 'All User Comments');
    }

    public static function cursive_user_comments_func_parameters() {
        return new external_function_parameters(
            [
                'modulename' => new external_value(PARAM_TEXT, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid'),
                'resourceid' => new external_value(PARAM_INT, 'resourceid'),
                'courseid' => new external_value(PARAM_INT, 'courseid'),
                'usercomment' => new external_value(PARAM_TEXT, 'usercomment'),
                'timemodified' => new external_value(PARAM_INT, 'timemodified'),
                'editorid' => new external_value(PARAM_TEXT, 'editorid'),
            ]
        );
    }

    public static function cursive_user_comments_func(
        $modulename,
        $cmid,
        $resourceid,
        $courseid,
        $usercomment,
        $timemodified,
        $editorid
    ) {
        global $DB, $USER;
        require_login();
        $userid = $USER->id;
        $editorid;
        $editoridarr = explode(':', $editorid);
        if (count($editoridarr) > 1) {
            $uniqueid = substr($editoridarr[0] . "\n", 1);
            $slot = substr($editoridarr[1] . "\n", 0, -11);
            $quba = question_engine::load_questions_usage_by_activity($uniqueid);
            $question = $quba->get_question($slot, false);
            $questionid = $question->id;
            $questionid;
        }
        $dataobject = new stdClass();
        $dataobject->userid = $userid;
        $dataobject->cmid = $cmid;
        $dataobject->modulename = $modulename;
        $dataobject->resourceid = $resourceid;
        $dataobject->courseid = $courseid;
        $dataobject->questionid = $questionid;
        $dataobject->usercomment = $usercomment;
        $dataobject->timemodified = $timemodified;

        try {
            $DB->insert_record('tiny_cursive_comments', $dataobject);
        } catch (Exception $e) {
            echo $e;
            die("error occored");
        }
    }

    public static function cursive_approve_token_func_is_allowed_from_ajax() {
        return true;
    }

    public static function cursive_approve_token_func_returns() {
        return new external_value(PARAM_RAW, 'Token Approved');
    }

    public static function cursive_approve_token_func_parameters() {
        return new external_function_parameters(
            [
                'token' => new external_value(PARAM_TEXT, 'userid'),
            ]
        );
    }

    public static function cursive_approve_token_func($token) {
        global $DB, $CFG;
        require_login();
        $remoteurl = get_config('tiny_cursive', 'python_server');
        $remoteurl = $remoteurl . '/verify-token';
        $moodleurl = $CFG->wwwroot;

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $remoteurl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ["token" => $token, "moodle_url" => $moodleurl]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'X-Moodle-Url:' . $moodleurl,
                'Content-Type: multipart/form-data',
                'Accept:application/json',
            ]);
            $result = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $result;
    }

    // Service for assignment comment list.
    public static function get_comment_link_is_allowed_from_ajax() {
        return true;
    }

    public static function get_comment_link_returns() {
        return new external_value(PARAM_RAW, 'Comment Link');
    }

    public static function get_comment_link_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
                'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid', false, 'cmid'),
                'questionid' => new external_value(PARAM_RAW, 'questionid', false, 'questionid'),
                'userid' => new external_value(PARAM_RAW, 'userid', false, 'questionid'),
            ]
        );
    }

    public static function get_comment_link($id, $modulename, $cmid = null, $questionid = null, $userid = null) {
        require_once('../../config.php');
        global $DB, $CFG;
        require_once($CFG->dirroot . '/lib/accesslib.php');
        require_once($CFG->dirroot . '/question/lib.php');
        require_login();
        $params = self::validate_parameters(
            self::get_comment_link_parameters(),
            [
                'id' => $id,
                'modulename' => $modulename,
                'cmid' => $cmid,
                'questionid' => $questionid,
                'userid' => $userid,
            ]
        );
        if ($modulename == 'quiz') {
            $data['filename'] = '';
            $conditions = ["resourceid" => $id, "cmid" => $cmid, "questionid" => $questionid, 'userid' => $userid];
            $table = 'tiny_cursive_comments';
            $recs = $DB->get_records($table, $conditions);

            $filename = $DB->get_record_sql('select filename from {tiny_cursive_files} where resourceid = :resourceid
AND cmid = :cmid
AND modulename = :modulename AND questionid=:questionid AND userid=:userid ',
                [
                    'resourceid' => $id,
                    'cmid' => $cmid,
                    'modulename' => $modulename,
                    'questionid' => $questionid, "userid" => $userid]);

            $data['filename'] = $filename->filename;
            $data['questionid'] = $questionid;

            $usercomment = [];
            if ($recs) {
                foreach ($recs as $key => $rec) {
                    array_push($usercomment, $rec);
                }
                return json_encode(['usercomment' => $usercomment, 'data' => $data]);

            } else {
                return json_encode(['usercomment' => 'comments', 'data' => $data]);
            }
        } else {
            $conditions = ["resourceid" => $id];
            $table = 'tiny_cursive_comments';
            $recs = $DB->get_records($table, $conditions);

            $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
        uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , uf.modulename,uf.userid, uf.filename
FROM {tiny_cursive_user_writing} uw
        INNER JOIN {tiny_cursive_files} uf
            ON uw.file_id =uf.id
where uf.resourceid = $id
  AND uf.cmid = $cmid
  AND uf.modulename='" . $modulename . "'";
            $data = $DB->get_record_sql($attempts);

            if (!isset($data->filename)) {
                $filename = $DB->get_record_sql('select filename from {tiny_cursive_files} where resourceid = :resourceid
  AND cmid = :cmid
  AND modulename = :modulename', ['resourceid' => $id, 'cmid' => $cmid, 'modulename' => $modulename]);

                $data['filename'] = $filename->filename;
            }

            $usercomment = [];
            if ($recs) {
                foreach ($recs as $key => $rec) {
                    array_push($usercomment, $rec);
                }
                return json_encode(['usercomment' => $usercomment, 'data' => $data]);

            } else {
                return json_encode(['usercomment' => 'comments', 'data' => $data]);
            }
        }
    }


    public static function get_forum_comment_link_is_allowed_from_ajax() {
        return true;
    }

    public static function get_forum_comment_link_returns() {
        return new external_value(PARAM_RAW, 'Comment Link');
    }

    public static function get_forum_comment_link_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
                'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
                'cmid' => new external_value(PARAM_RAW, 'cmid', false, 'cmid'),
            ]
        );
    }

    public static function get_forum_comment_link($id, $modulename, $cmid = null) {
        require_once('../../config.php');
        global $DB, $CFG;
        require_once($CFG->dirroot . '/lib/accesslib.php');
        require_once($CFG->dirroot . '/question/lib.php');
        require_login();
        $params = self::validate_parameters(
            self::get_comment_link_parameters(),
            [
                'id' => $id,
                'modulename' => $modulename,
                'cmid' => $cmid,
            ]
        );

        $conditions = ["resourceid" => $id];
        $table = 'tiny_cursive_comments';
        $recs = $DB->get_records($table, $conditions);

        $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
    uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , uf.modulename,uf.userid, uf.filename
FROM {tiny_cursive_user_writing} uw
    INNER JOIN {tiny_cursive_files} uf
        ON uw.file_id =uf.id
where uf.resourceid = $id
AND uf.cmid = $cmid
AND uf.modulename='" . $modulename . "'";
        $data = $DB->get_record_sql($attempts);

        if (!isset($data->filename)) {
            $filename = $DB->get_record_sql('select filename from {tiny_cursive_files} where resourceid = :resourceid
AND cmid = :cmid
AND modulename = :modulename', ['resourceid' => $id, 'cmid' => $cmid, 'modulename' => $modulename]);

            $data['filename'] = $filename->filename;
        }

        $usercomment = [];
        if ($recs) {
            foreach ($recs as $key => $rec) {
                array_push($usercomment, $rec);
            }
            return json_encode(['usercomment' => $usercomment, 'data' => $data]);

        } else {
            return json_encode(['usercomment' => 'comments', 'data' => $data]);
        }

    }

    public static function get_quiz_comment_link_is_allowed_from_ajax() {
        return true;
    }

    public static function get_quiz_comment_link_returns() {
        return new external_value(PARAM_RAW, 'Comment Link');
    }

    public static function get_quiz_comment_link_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
                'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid', false, 'cmid'),
                'questionid' => new external_value(PARAM_INT, 'questionid', false, 'questionid'),
            ]
        );
    }

    public static function get_quiz_comment_link(
        $id,
        $modulename,
        $cmid = null,
        $questionid = null
    ) {
        require_once('../../config.php');
        global $DB, $CFG;
        require_once($CFG->dirroot . '/lib/accesslib.php');
        require_once($CFG->dirroot . '/question/lib.php');
        require_login();
        $params = self::validate_parameters(
            self::get_comment_link_parameters(),
            [
                'id' => $id,
                'modulename' => $modulename,
                'cmid' => $cmid,
            ]
        );
        if ($modulename == 'quiz') {
            $conditions = ["resourceid" => $id, "cmid" => $cmid, "questionid" => $questionid];
            $table = 'tiny_cursive_comments';
            $recs = $DB->get_records($table, $conditions);

            $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
        uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , uf.modulename,uf.userid, uf.filename
FROM {tiny_cursive_user_writing} uw
        INNER JOIN {tiny_cursive_files} uf
            ON uw.file_id =uf.id
where uf.resourceid = $id
  AND uf.cmid = $cmid
  AND uf.modulename='" . $modulename . "'";
            $data = $DB->get_record_sql($attempts);

            if (!isset($data->filename)) {
                $filename = $DB->get_record_sql('select filename from {tiny_cursive_files} where resourceid = :resourceid
  AND cmid = :cmid
  AND modulename = :modulename', ['resourceid' => $id, 'cmid' => $cmid, 'modulename' => $modulename]);

                $data['filename'] = $filename->filename;
            }

        } else {
            $conditions = ["resourceid" => $id];
            $table = 'tiny_cursive_comments';
            $recs = $DB->get_records($table, $conditions);

            $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
        uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , uf.modulename,uf.userid, uf.filename
FROM {tiny_cursive_user_writing} uw
        INNER JOIN {tiny_cursive_files} uf
            ON uw.file_id =uf.id
where uf.resourceid = $id
  AND uf.cmid = $cmid
  AND uf.modulename='" . $modulename . "'";
            $data = $DB->get_record_sql($attempts);

            if (!isset($data->filename)) {
                $filename = $DB->get_record_sql('select filename from {tiny_cursive_files} where resourceid = :resourceid
  AND cmid = :cmid
  AND modulename = :modulename', ['resourceid' => $id, 'cmid' => $cmid, 'modulename' => $modulename]);

                $data['filename'] = $filename->filename;
            }
        }
        $usercomment = [];
        if ($recs) {
            foreach ($recs as $key => $rec) {
                array_push($usercomment, $rec);
            }
            return json_encode(['usercomment' => $usercomment, 'data' => $data]);

        } else {
            return json_encode(['usercomment' => 'comments', 'data' => $data]);
        }
    }


    public static function get_assign_comment_link_is_allowed_from_ajax() {
        return true;
    }

    public static function get_assign_comment_link_returns() {
        return new external_value(PARAM_RAW, 'Comment Link');
    }

    public static function get_assign_comment_link_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
                'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid', false, 'course_detail'),
            ]
        );
    }

    public static function get_assign_comment_link($id, $modulename, $cmid) {
        global $DB;
        require_login();
        $params = self::validate_parameters(
            self::get_assign_comment_link_parameters(),
            [
                'id' => $id,
                'modulename' => $modulename,
                'cmid' => $cmid,
            ]
        );
        $recassignsubmission = $DB->get_record('assign_submission', ['id' => $id], '*', false);
        $userid = $recassignsubmission->userid;
        $conditions = ["userid" => $userid, 'modulename' => $modulename, 'cmid' => $cmid];
        $table = 'tiny_cursive_comments';
        $recs = $DB->get_records($table, $conditions);
        $usercomment = [];
        if ($recs) {
            foreach ($recs as $rec) {
                array_push($usercomment, $rec);
            }
            return json_encode($usercomment);

        } else {
            return json_encode([['usercomment' => 'comments']]);
        }
    }

    // Submissions stats modal get_user_submissions_data.
    public static function get_assign_grade_comment_is_allowed_from_ajax() {
        return true;
    }

    public static function get_assign_grade_comment_returns() {
        return new external_value(PARAM_RAW, 'Comment Link');
    }

    public static function get_assign_grade_comment_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
                'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid', false, 'course_detail'),
            ]
        );
    }

    public static function get_assign_grade_comment($id, $modulename, $cmid) {
        global $DB;
        require_login();
        $params = self::validate_parameters(
            self::get_assign_comment_link_parameters(),
            [
                'id' => $id,
                'modulename' => $modulename,
                'cmid' => $cmid,
            ]
        );

        $conditions = ["userid" => $id, 'modulename' => $modulename, 'cmid' => $cmid];
        $table = 'tiny_cursive_comments';
        $recs = $DB->get_records($table, $conditions);
        $attempts = "SELECT  uw.total_time_seconds ,uw.word_count ,uw.words_per_minute,
        uw.backspace_percent,uw.score,uw.copy_behavior,uf.resourceid , uf.modulename,uf.userid, uf.filename
FROM {tiny_cursive_user_writing} uw
        INNER JOIN {tiny_cursive_files} uf
            ON uw.file_id =uf.id
where uf.userid = $id
  AND uf.cmid = $cmid
  AND uf.modulename='" . $modulename . "'";
        $data = $DB->get_record_sql($attempts);
        if (!isset($data->filename)) {
            $filename = $DB->get_record_sql('select filename from {tiny_cursive_files} where resourceid = :resourceid
  AND cmid = :cmid
  AND modulename = :modulename', ['resourceid' => $id, 'cmid' => $cmid, 'modulename' => $modulename]);

            $data['filename'] = $filename->filename;
        }
        $usercomment = [];
        if ($recs) {
            foreach ($recs as $key => $rec) {
                array_push($usercomment, $rec);
            }
            return json_encode(['usercomment' => $usercomment, 'data' => $data]);

        } else {
            return json_encode(['usercomment' => 'comments', 'data' => $data]);
        }
    }

    public static function get_user_list_submission_stats_is_allowed_from_ajax() {
        return true;
    }

    public static function get_user_list_submission_stats_returns() {
        return new external_value(PARAM_RAW, 'Comment Link');
    }

    public static function get_user_list_submission_stats_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', false, 'course_detail'),
                'modulename' => new external_value(PARAM_TEXT, 'modulename', false, 'modulename'),
                'cmid' => new external_value(PARAM_INT, 'cmid', false, 'course_detail'),
                'filename' => new external_value(PARAM_TEXT, 'filename', false),
            ]
        );
    }

    public static function get_user_list_submission_stats($id, $modulename, $cmid) {
        global $DB;
        require_login();
        $params = self::validate_parameters(
            self::get_user_list_submission_stats_parameters(),
            [
                'id' => $id,
                'modulename' => $modulename,
                'cmid' => $cmid,
            ]
        );
        $rec = get_user_submissions_data($id, $modulename, $cmid);

        return json_encode($rec);
    }

    public static function cursive_filtered_writing_func_is_allowed_from_ajax() {
        return true;
    }

    public static function cursive_filtered_writing_func_returns() {
        return new external_value(PARAM_RAW, 'Comment Link');
    }

    public static function cursive_filtered_writing_func_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_TEXT, 'id', false, 'id'),
            ]
        );
    }

    public static function cursive_filtered_writing_func($id) {
        global $DB, $USER;
        require_login();
        $userid = $USER->id;
        $params = self::validate_parameters(
            self::cursive_filtered_writing_func_parameters(),
            [
                'id' => $id,
            ]
        );
        $attempts = "SELECT qa.resourceid as attemptid,qa.timemodified,uw.score,uw.copy_behavior, u.id as userid,
       u.firstname, u.lastname, u.email,  qa.cmid as cmid ,qa.courseid,qa.filename,uw.word_count,
       uw.words_per_minute , uw.total_time_seconds ,uw.backspace_percent FROM {user} u
        INNER JOIN {tiny_cursive_files} qa ON u.id = qa.userid
        LEFT JOIN {tiny_cursive_user_writing} uw ON qa.id = uw.file_id
        WHERE qa.userid!=1";

        if ($userid != 0) {
            $attempts .= " AND  qa.userid = $userid";
        }
        if ($id != 0) {
            $attempts .= "  AND qa.courseid=$id";
        }
        $res = $DB->get_records_sql($attempts);
        $recs = [];
        foreach ($res as $key => $value) {
            $value->timemodified = date("l jS \of F Y h:i:s A", $value->timemodified);
            $value->icon = 'fa fa-circle-o';
            $value->color = 'grey';
            array_push($recs, $value);
        }
        $resncount = ['count' => $totalcount, 'data' => $recs];
        return json_encode($resncount);
    }
}


