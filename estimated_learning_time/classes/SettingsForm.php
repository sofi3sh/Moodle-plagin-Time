<?php

/**
 * Class form Edit Setting
 *
 * @package   local_estimated_learning_time
 */

namespace local_estimated_learning_time;

use pix_icon;

require_once($CFG->libdir.'/formslib.php');
require_once('lib.php');
require_once($CFG->libdir.'/completionlib.php');


class SettingsForm extends \moodleform {

    /**
     * Form definition
     *
     * @return void
     */
    public function definition()
    {

        $mform =& $this->_form;

        $instance = $this->_customdata['courseId'];

        $cmodule = new Content();

        $courseSections = $cmodule->getCourseSections($instance);

        $instances = $cmodule->getLearningTimesSelect($instance);

        $modinfo = get_fast_modinfo($instance);



        /**   Додаємо елементи в форму - тут потрібно визначити кількісь модулів і організувати цикл по додаванню УНІКАЛЬНИХ елементів форми   */

         foreach ($courseSections as $item) {

             $sequences = explode(",", $item->sequence);

             $coursesections = $modinfo->get_section_info($item->section, MUST_EXIST);

             $sectiontitle = get_section_name($instance,$coursesections); // назва секції


             $mform->addElement('header', 'general', $sectiontitle);

             foreach ($instances as $value) {

                     //якщо в масиві є модуль то виводжу його
                 $modnamesused = $modinfo->get_cm($value->id); // назва модуля

                 if (in_array($value->id ,$sequences)) {


                     $mform->addElement('hidden', 'cm_id' . $value->module.$item->section ); // Add elements to your form.
                     $mform->setType('cm_id' . $value->module.$item->section, PARAM_INT);
                     $mform->setDefault('cm_id' . $value->module.$item->section, $value->module);        // Default value.// Set type of element.

                     $mform->addElement('text', 'estimated_time' . $value->module.$item->section, $modnamesused->name, 'maxlength="2"');
                     $mform->setType('estimated_time' . $value->module.$item->section, PARAM_INT);

                     if ($value->module == 22) {
                         $mform->addElement('advcheckbox', 'scorm' . $value->module.$item->section, '', 'scorm', array('group' => 1), array(0, 1));

                     } else {
                         $mform->addElement('hidden', 'scorm' . $value->module.$item->section);
                         $mform->setType('scorm' . $value->module.$item->section, PARAM_INT);
                         $mform->setDefault('scorm' . $value->module.$item->section, '0');        // Default value.// Set type of element.
                     }
                     $mform->addElement('hidden', 'section'. $value->module.$item->section);
                     $mform->setType('section'. $value->module.$item->section, PARAM_INT);
                     $mform->setDefault('section'. $value->module.$item->section, $item->section);
                 }

            }
        }

        /** Buttons */
        $this->add_action_buttons(true, get_string('savechanges'));

        $mform->addElement('hidden', 'course_id');
        $mform->setType('course_id', PARAM_INT);
        $mform->setDefault('course_id', $instance);


    }
}
