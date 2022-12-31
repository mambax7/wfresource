<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

// ------------------------------------------------------------------------ //
// ------------------------------------------------------------------------ //
// WF-Channel - WF-Projects                                                 //
// Copyright (c) 2007 WF-Channel                                            //
// //
// Authors:                                                                 //
// John Neill ( AKA Catzwolf )                                              //
// //
// URL: https://catzwolf.x10hosting.com/                                     //
// Project: WF-Projects                                                     //
// -------------------------------------------------------------------------//

/**
 * Class Print
 */
class Printer
{
    public $options     = [];
    public $compression = false;
    public $font        = 'helvetica';
    public $fontsize    = '12';

    /**
     * @param array $opt
     */
    public function __construct($opt = [])
    {
        if (!\is_array($opt) || 0 === \count($opt)) {
            return false;
        }
        $this->options = $opt;
    }

    /**
     * wfc_doPrint::renderPrint()
     */
    public function renderPrint(): void
    {
        $ret = '<!DOCTYPE html>';
        $ret .= "\n";
        $ret .= '<html xml:lang="' . _LANGCODE . '" lang="' . _LANGCODE . '">';
        $ret .= "<head>\n";
        $ret .= '<title>' . \_MD_WFP_PRINTER . ' - ' . $this->options['title'] . ' - ' . $this->options['sitename'] . "</title>\n";
        $ret .= "<meta http-equiv='Content-Type' content='text/html; charset=" . _CHARSET . "'>\n";
        $ret .= "<meta name='author' content='" . $this->options['sitename'] . "'>\n";
        $ret .= "<meta name='keywords' content='" . ($this->options['keywords'] ?? '') . "'>\n";
        $ret .= "<meta name='copyright' content='Copyright (c) 2006 by " . $this->options['sitename'] . "'>\n";
        $ret .= "<meta name='description' content='" . ($this->options['meta'] ?? '') . "'>\n";
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
                          <td width=30% style='text-align: right;'>" . \_CONTENT_RENDERED . ' ' . $this->options['renderdate'] . '</td>
                         </tr>
                        </tfoot>
                        <tr>
                         <td colspan="3" align="left">
                           <hr>
                           <h2>' . $this->options['title'] . "</h2>\n
                           <div>" . \_CONTENT_AUTHOR . ' ' . ($this->options['author'] ?? '') . '</div>
                           <div>' . \_CONTENT_PUBLISHED . ' ' . ($this->options['pdate'] ?? '') . '</div>';
        if (isset($this->options['pdate'])) {
            $ret .= '<div>' . \_CONTENT_UPDATED . ' ' . ($this->options['udate'] ?? '') . '</div>';
        }

        if (isset($this->options['itemurl'])) {
            $ret .= '<br><br>' . \_CONTENT_URL_TOITEM . ' ' . $this->options['itemurl'] . '<br><br>';
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
                <div style='text-align: center;'><input type=button value='" . \_MD_WFP_PRINT_PAGE . "' onclick='window.print();'></div><br>
                </body></html>\n";
        echo $ret;
    }

    /**
     * @param string $value
     */
    public function setTitle($value = ''): void
    {
        $this->options['title'] = $value;
    }

    /**
     * @param string $value
     */
    public function setSubTitle($value = ''): void
    {
        $this->options['subtitle'] = $value;
    }

    /**
     * @param string $value
     */
    public function setCreater($value = ''): void
    {
        $this->options['creator'] = $value;
    }

    /**
     * @param string $value
     */
    public function setSlogan($value = ''): void
    {
        $this->options['slogan'] = $value;
    }

    /**
     * @param string $value
     */
    public function setAuthor($value = ''): void
    {
        $this->options['author'] = $value;
    }

    /**
     * @param string $value
     */
    public function setContent($value = ''): void
    {
        $this->options['content'] = $value;
    }

    /**
     * @param string $value
     */
    public function setPDate($value = ''): void
    {
        $this->options['pdate'] = $value;
    }

    /**
     * @param string $value
     */
    public function setUDate($value = ''): void
    {
        $this->options['udate'] = $value;
    }

    /**
     * @param string $value
     */
    public function setUrul($value = ''): void
    {
        $this->options['itemurl'] = $value;
    }

    /**
     * @param string $value
     */
    public function setFont($value = ''): void
    {
        $this->font = \trim($value);
    }

    /**
     * @param string $value
     */
    public function setFontSize($value = ''): void
    {
        $this->fontsize = (int)$value;
    }
}
