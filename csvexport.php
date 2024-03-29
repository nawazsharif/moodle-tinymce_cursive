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

require(__DIR__ . '/../../../../../config.php');
require_login();
require_once($CFG->libdir . "/csvlib.class.php");

require_once('lib.php');

// Use csv_export_writer.

$courseid = optional_param('courseid', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$moduleid = optional_param('moduleid', 0, PARAM_INT);
global $CFG, $DB, $OUTPUT;
$report = [];
$headers = [
    'FullName',
    'Email',
    'CourseID',
    'total_time_seconds',
    'key_count',
    'keys_per_minute',
    'character_count',
    'characters_per_minute	',
    'word_count',
    'words_per_minute',
    'backspace_percent',
    'score',
    'copybehavior',
];
$exportcsv = new csv_export_writer('comma');
$exportcsv->set_filename("ExportUsersData");
$exportcsv->add_data($headers); // Add Header Row.

if ($courseid != 0) {
    $attempts = "SELECT
        uf.id as fileid, u.id as usrid,
 u.firstname,u.email,uf.courseid,
  sum(uw.total_time_seconds) as total_time,
  sum(uw.key_count) as key_count,
  avg(uw.keys_per_minute) as keys_per_minute,
  sum(uw.character_count)as character_count,
  avg(uw.characters_per_minute) as characters_per_minute,
  sum(uw.word_count) as word_count,
  avg(uw.words_per_minute) as words_per_minute,
  avg(uw.backspace_percent) as backspace_percent,
  avg(uw.score) as score,
  sum(uw.copy_behavior) as copybehavior
   FROM  {tiny_cursive_files} uf
   INNER JOIN {user} u ON uf.userid =u.id
   LEFT JOIN {tiny_cursive_user_writing} uw ON uw.file_id =uf.id
   WHERE uf.userid!=1 ";
    if ($userid != 0) {
        $attempts .= " AND  uf.userid = $userid";
    }
    if ($courseid != 0) {
        $attempts .= "  AND uf.courseid = $courseid";
    }

    if ($moduleid != 0) {
        $attempts .= "  AND uf.cmid = $moduleid";
    }
    $attempts .= " GROUP BY uf.userid;";
    $ress = $DB->get_records_sql($attempts);
    foreach ($ress as $key => $res) {
        if ($res != null) {
            $userrow = [
                $res->firstname . ' ' . $res->lastname,
                $res->email,
                $res->courseid,
                $res->total_time,
                $res->key_count,
                $res->keys_per_minute,
                $res->character_count,
                $res->characters_per_minute,
                $res->word_count,
                $res->words_per_minute,
                $res->backspace_percent,
                $res->score,
                $res->copybehavior,
                $usercourse->fullname,
            ];
            $exportcsv->add_data($userrow);
        }
    }
}

$exportcsv->download_file();
