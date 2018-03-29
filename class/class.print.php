<?php

// ------------------------------------------------------------------------ //
// ------------------------------------------------------------------------ //
// WF-Channel - WF-Projects                                                 //
// Copyright (c) 2007 WF-Channel                                            //
// //
// Authors:                                                                 //
// John Neill ( AKA Catzwolf )                                              //
// //
// URL: http://catzwolf.x10hosting.com/                                     //
// Project: WF-Projects                                                     //
// -------------------------------------------------------------------------//
defined('XOOPS_ROOT_PATH') || die('You do not have permission to access this file!');

/**
 * Class wfp_doPrint
 */
class wfp_doPrint
{
    public $options     = [];
    public $compression = false;
    public $font        = 'helvetica';
    public $fontsize    = '12';

    /**
     * wfc_doPrint::wfc_doPrint()
     *
     * @param array $opt
     */
    public function __construct($opt = [])
    {
        if (!is_array($opt) || 0 === count($opt)) {
            return false;
        }
        $this->options = $opt;
    }

    /**
     * wfc_doPrint::renderPrint()
     *
     */
    public function renderPrint()
    {
        $ret = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $ret .= "\n";
        $ret .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . _LANGCODE . '" lang="' . _LANGCODE . '">';
        $ret .= "<head>\n";
        $ret .= '<title>' . _MD_WFP_PRINTER . ' - ' . $this->options['title'] . ' - ' . $this->options['sitename'] . "</title>\n";
        $ret .= "<meta http-equiv='Content-Type' content='text/html; charset=" . _CHARSET . "'>\n";
        $ret .= "<meta name='author' content='" . $this->options['sitename'] . "'>\n";
        $ret .= "<meta name='keywords' content='" . (isset($this->options['keywords']) ? $this->options['keywords'] : '') . "'>\n";
        $ret .= "<meta name='copyright' content='Copyright (c) 2006 by " . $this->options['sitename'] . "'>\n";
        $ret .= "<meta name='description' content='" . (isset($this->options['meta']) ? $this->options['meta'] : '') . "'>\n";
        $ret .= "<meta name='generator' content='Xoops'>\n";
        $ret .= "<style type=\"text/css\">
            body { margin: 10px; font-family: {$this->font}; font-size: {$this->fontsize}px; }
            div { font-family: inherit; }
            a:link { color: #000000; }
            a:visited { color: #000000; }
            a:active { color: #000000; }
            a:hover { color: #ff0000; }
            </style>";
        $ret .= "</head>\n";
        $ret .= "<body bgcolor='#ffffff' text='#000000' onload=''>\n
                 <div>
                       <table border=\"0\" width=\"100%\"  cellpadding=\"2\" cellspacing=\"1\" bgcolor=\"#ffffff\">
                        <thead>
                         <tr>
                          <th colspan=\"3\" width=100% style='text-align: left;'>" . $this->options['slogan'] . "<th>
                         </tr>
                        </thead>
                        <tfoot>
                         <tr>
                          <td width=30% style='text-align: left;'>" . XOOPS_URL . "</td>
                          <td width=40% style='text-align: center;'>" . $this->options['creator'] . "</td>
                          <td width=30% style='text-align: right;'>" . _CONTENT_RENDERED . ' ' . $this->options['renderdate'] . '</td>
                         </tr>
                        </tfoot>
                        <tr>
                         <td colspan="3" align="left">
                           <hr>
                           <h2>' . $this->options['title'] . "</h2>\n
                           <div>" . _CONTENT_AUTHOR . ' ' . (isset($this->options['author']) ? $this->options['author'] : '') . '</div>
                           <div>' . _CONTENT_PUBLISHED . ' ' . (isset($this->options['pdate']) ? $this->options['pdate'] : '') . '</div>';
        if (isset($this->options['pdate'])) {
            $ret .= '<div>' . _CONTENT_UPDATED . ' ' . (isset($this->options['udate']) ? $this->options['udate'] : '') . '</div>';
        }

        if (isset($this->options['itemurl'])) {
            $ret .= '<br><br>' . _CONTENT_URL_TOITEM . ' ' . $this->options['itemurl'] . '<br><br>';
        }

        $ret .= "<br><div><strong>{$this->options['subtitle']}</strong></div><br>
                        </td>\n
                       </tr>\n
                       <tr colspan=\"3\" valign='top' style='font:12px;'>
                           <td colspan=\"3\">" . $this->options['content'] . '<br><br>';
        $ret .= "<hr></td>
                       </tr>
                      </table>
                     </div>
                    <br>
                <div style='text-align: center;'><input type=button value='" . _MD_WFP_PRINT_PAGE . "' onclick='window.print();'></div><br>
                </body></html>\n";
        echo $ret;
    }

    /**
     * @param string $value
     */
    public function setTitle($value = '')
    {
        $this->options['title'] = $value;
    }

    /**
     * @param string $value
     */
    public function setSubTitle($value = '')
    {
        $this->options['subtitle'] = $value;
    }

    /**
     * @param string $value
     */
    public function setCreater($value = '')
    {
        $this->options['creator'] = $value;
    }

    /**
     * @param string $value
     */
    public function setSlogan($value = '')
    {
        $this->options['slogan'] = $value;
    }

    /**
     * @param string $value
     */
    public function setAuthor($value = '')
    {
        $this->options['author'] = $value;
    }

    /**
     * @param string $value
     */
    public function setContent($value = '')
    {
        $this->options['content'] = $value;
    }

    /**
     * @param string $value
     */
    public function setPDate($value = '')
    {
        $this->options['pdate'] = $value;
    }

    /**
     * @param string $value
     */
    public function setUDate($value = '')
    {
        $this->options['udate'] = $value;
    }

    /**
     * @param string $value
     */
    public function setUrul($value = '')
    {
        $this->options['itemurl'] = $value;
    }

    /**
     * @param string $value
     */
    public function setFont($value = '')
    {
        $this->font = trim($value);
    }

    /**
     * @param string $value
     */
    public function setFontSize($value = '')
    {
        $this->fontsize = (int)$value;
    }
}
