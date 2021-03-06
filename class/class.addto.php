<?php
/**
 * Name: class.addto.php
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
 * Class wfp_addto
 */
class wfp_addto
{
    public $bookMarklist = [];
    public $itemTitle;
    public $itemUrl;
    public $method       = 0; //
    public $layout       = 0; //H or V
    public $addText      = false;

    /**
     * wfp_addtoo::__construct()
     */
    public function __construct()
    {
    }

    /**
     * wfp_addtoo::render()
     *
     * @param  string $title
     * @return mixed|string $ret
     */
    public function render($title = '')
    {
        $this->itemTitle = htmlspecialchars($title);
        $this->addText   = (int)$GLOBALS['xoopsModuleConfig']['bookmarktextadd'];
        $this->layout    = (int)$GLOBALS['xoopsModuleConfig']['bookmarklayout'];
        $this->method    = 0;
        /**
         */
        xoops_load('xoopscache');
        if ($GLOBALS['xoopsModuleConfig']['allowaddthiscode']) {
            $ret = XoopsCache::read('wfc_bookmarks' . md5('wfc_addthisBookmarks'));
            if (!$ret) {
                $ret = $this->addThisCode();
                XoopsCache::write('wfc_bookmarks' . md5('wfc_addthisBookmarks'), $ret);
            }
        } else {
            $ret = XoopsCache::read('wfc_bookmarks' . md5('wfc_doBookmarks'));
            if (!$ret) {
                $ret = $this->doBookMarks();
                XoopsCache::write('wfc_bookmarks' . md5('wfc_doBookmarks'), $ret);
            }
        }

        return $ret;
    }

    /**
     * wfp_addto::bookmarklist()
     * @return array
     */
    public function bookMarkList()
    {
        $ret[] = [
            'title' => 'blinklist',
            'url'   => 'http://www.blinklist.com/index.php?Action=Blink/addblink.php&amp;Description=&amp;Url=<$BlogItemPermalinkURL$>&amp;Title=<$BlogItemTitle$>'
        ];
        $ret[] = [
            'title' => 'delicious',
            'url'   => 'http://del.icio.us/post?url=<$BlogItemPermalinkURL$>&amp;title=<$BlogItemTitle$>'
        ];
        $ret[] = ['title' => 'digg', 'url' => 'http://digg.com/submit?phase=2&amp;url=<$BlogItemPermalinkURL$>'];
        $ret[] = [
            'title' => 'fark',
            'url'   => 'http://cgi.fark.com/cgi/fark/edit.pl?new_url=<$BlogItemPermalinkURL$>&amp;new_comment=<$BlogItemTitle$>&amp;new_link_other=<$BlogItemTitle$>&amp;linktype=Misc'
        ];
        $ret[] = [
            'title' => 'furl',
            'url'   => 'http://www.furl.net/storeIt.jsp?t=<$BlogItemTitle$>&amp;u=<$BlogItemPermalinkURL$>'
        ];
        $ret[] = [
            'title' => 'newsvine',
            'url'   => 'http://www.newsvine.com/_tools/seed&amp;save?u=<$BlogItemPermalinkURL$>&amp;h=<$BlogItemTitle$>'
        ];
        $ret[] = [
            'title' => 'reddit',
            'url'   => 'http://reddit.com/submit?url=<$BlogItemPermalinkURL$>&amp;title=<$BlogItemTitle$>'
        ];
        $ret[] = [
            'title' => 'simpy',
            'url'   => 'http://www.simpy.com/simpy/LinkAdd.do?href=<$BlogItemPermalinkURL$>&amp;title=<$BlogItemTitle$>'
        ];
        $ret[] = [
            'title' => 'spurl',
            'url'   => 'http://www.spurl.net/spurl.php?title=<$BlogItemTitle$>&amp;url=<$BlogItemPermalinkURL$>'
        ];
        $ret[] = [
            'title' => 'yahoomyweb',
            'url'   => 'http://myweb2.search.yahoo.com/myresults/bookmarklet?t=<$BlogItemTitle$>&amp;u=<$BlogItemPermalinkURL$>'
        ];
        $ret[] = [
            'title' => 'facebook',
            'url'   => 'http://www.facebook.com/sharer.php?u=<$BlogItemPermalinkURL$>&amp;t=<$BlogItemTitle$>'
        ];

        return $ret;
    }

    /**
     * wfp_addto::addThisCode()
     * @return string
     */
    public function addThisCode()
    {
        $code = $GLOBALS['xoopsModuleConfig']['addthiscode'];
        if (empty($GLOBALS['xoopsModuleConfig']['addthiscode'])) {
            return $this->doBookMarks();
        }

        return $GLOBALS['xoopsModuleConfig']['addthiscode'];
    }

    /**
     * wfp_addto::doBookMarks()
     * @return string
     */
    public function doBookMarks()
    {
        $ret = '<div>';
        foreach ($this->bookMarkList() as $b_marks) {
            $ret .= '<a rel="nofollow" href="' . $this->getBookMarkUrl($b_marks['url']) . '" title="' . $this->getBookMarkName($b_marks['title']) . '" target="' . $this->getMethod() . '">';
            $ret .= $this->getBookMarkImage($b_marks['title']);
            if (true === $this->addText) {
                $ret .= '&nbsp;' . $this->getBookMarkName($b_marks['title']);
            }
            $ret .= '</a>';
            $ret .= $this->getLayout();
        }
        $ret .= '</div>';

        return $ret;
    }

    /**
     * wfp_addto::replace()
     *
     * @param  mixed $text
     * @return mixed
     */
    public function replace(&$text)
    {
        $patterns           = [];
        $replacements       = [];
        $patterns[]         = '<$BlogItemPermalinkURL$>';
        $replacements[]     = $this->getItemUrl();
        $patterns[]         = '<$BlogItemTitle$>';
        $replacements[]     = $this->getItemTitle();
        $this->text         =& $text;
        $this->patterns     = $patterns;
        $this->replacements = $replacements;
        $text               = str_replace($this->patterns, $this->replacements, $this->text);

        return $text;
    }

    /**
     * wfp_addto::getItemTitle()
     * @return string
     */
    public function getItemTitle()
    {
        return rawurlencode($this->itemTitle);
    }

    /**
     * wfp_addto::getUrl()
     * @return string
     */
    public function getItemUrl()
    {
        return XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/' . basename($_SERVER['SCRIPT_NAME']) . '?' . $_SERVER['QUERY_STRING'];
    }

    /**
     * wfp_addto::getBookMarkUrl()
     *
     * @param  string $value
     * @return mixed|string
     */
    private function getBookMarkUrl($value = '')
    {
        return (!empty($value)) ? $this->replace($value) : '';
    }

    /**
     * wfp_addto::getBookMarkImage()
     *
     * @param  string $value
     * @return string
     */
    private function getBookMarkImage($value = '')
    {
        return (!empty($value)) ? '<img style="vertical-align: middle;" src="' . XOOPS_URL . '/modules/wfresource/images/icon/bookmark/' . $value . '.png" border="0" title="' . $this->getBookMarkName($value) . '" alt="' . $this->getBookMarkName($value) . '" >' : '';
    }

    /**
     * wfp_addto::getBookMarkName()
     *
     * @param $value
     * @return string
     */
    private function getBookMarkName($value)
    {
        return (!empty($value)) ? _MD_WFP_BOOKMARKTO . ucfirst((string)$value) : '';
    }

    /**
     * wfp_addto::method()
     * @return string
     */
    public function getMethod()
    {
        return $this->method ? '_blank' : '_self';
    }

    /**
     * wfp_addto::layout()
     * @return string
     */
    public function getLayout()
    {
        return (0 === $this->layout) ? '&nbsp;' : '</div><div>';
    }
}
