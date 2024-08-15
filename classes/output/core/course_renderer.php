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
 * Course renderer.
 *
 * @package theme_academi
 * @copyright 2024 onwards ColoredCow Team (https://coloredcow.com/)
 * @author ColoredCow Team
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_academi\output\core;

use html_writer;
use moodle_url;
use lang_string;
use stdClass;
use core\chart_pie;
use core\chart_bar;
use core\chart_series;

/**
 * The core course renderer.
 *
 * Can be retrieved with the following:
 * $renderer = $PAGE->get_renderer('core','course');
 */
class course_renderer extends \core_course_renderer {

    /**
     * Call the frontpage slider js.
     * @param string $blockid
     * @return void
     */
    public function include_frontslide_js($blockid) {
        $this->page->requires->js_call_amd('theme_academi/frontpage', $blockid, []);
    }


    /**
     * Returns HTML to print list of available courses for the frontpage.
     *
     * @return string
     */
    public function frontpage_available_courses() {
        global $CFG;
        $displayoption = theme_academi_get_setting('availablecoursetype');
        if ($displayoption != '1') {
            return parent::frontpage_available_courses();
        }

        $chelper = new \coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->set_courses_display_options(
                [
                    'recursive' => true,
                    'limit' => $CFG->frontpagecourselimit,
                    'viewmoreurl' => new moodle_url('/course/index.php'),
                    'viewmoretext' => new lang_string('fulllistofcourses'),
                ]);

        $chelper->set_attributes(['class' => 'frontpage-course-list-all']);
        $courses = \core_course_category::top()->get_courses($chelper->get_courses_display_options());
        $totalcount = \core_course_category::top()->get_courses_count($chelper->get_courses_display_options());
        if (!$totalcount && !$this->page->user_is_editing() &&
            has_capability('moodle/course:create', \context_system::instance())) {
            // Print link to create a new course, for the 1st available category.
            return $this->add_new_course_button();
        }
        if (!empty($courses)) {
            $data = [];
            $attributes = $chelper->get_and_erase_attributes('courses');
            $content = \html_writer::start_tag('div', $attributes);
            foreach ($courses as $course) {
                $data[] = $this->available_coursebox($chelper, $course);
            }
            $totalcourse = count($data);
            $content .= $this->render_template('availablecourses', ['courses' => $data, 'totalavacount' => $totalcourse]);
            $content .= \html_writer::end_tag('div');
            $this->include_frontslide_js('availablecourses');
            return $content;
        }
    }

    /**
     * Return contents for the available course block on the frontpage.
     *
     * @param coursecat_helper $chelper course helper.
     * @param array $course course detials.
     *
     * @return array $data available course data.
     */
    public function available_coursebox(\coursecat_helper $chelper, $course) {
        global $CFG;
        $coursename = $chelper->get_course_formatted_name($course);
        $data['name'] = $coursename;
        $data['link'] = new \moodle_url('/course/view.php', ['id' => $course->id]);
        $noimgurl = $this->output->image_url('no-image', 'theme');
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $imgurl = file_encode_url("$CFG->wwwroot/pluginfile.php",
                '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            if (!$isimage) {
                $imgurl = $noimgurl;
            }
        }
        if (empty($imgurl)) {
            $imgurl = $noimgurl;
        }
        $data['imgurl'] = $imgurl;
        return $data;
    }

    /**
     * Render the template.
     *
     * @param string $template name of the template.
     * @param array $data Data.
     *
     * @return string.
     */
    public function render_template($template, $data) {
        $data[$template] = 1;
        $data['ouput'] = $this->output;
        return $this->output->render_from_template('theme_academi/course_blocks', $data);
    }

    /**
     * Promoted course content for the theme front page.
     *
     * @return string
     */
    public function promoted_courses() {
        global $CFG, $DB;

        $pcoursestatus = theme_academi_get_setting('pcoursestatus');
        $promotedtitle = theme_academi_get_setting('promotedtitle', 'format_html');
        $promotedtitle = theme_academi_lang($promotedtitle);
        $promotedcoursedesc = theme_academi_lang(theme_academi_get_setting('promotedcoursedesc'));
        $featuredids = theme_academi_get_setting('promotedcourses');
        $promotedcontent = empty($promotedtitle) && empty($promotedcoursedesc) ? false : true;
        $blockisempty = empty($promotedtitle) && empty($promotedcoursedesc) && empty($featuredids) ? false : $pcoursestatus;
        $blocks = [];
        if (!empty($featuredids)) {
            /* Get Featured courses id from DB */
            $rcourseids = (!empty($featuredids)) ? explode(",", $featuredids) : [];
            $helperobj = new \theme_academi\helper();
            $hcourseids = $helperobj->hidden_courses_ids();

            if (!empty($hcourseids)) {
                foreach ($rcourseids as $key => $val) {
                    if (in_array($val, $hcourseids)) {
                        unset($rcourseids[$key]);
                    }
                }
            }

            foreach ($rcourseids as $key => $val) {
                $ccourse = $DB->get_record('course', ['id' => $val]);
                if (empty($ccourse)) {
                    unset($rcourseids[$key]);
                    continue;
                }
            }

            $fcourseids = $rcourseids;
            $totalfcourse = count($fcourseids);
            if (!empty($fcourseids)) {
                $i = 0;
                foreach ($fcourseids as $courseid) {
                    $info = [];
                    $course = get_course($courseid);
                    $noimgurl = $this->output->image_url('no-image', 'theme');
                    $courseurl = new moodle_url('/course/view.php', ['id' => $courseid]);

                    if ($course instanceof stdClass) {
                        $course = new \core_course_list_element($course);
                    }

                    $imgurl = '';
                    $summary = $helperobj->strip_html_tags($course->summary);
                    $summary = $helperobj->course_trim_char($summary, 75);
                    foreach ($course->get_course_overviewfiles() as $file) {
                        $isimage = $file->is_valid_image();
                        $imgurl = file_encode_url("$CFG->wwwroot/pluginfile.php",
                        '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                        $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                        if (!$isimage) {
                            $imgurl = $noimgurl;
                        }
                    }
                    if (empty($imgurl)) {
                        $imgurl = $noimgurl;
                    }
                    $info['courseurl'] = $courseurl;
                    $info['imgurl'] = $imgurl;
                    $info['coursename'] = $course->get_formatted_name();
                    $info['active'] = ($i == 1) ? true : false;
                    $blocks[] = $info;
                    $i++;
                }
            }
            $template['totalfcourse'] = $totalfcourse;
        }
        $template['coursestatus'] = !empty($featuredids) ? true : false;
        $template['courses'] = array_chunk($blocks, 5);
        $template['promatedcourse'] = $pcoursestatus;
        $template['blockisempty'] = $blockisempty;
        if (!$blockisempty) {
            $template['isblockempty'] = is_siteadmin() || $this->page->user_is_editing() ? true : false;
        }
        $template['promotedcontent'] = $promotedcontent;
        $template['promotedtitle'] = $promotedtitle;
        $template['promotedcoursedesc'] = $promotedcoursedesc;
        $this->include_frontslide_js('promotedcourse');
        return $this->output->render_from_template("theme_academi/course_blocks", $template);
    }

    /**
     * Outputs contents for frontpage as configured in $CFG->frontpage or $CFG->frontpageloggedin
     *
     * @return string
     */
    public function frontpage() {
        global $CFG, $SITE;
        $output = '';
        $survey = new \local_moodle_survey\model\survey();
        $audienceaccess = new \local_moodle_survey\model\audience_access();
        $activesurveycount = $survey->get_active_survey_count();
        $totalschoolcount = $audienceaccess->get_schools_count();
        $frontpagelayout = ['overview', 'quickaction', 'insights'];

        foreach ($frontpagelayout as $section) {
            switch($section) {
                case 'overview':
                    $output .= $this->frontpage_overview($activesurveycount, $totalschoolcount);
                    break;
                case 'quickaction':
                    $output .= $this->quick_action();
                    break;
                case 'insights':
                    $output .= $this->frontpage_insights($survey);
                    break;
            }
            $output .= '<br />';
        }
        return $output;
    }

    public function frontpage_overview($activesurveycount, $totalschoolcount) {
        global $CFG, $DB, $USER;
        $template = ['overview'=> true];
        $template['username'] =  $USER->firstname;
        $template['activesurveycount'] = $activesurveycount;
        $template['totalschoolcount'] = $totalschoolcount;

        return $this->output->render_from_template("theme_academi/course_blocks", $template);
    }

    public function quick_action() {
        $template = ['quickaction'=> true];
        $template['createnewschool'] = new moodle_url('/blocks/iomad_company_admin/company_edit_form.php', ['createnew' => 1]);
        $helper = new \theme_academi\helper();
        $coursescategory = $helper->get_top_level_category_by_name('Courses');
        $template['createnewcourseurl'] = new \moodle_url('/course/edit.php', ['category'=>$coursescategory->id]);
        $template['createsurveyurl'] = new moodle_url('/local/moodle_survey/create_survey.php');
        $template['plusicon'] = '<img src="' . new moodle_url('/theme/academi/pix/plus-icon.svg') . '" alt="Plus Icon" class="plus-icon" />';

        return $this->output->render_from_template("theme_academi/course_blocks", $template);
    }

    public function frontpage_insights($survey) {
        global $CFG, $DB, $PAGE;
    
        $pieChartsHtml = '';
        $horizontalBarChartsHtml = '';
    
        $activesurveycount = $survey->get_active_survey_count();
        $completedsurveycount = $survey->get_survey_count_by_status('Completed');
        $totaldraftsurveycount = $survey->get_survey_count_by_status('Draft');
        $surveycategorydata = $survey->get_all_survey_categories();
        $surveyquestioncatgorycount = $survey->get_question_category_count();
    
        $surveycatgories = [];
        foreach ($surveycategorydata as $surveycategory) {
            $surveycatgories[] = [
                'slug' => $surveycategory->slug,
                'name' => $surveycategory->label,
            ];
        }
    
        $surveycategorieshtml = $this->get_survey_category_dropdown_field($surveycatgories, $PAGE);
        $selectedCategory = $_GET['category'] ?? 'all';
        $horizontalbarchartdata = $this->get_horizontal_bar_chart_data($selectedCategory);
        $underdeveloped = $horizontalbarchartdata[0];
        $developing = $horizontalbarchartdata[1];
        $remarkeble = $horizontalbarchartdata[2];
        for ($i = 0; $i < $surveyquestioncatgorycount; $i++) {
            $pieChart = new chart_pie();
            $pieChartData = [$activesurveycount, $completedsurveycount, $totaldraftsurveycount];
            $series = new chart_series('Survey', $pieChartData);
            $pieChart->add_series($series);
            $pieChartLabels = ['Active Surveys', 'Completed Surveys', 'Draft Surveys'];
            $pieChart->set_labels($pieChartLabels);
            $pieChartHtml = $this->output->render_chart($pieChart, false);
            $pieChartsHtml .= '<div class="chart mt-4 border border-secondary rounded mx-4 py-3">' . $pieChartHtml . '</div>';
    
            $horizontalBarChart = $this->get_bar_chart($underdeveloped, $developing, $remarkeble);
            $horizontalBarChartHtml = $this->output->render_chart($horizontalBarChart, false);
            $horizontalBarChartsHtml .= '<div class="chart mt-4 border border-secondary rounded py-3">' . $horizontalBarChartHtml . '</div>';
        }
    
        $template['insights'] = true;
        $template['surveycatgories'] = $surveycategorieshtml;
        $template['chart'] = $pieChartsHtml;
        $template['horizontalbarchart'] = $horizontalBarChartsHtml;
    
        return $this->output->render_from_template("theme_academi/course_blocks", $template);
    }

    public function get_bar_chart($underdeveloped, $developing, $remarkeble) {
        $chartbar = new chart_bar();
        
        $chartbar->set_horizontal(true);
        $underdevelopedseries = new chart_series('Underdeveloped', $underdeveloped);
        $developingseries = new chart_series('Developing', $developing);
        $remarkableseries = new chart_series('Remarkable', $remarkeble);
        $chartbar->add_series($underdevelopedseries);
        $chartbar->add_series($developingseries);
        $chartbar->add_series($remarkableseries);
        $chartbar->set_labels(['Empathy', 'Growth mindset', 'Well being', 'Social Awareness', 'Self Awareness']);
        
        return $chartbar;
    }

    public function get_survey_category_dropdown_field($surveycatgories, $PAGE) {
        $selectedCategory = optional_param('category', 'all', PARAM_ALPHANUM);
    
        $surveycategorieshtml = '<form method="get" id="surveyForm" action="' . new moodle_url($PAGE->url) .'">';
    
        $surveycategorieshtml .= '<select name="surveycategories" id="surveycategories" class="surveycategories">';
        $surveycategorieshtml .= '<option value="all" ' . ($selectedCategory === 'all' ? 'selected' : '') . '>All</option>';
    
        foreach ($surveycatgories as $surveycategory) {
            $selected = ($selectedCategory === $surveycategory['slug']) ? 'selected' : '';
            $surveycategorieshtml .= sprintf(
                '<option value="%s" %s>%s</option>',
                s($surveycategory['slug']),
                $selected,
                s($surveycategory['name'])
            );
        }
    
        $surveycategorieshtml .= '</select>';
        $surveycategorieshtml .= $this->render_html_dyanmic_script();
        $surveycategorieshtml .= '</form>';
    
        return $surveycategorieshtml;
    }

    public function get_horizontal_bar_chart_data($selectedCategory) {
        switch ($selectedCategory) {
            case 'government':
                $underdeveloped = [0, 0, 20, 0, 0];
                $developing = [10, 0, 0, 15, 0];
                $remarkeble = [0, 15, 0, 0, 10];
                break;
            case 'private':
                $underdeveloped = [0, 0, 10, 0, 0];
                $developing = [5, 0, 0, 20, 0];
                $remarkeble = [0, 15, 0, 0, 10];
                break;
            default:
                $underdeveloped = [0, 0, 30, 0, 0];
                $developing = [15, 0, 0, 35, 0];
                $remarkeble = [0, 30, 0, 0, 20];
                break;
        }

        return [$underdeveloped, $developing, $remarkeble];
    }

    public function render_html_dyanmic_script() {
        $surveycategorieshtml = <<<HTML
        <script>
        document.getElementById('surveycategories').addEventListener('change', function() {
            var selectedCategory = this.value;
            var baseUrl = window.location.href.split('?')[0];
            var newUrl = baseUrl + '?category=' + encodeURIComponent(selectedCategory);
            window.location.href = newUrl;
        });
        </script>
        HTML;

        return $surveycategorieshtml;
    }

    /**
     * Returns HTML to display a course category as a part of a tree
     *
     * This is an internal function, to display a particular category and all its contents.
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_category $coursecat
     * @param int $depth depth of this category in the current tree
     * @return string
     */
    protected function coursecat_category(\coursecat_helper $chelper, $coursecat, $depth) {
        // Open category tag.
        $classes = ['category'];
        if (empty($coursecat->visible)) {
            $classes[] = 'dimmed_category';
        }
        if ($chelper->get_subcat_depth() > 0 && $depth >= $chelper->get_subcat_depth()) {
            // Do not load content.
            $categorycontent = '';
            $classes[] = 'notloaded';
            if ($coursecat->get_children_count() ||
                    ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_COLLAPSED && $coursecat->get_courses_count())) {
                $classes[] = 'with_children';
                $classes[] = 'collapsed';
            }
        } else {
            // Load category content.
            $categorycontent = $this->coursecat_category_content($chelper, $coursecat, $depth);
            $classes[] = 'loaded';
            if (!empty($categorycontent)) {
                $classes[] = 'with_children';
                // Category content loaded with children.
                $this->categoryexpandedonload = true;
            }
        }
        $combolistboxtype = (theme_academi_get_setting('comboListboxType') == 1) ? true : false;
        if ($combolistboxtype) {
            $classes[] = 'collapsed';
        }

        // Make sure JS file to expand category content is included.
        $this->coursecat_include_js();

        $content = html_writer::start_tag('div', [
            'class' => join(' ', $classes),
            'data-categoryid' => $coursecat->id,
            'data-depth' => $depth,
            'data-showcourses' => $chelper->get_show_courses(),
            'data-type' => self::COURSECAT_TYPE_CATEGORY,
        ]);

        // Category name.
        $categoryname = $coursecat->get_formatted_name();
        $categoryname = html_writer::link(new moodle_url('/course/index.php',
                ['categoryid' => $coursecat->id]),
                $categoryname);
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_COUNT
                && ($coursescount = $coursecat->get_courses_count())) {
            $categoryname .= html_writer::tag('span', ' ('. $coursescount.')',
                    ['title' => get_string('numberofcourses'), 'class' => 'numberofcourse']);
        }
        $content .= html_writer::start_tag('div', ['class' => 'info']);

        $content .= html_writer::tag(($depth > 1) ? 'h4' : 'h3', $categoryname, ['class' => 'categoryname aabtn']);
        $content .= html_writer::end_tag('div'); // Info.

        // Add category content to the output.
        $content .= html_writer::tag('div', $categorycontent, ['class' => 'content']);

        $content .= html_writer::end_tag('div'); // Category.

        // Return the course category tree HTML.
        return $content;
    }

    /**
     * Returns HTML to display a tree of subcategories and courses in the given category
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_category $coursecat top category (this category's name and description will NOT be added to the tree)
     * @return string
     */
    protected function coursecat_tree(\coursecat_helper $chelper, $coursecat) {
        // Reset the category expanded flag for this course category tree first.
        $this->categoryexpandedonload = false;
        $categorycontent = $this->coursecat_category_content($chelper, $coursecat, 0);
        if (empty($categorycontent)) {
            return '';
        }

        // Start content generation.
        $content = '';
        $attributes = $chelper->get_and_erase_attributes('course_category_tree clearfix');
        $content .= html_writer::start_tag('div', $attributes);

        if ($coursecat->get_children_count()) {
            $classes = [
                'collapseexpand',
                'aabtn',
            ];

            // Check if the category content contains subcategories with children's content loaded.
            $combolistboxtype = (theme_academi_get_setting('comboListboxType') == 1) ? true : false;
            if ($this->categoryexpandedonload && !$combolistboxtype) {
                $classes[] = 'collapse-all';
                $linkname = get_string('collapseall');
            } else {
                $linkname = get_string('expandall');
            }

            // Only show the collapse/expand if there are children to expand.
            $content .= html_writer::start_tag('div', ['class' => 'collapsible-actions']);
            $content .= html_writer::link('#', $linkname, ['class' => implode(' ', $classes)]);
            $content .= html_writer::end_tag('div');
            $this->page->requires->strings_for_js(['collapseall', 'expandall'], 'moodle');
        }

        $content .= html_writer::tag('div', $categorycontent, ['class' => 'content']);

        $content .= html_writer::end_tag('div');

        return $content;
    }
}
