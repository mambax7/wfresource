<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

/**
 * @author     Kazumi Ono <onokazu@xoops.org>
 * @copyright  copyright (c) 2000-2003 XOOPS.org
 */

/**
 * A simple text field
 *
 * @author     Kazumi Ono <onokazu@xoops.org>
 * @copyright  copyright (c) 2000-2003 XOOPS.org
 */
class XoopsFormTextAdd extends XoopsFormElement
{
    /**
     * Size
     *
     * @var int
     */
    public $_size;
    /**
     * Maximum length of the text
     *
     * @var int
     */
    public $_maxlength;
    /**
     * Initial text
     *
     * @var string
     */
    public $_value;
    /**
     * Initial Number of boxes to display
     *
     * @var intval
     */
    public $_number;

    /**
     * Constructor
     *
     * @param string $caption   Caption
     * @param string $name      "name" attribute
     * @param int    $size      Size
     * @param int    $maxlength Maximum length of text
     * @param string $value     Initial text
     * @param int    $number
     */
    public function __construct($caption, $name, $size, $maxlength, $value = '', $number = 5)
    {
        parent::__construct();
        $this->setCaption($caption);
        $this->setName($name);
        $this->_size      = (int)$size;
        $this->_maxlength = (int)$maxlength;
        $this->setValue($value);
        $this->setNumber($number);
    }

    /**
     * Get size
     */
    public function getSize(): int
    {
        return $this->_size;
    }

    /**
     * Get maximum text length
     */
    public function getMaxlength(): int
    {
        return $this->_maxlength;
    }

    /**
     * Get initial content
     *
     * @param bool $encode To sanitizer the text? Default value should be "true"; however we have to set "false" for backward compat
     */
    public function getValue($encode = false): string
    {
        return $encode ? \htmlspecialchars($this->_value, \ENT_QUOTES) : $this->_value;
    }

    /**
     * Set initial text value
     *
     * @param string $value
     */
    public function setValue($value): void
    {
        $this->_value = $value;
    }

    /**
     * Set initial text value
     *
     * @param string $value
     */
    public function setNumber($value): void
    {
        $this->_number = $value;
    }

    /**
     * Prepare HTML for output
     */
    public function render(): void
    {
    }
}
