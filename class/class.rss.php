<?php
/**
 * Name: class.rss.php
 * Description:
 *
 * @package    : Xoosla Modules
 * @Module     :
 * @subpackage :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 * @version    : $Id: class.rss.php 8181 2011-11-07 01:14:53Z beckmi $
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * wpp_Rss
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @version   $Id: class.rss.php 8181 2011-11-07 01:14:53Z beckmi $
 * @access    public
 */
class wfp_Rss
{
    public $channel = array();
    public $values;

    public function __construct()
    {
        xoops_load('XoopsLocal');
    }

    /**
     * wpp_Rss::basics()
     *
     * @return
     */
    public function basics($image, $path)
    {
        $this->channel['channel_title']       = self::setEncoding(self::getChannelTitle());
        $this->channel['channel_link']        = self::getChannelLink();
        $this->channel['channel_desc']        = self::setEncoding($GLOBALS['xoopsConfig']['slogan']);
        $this->channel['channel_lastbuild']   = formatTimestamp(time(), 'rss');
        $this->channel['channel_webmaster']   = self::checkEmail($GLOBALS['xoopsConfig']['adminmail']);
        $this->channel['channel_editor']      = self::checkEmail($GLOBALS['xoopsConfig']['adminmail']);
        $this->channel['channel_editor_name'] = self::setEncoding($GLOBALS['xoopsConfig']['sitename']);
        $this->channel['channel_category']    = self::setEncoding($GLOBALS['xoopsModule']->getVar('name'));
        $this->channel['channel_generator']   = 'PHP';
        $this->channel['channel_language']    = _LANGCODE;
        self::getChannelImage($image, $path);
    }

    /**
     * wpp_Rss::getChannelTitle()
     *
     * @return
     */
    public function getChannelTitle()
    {
        return (is_object($GLOBALS['xoopsModule'])) ? $GLOBALS['xoopsConfig']['sitename'] . ' - ' . $GLOBALS['xoopsModule']->getVar('name', 'e') : $GLOBALS['xoopsConfig']['sitename'];
    }

    /**
     * wpp_Rss::getChannelLink()
     *
     * @return
     */
    public function getChannelLink()
    {
        $moduleUrl = XOOPS_URL;
        if (is_object($GLOBALS['xoopsModule'])) {
            $moduleUrl .= '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname');
        }

        return $moduleUrl;
        unset($moduleUrl);
    }

    /**
     * wpp_Rss::getChannelImage()
     *
     * @return
     */
    public function getChannelImage($image, $path = '')
    {
        // image_url
        if (file_exists($file = XOOPS_ROOT_PATH . '/' . $path . '/' . $image)) {
            $dimention = getimagesize($file);
            $width     = (empty($dimention[0])) ? 88 : ($dimention[0] > 144) ? 144 : $dimention[0];
            $height    = (empty($dimention[0])) ? 31 : ($dimention[0] > 400) ? 400 : $dimention[1];
            /**
             */
            $this->channel['image_url']    = XOOPS_URL . '/' . $path . '/' . $image;
            $this->channel['image_width']  = (int)$width;
            $this->channel['image_height'] = (int)$height;
        }
    }

    /**
     * wpp_Rss::setEncoding()
     *
     * @return
     */
    public function setEncoding($value)
    {
        return XoopsLocal::convert_encoding(htmlspecialchars($value, ENT_QUOTES));
    }

    /**
     * wpp_Rss::CheckEmail()
     *
     * @return
     */
    public function checkEmail($value)
    {
        return checkEmail($value);
    }

    /**
     * wpp_Rss::setRssValue()
     *
     * @param mixed $name
     * @param mixed $value
     * @param mixed $special
     * @return
     */
    public function setChannelValue($name, $value, $special = true)
    {
        $this->channel[$name] = ($special) ? htmlspecialchars($value, ENT_QUOTES) : $value;
    }

    /**
     * wfp_Rss::render()
     *
     * @return
     */
    public function render()
    {
        return $this->channel;
    }
}
