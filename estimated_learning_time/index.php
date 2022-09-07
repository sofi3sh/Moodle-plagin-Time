<?php

// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_estimated_learning_time
 * @copyright   2022 sofiia <sofiia.shylinska@tmx.com.ua>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_estimated_learning_time\content;

define('NO_OUTPUT_BUFFERING', true);
require_once(__DIR__ . '/../../config.php');

require_once('lib.php');
require_once($CFG->libdir.'/completionlib.php');



global $DB, $CFG, $COURSE;

require_login();


$PAGE->set_url(new moodle_url('/local/estimated_learning_time/index.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_estimated_learning_time'));
$PAGE->navbar->add(get_string('pluginname', 'local_estimated_learning_time'));
$PAGE->set_heading(get_string('pluginname', 'local_estimated_learning_time'));


$courseId = optional_param('id', null, PARAM_INT);

$settingUrl = '/course/view.php?id=';


/** @var  $mform \local_estimated_learning_time\SettingsForm */
$cache = \cache::make_from_params(\cache_store::MODE_SESSION, 'local_estimated_learning_time', 'items');

$customdata = [
    'courseId' => $cache->get('courseId')
];
$cache->set('courseId', $courseId);


$mform = new local_estimated_learning_time\SettingsForm(null, $customdata);

if ($data = $mform->get_data()) {


    /** @var  $settings \local_estimated_learning_time\Content */

    $settings = new Content;
    $courseConfig = $settings->getData($data->course_id);

    if (empty($courseConfig)) {

       $settings->createTimeAll($data);

    } else {

        $settings->updateTimes($data->course_id, $data);

    }
    redirect(new moodle_url($settingUrl,['id'=>$data->course_id]));

} else { // чистая форма/форма с данными
    $cache = \cache::make_from_params(\cache_store::MODE_SESSION, 'local_estimated_learning_time', 'items');
    if ($mform->is_cancelled()) {

        $cacheCategory = $cache->get('courseid');

        redirect(new moodle_url($settingUrl,['id'=>$cacheCategory]));

    }

        $cache->set('courseid', $courseId);

    /** @var  $settings \local_estimated_learning_time\Content */
    //$settings = local_estimated_learning_time\Content::getInstance($courseId);

    $settings = new Content;

    $courseConfig = $settings->getData($courseId);

    $courseConfigForm = new stdClass();

        foreach ($courseConfig as $value) {

            $cmid = "cm_id" . $value->cm_id . $value->section;
            $estimatedtime = "estimated_time" . $value->cm_id .$value->section;
            $scorm = "scorm" . $value->cm_id . $value->section;
            $section = "section" . $value->cm_id . $value->section;

            $courseConfigForm->$cmid = $value->cm_id;
            $courseConfigForm->course_id = $value->course_id;
            $courseConfigForm->$estimatedtime = $value->estimated_time;
            $courseConfigForm->$scorm = $value->scorm;
            $courseConfigForm->$section = $value->section;

   }

    $mform->set_data($courseConfigForm);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
