<?php
namespace theme_academi;

use core_text;
use html_writer;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir.'/formslib.php';
require_once($CFG->dirroot . '/user/editlib.php');

/**
 * Upload a file CVS file with user information.
 *
 * @copyright  2007 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_users_form extends \moodleform {
    function definition () {
        $mform = $this->_form;
        $usertype = $this->_customdata['usertype'];
        $headingtext = get_string('uploadstudent', 'theme_academi');

        switch ($usertype) {
            case 'teacher':
                $headingtext = get_string('uploadteacher', 'theme_academi');
                break;
            case 'counsellor':
                $headingtext = get_string('uploadcounsellor', 'theme_academi');
                break;
            case 'principal':
                $headingtext = get_string('uploadprincipal', 'theme_academi');
                break;
        }

        $mform->addElement('header', 'settingsheader', $headingtext);

        $url = new moodle_url('example.csv');
        $link = html_writer::link($url, 'example.csv');
        $mform->addElement('static', 'examplecsv', get_string('examplecsv', 'tool_uploaduser'), $link);
        $mform->addHelpButton('examplecsv', 'examplecsv', 'tool_uploaduser');

        $mform->addElement('filepicker', 'userfile', get_string('file'));
        $mform->addRule('userfile', null, 'required');

        $this->add_action_buttons(false, get_string('uploadusers', 'tool_uploaduser'));
    }
}