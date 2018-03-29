<?php
/**
 * Name: formtabs.php
 * Description:
 *
 * @package    : Xoosla Modules
 * @Module     :
 * @subpackage :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

define('newLine', "\n");

/**
 * XooslaFormTabs
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class XooslaFormTabs
{
    public $_useCookies = 1;

    /**
     * Constructor
     * Includes files needed for displaying tabs and sets cookie options
     *
     * @param int $useCookies - useCookies, if set to 1 cookie will hold last used tab between page refreshes
     */
    public function __construct($useCookies = 0)
    {
        $this->_useCookies = (1 == $useCookies) ? 1 : 0;
        if (isset($GLOBALS['xoTheme'])) {
            $GLOBALS['xoTheme']->addStylesheet('/modules/wfresource/include/js/tabs/tabpane.css', ['id="luna-tab-style-sheet"']);
            $GLOBALS['xoTheme']->addScript('/modules/wfresource/include/js/tabs/tabpane.js');
        } else {
            echo '<link rel="stylesheet" type="text/css" media="all" href="' . XOOPS_URL . '/modules/wfresource/include/js/tabs/tabpane.css">';
            echo '<script type="text/javascript" src="' . XOOPS_URL . '/modules/wfresource/include/js/tabs/tabpane.js"></script>';
        }
        unset($useCookies);
    }

    /**
     * xo_FormTab::startPane()
     *
     * @param  mixed $id
     * @return string
     */
    public function startPane($id)
    {
        $id  = preg_replace('/[^a-z0-9]+/i', '', $id);
        $ret = '<div class="tab-pageouter" id="' . $id . '"><script type="text/javascript">var tabPane1 = new WebFXTabPane( document.getElementById( "' . $id . '" ), ' . $this->_useCookies . ' )</script>';

        return $ret;
    }

    /**
     * xo_FormTab::endPane()
     * @return string
     */
    public function endPane()
    {
        $ret = '</div>';

        return $ret;
    }

    /**
     * Creates a tab with title text and starts that tabs page
     *
     * @param $tabText - This is what is displayed on the tab
     * @param $paneid  - This is the parent pane to build this tab on
     * @return string
     */
    public function startTab($tabText, $paneid)
    {
        $tabText = preg_replace('/[^A-Za-z0-9\s\s+]/', '_', $tabText);
        $paneid  = preg_replace('/[^A-Za-z0-9\s\s+]/', '_', $paneid);
        $ret     = '<div class="tab-page" id="' . $paneid . '">
            <h2 class="tab">' . $tabText . '</h2>
            <script type="text/javascript">tabPane1.addTabPage( document.getElementById( "' . $paneid . '" ) );</script>
          <table width="100%" cellspacing="1">';

        return $ret;
    }

    /**
     * Ends a tab page
     */
    public function endTab()
    {
        $ret = '</table></div>';

        return $ret;
    }
}
