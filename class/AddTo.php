<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

/**
 * Name: class.addto.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

/**
 * Class AddTo
 */
class AddTo
{
    public $bookMarklist = [];
    public $itemTitle;
    public $itemUrl;
    public $method       = 0;
    public $layout       = 0; //H or V
    public $addText      = false;

    /**
     * AddToo::__construct()
     */
    public function __construct()
    {
    }

    /**
     * AddToo::render()
     *
     * @param string $title
     * @return mixed|string
     */
    public function render($title = '')
    {
        $this->itemTitle = \htmlspecialchars($title, \ENT_QUOTES | \ENT_HTML5);
        $this->addText   = (int)$GLOBALS['xoopsModuleConfig']['bookmarktextadd'];
        $this->layout    = (int)$GLOBALS['xoopsModuleConfig']['bookmarklayout'];
        $this->method    = 0;

        \xoops_load('xoopscache');
        if ($GLOBALS['xoopsModuleConfig']['allowaddthiscode']) {
            $ret = \XoopsCache::read('wfc_bookmarks' . \md5('wfc_addthisBookmarks'));
            if (!$ret) {
                $ret = $this->addThisCode();
                \XoopsCache::write('wfc_bookmarks' . \md5('wfc_addthisBookmarks'), $ret);
            }
        } else {
            $ret = \XoopsCache::read('wfc_bookmarks' . \md5('wfc_doBookmarks'));
            if (!$ret) {
                $ret = $this->doBookMarks();
                \XoopsCache::write('wfc_bookmarks' . \md5('wfc_doBookmarks'), $ret);
            }
        }

        return $ret;
    }

    /**
     * AddTo::bookmarklist()
     */
    public function bookMarkList(): array
    {
        $ret[] = [
            'title' => 'blinklist',
            'url'   => 'https://www.blinklist.com/index.php?Action=Blink/addblink.php&amp;Description=&amp;Url=<$BlogItemPermalinkURL$>&amp;Title=<$BlogItemTitle$>',
        ];
        $ret[] = [
            'title' => 'delicious',
            'url'   => 'https://del.icio.us/post?url=<$BlogItemPermalinkURL$>&amp;title=<$BlogItemTitle$>',
        ];
        $ret[] = ['title' => 'digg', 'url' => 'https://digg.com/submit?phase=2&amp;url=<$BlogItemPermalinkURL$>'];
        $ret[] = [
            'title' => 'fark',
            'url'   => 'https://cgi.fark.com/cgi/fark/edit.pl?new_url=<$BlogItemPermalinkURL$>&amp;new_comment=<$BlogItemTitle$>&amp;new_link_other=<$BlogItemTitle$>&amp;linktype=Misc',
        ];
        $ret[] = [
            'title' => 'furl',
            'url'   => 'https://www.furl.net/storeIt.jsp?t=<$BlogItemTitle$>&amp;u=<$BlogItemPermalinkURL$>',
        ];
        $ret[] = [
            'title' => 'newsvine',
            'url'   => 'https://www.newsvine.com/_tools/seed&amp;save?u=<$BlogItemPermalinkURL$>&amp;h=<$BlogItemTitle$>',
        ];
        $ret[] = [
            'title' => 'reddit',
            'url'   => 'https://reddit.com/submit?url=<$BlogItemPermalinkURL$>&amp;title=<$BlogItemTitle$>',
        ];
        $ret[] = [
            'title' => 'simpy',
            'url'   => 'https://www.simpy.com/simpy/LinkAdd.do?href=<$BlogItemPermalinkURL$>&amp;title=<$BlogItemTitle$>',
        ];
        $ret[] = [
            'title' => 'spurl',
            'url'   => 'https://www.spurl.net/spurl.php?title=<$BlogItemTitle$>&amp;url=<$BlogItemPermalinkURL$>',
        ];
        $ret[] = [
            'title' => 'yahoomyweb',
            'url'   => 'https://myweb2.search.yahoo.com/myresults/bookmarklet?t=<$BlogItemTitle$>&amp;u=<$BlogItemPermalinkURL$>',
        ];
        $ret[] = [
            'title' => 'facebook',
            'url'   => 'https://www.facebook.com/sharer.php?u=<$BlogItemPermalinkURL$>&amp;t=<$BlogItemTitle$>',
        ];

        return $ret;
    }

    /**
     * AddTo::addThisCode()
     */
    public function addThisCode(): string
    {
        $code = $GLOBALS['xoopsModuleConfig']['addthiscode'];
        if (empty($GLOBALS['xoopsModuleConfig']['addthiscode'])) {
            return $this->doBookMarks();
        }

        return $GLOBALS['xoopsModuleConfig']['addthiscode'];
    }

    /**
     * AddTo::doBookMarks()
     */
    public function doBookMarks(): string
    {
        $ret = '<div>';
        foreach ($this->bookMarkList() as $b_marks) {
            $ret .= '<a rel="nofollow" href="' . $this->getBookMarkUrl($b_marks['url']) . '" title="' . $this->getBookMarkName($b_marks['title']) . '" target="' . $this->getMethod() . '">';
            $ret .= $this->getBookMarkImage($b_marks['title']);
            if ($this->addText) {
                $ret .= '&nbsp;' . $this->getBookMarkName($b_marks['title']);
            }
            $ret .= '</a>';
            $ret .= $this->getLayout();
        }
        $ret .= '</div>';

        return $ret;
    }

    /**
     * AddTo::replace()
     *
     * @param mixed $text
     * @return array|string|string[]
     */
    public function replace(&$text)
    {
        $patterns           = [];
        $replacements       = [];
        $patterns[]         = '<$BlogItemPermalinkURL$>';
        $replacements[]     = $this->getItemUrl();
        $patterns[]         = '<$BlogItemTitle$>';
        $replacements[]     = $this->getItemTitle();
        $this->text         = &$text;
        $this->patterns     = $patterns;
        $this->replacements = $replacements;
        $text               = \str_replace($this->patterns, $this->replacements, $this->text);

        return $text;
    }

    /**
     * AddTo::getItemTitle()
     */
    public function getItemTitle(): string
    {
        return \rawurlencode($this->itemTitle);
    }

    /**
     * AddTo::getUrl()
     */
    public function getItemUrl(): string
    {
        return XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/' . \basename($_SERVER['SCRIPT_NAME']) . '?' . $_SERVER['QUERY_STRING'];
    }

    /**
     * AddTo::getBookMarkUrl()
     *
     * @param string $value
     * @return mixed|string
     */
    private function getBookMarkUrl($value = '')
    {
        return !empty($value) ? $this->replace($value) : '';
    }

    /**
     * AddTo::getBookMarkImage()
     *
     * @param string $value
     */
    private function getBookMarkImage($value = ''): string
    {
        return !empty($value) ? '<img style="vertical-align: middle;" src="' . XOOPS_URL . '/modules/wfresource/images/icon/bookmark/' . $value . '.png" border="0" title="' . $this->getBookMarkName($value) . '" alt="' . $this->getBookMarkName($value) . '" >' : '';
    }

    /**
     * AddTo::getBookMarkName()
     *
     * @param $value
     */
    private function getBookMarkName($value): string
    {
        return !empty($value) ? \_MD_WFP_BOOKMARKTO . \ucfirst((string)$value) : '';
    }

    /**
     * AddTo::method()
     */
    public function getMethod(): string
    {
        return $this->method ? '_blank' : '_self';
    }

    /**
     * AddTo::layout()
     */
    public function getLayout(): string
    {
        return (0 === $this->layout) ? '&nbsp;' : '</div><div>';
    }
}
