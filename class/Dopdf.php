<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

use function md5;
use MyTextSanitizer;

/**
 * Name: class.pdf.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

/**
 * wfp_dopdf
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class Dopdf
{
    public $options     = [];
    public $compression = false;
    public $font        = 'Helvetica.afm';
    public $cachekey    = null;

    /**
     * Dopdf::__construct()
     */
    public function __construct()
    {
    }

    /**
     * Dopdf::setOptions()
     *
     * @param array $opt
     * @return bool
     */
    public function setOptions($opt = []): ?bool
    {
        if (!\is_array($opt) || 0 === \count($opt)) {
            return false;
        }
        $this->cachedir = XOOPS_ROOT_PATH . '/cache/';
        $this->options  = $opt;
    }

    /**
     * wfp_dopdf::renderpdf()
     */
    public function doRender(): void
    {
        Utility::loadLanguage('print', 'wfresource');
        \error_reporting(0);
        $this->stdoutput = $this->getCache($this->options['id'], $this->options['title']);
        if (!$this->stdoutput) {
            //            require_once _WFP_RESOURCE_PATH . '/class/pdf/class.ezpdf.php';
            $pdf                         = new XoopsModules\Wfresource\Pdf\Cezpdf('a4', 'P'); //A4 Portrait
            $pdf->options['compression'] = $this->compression;
            $pdf->ezSetCmMargins(2, 1.5, 1, 1);
            // select font
            $pdf->selectFont(\_WFP_RESOURCE_PATH . '/class/pdf/fonts/' . $this->font, _CHARSET); //choose font
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
            $pdf->ezText(\strip_tags($this->options['title']), 16);
            $pdf->ezText("\n", 6);
            if (!empty($this->options['author'])) {
                $pdf->ezText(\_CONTENT_AUTHOR . $this->options['author'], 8);
            }
            if (!empty($this->options['pdate'])) {
                $pdf->ezText(\_CONTENT_PUBLISHED . $this->options['pdate'], 8);
            }
            if (!empty($this->options['udate'])) {
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
            $pdf->ezText($this->getContent(), 10);
            if ('file' === $this->options['stdoutput']) {
                $this->stdoutput = $pdf->ezOutput(0);
                $this->createCache($this->options['id'], $this->options['title']);
            } else {
                $pdf->ezStream(1);
                exit();
            }
        }
        $this->doDisplay();
    }

    /**
     * wfp_dopdf::xo_Display()
     */
    public function doDisplay(): void
    {
        $fileName = (isset($this->options['title']) ? $this->options['title'] . '.pdf' : 'file.pdf');

        \header('Content-type: application/pdf');
        \header('Content-Length: ' . mb_strlen(\ltrim($fileName)));
        \header('Content-Disposition: inline; filename=' . $fileName);
        if (isset($options['Accept-Ranges']) && 1 === $options['Accept-Ranges']) {
            \header('Accept-Ranges: ' . mb_strlen(\ltrim($tmp)));
        }
        echo $this->stdoutput;
        exit();
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
        $this->compression = ($value) ? true : false;
    }

    /**
     * Dopdf::getContent()
     * @return mixed
     */
    public function getContent()
    {
        return $this->cleanPDF($this->options['content']);
    }

    /**
     * wfp_Clean::cleanPDF()
     *
     * @param $text
     * @return mixed
     */
    public function cleanPDF($text)
    {
        $myts = MyTextSanitizer::getInstance();
        $text = $myts->undoHtmlSpecialChars($text);
        $text = \preg_replace('/\<style[\w\W]*?\<\/style\>/i', '', $text);
        $text = \preg_replace('/<img[^>]+\>/i', '', $text);
        $text = \str_replace('[pagebreak]', '<br><br>', $text);

        $htmltidy = new Htmltidy(); //wfp_getClass('htmltidy', _RESOURCE_DIR, _RESOURCE_CLASS);
        if ($htmltidy) {
            $htmltidy->Options['UseTidy']     = false;
            $htmltidy->Options['OutputXHTML'] = true;
            $htmltidy->Options['Optimize']    = true;
            $htmltidy->Options['Compress']    = true;
            $htmltidy->html                   = $text;
            $text                             = $htmltidy->cleanUp();
        }

        $text = \str_replace(['<p>', '</p>'], "\n", $text);
        $text = \str_replace('<P>', "\n", $text);
        $text = \str_replace('<br>', "\n", $text);
        $text = \str_replace('<br>', "\n", $text);
        $text = \str_replace('<br>', "\n", $text);
        $text = \str_replace('<br>', "\n", $text);
        $text = \str_replace('<li>', "\n - ", $text);
        $text = \str_replace('<li>', "\n - ", $text);
        $text = \str_replace('[pagebreak]', '', $text);
        $text = \strip_tags(\ltrim($text));
        $text = htmlspecialchars_decode(\ltrim($text));

        return $text;
    }

    /**
     * wfp_dopdf::setFilename()
     *
     * @param mixed $id
     * @param mixed $title
     */
    public function setFilename($id, $title): string
    {
        $module = $GLOBALS['xoopsModule']->getVar('dirname');
        $id     = md5((int)$id);
        $title  = \str_replace(' ', '_', \mb_strtolower($title));

        return 'wfp_pdffile' . md5($module . $id . $title) . '.pdf';
    }

    /**
     * wfp_dopdf::createCache()
     *
     * @param mixed $id
     * @param mixed $title
     * @return bool
     */
    public function getCache($id, $title)
    {
        \xoops_load('xoopscache');
        $this->stdoutput = XoopsCache::read($this->setFilename($id, $title));
        if ($this->stdoutput) {
            $this->doDisplay();
            exit();
        }

        return false;
    }

    /**
     * wfp_dopdf::deleteCache()
     *
     * @param $id
     * @param $title
     */
    public function deleteCache($id, $title): void
    {
        $loaded = \xoops_load('xoopscache');
        XoopsCache::delete($this->setFilename($id, $title));
    }

    /**
     * wfp_dopdf::createCache()
     *
     * @param mixed $id
     * @param mixed $title
     */
    public function createCache($id, $title): void
    {
        \xoops_load('xoopscache');
        XoopsCache::write($this->setFilename($id, $title), $this->stdoutput);
    }
}
