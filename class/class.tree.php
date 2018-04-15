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

/**
 * A tree structures with {@link XoopsObject}s as nodes
 *
 * @package       kernel
 * @subpackage    core
 * @author        Kazumi Ono <onokazu@xoops.org>
 * @copyright (c) 2000-2003 The Xoops Project - www.xoops.org
 */
require_once XOOPS_ROOT_PATH . '/class/tree.php';

/**
 * Class wfpObjectTree
 */
class wfpObjectTree extends \XoopsObjectTree
{
    /**
     * Make a select box with options from the tree
     *
     * @param  string  $name           Name of the select box
     * @param  string  $fieldName      Name of the member variable from the
     *                                 node objects that should be used as the title for the options.
     * @param  string  $prefix         String to indent deeper levels
     * @param  string  $selected       Value to display as selected
     * @param  bool    $addEmptyOption Set TRUE to add an empty option with value "0" at the top of the hierarchy
     * @param  integer $key            ID of the object to display as the root of select options
     * @param  string  $extra
     * @return string  HTML select box
     */
    public function makeSelBox(
        $name,
        $fieldName,
        $prefix = '-',
        $selected = '',
        $addEmptyOption = false,
        $key = 0,
        $extra = ''
    ) {
        $ret = '<select name="' . $name . '" id="' . $name . '" ' . $extra . '>';
        if (false !== $addEmptyOption) {
            $ret .= '<option value="0"></option>';
        }
        $this->_makeSelBoxOptions($fieldName, $selected, $key, $ret, $prefix);

        return $ret . '</select>';
    }
}
