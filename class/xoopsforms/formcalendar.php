<?php
/**
 * Name: formcalendar.php
 * Description:
 *
 * @package    : Xoosla Modules
 * @Module     :
 * @subpackage :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * See File: class/calendar/calendar.php | (c) dynarch.com 2004
 *                                         Distributed as part of "The Coolest DHTML Calendar"
 *                                         under the same terms.
 */

define('NEWLINE', "\n");

/**
 * Class XoopsFormCalendar
 */
class XoopsFormCalendar extends XoopsFormElement
{
    public $calendar_lib_path;
    public $calendar_file;
    public $calendar_lang_file;
    public $calendar_setup_file;
    public $calendar_theme_file;
    public $calendar_theme_url;
    public $calendar_options          = array();
    public $calendar_field_attributes = array();

    /**
     * Constuctor
     *
     * @param string $caption          caption
     * @param string $name             name
     * @param int    $initial_value    initial content
     * @param array  $calendar_options Extra options - see class/calendar/calendar-setup.js for more info on possible parameters
     * @param array  $calendar_field_attributes
     */
    public function __construct(
        $caption,
        $name,
        $initial_value = 0,
        $calendar_options = array(),
        $calendar_field_attributes = array()
    ) {
        parent::__construct();
        $stripped = false;
        $this->setCaption($caption);
        $this->setName($name);
        if (!$initial_value) {
            $initial_value = time();
        }

        $this->set_option('date', $initial_value);
        $this->set_option('ifFormat', '%m/%d/%Y %H:%M');
        $this->set_option('daFormat', '%m/%d/%Y %H:%M');
        $this->set_option('firstDay', 1); // show Monday first
        $this->set_option('showOthers', true);
        foreach ($calendar_options as $name => $value) {
            $this->set_option($name, $value);
        }
        foreach ($calendar_field_attributes as $name => $value) {
            $this->set_field_attribute($name, $value);
        }
        if ($stripped) {
            $this->calendar_file       = 'calendar_stripped.js';
            $this->calendar_setup_file = 'calendar-setup_stripped.js';
        } else {
            $this->calendar_file       = 'calendar.js';
            $this->calendar_setup_file = 'calendar-setup.js';
        }
        $lang                      = file_exists(XOOPS_ROOT_PATH . 'modules/wfresource/class/calendar/lang/calendar-' . _LANGCODE . '.js') ? _LANGCODE : 'en';
        $this->calendar_lang_file  = 'lang/calendar-' . $lang . '.js';
        $this->calendar_lib_path   = 'modules/wfresource/class/calendar/';
        $this->calendar_theme_file = 'calendar-blue.css';
        $this->calendar_theme_url  = 'modules/wfresource/class/calendar/css/';
    }

    /**
     * XoopsFormCalendar::set_option()
     *
     * @param mixed $name
     * @param mixed $value
     */
    public function set_option($name, $value)
    {
        $this->calendar_options[$name] = $value;
    }

    /**
     * XoopsFormCalendar::set_field_attribute()
     *
     * @param mixed $name
     * @param mixed $value
     */
    public function set_field_attribute($name, $value)
    {
        $this->calendar_field_attributes[$name] = $value;
    }

    /**
     * XoopsFormCalendar::load_head_files()
     * @return string
     */
    public function load_head_files()
    {
        if (isset($GLOBALS['xo_Theme'])) {
            $GLOBALS['xo_Theme']->addStylesheet($this->calendar_theme_url . $this->calendar_theme_file);
            $GLOBALS['xo_Theme']->addScript($this->calendar_lib_path . $this->calendar_file);
            $GLOBALS['xo_Theme']->addScript($this->calendar_lib_path . $this->calendar_lang_file);
            $GLOBALS['xo_Theme']->addScript($this->calendar_lib_path . $this->calendar_setup_file);
        } else {
            $ret = '<link rel="stylesheet" type="text/css" media="all" href="' . XOOPS_URL . '/' . $this->calendar_theme_url . $this->calendar_theme_file . '" />';
            $ret .= '<script type="text/javascript" src="' . XOOPS_URL . '/' . $this->calendar_lib_path . $this->calendar_file . '"></script>';
            $ret .= '<script type="text/javascript" src="' . XOOPS_URL . '/' . $this->calendar_lib_path . $this->calendar_lang_file . '"></script>';
            $ret .= '<script type="text/javascript" src="' . XOOPS_URL . '/' . $this->calendar_lib_path . $this->calendar_setup_file . '"></script>';

            return $ret;
        }
    }

    /**
     * XoopsFormCalendar::_make_calendar()
     *
     * @param  array $other_options
     * @return string
     */
    public function _make_calendar($other_options = array())
    {
        $js_options = $this->_make_js_hash(array_merge($this->calendar_options, $other_options));
        $code       = ('<script type="text/javascript">Calendar.setup({' . $js_options . '});</script>');

        return $code;
    }

    /**
     * XoopsFormCalendar::render()
     * @return string|void
     */
    public function render()
    {
        $id  = $this->_gen_id();
        $ret = '';
        if ($id == 1) {
            $ret .= $this->load_head_files();
        }
        $attrstr = $this->_make_html_attr(array_merge($this->calendar_field_attributes, array(
            'id'   => $this->_get_id($id),
            'type' => 'text',
            'name' => $this->getName()
        )));
        $ret .= '<input ' . $attrstr . '/>';
        $ret .= '<a href="#" id="' . $this->_trigger_id($id) . '">' . '&nbsp;<img src="' . XOOPS_URL . '/' . $this->calendar_lib_path . 'img.png" style="vertical-align: middle; border: 0px;" alt="" /></a>';
        $options = array('inputField' => $this->_get_id($id), 'button' => $this->_trigger_id($id));
        $ret .= $this->_make_calendar($options);

        return $ret;
    }

    /**
     * XoopsFormCalendar::_get_id()
     *
     * @param  mixed $id
     * @return string
     */
    public function _get_id($id)
    {
        return 'calendar-field-' . $id;
    }

    /**
     * XoopsFormCalendar::_trigger_id()
     *
     * @param  mixed $id
     * @return string
     */
    public function _trigger_id($id)
    {
        return 'calendar-trigger-' . $id;
    }

    /**
     * XoopsFormCalendar::_gen_id()
     * @return int
     */
    public function _gen_id()
    {
        static $idno = 0;
        ++$idno;

        return $idno;
    }

    /**
     * XoopsFormCalendar::_make_js_hash()
     *
     * @param  mixed $array
     * @return string
     */
    public function _make_js_hash($array)
    {
        $jstr = '';
        reset($array);
        while (false !== (list($key, $val) = each($array))) {
            if (is_bool($val)) {
                $val = $val ? 'true' : 'false';
            } elseif (!is_numeric($val)) {
                $val = '"' . $val . '"';
            }
            if ($jstr) {
                $jstr .= ',';
            }
            $jstr .= '"' . $key . '":' . $val;
        }

        return $jstr;
    }

    /**
     * XoopsFormCalendar::_make_html_attr()
     *
     * @param  mixed $array
     * @return string
     */
    public function _make_html_attr($array)
    {
        $attrstr = '';
        reset($array);
        while (false !== (list($key, $val) = each($array))) {
            $attrstr .= $key . '="' . $val . '" ';
        }

        return $attrstr;
    }
}
