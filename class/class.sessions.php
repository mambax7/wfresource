<?php
// $Id: class.sessions.php 8181 2011-11-07 01:14:53Z beckmi $
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
defined('XOOPS_ROOT_PATH') || exit('You do not have permission to access this file!');

/**
 * Class wfp_Sessions
 */
class wfp_Sessions
{
    public $s_name;
    public $s_vars = array();

    /**
     * wpf_Help::__construct()
     */
    public function __construct()
    {
    }

    /**
     * @return wfp_Sessions
     */
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * @param string $value
     */
    public function setSessionName($value = 'wfs_default')
    {
        $this->s_name = htmlspecialchars($value);
    }

    /**
     * @param array $value
     */
    public function setSessionVars(array $value = null)
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

    /**
     * @param null $value
     */
    public function delSessions($value = null)
    {
        if (null === $value) {
            unset($_SESSION['wfsection']);
        } else {
            unset($_SESSION['wfsection'][$value]);
        }
    }

    /**
     * @return mixed
     */
    public function getSession()
    {
        return $_SESSION['wfsection'][$name][$this->varible];
    }

    /**
     * @return mixed
     */
    public function doSession()
    {
        foreach (array_keys($this->s_vars) as $k) {
            $type                                     = is_numeric(@$_REQUEST[$k]) ? 'int' : 'textbox';
            $ret[$k]                                  = wfp_Request::doRequest($_REQUEST, $k, $_SESSION['wfsection'][$this->s_name][$k], $type);
            $_SESSION['wfsection'][$this->s_name][$k] = htmlspecialchars($ret[$k], ENT_QUOTES);
        }

        return $ret;
    }
}
