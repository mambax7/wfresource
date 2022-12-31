<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

/**
 * @Zarilia   -    PHP Content Management System
 * @copyright 2007 Zarilia
 * @Author    :    John (AKA Catzwolf)
 * @URL       :        https://zarilia.com
 * @Project   :    Zarilia CMS
 */
class Tabs
{
    /**
     * @var int Use cookies
     */
    public $_useCookies = true;
    public $_echo       = false;
    public $_contents;

    /**
     * Constructor
     * Includes files needed for displaying tabs and sets cookie options
     *
     * @param bool $useCookies - if set to 1 cookie will hold last used tab between page refreshes
     * @param bool $echo
     */
    public function __construct($useCookies = true, $echo = false)
    {
        $this->_useCookies = 1 === ((int)$useCookies) ? 1 : 0;
        $this->_echo       = (1 === (int)$echo) ? true : false;
        //$GLOBALS['xoTheme']->addStylesheet( '/include/js/tabs/tabpane.css', array( 'id="luna-tab-style-sheet"' ) );
        //$GLOBALS['xoTheme']->addScript( '/include/js/tabs/tabpane.js' );
        //$this->contents[] = "<script type=\"text/javascript\" src=\"" . XOOPS_URL . "/include/js/tabs/tabpane.js\"></script>";
    }

    /**
     * @param $value
     */
    public function setEcho($value): void
    {
        $this->_echo = $value;
    }

    /**
     * creates a tab pane and creates JS obj
     *
     * @param $id
     */
    public function startPane($id): void
    {
        $output           = '<div class="tab-pageouter" id="' . $id . "\">
            <script type=\"text/javascript\">\n
            var tabPane1 = new WebFXTabPane( document.getElementById( \"" . $id . '" ), ' . $this->_useCookies . " )\n
        </script>\n";
        $this->contents[] = $output;
    }

    /**
     * Ends Tab Pane
     */
    public function endPane(): void
    {
        $output           = '</div>';
        $this->contents[] = $output;
    }

    /**
     * Creates a tab with title text and starts that tabs page
     *
     * @param $tabText - This is what is displayed on the tab
     * @param $paneid  - This is the parent pane to build this tab on
     */
    public function startTab($tabText, $paneid): void
    {
        $output           = '<div class="tab-page" id="' . $paneid . '">
            <h2 class="tab">' . $tabText . "</h2>
            <script type=\"text/javascript\">\n
            tabPane1.addTabPage( document.getElementById( \"" . $paneid . '" ) );
          </script>';
        $this->contents[] = $output;
    }

    /**
     * Ends a tab page
     */
    public function endTab(): void
    {
        $output           = '</div>';
        $this->contents[] = $output;
    }

    /**
     * @param null $value
     */
    public function addContent($value = null): void
    {
        $this->contents[] = $value;
    }

    /**
     * @return string
     */
    public function render(): ?string
    {
        $cont = implode('', $this->contents);
        if (!$this->_echo) {
            return $cont;
        }
        echo $cont;
    }
}
