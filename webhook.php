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

// Set error log path.
ini_set('error_log', __DIR__ . '/lib/editor/tiny/plugins/cursive/my-errors.log');

ini_set('error_log', __DIR__ . '/lib/editor/tiny/plugins/cursive/my-errors.log');
require(__DIR__ . '/../../../../../config.php');
$errfile = __DIR__ . '/my-errors.log';
global $DB, $CFG, $SESSION, $PAGE;
$payload = @file_get_contents('php://input');
$event = null;
$payload = json_decode($payload, true);
try {
    $dataobj = (object)$payload;
    $dataobj->backspace_percent = round($dataobj->backspace_percent, 4);
    $table = 'tiny_cursive_user_writing';

    $sql = "INSERT INTO {tiny_cursive_user_writing}
    (file_id, total_time_seconds, key_count, keys_per_minute,character_count,characters_per_minute,
     word_count,words_per_minute,backspace_percent,score,copy_behavior)
VALUES ($dataobj->file_id,$dataobj->total_time_seconds,$dataobj->key_count,
$dataobj->keys_per_minute,$dataobj->character_count,$dataobj->characters_per_minute,
$dataobj->word_count,$dataobj->words_per_minute,$dataobj->backspace_percent,
$dataobj->score,$dataobj->copy_behavior)";
    $DB->execute($sql);
} catch (Exception $e) {
    debugging($e, 3, $errfile);
    exit();
}

$responsedata = [
    'status' => 'success',
    'message' => "Data saved successfully",
];

$responsejson = json_encode($responsedata);

http_response_code(200);
header('Content-Type: application/json');
echo $responsejson;
