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
 * @category tiny
 * @copyright  CTI <info@cursivetechnology.com>
 * @author eLearningstack
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/formslib.php');

class filterreportform extends moodleform {
    public function definition() {
        global $DB, $USER;
        $courses = $DB->get_records('course');
        $options = [];

        foreach ($courses as $course) {
            $options[$course->id] = $course->fullname;
        }
        $mform = &$this->_form;
        $mform->addElement('select', 'coursename', 'Course', $options);
        $mform->addRule('coursename', null, 'required', null, 'client');
    }
}
