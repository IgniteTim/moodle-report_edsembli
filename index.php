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
 * EdSembli Synchronization Log
 *
 * @package    report_edsembli
 * @author     Tim Martinez <tim.martinez@ignitecentre.ca>
 * @copyright  2021 Tim Martinez <tim.martinez@ignitecentre.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/lib/tablelib.php');

$download = optional_param('download', '', PARAM_ALPHA);
$perpage = optional_param('perpage', 30, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);

$context = context_system::instance();
$PAGE->set_context($context);
$baseurl = new \moodle_url('/report/edsembli/index.php');
$PAGE->set_title(get_string('pluginname', 'report_edsembli'));
$PAGE->set_url($baseurl);
$PAGE->set_pagelayout('report');
$PAGE->requires->js_call_amd('report_edsembli/tablehelpers', 'init');

require_login();
require_capability('report/edsembli:view', $context);

$report = new \report_edsembli\table($perpage);

if (!$report->is_downloading($download, 'EdSembli Synchronization Logs')) {
//We're displaying the report.
    echo $OUTPUT->header();
    echo $report->print_report();
    echo $OUTPUT->footer();
} else {
    echo $report->print_report();
}

// Trigger a report viewed event.
$event = \report_edsembli\event\report_viewed::create(array('context' => $context));
$event->trigger();
