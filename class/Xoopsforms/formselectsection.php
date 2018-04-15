<?php

// ------------------------------------------------------------------------ //
// Xoops - PHP Content Management System                                //
// Copyright (c) 2007 Xoops                                         //
//                                                                          //
// Authors:                                                                 //
// John Neill ( AKA Catzwolf )                                              //
// Raimondas Rimkevicius ( AKA Mekdrop )                                    //
//                                                                          //
// URL: http:www.xoops.com                                              //
// Project: Xoops Project                                               //
// -------------------------------------------------------------------------//

/**
 *
 * @package    kernel
 * @subpackage form
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.xoops.com
 */

/**
 * Parent
 */
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formselect.php';
// RMV-NOTIFY

/**
 * A select field with a choice of available users
 *
 * @package    kernel
 * @subpackage form
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.xoops.com
 */
class XoopsFormSelectSection extends \XoopsFormSelect
{
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param mixed  $value    Pre-selected value (or array of them).
     * @param int    $size     Number or rows. "1" makes a drop-down-list.
     * @param bool   $multiple Allow multiple selections?
     * @param null   $groupid
     * @param bool   $static
     */
    public function __construct(
        $caption,
        $name,
        $value = null,
        $size = 1,
        $multiple = false,
        $groupid = null,
        $static = false
    ) {
        $sectionHandler = wfp_getHandler('section', 'wfsection', 'wfs_');
        $sections       = $sectionHandler->getList();
        parent::__construct($caption, $name, $value, $size, $multiple);
        if (true === $static) {
            $this->addOption(0, 'Static Content');
        }
        if ($sections) {
            $this->addOptionArray($sections);
        }
    }
}
