<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

/**
 * Name: formtabs.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

use XoopsModules\Wfresource;
use XoopsModules\Wfresource\Xoopsforms;

/**
 * base class
 */
//require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/form.php';

/**
 * XoopsThemeTabForm
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class XoopsThemeTabForm extends WfpForm
{
    /**
     * XoopsThemeTabForm::__construct()
     * @param mixed $extra
     * @param mixed $class
     */
    // function __construct() {
    // $this->doTabs();
    // }

    /**
     * Insert an empty row in the table to serve as a seperator.
     *
     * @param string $extra HTML to be displayed in the empty row.
     * @param string $class CSS class name for <td> tag
     */
    public function insertBreak($extra = '', $class = ''): void
    {
        $class = ('' !== $class) ? " class='" . \htmlspecialchars($class, \ENT_QUOTES) . "'" : '';
        // Fix for $extra tag not showing
        if ($extra) {
            $extra = "<tr><td colspan='2' $class>$extra</td></tr>";
            $this->addElement($extra);
        } else {
            $extra = "<tr><td colspan='2' $class>&nbsp;</td></tr>";
            $this->addElement($extra);
        }
    }

    /**
     * XoopsThemeTabForm::insertSplit()
     *
     * @param string $extra
     */
    public function insertSplit($extra = ''): void
    {
        $extra = $extra ?: '&nbsp;';
        $ret   = "<tr>\n<td colspan=\"2\" class=\"foot\">&nbsp;</td>\n</tr></table>\n<br>\n<br>\n
        <table width=\"100%\" class=\"outer\" cellspacing=\"1\">
         <tr>\n<th colspan=\"2\">$extra</th>\n</tr>\n";
        $this->addElement($extra);
    }

    /**
     * create HTML to output the form as a theme-enabled table with validation.
     *
     * @return string
     */
    public function render()
    {
        $ele_name = $this->getName();
        $ret      = '';
        //$ret = '<div>' . $this->getTitle() . '</div>';
        $ret    .= "<form name='" . $ele_name . "' id='" . $ele_name . "' action='" . $this->getAction() . "' method='" . $this->getMethod() . "' onsubmit='return xoopsFormValidate_" . $ele_name . "();'" . $this->getExtra() . '>';
        $ret    .= '<div style="text-align: right; padding-right: 20px;">
            <input type="button" class="formbutton"  name="cancels1"  id="cancel1" value="' . _CANCEL . '" onClick="history.go(-1);return true;">
            <input type="reset" class="formbutton"  name="resets1"  id="reset1" value="' . _RESET . '" >
            <input type="submit" class="formbutton"  name="submits1"  id="submit1" value="' . _SUBMIT . '"></div>';
        $ret    .= "<table width='100%' cellspacing='1'><tr><td colspan=\"2\">\n";
        $ret    .= $this->_tabs->startPane('tab_' . $this->getTitle());
        $hidden = '';
        $class  = 'even';
        foreach ($this->getElements() as $ele) {
            if (!\is_object($ele)) {
                $ret .= $ele;
            } elseif (!$ele->isHidden()) {
                if (!$ele->getNocolspan()) {
                    $ret .= "<tr valign='top' align='left'><td class='head' width='35%'>";
                    if ('' !== ($caption = $ele->getCaption())) {
                        $ret .= "<div class='xoops-form-element-caption" . ($ele->isRequired() ? '-required' : '') . "'>" . "<span class='caption-text'>{$caption}</span>" . "<span class='caption-marker'>*</span>" . '</div>';
                    }
                    if ('' !== ($desc = $ele->getDescription())) {
                        $ret .= "<div class='xoops-form-element-help'>{$desc}</div>";
                    }
                    $ret .= "</td><td class='$class'>" . $ele->render() . "</td></tr>\n";
                } else {
                    $ret .= "<tr valign='top' align='left'><td class='head' colspan='2'>";
                    if ('' !== ($caption = $ele->getCaption())) {
                        $ret .= "<div class='xoops-form-element-caption" . ($ele->isRequired() ? '-required' : '') . "'>" . "<span class='caption-text'>{$caption}</span>" . "<span class='caption-marker'>*</span>" . '</div>';
                    }
                    $ret .= "</td></tr><tr valign='top' align='left'><td class='$class' colspan='2'>" . $ele->render() . '</td></tr>';
                }
            } else {
                $hidden .= $ele->render();
            }
        }
        $ret .= $this->_tabs->endPane();
        $ret .= "</tr></table>\n$hidden\n</form>\n";
        $ret .= $this->renderValidationJS(true);

        return $ret;
    }

    /**
     * XoopsThemeTabForm::startTab()
     *
     * @param mixed $tabText
     * @param mixed $paneid
     */
    public function startTab($tabText, $paneid): void
    {
        $this->addElement($this->_tabs->startTab($tabText, $paneid));
    }

    /**
     * XoopsThemeTabForm::endTab()
     */
    public function endTab(): void
    {
        $this->addElement($this->_tabs->endTab());
    }
}
