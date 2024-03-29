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
 * @author     Taiwen Jiang <phppp@users.sourceforge.net>
 * @copyright  copyright (c) 2000-2007 XOOPS.org
 */

/**
 * Abstract base class for form elements
 *
 * @author     Kazumi Ono <onokazu@xoops.org>
 * @author     Taiwen Jiang <phppp@users.sourceforge.net>
 * @copyright  copyright (c) 2000-2007 XOOPS.org
 */
class XoopsFormElement
{
    /**
     * Javascript performing additional validation of this element data
     *
     * This property contains a list of Javascript snippets that will be sent to
     * XoopsForm::renderValidationJS().
     * NB: All elements are added to the output one after the other, so don't forget
     * to add a ";" after each to ensure no Javascript syntax error is generated.
     *
     * @var array ()
     */
    public $customValidationCode = [];
    /**
     * *#@+
     */
    /**
     * "name" attribute of the element
     *
     * @var string
     */
    public $_name;
    /**
     * caption of the element
     *
     * @var string
     */
    public $_caption;
    /**
     * Accesskey for this element
     *
     * @var string
     */
    public $_accesskey = '';
    /**
     * HTML classes for this element
     *
     * @var array
     */
    public $_class = [];
    /**
     * hidden?
     *
     * @var bool
     */
    public $_hidden = false;
    /**
     * extra attributes to go in the tag
     *
     * @var array
     */
    public $_extra = [];
    /**
     * required field?
     *
     * @var bool
     */
    public $_required = false;
    /**
     * description of the field
     *
     * @var string
     */
    public $_description = '';
    /**
     * *#@-
     */

    /**
     * CAtzwolf: Modified for No Colspan
     *
     * Lets a developer have only one column in a form element rather than two.
     * Example of usgage: Allows text editors to span 2 columns rathe than pushed into one column
     *
     * @var bool
     */
    public $_nocolspan = false;

    /**
     * constructor
     */
    public function __construct()
    {
        exit('This class cannot be instantiated!');
    }

    /**
     * Is this element a container of other elements?
     *
     * @return bool false
     */
    public function isContainer()
    {
        return false;
    }

    /**
     * set the "name" attribute for the element
     *
     * @param string $name "name" attribute for the element
     */
    public function setName($name): void
    {
        $this->_name = \trim($name);
    }

    /**
     * get the "name" attribute for the element
     *
     * @param bool $encode
     * @return string "name" attribute
     */
    public function getName($encode = true)
    {
        if ($encode) {
            return \str_replace('&amp;', '&', \htmlspecialchars($this->_name, \ENT_QUOTES));
        }

        return $this->_name;
    }

    /**
     * set the "accesskey" attribute for the element
     *
     * @param string $key "accesskey" attribute for the element
     */
    public function setAccessKey($key): void
    {
        $this->_accesskey = \trim($key);
    }

    /**
     * get the "accesskey" attribute for the element
     *
     * @return string "accesskey" attribute value
     */
    public function getAccessKey(): string
    {
        return $this->_accesskey;
    }

    /**
     * If the accesskey is found in the specified string, underlines it
     *
     * @param string $str String where to search the accesskey occurence
     * @return string Enhanced string with the 1st occurence of accesskey underlined
     */
    public function getAccessString($str): string
    {
        $access = $this->getAccessKey();
        if (!empty($access) && (false !== ($pos = mb_strpos($str, $access)))) {
            $str = mb_substr($str, 0, $pos) . '<span style="text-decoration:underline">' . mb_substr($str, $pos, 1) . '</span>' . mb_substr($str, $pos + 1);
        }

        return $str;
    }

    /**
     * set the "class" attribute for the element
     *
     * @param $class "class" attribute for the element
     */
    public function setClass($class): void
    {
        $class = \trim($class);
        if (!empty($class)) {
            $this->_class[] = $class;
        }
    }

    /**
     * get the "class" attribute for the element
     *
     * @return string "class" attribute value
     */
    public function getClass(): string
    {
        if (0 == \count($this->_class)) {
            return '';
        }
        $class = [];
        foreach ($this->_class as $class) {
            $class[] = \htmlspecialchars($class, \ENT_QUOTES);
        }

        return \implode(' ', $class);
    }

    /**
     * set the caption for the element
     *
     * @param string $caption
     */
    public function setCaption($caption): void
    {
        $this->_caption = \trim($caption);
    }

    /**
     * get the caption for the element
     *
     * @param bool $encode To sanitizer the text?
     */
    public function getCaption($encode = false): string
    {
        return $encode ? \htmlspecialchars($this->_caption, \ENT_QUOTES) : $this->_caption;
    }

    /**
     * set the element's description
     *
     * @param string $description
     */
    public function setDescription($description): void
    {
        $this->_description = \trim($description);
    }

    /**
     * get the element's description
     *
     * @param bool $encode To sanitizer the text?
     */
    public function getDescription($encode = false): string
    {
        return $encode ? \htmlspecialchars($this->_description, \ENT_QUOTES) : $this->_description;
    }

    /**
     * flag the element as "hidden"
     */
    public function setHidden(): void
    {
        $this->_hidden = true;
    }

    /**
     * Find out if an element is "hidden".
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->_hidden;
    }

    /**
     * Find out if an element is required.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->_required;
    }

    /**
     * Add extra attributes to the element.
     *
     * This string will be inserted verbatim and unvalidated in the
     * element's tag. Know what you are doing!
     *
     * @param string      $extra
     * @param bool|string $replace If true, passed string will replace current content otherwise it will be appended to it
     * @return array       New content of the extra string
     */
    public function setExtra($extra, $replace = false): array
    {
        if ($replace) {
            $this->_extra = [\trim($extra)];
        } else {
            $this->_extra[] = \trim($extra);
        }

        return $this->_extra;
    }

    /**
     * Get the extra attributes for the element
     *
     * @param bool $encode To sanitizer the text?
     */
    public function getExtra($encode = false): string
    {
        if (!$encode) {
            return \implode(' ', $this->_extra);
        }
        $value = [];
        foreach ($this->_extra as $val) {
            $value[] = \str_replace('>', '&gt;', \str_replace('<', '&lt;', $val));
        }

        return 0 == \count($value) ? '' : ' ' . \implode(' ', $value);
    }

    /**
     * Set the element's nocolspan
     * Modified by Catzwolf
     *
     * @param bool $nocolspan
     */
    public function setNocolspan($nocolspan = false): void
    {
        $this->_nocolspan = (int)$nocolspan;
    }

    /**
     * Get the element's nocolspan
     * Modified by Catzwolf
     *
     * @return bool
     */
    public function getNocolspan()
    {
        return $this->_nocolspan;
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

            return "if (myform.{$eltname}.value == \"\") { window.alert(\"{$eltmsg}\"); myform.{$eltname}.focus(); return false; }";
        }

        return '';
    }

    /**
     * Generates output for the element.
     *
     * This method is abstract and must be overwritten by the child classes.
     *
     * @abstract
     */
    public function render()
    {
    }

    /**
     * get the caption for the element
     *
     * @param bool $encode To sanitizer the text?
     */
    public function getTitle($encode = false): string
    {
        if (mb_strlen($this->_description) > 0) {
            return $encode ? \htmlspecialchars(\strip_tags($this->_caption . ' - ' . $this->_description), \ENT_QUOTES) : \strip_tags($this->_caption . ' - ' . $this->_description);
        }

        return $encode ? \htmlspecialchars(\strip_tags($this->_caption), \ENT_QUOTES) : \strip_tags($this->_caption);
    }
}
