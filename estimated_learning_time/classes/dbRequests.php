<?php

namespace local_estimated_learning_time;


class dbRequests
{
    /** @var moodle_database */
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /** Gets all module
     * @param int $courseid the user that we are getting time for
     * @return array of time
     * AND cm.deletioninprogress = 1
     */
    public function getCoursesForLearningTimes($courseId): array
    {

        $sql = "SELECT cm.* FROM {course_modules} cm
                WHERE cm.course= :course_id AND cm.visible = 1";
        return $this->db->get_records_sql($sql, ['course_id' => $courseId]);
    }



    /** Gets all time
     * @param int $courseid the user that we are getting time for
     * @return array of time

     */
    public function  getModuleTimes($courseId)
    {
        $sql = "SELECT elt.* FROM {estimated_learning_time} elt
                where elt.course_id = :course_id";
        return $this->db->get_records_sql($sql, ['course_id' => $courseId]);

    }

    /** Gets all section
     * @param int $courseid the user that we are getting time for
     * @return array of time

     */
    public function  getCourseSections($courseId)
    {
        $sql = "SELECT cs.* FROM {course_sections} cs
                where cs.course = :course_id";
        return $this->db->get_records_sql($sql, ['course_id' => $courseId]);

    }

    /** Save Form Data
     *
     *
     * @return boolean
     */
    public function saveForm($courseConfigForm)
    {

       $this->db->insert_record('estimated_learning_time', $courseConfigForm);

        return true;
    }

}
