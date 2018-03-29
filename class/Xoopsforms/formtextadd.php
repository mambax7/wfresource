<?php
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
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');
/**
 *
 * @package    kernel
 * @subpackage form
 * @author     Kazumi Ono <onokazu@xoops.org>
 * @copyright  copyright (c) 2000-2003 XOOPS.org
 */

/**
 * A simple text field
 *
 * @package    kernel
 * @subpackage form
 * @author     Kazumi Ono <onokazu@xoops.org>
 * @copyright  copyright (c) 2000-2003 XOOPS.org
 */
class XoopsFormTextAdd extends XoopsFormElement
{
    /**
     * Size
     *
     * @var int
     * @access private
     */
    public $_size;

    /**
     * Maximum length of the text
     *
     * @var int
     * @access private
     */
    public $_maxlength;

    /**
     * Initial text
     *
     * @var string
     * @access private
     */
    public $_value;

    /**
     * Initial Number of boxes to display
     *
     * @var intval
     * @access private
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
     *
     * @return int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * Get maximum text length
     *
     * @return int
     */
    public function getMaxlength()
    {
        return $this->_maxlength;
    }

    /**
     * Get initial content
     *
     * @param  bool $encode To sanitizer the text? Default value should be "true"; however we have to set "false" for backward compat
     * @return string
     */
    public function getValue($encode = false)
    {
        return $encode ? htmlspecialchars($this->_value, ENT_QUOTES) : $this->_value;
    }

    /**
     * Set initial text value
     *
     * @param  $value string
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Set initial text value
     *
     * @param  $value string
     */
    public function setNumber($value)
    {
        $this->_number = $value;
    }

    /**
     * Prepare HTML for output
     *
     * @return void HTML
     */
    public function render()
    {
    }
}
