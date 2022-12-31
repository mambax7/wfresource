<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

// ------------------------------------------------------------------------ //
// Xoops - PHP Content Management System                                //
// Copyright (c) 2007 Xoops                                         //
// //
// Authors:                                                                 //
// John Neill ( AKA Catzwolf )                                              //
// Raimondas Rimkevicius ( AKA Mekdrop )                                    //
// //
// URL: http:www.xoops.com                                              //
// Project: Xoops Project                                               //
// -------------------------------------------------------------------------//

/**
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.xoops.com
 */

/**
 * Parent
 */
//require_once XOOPS_ROOT_PATH . '/class/xoopsform/formselect.php';
// RMV-NOTIFY

/**
 * A select field with a choice of available users
 *
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.xoops.com
 */
class XoopsFormSelectRDirList extends \XoopsFormSelect
{
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param mixed  $value    Pre-selected value (or array of them).
     * @param int    $size     Number or rows. "1" makes a drop-down-list.
     * @param bool   $multiple Allow multiple selections?
     * @param bool   $is_cat
     */
    public function __construct($caption, $name, $value, $size = 1, $multiple = false, $is_cat = false)
    {
        parent::__construct($caption, $name, $value, $size, $multiple);
        if ($is_cat) {
            $this->addOption('*2*', 'Use Section');
            $this->addOption('*#*', '---------------------');
        }
        $this->addOption('*1*', 'All');
        $this->addOption('*0*', 'None');
        $this->addOption('*#*', '---------------------');
        $this->addOption('*/*', '/');
        $filelist = $this->getRecDirlistAsArray(XOOPS_MEDIA_PATH . '/images');
    }

    /**
     * XoopsFormSelectRDirList::getRecDirlistAsArray()
     *
     * @param mixed $dirname
     */
    public function getRecDirlistAsArray($dirname): void
    {
        static $filelist = [];
        static $dirlist = [];

        if (\is_dir($dirname)) {
            $dh = \opendir($dirname);
            while (false !== ($dir = \readdir($dh))) {
                if ('.' !== $dir && '..' !== $dir && \is_dir($dirname . '/' . $dir) && 'cvs' !== \mb_strtolower($dir)
                    && '.svn' !== \mb_strtolower($dir)) {
                    $subdirname = $dirname . '/' . $dir;
                    $this->addOption($this->processDir($dirname) . '/' . $dir);
                    $subdirlist = $this->getRecDirlistAsArray($subdirname);
                }
            }
            \closedir($dh);
        }
    }

    /**
     * XoopsFormSelectRDirList::processDir()
     *
     * @param mixed $dirname
     * @return mixed
     */
    public function processDir($dirname)
    {
        return \str_replace(XOOPS_MEDIA_PATH . '/images', '', $dirname);
    }
}
