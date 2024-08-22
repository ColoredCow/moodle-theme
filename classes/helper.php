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

    public function get_courses_list_by_category_id($categoryid) {
        global $DB;
       
        return $DB->get_records('course', ['category' => $categoryid]); 
    }

    public function get_school_list() {
        global $DB;
        return $DB->get_records('company', []);
    }

    public function get_category_of_course($course) {
        global $DB;

        return $DB->get_record('course_categories', ['id' => $course->category]);
    }

    public function get_assignees_count_for_course($course) {
        global $DB;

        return 200;
    }
    
    public function get_schools_count_for_course($course) {
        global $DB;

        return $DB->count_records('company_course', ['courseid' => $course->id]);
    }

    public function get_users_list_by_role_for_school() {
        global $DB;
        $sql = "SELECT
                    ra.userid,
                    u.firstname,
                    u.id,
                    r.shortname as rolename
                FROM
                    {company_users} cu
                    JOIN {role_assignments} ra ON cu.userid = ra.userid
                    LEFT JOIN mdl_user u ON ra.userid = u.id
                    LEFT JOIN mdl_role r ON ra.roleid = r.id
                WHERE
                    r.shortname IN ('teacher', 'student', 'counsellor')
                    and cu.companyid = :schoolid
                ";
        return $DB->get_records_sql($sql, ['schoolid' => get_user_school()->companyid]);
    }

    public function get_role_id_by_name($rolename) {
        global $DB;
        return $DB->get_record('role', ['shortname' => $rolename]);
    }

    public function create_user($record) {
        global $DB;
        return $DB->insert_record('user', $record);
    }

    public function assign_role($record) {
        global $DB;
        return $DB->insert_record('role_assignments', $record);
    }

    public function assign_user_to_school($record) {
        global $DB;
        return $DB->insert_record('company_users', $record);
    }
}
