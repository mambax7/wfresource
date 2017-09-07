<?php
/**
 * Name: class.pdf.php
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
defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

/**
 * wfp_dopdf
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class wfp_Dopdf
{
    public $options     = [];
    public $compression = false;
    public $font        = 'Helvetica.afm';
    public $cachekey    = null;

    /**
     * wfp_Dopdf::__construct()
     */
    public function __construct()
    {
    }

    /**
     * wfp_Dopdf::setOptions()
     *
     * @param  array $opt
     * @return bool
     */
    public function setOptions($opt = [])
    {
        if (!is_array($opt) || 0 === count($opt)) {
            return false;
        }
        $this->cachedir = XOOPS_ROOT_PATH . '/cache/';
        $this->options  = $opt;
    }

    /**
     * wfp_dopdf::renderpdf()
     *
     */
    public function doRender()
    {
        wfp_loadLangauge('print', 'wfresource');
        error_reporting(0);
        $this->stdoutput = $this->getCache($this->options['id'], $this->options['title']);
        if (!$this->stdoutput) {
            /**
             */
            require_once _WFP_RESOURCE_PATH . '/class/pdf/class.ezpdf.php';
            $pdf                         = new Cezpdf('a4', 'P'); //A4 Portrait
            $pdf->options['compression'] = $this->compression;
            $pdf->ezSetCmMargins(2, 1.5, 1, 1);
            // select font
            $pdf->selectFont(_WFP_RESOURCE_PATH . '/class/pdf/fonts/' . $this->font, _CHARSET); //choose font
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
            $pdf->addText(450, 34, 6, _CONTENT_RENDERED . ' ' . $this->options['renderdate']);
            $pdf->restoreState();
            $pdf->closeObject();
            $pdf->addObject($all, 'all');
            $pdf->ezSetDy(30);
            // title
            $pdf->ezText(strip_tags($this->options['title']), 16);
            $pdf->ezText("\n", 6);
            if (!empty($this->options['author'])) {
                $pdf->ezText(_CONTENT_AUTHOR . $this->options['author'], 8);
            }
            if (!empty($this->options['pdate'])) {
                $pdf->ezText(_CONTENT_PUBLISHED . $this->options['pdate'], 8);
            }
            if (!empty($this->options['udate'])) {
                $pdf->ezText(_CONTENT_UPDATED . $this->options['udate'], 8);
            }
            $pdf->ezText("\n", 6);
            if ($this->options['itemurl']) {
                $pdf->ezText(_CONTENT_URL_TOITEM . $this->options['itemurl'], 8);
                $pdf->ezText("\n", 6);
            }

            if ($this->options['subtitle']) {
                $pdf->ezText($this->options['subtitle'], 14);
                $pdf->ezText("\n", 6);
            }
            $pdf->ezText($this->getContent(), 10);
            if ($this->options['stdoutput'] === 'file') {
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
     *
     */
    public function doDisplay()
    {
        $fileName = (isset($this->options['title']) ? $this->options['title'] . '.pdf' : 'file.pdf');

        header('Content-type: application/pdf');
        header('Content-Length: ' . strlen(ltrim($fileName)));
        header('Content-Disposition: inline; filename=' . $fileName);
        if (isset($options['Accept-Ranges']) && $options['Accept-Ranges'] === 1) {
            header('Accept-Ranges: ' . strlen(ltrim($tmp)));
        }
        echo $this->stdoutput;
        exit();
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
    public function setFont($value = '')
    {
        $this->font = (string)trim($value);
    }

    /**
     * @param bool|false $value
     */
    public function useCompression($value = false)
    {
        $this->compression = ($value === true) ? true : false;
    }

    /**
     * wfp_Dopdf::getContent()
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
        $text = preg_replace('/\<style[\w\W]*?\<\/style\>/i', '', $text);
        $text = preg_replace("/<img[^>]+\>/i", '', $text);
        $text = str_replace('[pagebreak]', '<br><br>', $text);

        $htmltidy = wfp_getClass('htmltidy', _RESOURCE_DIR, _RESOURCE_CLASS);
        if ($htmltidy) {
            $htmltidy->Options['UseTidy']     = false;
            $htmltidy->Options['OutputXHTML'] = true;
            $htmltidy->Options['Optimize']    = true;
            $htmltidy->Options['Compress']    = true;
            $htmltidy->html                   = $text;
            $text                             = $htmltidy->cleanUp();
        }

        $text = str_replace(['<p>', '</p>'], "\n", $text);
        $text = str_replace('<P>', "\n", $text);
        $text = str_replace('<br>', "\n", $text);
        $text = str_replace('<br>', "\n", $text);
        $text = str_replace('<br>', "\n", $text);
        $text = str_replace('<br>', "\n", $text);
        $text = str_replace('<li>', "\n - ", $text);
        $text = str_replace('<li>', "\n - ", $text);
        $text = str_replace('[pagebreak]', '', $text);
        $text = strip_tags(ltrim($text));
        $text = htmlspecialchars_decode(ltrim($text));

        return $text;
    }

    /**
     * wfp_dopdf::setFilename()
     *
     * @param  mixed $id
     * @param  mixed $title
     * @return string
     */
    public function setFilename($id, $title)
    {
        $module = $GLOBALS['xoopsModule']->getVar('dirname');
        $id     = md5((int)$id);
        $title  = str_replace(' ', '_', strtolower($title));

        return 'wfp_pdffile' . md5($module . $id . $title) . '.pdf';
    }

    /**
     * wfp_dopdf::createCache()
     *
     * @param  mixed $id
     * @param  mixed $title
     * @return bool
     */
    public function getCache($id, $title)
    {
        xoops_load('xoopscache');
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
    public function deleteCache($id, $title)
    {
        $loaded = xoops_load('xoopscache');
        XoopsCache::delete($this->setFilename($id, $title));
    }

    /**
     * wfp_dopdf::createCache()
     *
     * @param mixed $id
     * @param mixed $title
     */
    public function createCache($id, $title)
    {
        xoops_load('xoopscache');
        XoopsCache::write($this->setFilename($id, $title), $this->stdoutput);
    }
}
