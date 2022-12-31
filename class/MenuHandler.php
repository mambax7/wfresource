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
// URL: http:www.Xoops.com                                              //
// Project: Xoops Project                                               //
// -------------------------------------------------------------------------//
use const ENT_HTML5;
use XoopsTpl;

xoops_loadLanguage('admin', 'wfresource');

/**
 * MenuHandler
 *
 * @author    John
 * @copyright Copyright (c) 2006
 */
class MenuHandler
{
    /**
     * @var string
     */
    public $_menutop   = [];
    public $_menutabs  = [];
    public $_menuicons = [];
    public $_obj;
    public $_header;
    public $_subheader;
    public $_icon;
    /**
     * @var string
     */
    // var $adminmenu;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_obj = &$GLOBALS['xoopsModule'];
    }

    /**
     * MenuHandler::addMenuTop()
     *
     * @param mixed  $value
     * @param string $name
     */
    public function addMenuTop($value, $name = ''): void
    {
        if ('' !== $name) {
            $this->_menutop[$value] = $name;
        } else {
            $this->_menutop[$value] = $value;
        }
    }

    /**
     * MenuHandler::addMenuTopArray()
     *
     * @param mixed $options
     * @param mixed $multi
     */
    public function addMenuTopArray($options, $multi = true): void
    {
        if (\is_array($options)) {
            if (true === $multi) {
                foreach ($options as $k => $v) {
                    $this->addMenuTop($k, $v);
                }
            } else {
                foreach ($options as $k) {
                    $this->addMenuTop($k, $k);
                }
            }
        }
    }

    /**
     * MenuHandler::addMenuTabs()
     *
     * @param mixed  $value
     * @param string $name
     */
    public function addMenuTabs($value, $name = ''): void
    {
        if ('' !== $name) {
            $this->_menutabs[$value] = $name;
        } else {
            $this->_menutabs[$value] = $value;
        }
    }

    /**
     * MenuHandler::addMenuTabsArray()
     *
     * @param mixed $options
     * @param mixed $multi
     */
    public function addMenuTabsArray($options, $multi = true): void
    {
        if (\is_array($options)) {
            if (true === $multi) {
                foreach ($options as $k => $v) {
                    $this->addMenuTabs($k, $v);
                }
            } else {
                foreach ($options as $k) {
                    $this->addMenuTabs($k, $k);
                }
            }
        }
    }

    /**
     * xo_Adminmenu::addMenuIcons()
     *
     * @param mixed  $value
     * @param string $name
     */
    public function addMenuIcons($value, $name = ''): void
    {
        if ('' !== $name) {
            $this->_menuicons[$value] = $name;
        } else {
            $this->_menuicons[$value] = $value;
        }
    }

    /**
     * xo_Adminmenu::addMenuIconsArray()
     *
     * @param mixed $options
     * @param mixed $multi
     */
    public function addMenuIconsArray($options, $multi = true): void
    {
        if (\is_array($options)) {
            if (true === $multi) {
                foreach ($options as $k => $v) {
                    $this->addMenuIcons($k, $v);
                }
            } else {
                foreach ($options as $k) {
                    $this->addMenuIcons($k, $k);
                }
            }
        }
    }

    /**
     * MenuHandler::addHeader()
     *
     * @param mixed $value
     */
    public function addHeader($value): void
    {
        $this->_header = $value;
    }

    /**
     * MenuHandler::addSubHeader()
     *
     * @param mixed $value
     */
    public function addSubHeader($value): void
    {
        $this->_subheader = $value;
    }

    /**
     * xo_Adminmenu::addIcon()
     *
     * @param mixed $value
     */
    public function addIcon($value = ''): void
    {
        $this->_icon = $value;
    }

    /**
     * xo_Adminmenu::getIcon()
     */
    public function getIcon(): string
    {
        return $this->_icon . '_admin.png';
    }

    /**
     * xo_Adminmenu::getNavMenuIcons()
     */
    public function getNavMenuIcons(): string
    {
        $menu = '';
        if (0 !== \count($this->_menuicons)) {
            foreach ($this->_menuicons as $k => $v) {
                $menu .= '<a href="' . $v . '">' . Utility::showImage('cpanel_' . $k, $k, '', 'png') . '</a>';
            }
        }

        return $menu;
    }

    /**
     * MenuHandler::render()
     *
     * @param int   $currentoption
     * @param mixed $display
     * @return bool
     */
    public function render($currentoption = 1, $display = true)
    {
        global $modversion;

        /**
         * Menu Top Links
         */
        $menuTopLinks = "<a class='nobutton' href='" . XOOPS_URL . '/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=' . $GLOBALS['xoopsModule']->getVar('mid') . "'>" . \_AM_WFP_ADMINPREFS . '</a>';
        foreach ($this->_menutop as $k => $v) {
            $menuTopLinks .= ' | <a href="' . \htmlentities($k, \ENT_QUOTES | ENT_HTML5) . '"><span>' . $v . '</span></a>';
        }
        /**
         * Menu Items
         */
        $menuItems = [];
        foreach ($this->_menutabs as $k => $menus) {
            $menuItems[] = $menus;
        }
        $breadcrumb                = isset($menuItems[$currentoption]) ?: '';
        $menuItems[$currentoption] = 'current';

        $i              = 0;
        $menuBottomTabs = '';
        foreach ($this->_menutabs as $k => $v) {
            $menuBottomTabs .= '<li id="' . \mb_strtolower(\str_replace(' ', '_', $menuItems[$i])) . '"><a href="' . \htmlentities($k, \ENT_QUOTES | ENT_HTML5) . '"><span>' . $v . '</span></a></li>';
            ++$i;
        }

        require_once XOOPS_ROOT_PATH . '/class/template.php';
        $tpl = new XoopsTpl();
        $tpl->assign([
                         'menu_links'     => $menuTopLinks,
                         'menu_tabs'      => $menuBottomTabs,
                         'menu_subheader' => $this->_subheader,
                         'menu_header'    => $this->_header,
                         'menu_icons'     => $this->getNavMenuIcons(),
                         'menu_module'    => $GLOBALS['xoopsModule']->getVar('name'),
                     ]);

        $tpl->display(XOOPS_ROOT_PATH . '/modules/wfresource/templates/wfp_adminmenu.tpl');

        return true;
    }
}
