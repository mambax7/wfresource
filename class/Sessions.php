<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

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
 * Class Sessions
 */
class Sessions
{
    public $s_name;
    public $s_vars = [];

    /**
     * Sessions::__construct()
     */
    public function __construct()
    {
    }

    public static function getInstance(): self
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * @param string $value
     */
    public function setSessionName($value = 'wfs_default'): void
    {
        $this->s_name = \htmlspecialchars($value, \ENT_QUOTES | \ENT_HTML5);
    }

    public function setSessionVars(array $value = null): void
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
    public function delSessions($value = null): void
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
        foreach (\array_keys($this->s_vars) as $k) {
            $type                                     = \is_numeric(@$_REQUEST[$k]) ? 'int' : 'textbox';
            $ret[$k]                                  = Wfresource\Request::doRequest($_REQUEST, $k, $_SESSION['wfsection'][$this->s_name][$k], $type);
            $_SESSION['wfsection'][$this->s_name][$k] = \htmlspecialchars($ret[$k], \ENT_QUOTES);
        }

        return $ret;
    }
}
