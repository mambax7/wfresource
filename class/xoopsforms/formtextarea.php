<?php
/**
 * Name: formtextarea.php
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
 *
 * @package    kernel
 * @subpackage form
 * @author     Kazumi Ono <onokazu@xoops.org>
 * @copyright  copyright (c) 2000-2003 XOOPS.org
 */

/**
 * A textarea
 *
 * @author     Kazumi Ono <onokazu@xoops.org>
 * @copyright  copyright (c) 2000-2003 XOOPS.org
 * @package    kernel
 * @subpackage form
 */
class XoopsFormTextArea extends XoopsFormElement
{
    /**
     * number of columns
     *
     * @var int
     * @access private
     */
    public $_cols;

    /**
     * number of rows
     *
     * @var int
     * @access private
     */
    public $_rows;

    /**
     * initial content
     *
     * @var string
     * @access private
     */
    public $_value;

    /**
     * Constuctor
     *
     * @param string $caption caption
     * @param string $name    name
     * @param string $value   initial content
     * @param int    $rows    number of rows
     * @param int    $cols    number of columns
     */
    public function __construct($caption, $name, $value = '', $rows = 5, $cols = 50)
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_rows = (int)$rows;
        $this->_cols = (int)$cols;
        $this->setValue($value);
    }

    /**
     * get number of rows
     *
     * @return int
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * Get number of columns
     *
     * @return int
     */
    public function getCols()
    {
        return $this->_cols;
    }

    /**
     * Get initial content
     *
     * @param  bool $encode To sanitizer the text? Default value should be "true"; however we have to set "false" for backward compat
     * @return string
     */
    public function getValue($encode = false)
    {
        return $encode ? htmlspecialchars($this->_value) : $this->_value;
    }

    /**
     * Set initial content
     *
     * @param  $value string
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * prepare HTML for output
     *
     * @return sting HTML
     */
    public function render()
    {
        return "<textarea name='" . $this->getName() . "' id='" . $this->getName() . "' rows='" . $this->getRows() . "' cols='" . $this->getCols() . "'" . $this->getExtra() . '>' . $this->getValue() . '</textarea>';
    }
}
