<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

/**
 * Name: class.help.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

/**
 * Clean
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class Clean
{
    public $content = null;

    /**
     * Wfresource\Clean::__construct()
     */
    public function __construct()
    {
    }

    /**
     * Clean::fileExists()
     *
     * @param mixed $file
     */
    public function getContentsCallback($file): void
    {
        static $content = null;
        if (null === $content) {
            if (\file_exists($file) && \is_readable($file)) {
                $this->content = file_get_contents($file);
            }
        }
    }

    /**
     * Clean::getHtml()
     *
     * @param string $file
     * @param string $content
     * @param string $uploaddir
     */
    public function getHtml($file = '', $content = '', $uploaddir = ''): ?string
    {
        if ('https://' === $file || !$file) {
            $this->content = $content;
        } else {
            if (\preg_match('/^[\.]{1,2}$/', $file)) {
                $obj->setVar('wfc_file', '');
            }
            $paths = [$file, XOOPS_ROOT_PATH . '/' . $uploaddir . '/' . $file];
            \array_walk_recursive($paths, ['self', 'getContentsCallback']);
        }

        return $this->content;
    }

    /**
     * Clean::importHtml()
     *
     * @param string $file
     * @param string $uploaddir
     * @return string
     */
    public function importHtml($file = '', $uploaddir = '')
    {
        if (\preg_match('/^[\.]{1,2}$/', $file)) {
            return '';
        }
        /**
         * Do array walk to get path and contents
         */
        $paths = [$file, XOOPS_ROOT_PATH . '/' . $uploaddir . '/' . $file];
        \array_walk_recursive($paths, ['self', 'getContentsCallback']);
        /**
         * Do array walk to get path and contents
         */
        $matches = [];
        \preg_match('/<title>(.*)<\/title>/', $this->content, $matches);
        $content['content'] = $this->content;
        $content['title']   = isset($matches[1]) ? (string)$matches[1] : '';

        return $content;
    }

    /**
     * Clean::cleanUpHTML()
     *
     * @param mixed $text
     * @param mixed $cleanlevel
     * @return mixed|string
     */
    public function &cleanUpHTML($text, $cleanlevel = 0)
    {
        $text     = \stripslashes($text);
        $htmltidy = new Htmltidy(); //wfp_getClass('htmltidy', _RESOURCE_DIR, _RESOURCE_CLASS);

        $htmltidy->Options['UseTidy']     = false;
        $htmltidy->Options['OutputXHTML'] = false;
        $htmltidy->Options['Optimize']    = true;
        $htmltidy->Options['Compress']    = true;
        switch ($cleanlevel) {
            case 1:
                $htmltidy->html = &$text;
                $text           = &$htmltidy->cleanUp();
                break;
            case 2:
                $text                        = \preg_replace('/\<style[\w\W]*?\<\/style\>/i', '', $text);
                $htmltidy->Options['IsWord'] = true;
                $htmltidy->html              = &$text;
                $text                        = &$htmltidy->cleanUp();
                break;
            case 3:
                $text                        = \preg_replace('/\<style[\w\W]*?\<\/style\>/i', '', $text);
                $htmltidy->Options['IsWord'] = true;
                $htmltidy->html              = &$text;
                $text                        = &$htmltidy->cleanUp();
                $text                        = \strip_tags($text, '<br><br><p>');
                break;
            default:
        } // switch

        return $text;
    }
}
