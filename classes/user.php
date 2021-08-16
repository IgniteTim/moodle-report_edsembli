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
 * Log User account Actions
 *
 * @package    report_edsembli
 * @author     Tim Martinez <tim.martinez@ignitecentre.ca>
 * @copyright  2021 Tim Martinez <tim.martinez@ignitecentre.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_edsembli;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->dirroot/report/edsembli/constants.php");

/**
 * Description of user
 *
 * @author Tim Martinez <tim.martinez@ignitecentre.ca>
 */
class user extends base {

    /**
     * The default id for the data
     * @var string
     */
    protected static $default_id = 'userid';

    /**
     * The name for the log
     * @var string
     */
    protected static $item_name = 'user';

    /**
     * Prepare the data to be stored
     * @param int $id
     * @param int $result
     * @param int $timestamp
     * @param \stdClass $userdata
     * @param string $action
     * @return \stdClass
     */
    protected static function prepare_data($userid, $result, $timestamp, $userdata, $action) {
        $data = parent::prepare_data($userid, $result, $timestamp, $userdata, $action);

        $data->data->id = $userdata->id;
        $data->data->idnumber = $userdata->idnumber;
        $data->data->username = $userdata->username;
        if (isset($userdata->password_clear)) {
            $data->data->password = $userdata->password_clear;
        }
        $data->data->suspended = $userdata->suspended;
        $data->data->email = $userdata->email;
        $data->data->firstname = $userdata->firstname;
        $data->data->lastname = $userdata->lastname;
        if (isset($userdata->exception)) {
            $data->data->exception = $userdata->exception;
        }
        //JSON encode the data
        $data->data = \json_encode($data->data);

        return $data;
    }

}
