<?php

/**
 * $Id: class.tabs.php 8181 2011-11-07 01:14:53Z beckmi $ Untitled 5.php v0.0 17/08/2007 03:24:56 John
 *
 * @Zarilia   -    PHP Content Management System
 * @copyright 2007 Zarilia
 * @Author    :    John (AKA Catzwolf)
 * @URL       :        http://zarilia.com
 * @Project   :    Zarilia CMS
 */
class xoops_Tabs
{
    /**
     *
     * @var int Use cookies
     */
    public $_useCookies = true;
    public $_echo       = false;
    public $_contents;

    /**
     * Constructor
     * Includes files needed for displaying tabs and sets cookie options
     *
     * @param int $ useCookies, if set to 1 cookie will hold last used tab between page refreshes
     */
    public function __construct($useCookies = true, $echo = false)
    {
        $this->_useCookies = ((int)$useCookies) == 1 ? 1 : 0;
        $this->_echo       = ((int)$echo == 1) ? true : false;
        //$GLOBALS['xoTheme']->addStylesheet( '/include/javascript/tabs/tabpane.css', array( 'id="luna-tab-style-sheet"' ) );
        //$GLOBALS['xoTheme']->addScript( '/include/javascript/tabs/tabpane.js' );
        //$this->contents[] = "<script type=\"text/javascript\" src=\"" . XOOPS_URL . "/include/javascript/tabs/tabpane.js\"></script>";
    }

    public function setEcho($value)
    {
        $this->_echo = $value;
    }

    /**
     * creates a tab pane and creates JS obj
     *
     * @param string $ The Tab Pane Name
     */
    public function startPane($id)
    {
        $output           = "<div class=\"tab-pageouter\" id=\"" . $id . "\">
            <script type=\"text/javascript\">\n
            var tabPane1 = new WebFXTabPane( document.getElementById( \"" . $id . "\" ), " . $this->_useCookies . " )\n
        </script>\n";
        $this->contents[] = $output;
    }

    /**
     * Ends Tab Pane
     */
    public function endPane()
    {
        $output           = "</div>";
        $this->contents[] = $output;
    }

    /**
     * Creates a tab with title text and starts that tabs page
     *
     * @param tabText $ - This is what is displayed on the tab
     * @param paneid  $ - This is the parent pane to build this tab on
     */
    public function startTab($tabText, $paneid)
    {
        $output           = "<div class=\"tab-page\" id=\"" . $paneid . "\">
            <h2 class=\"tab\">" . $tabText . "</h2>
            <script type=\"text/javascript\">\n
            tabPane1.addTabPage( document.getElementById( \"" . $paneid . "\" ) );
          </script>";
        $this->contents[] = $output;
    }

    /**
     * Ends a tab page
     */
    public function endTab()
    {
        $output           = "</div>";
        $this->contents[] = $output;
    }

    public function addContent($value = null)
    {
        $this->contents[] = $value;
    }

    public function render()
    {
        $cont = '';
        foreach ($this->contents as $contents) {
            $cont .= $contents;
        }
        if ($this->_echo == false) {
            return $cont;
        } else {
            echo $cont;
        }
    }
}
