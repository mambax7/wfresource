<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

/**
 * Name: class.rss.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

/**
 * wpp_Rss
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class Rss
{
    public $channel = [];
    public $values;

    public function __construct()
    {
        \xoops_load('XoopsLocal');
    }

    /**
     * wpp_Rss::basics()
     *
     * @param $image
     * @param $path
     */
    public function basics($image, $path): void
    {
        $this->channel['channel_title']       = $this->setEncoding($this->getChannelTitle());
        $this->channel['channel_link']        = $this->getChannelLink();
        $this->channel['channel_desc']        = $this->setEncoding($GLOBALS['xoopsConfig']['slogan']);
        $this->channel['channel_lastbuild']   = \formatTimestamp(\time(), 'rss');
        $this->channel['channel_webmaster']   = $this->checkEmail($GLOBALS['xoopsConfig']['adminmail']);
        $this->channel['channel_editor']      = $this->checkEmail($GLOBALS['xoopsConfig']['adminmail']);
        $this->channel['channel_editor_name'] = $this->setEncoding($GLOBALS['xoopsConfig']['sitename']);
        $this->channel['channel_category']    = $this->setEncoding($GLOBALS['xoopsModule']->getVar('name'));
        $this->channel['channel_generator']   = 'PHP';
        $this->channel['channel_language']    = _LANGCODE;
        $this->getChannelImage($image, $path);
    }

    /**
     * wpp_Rss::getChannelTitle()
     */
    public function getChannelTitle(): string
    {
        return \is_object($GLOBALS['xoopsModule']) ? $GLOBALS['xoopsConfig']['sitename'] . ' - ' . $GLOBALS['xoopsModule']->getVar('name', 'e') : $GLOBALS['xoopsConfig']['sitename'];
    }

    /**
     * wpp_Rss::getChannelLink()
     * @return string
     */
    public function getChannelLink(): ?string
    {
        $moduleUrl = XOOPS_URL;
        if (\is_object($GLOBALS['xoopsModule'])) {
            $moduleUrl .= '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname');
        }

        return $moduleUrl;
        unset($moduleUrl);
    }

    /**
     * wpp_Rss::getChannelImage()
     *
     * @param        $image
     * @param string $path
     */
    public function getChannelImage($image, $path = ''): void
    {
        // image_url
        if (\file_exists($file = XOOPS_ROOT_PATH . '/' . $path . '/' . $image)) {
            $dimention = \getimagesize($file);
//            $width     = empty($dimention[0]) ? 88 : ($dimention[0] > 144) ? 144 : $dimention[0];
            $width     = empty($dimention[0]) ? 88 : (($dimention[0] > 144) ? 144 : $dimention[0]);
//            $height    = empty($dimention[0]) ? 31 : ($dimention[0] > 400) ? 400 : $dimention[1];
            $height    = (empty($dimention[0]) ? 31 : ($dimention[0] > 400)) ? 400 : $dimention[1];

            $this->channel['image_url']    = XOOPS_URL . '/' . $path . '/' . $image;
            $this->channel['image_width']  = $width;
            $this->channel['image_height'] = $height;
        }
    }

    /**
     * wpp_Rss::setEncoding()
     *
     * @param $value
     * @return mixed|string
     */
    public function setEncoding($value)
    {
        return XoopsLocal::convert_encoding(\htmlspecialchars($value, \ENT_QUOTES));
    }

    /**
     * wpp_Rss::CheckEmail()
     *
     * @param $value
     * @return bool|mixed
     */
    public function checkEmail($value)
    {
        return \checkEmail($value);
    }

    /**
     * wpp_Rss::setRssValue()
     *
     * @param mixed $name
     * @param mixed $value
     * @param mixed $special
     */
    public function setChannelValue($name, $value, $special = true): void
    {
        $this->channel[$name] = $special ? \htmlspecialchars($value, \ENT_QUOTES) : $value;
    }

    /**
     * Rss::render()
     */
    public function render(): array
    {
        return $this->channel;
    }
}
