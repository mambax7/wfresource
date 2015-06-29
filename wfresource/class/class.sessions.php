<?php
// $Id: class.sessions.php 8181 2011-11-07 01:14:53Z beckmi $
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
defined('XOOPS_ROOT_PATH') || exit('You do not have permission to access this file!');

class wfp_Sessions
{
    public $s_name;
    public $s_vars = array();

    /**
     * wpf_Help::wpf_Help()
     *
     * @param string $aboutTitle
     */
    public function wfp_Sessions()
    {
    }

    public function &getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new wfp_Sessions();
        }

        return $instance;
    }

    public function setSessionName($value = 'wfs_default')
    {
        $this->s_name = htmlspecialchars($value);
    }

    public function setSessionVars($value = array())
    {
        foreach ($value as $k => $v) {
            $this->s_vars[$k] = $v;
        }
        if (!isset($_SESSION['wfsection'][$this->s_name])) {
            foreach ($this->s_vars as $k => $v) {
                $_SESSION['wfsection'][$this->s_name][$k] = $v;
            }
        }
    }

    public function delSessions($value = null)
    {
        if (is_null($value)) {
            unset($_SESSION['wfsection']);
        } else {
            unset($_SESSION['wfsection'][$value]);
        }
    }

    public function getSession()
    {
        return $_SESSION['wfsection'][$name][$this->varible];
    }

    public function doSession()
    {
        foreach (array_keys($this->s_vars) as $k) {
            $type                                     = (is_numeric(@$_REQUEST[$k])) ? 'int' : 'textbox';
            $ret[$k]                                  = wfp_Request::doRequest($_REQUEST, $k, $_SESSION['wfsection'][$this->s_name][$k], $type);
            $_SESSION['wfsection'][$this->s_name][$k] = htmlspecialchars($ret[$k], ENT_QUOTES);
        }

        return $ret;
    }
}
