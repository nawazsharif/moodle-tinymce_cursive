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

$observers = [
    [
        'eventname' => '\mod_forum\event\post_created',
        'callback' => '\tiny_cursive\observers::observer_login',
        'internal' => true,
        'priority' => 9999,
    ],
    [
        'eventname' => '\mod_forum\event\post_updated',
        'callback' => '\tiny_cursive\observers::post_updated',
        'internal' => true,
        'priority' => 9999,
    ],
    [
        'eventname' => '\mod_forum\event\discussion_created',
        'callback' => '\tiny_cursive\observers::discussion_created',
        'internal' => true,
        'priority' => 9999,
    ],
    [
        'eventname' => '\mod_assign\event\submission_created',
        'callback' => '\tiny_cursive\observers::submission_created',
        'internal' => true,
        'priority' => 9999,
    ],
    [
        'eventname' => '\mod_assign\event\assessable_submitted',
        'callback' => '\tiny_cursive\observers::assessable_submitted',
        'internal' => true,
        'priority' => 9999,
    ],
];

