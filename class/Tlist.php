<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

use const ENT_HTML5;

/**
 * Name: Tlist.php
 * Description:
 *
 * @since     v1.0.0
 * @author    John Neill <catzwolf@users.sourceforge.net>
 * @copyright Copyright (C) 2009 Xoosla. All rights reserved.
 * @license   GNU/LGPL, see docs/license.php
 */
if (!\defined('_NEWLINE')) {
    \define('_NEWLINE', "\n");
}

/**
 * Tlist
 *
 * @author    John
 * @copyright Copyright (c) 2008
 */
class Tlist
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
     * Tlist::__construct()
     *
     * @param array|string|null $headers
     */
    public function __construct($headers = null)
    {
        if (\is_array($headers)) {
            $this->_headers[] = $headers;
            //        } else {
        }
    }

    /**
     * Tlist::AddHeader()
     *
     * @param mixed  $name
     * @param int    $size
     * @param string $align
     * @param mixed  $islink
     */
    public function addHeader($name, $size = 0, $align = 'left', $islink = false): void
    {
        $temp             = [
            'name'   => (string)$name,
            'width'  => (string)$size,
            'align'  => (string)$align,
            'islink' => $islink,
        ];
        $this->_headers[] = $temp;
        //        $this->_headers[]       = ['name' => ( string )$name, 'width' => ( string )$size, 'align' => ( string )$align, 'islink' => $islink];
        $this->_headers_count = \count($this->_headers);
    }

    /**
     * Tlist::setPrefix()
     *
     * @param mixed $value
     */
    public function setPrefix($value = null): void
    {
        $this->_pre_fix = (null !== $value) ? (string)$value : '_MD_';
    }

    /**
     * Tlist::setOutput()
     *
     * @param mixed $value
     */
    public function setOutput($value = true): void
    {
        $this->_output = (true === $value) ? true : false;
    }

    /**
     * Tlist::setPath()
     *
     * @param mixed $value
     */
    public function setPath($value): void
    {
        $this->_path = (string)$value;
    }

    /**
     * Tlist::setOp()
     *
     * @param mixed $value
     */
    public function setOp($value): void
    {
        $this->_op = (string)$value;
    }

    /**
     * Tlist::add()
     *
     * @param array|string $data
     * @param mixed        $class
     * @param mixed        $isarray
     */
    public function add($data, $class = null, $isarray = false): void
    {
        if (\is_array($data) && false !== $isarray) {
            foreach ($data as $value) {
                $this->add($value, $class); //$this->_data[] = array( $value, $class );
            }
        } else {
            $this->_data[] = [$data, $class];
        }
    }

    /**
     * Tlist::import()
     *
     * @param mixed $array
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
     * Tlist::addHidden()
     *
     * @param string $key
     * @param mixed  $value
     */
    public function addHidden($key = '', $value = ''): void
    {
        if (!empty($key)) {
            $this->_hidden[$key] = $value;
        } else {
            $this->_hidden[\htmlspecialchars($value, \ENT_QUOTES | ENT_HTML5)] = $value;
        }
    }

    /**
     * Tlist::addHiddenArray()
     *
     * @param mixed $options
     * @param mixed $multi
     */
    public function addHiddenArray($options, $multi = true): void
    {
        if (\is_array($options)) {
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
     * Tlist::noselection()
     */
    public function noselection(): string
    {
        $ret = '<tr><td colspan="' . $this->_headers_count . '" class="emptylist">' . \_AM_WFP_NORECORDS . '</td></tr>';
        if ($this->_output) {
            echo $ret;
        } else {
            return $ret;
        }

        return '';
    }

    /**
     * Tlist::addFooter()
     *
     * @param string $value
     */
    public function addFooter($value = ''): void
    {
        $this->_footer = !empty($value) ? $value : '';
    }

    /**
     * Tlist::footer_listing()
     *
     * @param string $align
     */
    public function footer_listing($align = 'right'): string
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
     * Tlist::addFormStart()
     *
     * @param string $method
     * @param string $action
     * @param string $name
     */
    public function addFormStart($method = 'post', $action = '', $name = ''): void
    {
        $this->_formName = (string)$name;
        if ($this->_formName) {
            if (!empty($action)) {
                $action = $action;
            } elseif (!empty($_SERVER['QUERY_STRING'])) {
                //                $action = 'index.php' . htmlspecialchars($_SERVER['QUERY_STRING']);
                $action = 'main.php' . \htmlspecialchars($_SERVER['QUERY_STRING'], \ENT_QUOTES | ENT_HTML5);
            } elseif (\Xmf\Request::hasVar('SCRIPT_FILENAME', 'SERVER')) {
                $action = \basename($_SERVER['SCRIPT_FILENAME']);
            } else {
                //                $action = 'index.php';
                $action = 'main.php';
            }
            $this->_formAction .= '<form name="' . $this->_formName . '" action="' . $action . '" method="' . $method . '" >';
        }
    }

    /**
     * Tlist::addFormEnd()
     */
    public function addFormEnd(): string
    {
        if ($this->_formName) {
            return '</form>';
        }

        return '';
    }

    /**
     * Tlist::addSubmit()
     *
     * @param array|string $array
     */
    public function setSubmit($array): string
    {
        if (empty($array)) {
            $array = [
                'updateall'    => \_AM_WFC_UPDATESELECTED,
                'deleteall'    => \_AM_WFC_DELETESELECTED,
                'duplicateall' => \_AM_WFC_DUPLICATESELECTED,
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
     * Tlist::render()
     *
     * @param bool $display
     */
    public function render($display = true): string
    {
        $ret   = $this->_formAction;
        $count = \count($this->_headers);
        $ret   .= '<table id="tlist" width="100%" cellpadding="0" cellspacing="1" class="outer" summary="">';
        $ret   .= '<tr style="text-align: center;">';
        //=======================================
        //     foreach ($this->_headers as $value) {
        foreach ($this->_headers as $key => $value) {
            //        $value = $this->_headers;
            $width = !empty($value['width']) ? 'width: ' . $value['width'] . ';' : '';
            $ret   .= '<th style="text-align: ' . $value['align'] . '; ' . $width . '" >';
            if (2 == (int)$value['islink']) {
                $ret .= Utility::getConstants($this->_pre_fix . $value['name']);
                $ret .= '<input name="' . $value['name'] . 'x_checkall" id="' . $value['name'] . 'x_checkall" onclick="xoopsCheckAll( \'' . $this->_formName . '\', \'' . $value['name'] . 'x_checkall\');" type="checkbox" value="Check All">';
            } elseif (true === $value['islink']) {
                $ret .= '<a href="main.php?';
                if ($this->_path) {
                    $ret .= $this->_path . '&amp;';
                }
                $ret .= 'sort=' . $value['name'] . '&amp;order=ASC">' . Utility::showImage('down', 'down', 'middle') . '</a>';
                $ret .= Utility::getConstants($this->_pre_fix . $value['name']);
                $ret .= '<a href="main.php?';
                if ($this->_path) {
                    $ret .= $this->_path . '&amp;';
                }
                $ret .= 'sort=' . $value['name'] . '&amp;order=DESC">' . Utility::showImage('up', 'up', 'middle') . '</a>';
            } else {
                $ret .= Utility::getConstants($this->_pre_fix . $value['name']);
            }
            $ret .= '</th>';
        }

        //===================================
        $ret   .= '</tr>';
        $count = \count($this->_data);
        $class = '';
        if (isset($this->_data[0]) && $count) {
            foreach ($this->_data as $data) {
                if (!empty($data[1])) {
                    $class = $data[1];
                } else {
                    $class = (null !== $class && 'even' === $class) ? 'odd' : 'even';
                }
                $ret .= '<tr class="' . $class . '">' . \_NEWLINE;
                $i   = 0;
                if (true !== $data[1]) {
                    foreach ($data[0] as $value) {
                        $ret .= '<td style="text-align: ' //                                . $this->_headers[$i]['align']
                                . ($this->_headers['align'] ?? '') . ';">' . $value . '</td>';
                        ++$i;
                    }
                    $ret .= '</tr>' . \_NEWLINE;
                }
            }
        } else {
            $ret .= $this->noselection();
        }
        $ret .= $this->footer_listing('right');
        $ret .= '</table>' . \_NEWLINE;
        if ($this->_hidden && \is_array($this->_hidden)) {
            foreach ($this->_hidden as $k => $v) {
                $ret .= '<input type="hidden" name="' . $k . '" id="' . $v . $k . '" value="' . \htmlspecialchars($v, \ENT_QUOTES | ENT_HTML5) . '">';
            }
        }
        $ret .= $GLOBALS['xoopsSecurity']->getTokenHTML();
        $ret .= $this->addFormEnd();
        if ($display) {
            echo $ret;
        } else {
            return $ret;
        }
        unset($ret, $count, $class);

        return '';
    }
}
