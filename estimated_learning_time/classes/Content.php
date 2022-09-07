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
namespace local_estimated_learning_time;

require_once('lib.php');

use stdClass;


class content
{

    /** @var moodle_cfg */
    protected $cfg;
    /** @var moodle_user */
    protected $user;
    /** @var moodle_page */
    protected $page;

    protected $dbRequests;


    public function __construct()
    {
        global $CFG, $DB, $USER, $PAGE;
        $this->cfg = $CFG;
        $this->user = $USER;
        $this->page = $PAGE;
        $this->dbRequests =  new \local_estimated_learning_time\dbRequests($DB);

    }

    /**
     * get Content отримуєм всі секції курсу
     * @param $courseId integer
     * @return array
     */
    public function getCourseSections($courseId)
    {
        return  $this->dbRequests->getCourseSections($courseId);
    }


         /**
         * get List module
         * @param $courseId integer
         * @return array
         */
        public function  getLearningTimesSelect($courseId)
        {
            return  $this->dbRequests->getCoursesForLearningTimes($courseId);
        }



    /** Get data into our database table.
     * @param int $courseId integer
     * @return array true if successful
     */
    public function getData($courseId)
    {
        return $this->dbRequests->getModuleTimes($courseId);
    }


    /**
     * Add time module
     *
     * @param array $data
     * return boolean
    */
    public function createTimeAll($data)
    {

        $courseSections = $this->getCourseSections($data->course_id);

        $instances = $this->dbRequests->getCoursesForLearningTimes($data->course_id);

        foreach ($courseSections as $item) {

            $sequences = explode(",", $item->sequence);

                foreach ($instances as $value) {

                    if (in_array($value->id ,$sequences)) {

                        $courseConfigForm = new stdClass();

                        $cmid = "cm_id" . $value->module.$item->section;
                        $estimatedtime = "estimated_time" . $value->module.$item->section;
                        $scorm = "scorm" . $value->module.$item->section;
                        $section = "section" . $value->module.$item->section;

                        $courseConfigForm->cm_id = $data->$cmid;
                        $courseConfigForm->course_id = $data->course_id;
                        $courseConfigForm->estimated_time = $data->$estimatedtime;
                        $courseConfigForm->scorm = ($data->$scorm) ? '1' : '0';
                        $courseConfigForm->section = $data->$section;
                        $this->dbRequests->saveForm($courseConfigForm);
                    }

                }
        }
    }

    /** Delete all time by course id and create new.
     * @param $courseId
     * @param $data
     * @return bool
     */
    public function updateTimes($courseId, $data){

        //delete...

        global $DB;
        $transaction = $DB->start_delegated_transaction();
        list($ids, $params) = $DB->get_in_or_equal($courseId);

        $deletedTimes = $DB->delete_records_select('estimated_learning_time', "course_id $ids", $params);
        if ($deletedTimes) {
            $DB->commit_delegated_transaction($transaction);
        }

         //insert...

       $this->createTimeAll($data);

        return true;

    }

}
