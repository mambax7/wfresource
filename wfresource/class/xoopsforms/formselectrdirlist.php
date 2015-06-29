<?php
// $Id: formselectrdirlist.php 8181 2011-11-07 01:14:53Z beckmi $
// ------------------------------------------------------------------------ //
// Xoops - PHP Content Management System                      			//
// Copyright (c) 2007 Xoops                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.xoops.com 												//
// Project: Xoops Project                                               //
// -------------------------------------------------------------------------//
/**
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Xoops Project - http.www.xoops.com
 */

/**
 * Parent
 */
include_once XOOPS_ROOT_PATH . "/class/xoopsform/formselect.php";
// RMV-NOTIFY
/**
 * A select field with a choice of available users
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Xoops Project - http.www.xoops.com
 */
class XoopsFormSelectRDirList extends XoopsFormSelect {
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param bool $include_anon Include user "anonymous"?
     * @param mixed $value Pre-selected value (or array of them).
     * @param int $size Number or rows. "1" makes a drop-down-list.
     * @param bool $multiple Allow multiple selections?
     */
    function XoopsFormSelectRDirList( $caption, $name, $value, $size = 1, $multiple = false, $is_cat = false ) {
        $this->XoopsFormSelect( $caption, $name, $value, $size, $multiple );
        if ( $is_cat == true ) {
            $this->addOption( '*2*', 'Use Section' );
            $this->addOption( '*#*', '---------------------' );
        }
        $this->addOption( '*1*', 'All' );
        $this->addOption( '*0*', 'None' );
        $this->addOption( '*#*', '---------------------' );
        $this->addOption( '*/*', '/' );
        $filelist = self::getRecDirlistAsArray( XOOPS_MEDIA_PATH.'/images' );
    }

    /**
     * XoopsFormSelectRDirList::getRecDirlistAsArray()
     *
     * @param mixed $dirname
     * @return
     */
    function getRecDirlistAsArray( $dirname ) {
        static $filelist = array();
        static $dirlist = array();

        if ( is_dir( $dirname ) ) {
            $dh = opendir( $dirname );
            while ( false !== ( $dir = readdir( $dh ) ) ) {
                if ( $dir !== '.' && $dir !== '..' && is_dir( $dirname . '/' . $dir ) && strtolower( $dir ) !== 'cvs' && strtolower( $dir ) !== '.svn' ) {
                    $subdirname = $dirname . '/' . $dir;
                    $this->addOption( self::processDir( $dirname ) . '/' . $dir );
                    $subdirlist = self::getRecDirlistAsArray( $subdirname );
                }
            }
            closedir( $dh );
        }
    }

    /**
     * XoopsFormSelectRDirList::processDir()
     *
     * @param mixed $dirname
     * @return
     */
    function processDir( $dirname ) {
        return str_replace( XOOPS_MEDIA_PATH.'/images', '', $dirname );
    }
}

?>