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
 * Base class for EdSembli logging
 *
 * @package    report_edsembli
 * @author     Tim Martinez <tim.martinez@ignitecentre.ca>
 * @copyright  2021 Tim Martinez <tim.martinez@ignitecentre.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_edsembli;

defined('MOODLE_INTERNAL') || die();


global $CFG;
require_once("$CFG->dirroot/report/edsembli/classes/user.php");
require_once("$CFG->dirroot/report/edsembli/constants.php");

/**
 * Base class for EdSembli logging
 *
 * @author Tim Martinez <tim.martinez@ignitecentre.ca>
 */
class base {

    /**
     * Prepare the data to be stored
     * @param int $id
     * @param int $result
     * @param int $timestamp
     * @param \stdClass $itemdata
     * @param string $action
     * @return \stdClass
     */
    protected static function prepare_data($id, $result, $timestamp, $itemdata, $action) {
        $data = new \stdClass();
        $default_id = static::$default_id;
        $data->$default_id = $id;
        $data->result = $result;
        $data->action = $action;
        $data->timecreated = $timestamp;

        $data->data = new \stdClass();

        return $data;
    }

    /**
     * Create item add log entry
     * @global \moodle_database $DB
     * @param int $coursecategoryid
     * @param int $result
     * @param int $timestamp
     * @param \stdClass $userdata
     */
    public static function create($id, $result, $timestamp, $itemdata) {
        $data = static::prepare_data($id, $result, $timestamp, $itemdata, static::$item_name . '_create');

        self::save($data, $result);
    }

    /**
     * Update item add log entry
     * @global \moodle_database $DB
     * @param int $id
     * @param int $result
     * @param int $timestamp
     * @param stdClass $itemdata
     */
    public static function update($id, $result, $timestamp, $itemdata) {
        $data = static::prepare_data($id, $result, $timestamp, $itemdata, static::$item_name . '_update');

        self::save($data, $result);
    }

    /**
     * Create/Update function
     * @param int $id
     * @param int $result
     * @param int $timestamp
     * @param \stdClass $itemdata
     */
    public static function create_update($id, $result, $timestamp, $itemdata) {
        if ($id == 0 || (isset($itemdata->id) && $itemdata->id == 0)) {
            self::create($id, $result, $timestamp, $itemdata);
        } else {
            self::update($id, $result, $timestamp, $itemdata);
        }
    }

    /**
     * Get Item info
     * @global \moodle_database $DB
     * @param int $id
     * @param int $result
     * @param int $timestamp
     * @param \stdClass $itemdata
     */
    public static function get($id, $result, $timestamp, $itemdata) {
        $data = static::prepare_data($id, $result, $timestamp, $itemdata, static::$item_name . '_get');

        self::save($data, $result);
    }

    /**
     * Save log
     * @global \moodle_database $DB
     * @param \stdClass $data
     * @param int $result
     */
    public static function save($data, $result) {
        global $DB, $CFG;

        $data->id = $DB->insert_record('report_edsembli', $data);
    }

}
