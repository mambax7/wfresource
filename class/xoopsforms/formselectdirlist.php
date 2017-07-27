<?php
// $Id: formselectdirlist.php 8181 2011-11-07 01:14:53Z beckmi $
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
class XoopsFormSelectDirList extends XoopsFormSelect
{
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param mixed  $value    Pre-selected value (or array of them).
     * @param int    $size     Number or rows. "1" makes a drop-down-list.
     * @param bool   $multiple Allow multiple selections?
     * @param null   $dirname
     * @param string $prefix
     * @param array  $type
     */
    public function __construct(
        $caption,
        $name,
        $value = null,
        $size = 1,
        $multiple = false,
        $dirname = null,
        $prefix = '',
        $type = array()
    ) {
        parent::__construct($caption, $name, $value, $size, $multiple);
        $filelist = $this->getFileListAsArray($dirname, $prefix, $type);
        $this->addOption('-1', '---------------------');
        $this->addOptionArray($filelist);
    }

    /**
     * @param         $dirname
     * @param  string $prefix
     * @param  array  $type
     * @return array
     */
    public function getFileListAsArray($dirname, $prefix = '', $type = array())
    {
        $string = '';
        foreach ($type as $types) {
            $string = "\.$types|";
        }

        $filelist = array();
        if (false !== ($handle = opendir($dirname))) {
            while (false !== ($file = readdir($handle))) {
                if (!preg_match("/^[\.]{1,2}$/", $file) && preg_match("/($string)$/i", $file)) {
                    $file            = $prefix . $file;
                    $filelist[$file] = $file;
                }
            }
            closedir($handle);
            asort($filelist);
            reset($filelist);
        }

        return $filelist;
    }
}
