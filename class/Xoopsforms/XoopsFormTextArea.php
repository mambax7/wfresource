<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

/**
 * Name: formtextarea.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

use const ENT_HTML5;

/**
 * @author     Kazumi Ono <onokazu@xoops.org>
 * @copyright  copyright (c) 2000-2003 XOOPS.org
 */

/**
 * A textarea
 *
 * @author     Kazumi Ono <onokazu@xoops.org>
 * @copyright  copyright (c) 2000-2003 XOOPS.org
 */
class XoopsFormTextArea extends XoopsFormElement
{
    /**
     * number of columns
     *
     * @var int
     */
    public $_cols;
    /**
     * number of rows
     *
     * @var int
     */
    public $_rows;
    /**
     * initial content
     *
     * @var string
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
     */
    public function getRows(): int
    {
        return $this->_rows;
    }

    /**
     * Get number of columns
     */
    public function getCols(): int
    {
        return $this->_cols;
    }

    /**
     * Get initial content
     *
     * @param bool $encode To sanitizer the text? Default value should be "true"; however we have to set "false" for backward compat
     */
    public function getValue($encode = false): string
    {
        return $encode ? \htmlspecialchars($this->_value, \ENT_QUOTES | ENT_HTML5) : $this->_value;
    }

    /**
     * Set initial content
     *
     * @param string $value
     */
    public function setValue($value): void
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
