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
 * Table definition for the EdSembli Synchronization Log.
 *
 * @package    report_edsembli
 * @author     Tim Martinez <tim.martinez@ignitecentre.ca>
 * @copyright  2021 Tim Martinez <tim.martinez@ignitecentre.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_edsembli;

defined('MOODLE_INTERNAL') || die();

/**
 * Table definition for the EdSembli Synchronization Log.
 *
 * @author Tim Martinez <tim.martinez@ignitecentre.ca>
 */
class table extends \flexible_table {
    private static $date_format = 'Y-m-d g:i:s a';

    private $perpage = 30;

    function __construct($perpage) {
        global $PAGE;
        parent::__construct('report-edsembli');

        $url = $PAGE->url;

        $this->perpage = $perpage;
        $this->set_attribute('class', 'report_edsembli');
        $this->define_baseurl($url);
        $this->define_table_columns();
        $this->sortable(false);
    }

    /*
     * Set up the headers for the table
     */

    protected function define_table_columns() {
        $cols = array();

        $cols['detail'] = '';
        $cols['timecreated'] = get_string('col_timecreated', 'report_edsembli');
        $cols['course'] = get_string('col_course', 'report_edsembli');
        $cols['user'] = get_string('col_user', 'report_edsembli');
        $cols['action'] = get_string('col_action', 'report_edsembli');
        $cols['result'] = get_string('col_result', 'report_edsembli');
        $this->define_columns(array_keys($cols));
        $this->define_headers(array_values($cols));
    }

    /*
     * Add the data to the table
     * 
     * @returns bool Set to true if there's data and false if there's none.
     */

    public function setrowdata() {
        global $DB, $PAGE, $OUTPUT;

        $sql = 'SELECT re.id,u.firstname,u.lastname,u.idnumber,c.fullname as course, re.action, re.result, re.timecreated, re.data 
            FROM {report_edsembli} re 
            LEFT JOIN {user} u ON re.userid = u.id 
            LEFT JOIN {course} c ON re.courseid = c.id 
            ORDER BY re.timecreated DESC';
        $logs = $DB->get_records_sql($sql, null, $this->get_page_start(), $this->get_page_size());

        
        foreach($logs as $entry) {
            $row = array();
            $row['detail'] = $this->col_detail($entry);
            $row['timecreated'] = \date(self::$date_format, $entry->timecreated);
            $row['course'] = $entry->course;
            if ($entry->firstname) {
                $row['user'] = $entry->firstname . ' ' . $entry->lastname;
            } else {
                $row['user'] = '';
            }
            $row['action'] = get_string('action_' . $entry->action, 'report_edsembli');
            $row['result'] = get_string('result_' . $entry->result, 'report_edsembli');
            $this->add_data_keyed($row);
        }
        
        if (count($logs) > 0) {
            return true;
        }
        
        return false;
    }

    /**
     * Generate the detail column.
     *
     * @param stdClass $event event data.
     * @return string HTML for the time column
     */
     private function col_detail($event) {
        $modal = '<div id="'. $event->id .'" class="detail"><div class="container">';
        $modal .= '<div class="row row-fluid mb-3">'
                    . ' <div class="col-3 span3">'
                    .   \get_string('col_id', 'report_edsembli')
                    . '</div>'
                    . '<div class="col-9 span9">'
                    .   $event->id
                    . '</div>'
                    . '</div>';
        $data = json_decode($event->data);
        $data->timecreated = \date(self::$date_format, $event->timecreated);
        $data->result = get_string('result_' . $event->result, 'report_edsembli');
        foreach ($data as $data_key => $data_value) {
            $modal .= '<div class="row row-fluid mb-3 ">
                        <div class="col-3 span3">' .
                            $data_key .
                        '</div>
                         <div class="col-9 span9">';
                        if (\is_object($data_value) || \is_array($data_value)) {
                            $modal .= '  <pre>' . \var_export($data_value, true) . '</pre>';
                        } else {
                            $modal .= '  ' . $data_value;
                        }
            $modal .=   '</div>'
                      . '</div>';            
        }
        $modal .= '</div></div>';
        return '<i class="fa fa-info-circle" log_detail="' . $event->id . '"></i>' . $modal;
    }
    
    /*
     * Output the table
     */

    public function print_report() {
        global $OUTPUT;

        $this->setup();

        if ($this->setrowdata() == false) {
            echo $OUTPUT->box(get_string('noresults', 'report_edsembli'), 'generalbox boxaligncenter');
            return;
        }

        $this->finish_output();
    }
}
