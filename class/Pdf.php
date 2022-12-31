<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

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

use XoopsModules\Wfresource;

/**
 * wfp_dopdf
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class Pdf
{
    public $options     = [];
    public $compression = false;
    public $font        = 'Helvetica.afm';

    /**
     * wfp_dopdf::wfp_dopdf()
     */
    public function __construct(array $opt)
    {
        if (!\is_array($opt) || empty($opt)) {
            //            return false;
        }
        $this->options = $opt;
    }

    public function renderpdf(): void
    {
        //        require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/pdf/class.ezpdf.php';
        $pdf                         = new Cezpdf('a4', 'P'); //A4 Portrait
        $pdf->options['compression'] = $this->compression;
        $pdf->ezSetCmMargins(2, 1.5, 1, 1);
        // select font
        $pdf->selectFont(XOOPS_ROOT_PATH . '/modules/wfresource/class/pdf/fonts/' . $this->font, _CHARSET); //choose font
        $all = $pdf->openObject();
        $pdf->saveState();
        $pdf->setStrokeColor(0, 0, 0, 1);
        // footer
        $pdf->addText(30, 822, 6, $this->options['slogan']);
        $pdf->line(10, 40, 578, 40);
        $pdf->line(10, 818, 578, 818);
        // add url to footer
        $pdf->addText(30, 34, 6, XOOPS_URL);
        // add pdf creater
        $pdf->addText(250, 34, 6, $this->options['creator']);
        // add render date to footer
        $pdf->addText(450, 34, 6, \_CONTENT_RENDERED . ' ' . $this->options['renderdate']);
        $pdf->restoreState();
        $pdf->closeObject();
        $pdf->addObject($all, 'all');
        $pdf->ezSetDy(30);
        // title
        $pdf->ezText($this->options['title'], 16);
        $pdf->ezText("\n", 6);
        if ($this->options['author']) {
            $pdf->ezText(\_CONTENT_AUTHOR . $this->options['author'], 8);
        }
        if ($this->options['pdate']) {
            $pdf->ezText(\_CONTENT_PUBLISHED . $this->options['pdate'], 8);
        }
        if ($this->options['udate']) {
            $pdf->ezText(\_CONTENT_UPDATED . $this->options['udate'], 8);
        }
        $pdf->ezText("\n", 6);
        if ($this->options['itemurl']) {
            $pdf->ezText(\_CONTENT_URL_TOITEM . $this->options['itemurl'], 8);
            $pdf->ezText("\n", 6);
        }

        if ($this->options['subtitle']) {
            $pdf->ezText($this->options['subtitle'], 14);
            $pdf->ezText("\n", 6);
        }
        $pdf->ezText($this->options['content'], 10);
        $pdf->ezStream();
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
    public function setFont($value = ''): void
    {
        $this->font = \trim($value);
    }

    /**
     * @param bool|false $value
     */
    public function useCompression($value = false): void
    {
        $this->compression = ($value);
    }
}
