<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

/**
 * Name: formselect.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

//use XoopsModules\Wfresource;


//if (!class_exists('XoopsFormElement')) {
//    require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formelement.php';
//}

/**
 * A select field
 *
 * @author     Kazumi Ono <onokazu@xoops.org>
 * @copyright  copyright (c) 2000-2003 XOOPS.org
 */
class XoopsFormSelect extends XoopsFormElement
{
    /**
     * Options
     *
     * @var array
     */
    public $_options = [];
    /**
     * Allow multiple selections?
     *
     * @var bool
     */
    public $_multiple = false;
    /**
     * Number of rows. "1" makes a dropdown list.
     *
     * @var int
     */
    public $_size;
    /**
     * Pre-selcted values
     *
     * @var array
     */
    public $_value = [];

    /**
     * Constructor
     *
     * @param string $caption  Caption
     * @param string $name     "name" attribute
     * @param mixed  $value    Pre-selected value (or array of them).
     * @param int    $size     Number or rows. "1" makes a drop-down-list
     * @param bool   $multiple Allow multiple selections?
     */
    public function __construct($caption, $name, $value = null, $size = 1, $multiple = false)
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_multiple = $multiple;
        $this->_size     = (int)$size;
        if (null !== $value) {
            $this->setValue($value);
        }
    }

    /**
     * Are multiple selections allowed?
     *
     * @return bool
     */
    public function isMultiple()
    {
        return $this->_multiple;
    }

    /**
     * Get the size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * Get an array of pre-selected values
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
     * Set pre-selected values
     *
     * @param mixed $value
     */
    public function setValue($value): void
    {
        if (\is_array($value)) {
            foreach ($value as $v) {
                $this->_value[] = $v;
            }
        } elseif (null !== $value) {
            $this->_value[] = $value;
        }
    }

    /**
     * Add an option
     *
     * @param string $value "value" attribute
     * @param string $name  "name" attribute
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
     * Add multiple options
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
     * Note: both name and value should be sanitized. However, for backward compatibility, only value is sanitized for now.
     *
     * @param bool|int $encode To sanitizer the text? potential values: 0 - skip; 1 - only for value; 2 - for both value and name
     * @return array    Associative array of value->name pairs
     */
    public function getOptions($encode = false)
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
     * Prepare HTML for output
     *
     * @return string HTML
     */
    public function render()
    {
        $ele_name    = $this->getName();
        $ele_value   = $this->getValue();
        $ele_options = $this->getOptions();
        $ret         = "<select size='" . $this->getSize() . "'" . $this->getExtra();
        if ($this->isMultiple()) {
            $ret .= " name='{$ele_name}[]' id='{$ele_name}' multiple='multiple'>\n";
        } else {
            $ret .= " name='{$ele_name}' id='{$ele_name}'>\n";
        }
        foreach ($ele_options as $value => $name) {
            $ret .= "<option value='" . \htmlspecialchars($value, \ENT_QUOTES) . "'";
            if (\count($ele_value) > 0 && \in_array($value, $ele_value, true)) {
                $ret .= ' selected';
            }
            $ret .= ">{$name}</option>\n";
        }
        $ret .= '</select>';

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

            return "\nvar hasSelected = false; var selectBox = myform.{$eltname};"
                   . 'for (i = 0; i < selectBox.options.length; i++) { if (selectBox.options[i].selected === true) { hasSelected = true; break; } }'
                   . "if (!hasSelected) { window.alert(\"{$eltmsg}\"); selectBox.focus(); return false; }";
        }

        return '';
    }
}
