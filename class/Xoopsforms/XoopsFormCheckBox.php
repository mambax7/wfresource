<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

/**
 * Name: formcheckbox.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

use const ENT_HTML5;

\xoops_load('xoopsformelement');

/**
 * Class XoopsFormCheckBox
 */
class XoopsFormCheckBox extends XoopsFormElement
{
    /**
     * Availlable options
     *
     * @var array
     */
    public $_options = [];
    /**
     * pre-selected values in array
     *
     * @var array
     */
    public $_value = [];
    /**
     * HTML to seperate the elements
     *
     * @var string
     */
    public $_delimeter;
    /**
     * Column number for rendering
     *
     * @var int
     */
    public $columns;
    private bool $_showbutton;

    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param mixed  $value Either one value as a string or an array of them.
     * @param string $delimeter
     * @param bool   $show_button
     */
    public function __construct($caption, $name, $value = null, $delimeter = '&nbsp;', $show_button = false)
    {
        $this->setCaption($caption);
        $this->setName($name);
        if (null !== $value) {
            $this->setValue($value);
        }
        $this->_delimeter  = $delimeter;
        $this->_showbutton = $show_button;
    }

    /**
     * Get the "value"
     *
     * @param bool $encode To sanitizer the text?
     */
    public function getValue($encode = false): array
    {
        if (!$encode) {
            return $this->_value;
        }
        $value = [];
        foreach ($this->_value as $val) {
            $value[] = $val ? \htmlspecialchars($val, \ENT_QUOTES) : $val;
        }

        return $value;
    }

    /**
     * Set the "value"
     *
     * @param $value
     */
    public function setValue($value): void
    {
        $this->_value = [];
        if (\is_array($value)) {
            foreach ($value as $v) {
                $this->_value[] = $v;
            }
        } else {
            $this->_value[] = $value;
        }
    }

    /**
     * Add an option
     *
     * @param string $value
     * @param string $name
     */
    public function addOption($value, $name = ''): void
    {
        if ('' !== $name) {
            $this->_options[$value] = $name;
        } else {
            $this->_options[$value] = $value;
        }
    }

    /**
     * Add multiple Options at once
     *
     * @param array $options Associative array of value->name pairs
     */
    public function addOptionArray($options): void
    {
        if (\is_array($options)) {
            foreach ($options as $k => $v) {
                $this->addOption($k, $v);
            }
        }
    }

    /**
     * Get an array with all the options
     *
     * @param bool|int $encode To sanitizer the text? potential values: 0 - skip; 1 - only for value; 2 - for both value and name
     * @return array    Associative array of value->name pairs
     */
    public function getOptions($encode = false): array
    {
        if (!$encode) {
            return $this->_options;
        }
        $value = [];
        foreach ($this->_options as $val => $name) {
            $value[$encode ? \htmlspecialchars($val, \ENT_QUOTES) : $val] = ($encode > 1) ? \htmlspecialchars($name, \ENT_QUOTES) : $name;
        }

        return $value;
    }

    /**
     * Get the delimiter of this group
     *
     * @param bool $encode To sanitizer the text?
     * @return string The delimiter
     */
    public function getDelimeter($encode = false): string
    {
        return $encode ? \htmlspecialchars(\str_replace('&nbsp;', ' ', $this->_delimeter), \ENT_QUOTES | ENT_HTML5) : $this->_delimeter;
    }

    /**
     * prepare HTML for output
     *
     * @return string
     */
    public function render()
    {
        require_once XOOPS_ROOT_PATH . '/modules/wfresource/include/js/wfpselectall.js.php';

        $ele_name      = $this->getName();
        $ele_id        = $ele_name;
        $ele_value     = $this->getValue();
        $ele_options   = $this->getOptions();
        $ele_extra     = $this->getExtra();
        $ele_delimeter = empty($this->columns) ? $this->getDelimeter() : '';
        if (\count($ele_options) > 1 && '[]' !== mb_substr($ele_name, -2, 2)) {
            $ele_name .= '[]';
            $this->setName($ele_name);
        }
        $ret = '';
        if (!empty($this->columns)) {
            $ret .= '<table><tr>';
        }
        $i      = 0;
        $id_ele = 0;
        foreach ($ele_options as $value => $name) {
            ++$id_ele;
            if (!empty($this->columns)) {
                if (0 == $i % $this->columns) {
                    $ret .= '<tr>';
                }
                $ret .= '<td>';
            }
            $ret .= "<input type='checkbox' name='{$ele_name}' id='{$ele_id}{$id_ele}' value='" . \htmlspecialchars($value, \ENT_QUOTES) . "'";
            if (\count($ele_value) > 0 && \in_array($value, $ele_value, true)) {
                $ret .= ' checked';
            }
            $ret .= $ele_extra . '>' . $name . $ele_delimeter . "\n";
            if (!empty($this->columns)) {
                $ret .= '</td>';
                if (0 == ++$i % $this->columns) {
                    $ret .= '</tr>';
                }
            }
        }
        if (!empty($this->columns)) {
            if (false !== ($span = $i % $this->columns)) {
                $ret .= "<td width='33%' colspan='" . ($this->columns - $span) . "'></td></tr>";
            }
            $ret .= '</table>';
        }
        if ($this->_showbutton) {
            $ret .= \_AM_WFP_SELECTALL . '<input name="' . $ele_name . 'x_checkall" type="checkbox" onClick="this.value=xoopsCheckAllElementsButton(this,\'' . $this->getName() . '\')" value="' . \_AM_WFP_SELECTALL . '">';
        }

        return $ret;
    }

    /**
     * Render custom javascript validation code
     *
     * @seealso XoopsForm::renderValidationJS
     */
    public function renderValidationJS()
    {
        // render custom validation code if any
        if (0 !== \count($this->customValidationCode)) {
            return \implode("\n", $this->customValidationCode);
            // generate validation code if required
        }

        if ($this->isRequired()) {
            $eltname    = $this->getName();
            $eltcaption = $this->getCaption();
            $eltmsg     = empty($eltcaption) ? \sprintf(_FORM_ENTER, $eltname) : \sprintf(_FORM_ENTER, $eltcaption);
            $eltmsg     = \str_replace('"', '\"', \stripslashes($eltmsg));

            return "\nvar hasChecked = false; var checkBox = myform.elements['{$eltname}'];" . 'for (var i = 0; i < checkBox.length; i++) { if (checkBox[i].checked === true) { hasChecked = true; break; } }' . "if (!hasChecked) { window.alert(\"{$eltmsg}\"); checkBox[0].focus(); return false; }";
        }

        return '';
    }
}
