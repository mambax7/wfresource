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
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

if (!defined('_NEWLINE')) {
    define('_NEWLINE', "\n");
}

/**
 * wfp_Tlist
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2008
 * @access    public
 */
class wfp_Tlist
{
    public $_headers       = [];
    public $_data          = [];
    public $_pre_fix       = '_AM_';
    public $_output        = false;
    public $_footer        = false;
    public $_hidden;
    public $_path;
    public $_formName;
    public $_formAction;
    public $_submitArray;
    public $_headers_count = 0;

    /**
     * wfp_Tlist::__construct()
     *
     * @param array|string $headers
     */
    public function __construct($headers)
    {
        if (is_array($headers)) {
            $this->_headers[] = $headers;
//        } else {
        }
    }

    /**
     * wfp_Tlist::AddHeader()
     *
     * @param mixed   $name
     * @param integer $size
     * @param string  $align
     * @param mixed   $islink
     */
    public function addHeader($name, $size = 0, $align = 'left', $islink = false)
    {
        $temp                 = [
            'name'   => (string)$name,
            'width'  => (string)$size,
            'align'  => (string)$align,
            'islink' => $islink
        ];
        $this->_headers[]     = $temp;
        $this->_headers_count = count($this->_headers);
    }

    /**
     * wfp_Tlist::setPrefix()
     *
     * @param mixed $value
     */
    public function setPrefix($value = null)
    {
        $this->_pre_fix = (null !== $value) ? (string)$value : '_MD_';
    }

    /**
     * wfp_Tlist::setOutput()
     *
     * @param mixed $value
     */
    public function setOutput($value = true)
    {
        $this->_output = (true === $value) ? true : false;
    }

    /**
     * wfp_Tlist::setPath()
     *
     * @param mixed $value
     */
    public function setPath($value)
    {
        $this->_path = (string)$value;
    }

    /**
     * wfp_Tlist::setOp()
     *
     * @param mixed $value
     */
    public function setOp($value)
    {
        $this->_op = (string)$value;
    }

    /**
     * wfp_Tlist::add()
     *
     * @param array|string $data
     * @param mixed $class
     * @param mixed $isarray
     */
    public function add($data, $class = null, $isarray = false)
    {
        if (is_array($data) && false !== $isarray) {
            foreach ($data as $value) {
                $this->add($value, $class); //$this->_data[] = array( $value, $class );
            }
        } else {
            $this->_data[] = [$data, $class];
        }
    }

    /**
     * wfp_Tlist::import()
     *
     * @param  mixed $array
     * @return bool
     */
    public function import($array)
    {
        if (empty($array)) {
            return false;
        }
        foreach ((array)$array as $a_rrays) {
            $this->add($a_rrays, $class = null, $isarray = false);
        }
        return '';
    }

    /**
     * wfp_Tlist::addHidden()
     *
     * @param string $key
     * @param mixed  $value
     */
    public function addHidden($key = '', $value = '')
    {
        if (!empty($key)) {
            $this->_hidden[$key] = $value;
        } else {
            $this->_hidden[htmlspecialchars($value, ENT_QUOTES | ENT_HTML5)] = $value;
        }
    }

    /**
     * wfp_Tlist::addHiddenArray()
     *
     * @param mixed $options
     * @param mixed $multi
     */
    public function addHiddenArray($options, $multi = true)
    {
        if (is_array($options)) {
            if (true === $multi) {
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
     * @return string
     */
    public function noselection()
    {
        $ret = '<tr><td colspan="' . $this->_headers_count . '" class="emptylist">' . _AM_WFP_NORECORDS . '</td></tr>';
        if ($this->_output) {
            echo $ret;
        } else {
            return $ret;
        }
        return '';
    }

    /**
     * wfp_Tlist::addFooter()
     *
     * @param string $value
     */
    public function addFooter($value = '')
    {
        $this->_footer = (!empty($value)) ? $value : '';
    }

    /**
     * wfp_Tlist::footer_listing()
     *
     * @param  string $align
     * @return string
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
        return '';
    }

    /**
     * wfp_Tlist::addFormStart()
     *
     * @param string $method
     * @param string $action
     * @param string $name
     */
    public function addFormStart($method = 'post', $action = '', $name = '')
    {
        $this->_formName = (string)$name;
        if ($this->_formName) {
            if (!empty($action)) {
                $action = $action;
            } elseif (!empty($_SERVER['QUERY_STRING'])) {
                //                $action = 'index.php' . htmlspecialchars($_SERVER['QUERY_STRING']);
                $action = 'main.php' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES | ENT_HTML5);
            } elseif (isset($_SERVER['SCRIPT_FILENAME'])) {
                $action = basename($_SERVER['SCRIPT_FILENAME']);
            } else {
                //                $action = 'index.php';
                $action = 'main.php';
            }
            $this->_formAction .= '<form name="' . $this->_formName . '" action="' . $action . '" method="' . $method . '" >';
        }
    }

    /**
     * wfp_Tlist::addFormEnd()
     * @return string
     */
    public function addFormEnd()
    {
        if ($this->_formName) {
            return '</form>';
        }
        return '';
    }

    /**
     * wfp_Tlist::addSubmit()
     *
     * @param  array|string $array
     * @return string
     */
    public function setSubmit($array)
    {
        if (empty($array)) {
            $array = [
                'updateall'    => _AM_WFC_UPDATESELECTED,
                'deleteall'    => _AM_WFC_DELETESELECTED,
                'duplicateall' => _AM_WFC_DUPLICATESELECTED
            ];
        }
        $ret = '<select size="1" name="op" id="op">';
        if (!empty($array)) {
            foreach ($array as $k => $v) {
                $ret .= '<option value="' . $k . '">' . $v . '</option>';
            }
        }
        $ret .= '</select>&nbsp;';
        $ret .= '<input type="submit" name="Submit" value="' . _SUBMIT . '" >';

        return $ret;
    }

    /**
     * wfp_Tlist::render()
     *
     * @param  bool $display
     * @return string
     */
    public function render($display = true)
    {
        $ret   = $this->_formAction;
        $count = count($this->_headers);
        $ret   .= '<table id="tlist" width="100%" cellpadding="0" cellspacing="1" class="outer" summary="">';
        $ret   .= '<tr style="text-align: center;">';
        foreach ($this->_headers as $value) {
            $width = (!empty($value['width'])) ? 'width: ' . $value['width'] . ';' : '';
            $ret   .= '<th style="text-align: ' . $value['align'] . '; ' . $width . '" >';
            if (2 === (int)$value['islink']) {
                $ret .= wfp_getConstants($this->_pre_fix . $value['name']);
                $ret .= '<input name="' . $value['name'] . 'x_checkall" id="' . $value['name'] . 'x_checkall" onclick="xoopsCheckAll( \'' . $this->_formName . '\', \'' . $value['name'] . 'x_checkall\');" type="checkbox" value="Check All">';
            } elseif (true === $value['islink']) {
                $ret .= '<a href="main.php?';
                if ($this->_path) {
                    $ret .= $this->_path . '&amp;';
                }
                $ret .= 'sort=' . $value['name'] . '&amp;order=ASC">' . wfp_showImage('down', 'down', 'middle') . '</a>';
                $ret .= wfp_getConstants($this->_pre_fix . $value['name']);
                $ret .= '<a href="main.php?';
                if ($this->_path) {
                    $ret .= $this->_path . '&amp;';
                }
                $ret .= 'sort=' . $value['name'] . '&amp;order=DESC">' . wfp_showImage('up', 'up', 'middle') . '</a>';
            } else {
                $ret .= wfp_getConstants($this->_pre_fix . $value['name']);
            }
            $ret .= '</th>';
        }
        $ret   .= '</tr>';
        $count = count($this->_data);
        $class = '';
        if (isset($this->_data[0]) && $count) {
            foreach ($this->_data as $data) {
                if (!empty($data[1])) {
                    $class = $data[1];
                } else {
                    $class = (null !== $class && 'even' === $class) ? 'odd' : 'even';
                }
                $ret .= '<tr class="' . $class . '">' . _NEWLINE;
                $i   = 0;
                if (true !== $data[1]) {
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
        if (is_array($this->_hidden) && count($this->_hidden)>0) {
            foreach ($this->_hidden as $k => $v) {
                $ret .= '<input type="hidden" name="' . $k . '" id="' . $v . $k . '" value="' . htmlspecialchars($v, ENT_QUOTES | ENT_HTML5) . '">';
            }
        }
        $ret .= $GLOBALS['xoopsSecurity']->getTokenHTML();
        $ret .= $this->addFormEnd();
        if (true === $display) {
            echo $ret;
        } else {
            return $ret;
        }
        unset($ret, $count, $class);
        return '';
    }
}
