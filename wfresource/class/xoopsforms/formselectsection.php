<?php
// $Id: formselectsection.php 8181 2011-11-07 01:14:53Z beckmi $
// ------------------------------------------------------------------------ //
// Xoops - PHP Content Management System                      			//
// Copyright (c) 2007 Xoops                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.xoops.com 												//
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
include_once XOOPS_ROOT_PATH . "/class/xoopsform/formselect.php";
// RMV-NOTIFY
/**
 * A select field with a choice of available users
 *
 * @package    kernel
 * @subpackage form
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.xoops.com
 */
class XoopsFormSelectSection extends XoopsFormSelect
{
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param bool   $include_anon Include user "anonymous"?
     * @param mixed  $value        Pre-selected value (or array of them).
     * @param int    $size         Number or rows. "1" makes a drop-down-list.
     * @param bool   $multiple     Allow multiple selections?
     */
    public function __construct($caption, $name, $value = null, $size = 1, $multiple = false, $groupid = null, $static = false)
    {
        $section_handler = &wfp_gethandler('section', 'wfsection', 'wfs_');
        $sections        = $section_handler->getList();
        parent:
        __construct($caption, $name, $value, $size, $multiple);
        if ($static == true) {
            $this->addOption(0, 'Static Content');
        }
        if ($sections) {
            $this->addOptionArray($sections);
        }
    }
}
