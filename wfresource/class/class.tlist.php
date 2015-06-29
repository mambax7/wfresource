<?php
/**
 * Name: wfp_Tlist.php
 * Description:
 *
 * @package   Xoosla CMS
 * @subpackage
 * @since     v1.0.0
 * @author    John Neill <catzwolf@users.sourceforge.net>
 * @copyright Copyright (C) 2009 Xoosla. All rights reserved.
 * @license   GNU/LGPL, see docs/license.php
 * @version   $Id: class.tlist.php 10055 2012-08-11 12:46:10Z beckmi $
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

if (!defined('_NEWLINE')) {
    define('_NEWLINE', "\n");
}

/**
 * wfp_Tlist
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2008
 * @version   $Id: class.tlist.php 10055 2012-08-11 12:46:10Z beckmi $
 * @access    public
 */
class wfp_Tlist
{
    public $_headers;
    public $_data          = array();
    public $_pre_fix       = '_MD_';
    public $_output        = false;
    public $_footer        = false;
    public $_hidden;
    public $_path;
    public $_formName;
    public $_formAction;
    public $_submitArray;
    public $_headers_count = 0;

    /**
     * wfp_Tlist::__Construct()
     *
     * @param array $headers
     */
    public function __Construct($headers = array())
    {
        $this->_headers = $headers;
    }

    /**
     * wfp_Tlist::AddHeader()
     *
     * @param mixed   $name
     * @param integer $size
     * @param string  $align
     * @param mixed   $islink
     * @return
     */
    public function AddHeader($name, $size = 0, $align = 'left', $islink = false)
    {
        $this->_headers[]     = array('name' => (string)$name, 'width' => (string)$size, 'align' => (string)$align, 'islink' => $islink);
        $this->_headers_count = count($this->_headers);
    }

    /**
     * wfp_Tlist::setPrefix()
     *
     * @param mixed $value
     * @return
     */
    public function setPrefix($value = null)
    {
        $this->_pre_fix = ($value != null) ? strval($value) : '_MD_';
    }

    /**
     * wfp_Tlist::setOutput()
     *
     * @param mixed $value
     * @return
     */
    public function setOutput($value = true)
    {
        $this->_output = ($value == true) ? true : false;
    }

    /**
     * wfp_Tlist::setPath()
     *
     * @param mixed $value
     * @return
     */
    public function setPath($value)
    {
        $this->_path = strval($value);
    }

    /**
     * wfp_Tlist::setOp()
     *
     * @param mixed $value
     * @return
     */
    public function setOp($value)
    {
        $this->_op = strval($value);
    }

    /**
     * wfp_Tlist::add()
     *
     * @param mixed $data
     * @param mixed $class
     * @param mixed $isarray
     * @return
     */
    public function add($data, $class = null, $isarray = false)
    {
        if ($isarray != false) {
            foreach ($data as $value) {
                self::add($value, $class); //$this->_data[] = array( $value, $class );
            }
        } else {
            $this->_data[] = array($data, $class);
        }
    }

    /**
     * wfp_Tlist::import()
     *
     * @param mixed $array
     * @return
     */
    public function import($array)
    {
        if (empty($array)) {
            return false;
        }
        foreach ((array)$array as $a_rrays) {
            $this->add($a_rrays, $class = null, $isarray = false);
        }
    }

    /**
     * wfp_Tlist::addHidden()
     *
     * @param mixed  $value
     * @param string $name
     * @return
     */
    public function addHidden($key = '', $value = '')
    {
        if (!empty($key)) {
            $this->_hidden[$key] = $value;
        } else {
            $this->_hidden[htmlspecialchars($value)] = $value;
        }
    }

    /**
     * wfp_Tlist::addHiddenArray()
     *
     * @param mixed $options
     * @param mixed $multi
     * @return
     */
    public function addHiddenArray($options, $multi = true)
    {
        if (is_array($options)) {
            if ($multi == true) {
                foreach ($options as $k => $v) {
                    $this->addHidden($k, $v);
                }
            } else {
                foreach ($options as $k) {
                    $this->addHidden($k, $k);
                }
            }
        }
    }

    /**
     * wfp_Tlist::noselection()
     *
     * @return
     */
    public function noselection()
    {
        $ret = '<tr><td colspan="' . $this->_headers_count . '" class="emptylist">' . _MD_WFP_NORECORDS . '</td></tr>';
        if ($this->_output) {
            echo $ret;
        } else {
            return $ret;
        }
    }

    /**
     * wfp_Tlist::addFooter()
     *
     * @param string $value
     * @return
     */
    public function addFooter($value = '')
    {
        $this->_footer = (!empty($value)) ? $value : '';
    }

    /**
     * wfp_Tlist::footer_listing()
     *
     * @param string $align
     * @return
     */
    public function footer_listing($align = 'right')
    {
        $ret = '<tr style="text-align: ' . $align . ';"><td colspan="' . $this->_headers_count . '" class="foot">';
        // if ( is_array( $this->_footer ) && !empty( $this->_footer ) ) {
        $ret .= $this->setSubmit($this->_footer);
        // }
        $ret .= '</td></tr>';
        if ($this->_output) {
            echo $ret;
        } else {
            return $ret;
        }
    }

    /**
     * wfp_Tlist::addFormStart()
     *
     * @param string $method
     * @param string $action
     * @param string $name
     * @return
     */
    public function addFormStart($method = 'post', $action = '', $name = '')
    {
        $this->_formName = strval($name);
        if ($this->_formName) {
            if (!empty($action)) {
                $action = $action;
            } elseif (!empty($_SERVER['QUERY_STRING'])) {
                $action = 'index.php' . htmlspecialchars($_SERVER['QUERY_STRING']);
            } elseif (isset($_SERVER['SCRIPT_FILENAME'])) {
                $action = basename($_SERVER['SCRIPT_FILENAME']);
            } else {
                $action = 'index.php';
            }
            $this->_formAction .= '<form name="' . $this->_formName . '" action="' . $action . '" method="' . $method . '" >';
        }
    }

    /**
     * wfp_Tlist::addFormEnd()
     *
     * @return
     */
    public function addFormEnd()
    {
        if ($this->_formName) {
            return '</form>';
        }
    }

    /**
     * wfp_Tlist::addSubmit()
     *
     * @param string $value
     * @param string $name
     * @param array  $_array
     * @return
     */
    public function setSubmit($array = array())
    {
        if (empty($array)) {
            $array = array(
                'updateall'    => _MD_WFC_UPDATESELECTED,
                'deleteall'    => _MD_WFC_DELETESELECTED,
                'duplicateall' => _MD_WFC_DUPLICATESELECTED);
        }
        $ret = '<select size="1" name="op" id="op">';
        if (!empty($array)) {
            foreach ($array as $k => $v) {
                $ret .= '<option value="' . $k . '">' . $v . '</option>';
            }
        }
        $ret .= '</select>&nbsp;';
        $ret .= '<input type="submit" name="Submit" value="' . _SUBMIT . '"  />';

        return $ret;
    }

    /**
     * wfp_Tlist::render()
     *
     * @return
     */
    public function render($display = true)
    {
        $ret   = $this->_formAction;
        $count = count($this->_headers);
        $ret .= '<table id="tlist" width="100%" cellpadding="0" cellspacing="1" class="outer" summary="">';
        $ret .= '<tr style="text-align: center;">';
        foreach ($this->_headers as $value) {
            $width = (!empty($value['width'])) ? 'width: ' . $value['width'] . ';' : '';
            $ret .= '<th style="text-align: ' . $value['align'] . '; ' . $width . '" >';
            if ((int)$value['islink'] == 2) {
                $ret .= wfp_getConstants($this->_pre_fix . $value['name']);
                $ret .= '<input name="' . $value['name'] . 'x_checkall" id="' . $value['name'] . 'x_checkall" onclick="xoopsCheckAll( \'' . $this->_formName . '\', \'' . $value["name"] . 'x_checkall\');" type="checkbox" value="Check All" />';
            } elseif ($value['islink'] == true) {
                $ret .= '<a href="index.php?';
                if ($this->_path) {
                    $ret .= $this->_path . '&amp;';
                }
                $ret .= 'sort=' . $value['name'] . '&amp;order=ASC">' . wfp_showImage('down', 'down', 'middle') . '</a>';
                $ret .= wfp_getConstants($this->_pre_fix . $value['name']);
                $ret .= '<a href="index.php?';
                if ($this->_path) {
                    $ret .= $this->_path . '&amp;';
                }
                $ret .= 'sort=' . $value['name'] . '&amp;order=DESC">' . wfp_showImage('up', 'up', 'middle') . '</a>';
            } else {
                $ret .= wfp_getConstants($this->_pre_fix . $value['name']);
            }
            $ret .= '</th>';
        }
        $ret .= '</tr>';
        $count = count($this->_data);
        if (isset($this->_data[0]) && $count) {
            foreach ($this->_data as $data) {
                if (!empty($data[1])) {
                    $class = $data[1];
                } else {
                    $class = (isset($class) && $class == 'even') ? 'odd' : 'even';
                }
                $ret .= '<tr class="' . $class . '">' . _NEWLINE;
                $i = 0;
                if ($data[1] != true) {
                    foreach ($data[0] as $value) {
                        $ret .= '<td style="text-align: ' . $this->_headers[$i]['align'] . ';">' . $value . '</td>';
                        ++$i;
                    }
                    $ret .= '</tr>' . _NEWLINE;
                }
            }
        } else {
            $ret .= $this->noselection();
        }
        $ret .= $this->footer_listing('right');
        $ret .= '</table>' . _NEWLINE;
        if (count($this->_hidden)) {
            foreach ($this->_hidden as $k => $v) {
                $ret .= '<input type="hidden" name="' . $k . '" id="' . $v . $k . '" value="' . htmlspecialchars($v) . '" />';
            }
        }
        $ret .= $GLOBALS['xoopsSecurity']->getTokenHTML();
        $ret .= $this->addFormEnd();
        if ($display == true) {
            echo $ret;
        } else {
            return $ret;
        }
        unset($ret, $count, $class);
    }
}
