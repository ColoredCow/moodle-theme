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
 * Theme helper class for the theme_academi.
 *
 * @package   theme_academi
 * @copyright 2024 onwards ColoredCow Team (https://coloredcow.com/)
 * @author    ColoredCow Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_academi;

use core_course_category;

/**
 * Helper class for additional function on the acadmei theme.
 */
class helper {

    /**
     * Load all configurable image url into the scss content.
     *
     * @param string $theme theme data.
     * @param string $scss scss data.
     * @return string $scss return the scss value.
     */
    public function load_bgimages($theme, $scss) {
        $configurable = [
            'footerbgimg' => ['footerbgimg'],
            'loginbg' => ['loginbg'],
        ];
        // Prepend variables first.
        foreach ($configurable as $configkey => $targets) {
            $url = ( $theme->setting_file_url($configkey, $configkey) ) ? $theme->setting_file_url($configkey, $configkey) : null;
            $value = (!empty($url)) ? "('".$url."')" : '';
            if (empty($value)) {
                continue;
            }
            array_map(function($target) use (&$scss, $value) {
                $scss .= '$' . $target . ': ' . $value . ";\n";
            }, (array) $targets);
        }
        return $scss;
    }

    /**
     * Load the additional theme settings values pass to SCSS variables.
     *
     * @return string $scss.
     */
    public function load_additional_scss_settings() {
        $scss = '';
        $primary = theme_academi_get_setting('primarycolor');
        $secondary = theme_academi_get_setting('secondarycolor');
        $slideoverlayval = theme_academi_get_setting('slideOverlay');
        $slideopacity = (!empty($slideoverlayval)) ? $this->get_hexa('#000000', $slideoverlayval) : 0.4;
        $footerbgoverlayval = theme_academi_get_setting('footerbgOverlay');
        $footerbgopacity = (!empty($footerbgoverlayval)) ? $this->get_hexa($primary, $footerbgoverlayval) : 0.4;
        $pagesizecustomval = theme_academi_get_setting('pagesizecustomval');
        $fontsize = theme_academi_get_setting('fontsize');
        $primary30 = $this->get_hexa($primary, '0.3');
        $secondary30 = $this->get_hexa($secondary, '0.3');
        $primary70 = $this->get_hexa($primary, '0.7');
        $secondary70 = $this->get_hexa($secondary, '0.7');
        $scss .= $primary ? '$primary:'.$primary.";\n" : "";
        $scss .= $secondary ? '$secondary:'.$secondary.";\n" : "";
        $scss .= $slideopacity ? '$url_1:'.$slideopacity.";\n" : "";
        $scss .= $pagesizecustomval ? '$custom-container:'.$pagesizecustomval."px;\n" : "";
        $scss .= $fontsize ? '$fontsize:'.$fontsize. "px;" : "";
        if (!empty($primary)) {
            $scss .= $footerbgopacity ? '$footerbgopacity:'.$footerbgopacity.";\n" : "";
            $scss .= $primary30 ? '$primary_30:'.$primary30.";\n" : "";
            $scss .= $primary70 ? '$primary_70:'.$primary70.";\n" : "";
        }
        if (!empty($secondary)) {
            $scss .= $secondary30 ? '$secondary_30:'.$secondary30.";\n" : "";
            $scss .= $secondary70 ? '$secondary_70:'.$secondary70.";\n" : "";
        }
        return $scss;
    }

    /**
     * Function returns the rgb format with the combination of passed color hex and opacity.
     *
     * @param string $hexa color.
     * @param int $opacity opacity.
     * @return string
     */
    public function get_hexa($hexa, $opacity) {
        $hexa = trim($hexa, "#");
        if ( strlen($hexa) == 6 ) {
            $r = hexdec( substr($hexa, 0, 2) );
            $g = hexdec( substr($hexa, 2, 2) );
            $b = hexdec( substr($hexa, 4, 2) );
            $a = (!empty($opacity)) ? $opacity : 0;
            return "rgba(".$r.", ".$g.", ".$b.", ".$a.")";
        }
        return "";
    }

    /**
     * Fetch the hide course ids.
     *
     * @return array
     */
    public function hidden_courses_ids() {
        global $DB;
        $hcourseids = [];
        $result = $DB->get_records_sql("SELECT id FROM {course} WHERE visible='0' ");
        if (!empty($result)) {
            foreach ($result as $row) {
                $hcourseids[] = $row->id;
            }
        }
        return $hcourseids;
    }

    /**
     * Remove the html special tags from course content.
     * This function used in course home page.
     *
     * @param string $text
     * @return string
     */
    public function strip_html_tags($text) {
        $text = preg_replace(
            [
                // Remove invisible content.
                '@<head[^>]*?>.*?</head>@siu',
                '@<style[^>]*?>.*?</style>@siu',
                '@<script[^>]*?.*?</script>@siu',
                '@<object[^>]*?.*?</object>@siu',
                '@<embed[^>]*?.*?</embed>@siu',
                '@<applet[^>]*?.*?</applet>@siu',
                '@<noframes[^>]*?.*?</noframes>@siu',
                '@<noscript[^>]*?.*?</noscript>@siu',
                '@<noembed[^>]*?.*?</noembed>@siu',
                // Add line breaks before and after blocks.
                '@</?((address)|(blockquote)|(center)|(del))@iu',
                '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
                '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
                '@</?((table)|(th)|(td)|(caption))@iu',
                '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
                '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
                '@</?((frameset)|(frame)|(iframe))@iu',
            ],
            [
                ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
                "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
                "\n\$0", "\n\$0",
            ],
            $text
        );
        return strip_tags( $text );
    }

    /**
     * Cut the Course content.
     *
     * @param string $str
     * @param integer $n
     * @param string $endchar
     * @return string $out
     */
    public function course_trim_char($str, $n = 500, $endchar = '&#8230;') {
        if (strlen($str) < $n) {
            return $str;
        }

        $str = preg_replace("/\s+/", ' ', str_replace(["\r\n", "\r", "\n"], ' ', $str));
        if (strlen($str) <= $n) {
            return $str;
        }

        $out = "";
        $small = substr($str, 0, $n);
        $out = $small.$endchar;
        return $out;
    }

    /**
     * Renderer the slider images.
     * @param string $p
     * @param string $slidername
     * @return string
     */
    public function render_slideimg($p, $slidername) {
        global $PAGE;
        // Get slide image or fallback to default.
        $slideimage = '';
        if (theme_academi_get_setting($slidername)) {
            $slideimage = $PAGE->theme->setting_file_url($slidername , $slidername);
        }
        if (empty($slidername)) {
            $slideimage = '';
        }
        return $slideimage;
    }

    public function get_top_level_category_by_name($name) {
        global $DB;
        
        return $DB->get_record('course_categories', ['name' => $name, 'parent' => 0]);       
    }

    public function get_course_by_id($id) {
        global $DB;
        return $DB->get_record('course', ['id' => $id]); 
    }

    public function get_courses_list_by_category_id($categoryid) {
        global $DB;

        return $DB->get_records('course', ['category' => $categoryid]); 
    }

    public function get_courses_list_by_user($filters) {
        global $DB, $USER;
        $sql = "SELECT
                    c.*
                FROM
                    {user_enrolments} ue
                    LEFT JOIN {enrol} e ON e.id = ue.enrolid
                    LEFT JOIN {course} c ON c.id = e.courseid
                    WHERE ue.userid = :userid";
        
        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'search':
                    if (!empty($value)) {
                        $sql .= " AND c.fullname LIKE :search";
                        $params['search'] = "%$value%";
                    }
                    continue;

                case 'createdon':
                    if (!empty($value)) {
                        $dateObject = new \DateTime($value);
                        $formattedDate = $dateObject->format('Y-m-d');
                        $sql .= " AND DATE(FROM_UNIXTIME(c.timecreated)) = :timecreated";
                        $params['timecreated'] = $formattedDate;
                    }
                    continue;
            }
        }

        $params['userid'] = $USER->id;

        return $DB->get_records_sql($sql, $params);
    }

    public function get_assigned_course_count($userid) {
        global $DB;
        $sql = "SELECT 
                COUNT(*) FROM 
                {user_enrolments} ue 
                LEFT JOIN {enrol} e ON e.id = ue.enrolid 
                LEFT JOIN {course} c ON c.id = e.courseid 
                WHERE ue.userid = :userid";

        $params['userid'] = $userid;
        return $DB->count_records_sql($sql, $params);
    }
    
    public function get_courses_list_by_top_level_category($categoryname, $filters) {
        global $DB;
        $toplevelcategory = $this->get_top_level_category_by_name($categoryname);

        if(is_student()){
            return self::get_courses_list_by_user($filters);
        }

        $sql = "SELECT c.* FROM {course} as c 
            JOIN {course_categories} as cc
            ON c.category = cc.id
            WHERE parent = :parentid
        ";

        $params['parentid'] = $toplevelcategory->id;

        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'search':
                    if (!empty($value)) {
                        $sql .= " AND fullname LIKE :search";
                        $params['search'] = "%$value%";
                    }
                    continue;

                case 'createdon':
                    if (!empty($value)) {
                        $dateObject = new \DateTime($value);
                        $formattedDate = $dateObject->format('Y-m-d');
                        $sql .= " AND DATE(FROM_UNIXTIME(timecreated)) = :timecreated";
                        $params['timecreated'] = $formattedDate;
                    }
                    continue;
                
                case 'categoryid':
                    if (!empty($value) && $value !== 'all') {
                        $sql .= " AND cc.id = :categoryid";
                        $params['categoryid'] = $value;
                    }
                    continue;
            }
        }

        return $DB->get_records_sql($sql, $params);
    }

    public function get_school_list($filters) {
        global $DB;
        $sql = "SELECT * FROM {company} as c 
            where CASE 
                WHEN :searchtext1 = ''
                THEN TRUE
                ELSE LOWER(c.name) LIKE LOWER(:searchtext2)
            END";
        
        return $DB->get_records_sql($sql, [
            'searchtext1' => $filters['name'],
            'searchtext2' => "%".$filters['name']."%"
        ]);
    }
    
    public function get_school_admin(int $schoolid) {
        global $DB;
        $sql = "SELECT
                    ra.userid,
                    u.firstname,
                    u.lastname,
                    u.idnumber,
                    r.shortname as rolename
                FROM
                    {company_users} cu
                    JOIN {role_assignments} ra ON cu.userid = ra.userid
                    LEFT JOIN {user} u ON ra.userid = u.id
                    LEFT JOIN {role} r ON ra.roleid = r.id
                WHERE
                    r.shortname = 'schooladmin'
                    and cu.companyid = :schoolid";
        return $DB->get_record_sql($sql, ['schoolid' => $schoolid]);
    }

    public function get_category_of_course($course) {
        global $DB;

        return $DB->get_record('course_categories', ['id' => $course->category]);
    }

    public function get_assignees_count_for_course($course) {
        global $DB;
        $params = [
            'courseid' => $course->id
        ];
        
        if (is_sel_admin()) {
            $sql = "SELECT count(ue.id) as total FROM {enrol} as enrol
                JOIN {user_enrolments} as ue ON ue.enrolid = enrol.id
                WHERE enrol.courseid = :courseid
            ";
        } else {
            $params['schoolid'] = get_user_school()->companyid;
            $sql = "SELECT count(ue.id) as total FROM {enrol} as enrol
                JOIN {user_enrolments} as ue ON ue.enrolid = enrol.id
                JOIN {company_users} as cu ON cu.userid = ue.userid
                WHERE enrol.courseid = :courseid
                AND cu.companyid = :schoolid
            ";
        }

        return $DB->get_field_sql($sql, $params);
    }
    
    public function get_schools_count_for_course($course) {
        global $DB;

        return $DB->count_records('company_course', ['courseid' => $course->id]);
    }

    public function get_users_list_by_role_for_school($filters) {
        global $DB;
        $sql = "SELECT
                    ra.userid,
                    u.firstname,
                    u.lastname,
                    u.idnumber,
                    u.id,
                    r.shortname as rolename
                FROM
                    {company_users} cu
                    JOIN {role_assignments} ra ON cu.userid = ra.userid
                    LEFT JOIN {user} u ON ra.userid = u.id
                    LEFT JOIN {role} r ON ra.roleid = r.id
                WHERE
                    r.shortname IN ('teacher', 'student', 'counsellor', 'principal')
                    and cu.companyid = :schoolid
                    and CASE 
                            WHEN :searchtext1 = ''
                            THEN TRUE
                            ELSE LOWER(CONCAT(u.firstname, ' ', u.lastname)) LIKE LOWER(:searchtext2)
                        END
                ";
        return $DB->get_records_sql($sql, [
            'schoolid' => get_user_school()->companyid,
            'searchtext1' => $filters['name'],
            'searchtext2' => "%".$filters['name']."%"
        ]);
    }

    public function get_role_id_by_name($rolename) {
        global $DB;
        return $DB->get_record('role', ['shortname' => $rolename]);
    }

    public function create_user($record) {
        global $DB;
        return $DB->insert_record('user', $record);
    }

    public static function update_user($record) {
        global $DB;
        return $DB->update_record('user', $record);
    }
    
    public static function get_user_by_id($id) {
        global $DB;
        return $DB->get_record('user', ['id' => $id]);
    }
    
    public static function get_all_schools() {
        global $DB;
        return $DB->get_records('company');
    }

    public function assign_role($record) {
        global $DB;
        return $DB->insert_record('role_assignments', $record);
    }

    public function assign_user_to_school($record) {
        global $DB;
        return $DB->insert_record('company_users', $record);
    }
    
    public function assign_course_to_school($record) {
        global $DB;
        return $DB->insert_record('company_course', $record);
    }
    
    public function get_mapping_for_school_course($schoolid, $courseid) {
        global $DB;
        return $DB->get_record('company_course', ['companyid' => $schoolid, 'courseid' => $courseid]);
    }
    
    public function unassign_course_from_school($schoolids, $courseid) {
        global $DB;

        if (empty($schoolids)) {
            return;
        }
        list($insql, $params) = $DB->get_in_or_equal($schoolids, SQL_PARAMS_NAMED, 'sch');
        $params['courseid'] = $courseid;
    
        $DB->delete_records_select('company_course', "courseid = :courseid AND companyid $insql", $params);
    }

    public function get_school_admins($filters) {
        global $DB;
        $sql = "SELECT
                    ra.userid,
                    u.firstname,
                    u.lastname,
                    u.idnumber,
                    cu.companyid,
                    c.name as schoolname,
                    c.shortname as schoolshortname
                FROM
                    {company_users} cu
                    JOIN {role_assignments} ra ON cu.userid = ra.userid
                    LEFT JOIN {user} u ON ra.userid = u.id
                    LEFT JOIN {role} r ON ra.roleid = r.id
                    LEFT JOIN {company} c ON cu.companyid = c.id
                WHERE
                    r.shortname = 'schooladmin'
                    and CASE 
                            WHEN :searchtext1 = ''
                            THEN TRUE
                            ELSE LOWER(CONCAT(u.firstname, ' ', u.lastname)) LIKE LOWER(:searchtext2)
                        END
                ";
        $queryParams = [
            'searchtext1' => $filters['name'],
            'searchtext2' => "%".$filters['name']."%"
        ];

        return $DB->get_records_sql($sql, $queryParams);
    }

    public function create_user_grade($record) {
        global $DB;
        $record->created_at = date('Y-m-d H:i:s');
        $record->updated_at = date('Y-m-d H:i:s');
        return $DB->insert_record('cc_user_grade', $record);
    }
    
    public function create_user_enrol($record) {
        global $DB;
        return $DB->insert_record('user_enrolments', $record);
    }
    
    public function get_user_enrol($userid, $enrolid) {
        global $DB;
        return $DB->get_record('user_enrolments', ['enrolid' => $enrolid, 'userid' => $userid]);
    }
    
    public function get_user_enrollment_ids($enrolid) {
        global $DB;
        $records = $DB->get_records('user_enrolments', ['enrolid' => $enrolid], '', 'userid');
        return array_keys($records);
    }
    
    public function unenroll_users($enrolid, $useridlist) {
        global $DB;
    
        if (empty($useridlist)) {
            return;
        }
    
        // Prepare the SQL IN clause for the user IDs.
        list($insql, $params) = $DB->get_in_or_equal($useridlist, SQL_PARAMS_NAMED, 'userid');
    
        // Add the enrolment ID to the parameters.
        $params['enrolid'] = $enrolid;
    
        // Delete records matching the enrolment ID and user IDs.
        $DB->delete_records_select('user_enrolments', "enrolid = :enrolid AND userid $insql", $params);
    }

    public function get_or_create_course_enrol($courseid) {
        global $DB;
        $enrol = $DB->get_record('enrol', ['courseid' => $courseid]);
        $studentrole = $DB->get_record('role', ['shortname' => "student"]);
        if(!$enrol) {
            $record = new \stdClass();
            $record->enrol = "manual";
            $record->status = 0;
            $record->courseid = $courseid;
            $record->roleid = $studentrole->id;
            $record->enrolstartdate = time();
            $DB->insert_record('enrol', $record);
        }

        return $enrol = $DB->get_record('enrol', ['courseid' => $courseid]);
    }

    public function get_students_eligible_for_course($courseid, $schoolid, $gradestoassign) {
        global $DB;
        $studentrole = $DB->get_record('role', ['shortname' => "student"]);
        $systemcontextid = \context_system::instance()->id;
        // Convert the grades array to a comma-separated list
        list($insql, $inparams) = $DB->get_in_or_equal($gradestoassign, SQL_PARAMS_NAMED, 'grade');
    
        $sql = "SELECT ug.* FROM {user} as u
            JOIN {company_users} as cu
            ON cu.userid = u.id
            JOIN {role_assignments} as ra
            ON ra.userid = u.id
            AND ra.contextid = :contextid
            JOIN {cc_user_grade} as ug
            ON ug.user_id = u.id
            WHERE cu.companyid = :schoolid
            AND ra.roleid = :roleid;
        ";
        $queryParams = array_merge($inparams, [
            'schoolid' => $schoolid,
            'roleid' => $studentrole->id,
            'contextid' => $systemcontextid
        ]);

        $users = $DB->get_records_sql($sql, $queryParams);

        foreach($users as $index => $user) {
            $grade = json_decode($user->user_grade);
            $commonelements = array_intersect($grade, $gradestoassign);
            if (empty($commonelements)) {
                unset($users[$index]);
            }
        }
        return array_values($users);
    }

    public function create_school_course_grade($record) {
        global $DB;
        $record->created_at = date('Y-m-d H:i:s');
        $record->updated_at = date('Y-m-d H:i:s');
        return $DB->insert_record('cc_school_course_grade', $record);
    }
    
    public function update_school_course_grade($record) {
        global $DB;
        $record->updated_at = date('Y-m-d H:i:s');
        return $DB->update_record('cc_school_course_grade', $record);
    }

    public function update_user_grade($record) {
        global $DB;
        $record->created_at = date('Y-m-d H:i:s');
        $record->updated_at = date('Y-m-d H:i:s');
        return $DB->update_record('cc_user_grade', $record);
    }

    public function get_school_by_id($id) {
        global $DB;
        return $DB->get_record('company', ['id' => $id]);
    }

    public function get_department_for_school($schoolid) {
        global $DB;
        $schoolshortname = $this->get_school_by_id($schoolid)->shortname;    
        return $DB->get_record('department', ['shortname' => $schoolshortname]);
    }

    public function get_assigned_schools_for_course($courseid) {
        global $DB;
        $records = $DB->get_records('company_course', ['courseid' => $courseid], '', 'companyid');
        return array_keys($records);
    }
    
    public function get_assigned_school_students_for_course($courseid, $schoolid) {
        global $DB;
        $records = $DB->get_records('company_course', ['courseid' => $courseid], '', 'companyid');
        return array_keys($records);
    }
    
    public function get_assigned_school_grades_for_course($courseid, $schoolid) {
        global $DB;
        return $DB->get_record('cc_school_course_grade', ['course_id' => $courseid, "school_id" => $schoolid]);
    }

    public function get_user_grade_by_user_id($userid) {
        global $DB;
        return $DB->get_record('cc_user_grade', ['user_id' => $userid]);
    }

    public function create_course_categories($categoryid, $categoryname) {
        $data = new \stdClass();
        $data->name = $categoryname;
        $data->description = '';
        $data->parent = $categoryid;
        $data->visible = 1;
        $data->timemodified = time();
        $data->timecreated = time();
        core_course_category::create($data);
    }

    public function get_categories_by_parent_id($categoryid) {
        global $DB;
        return $DB->get_records('course_categories', ['parent' => $categoryid]);
    }

    public function get_all_course_categories($filters) {
        global $DB;
    
        $categoryid = self::get_top_level_category_by_name('Courses')->id;
        $params = ['categoryid' => $categoryid];
        $sql = 'SELECT * FROM {course_categories} WHERE parent = :categoryid';
    
        if (isset($filters['categorytype'])) {
            switch ($filters['categorytype']) {
                case 'create':
                    if (!empty($filters['categoryname'])) {
                        self::create_course_categories($categoryid, $filters['categoryname']);
                    }
                    break;
    
                case 'delete':
                    if (!empty($filters['coursecategoryid'])) {
                        $DB->delete_records('course_categories', ['id' => $filters['coursecategoryid']]);
                    }
                    break;
            }
        }
    
        if (!empty($filters['search'])) {
            $sql .= " AND name LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }
    
        return $DB->get_records_sql($sql, $params);
    }
}
