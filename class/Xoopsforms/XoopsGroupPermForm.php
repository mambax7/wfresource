<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

use XoopsForm;
use XoopsFormButton;
use XoopsFormElementTray;
use XoopsFormHidden;
use XoopsModules\Wfresource;
use XoopsModules\Wfresource\Xoopsforms;

//require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formelement.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formhidden.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formbutton.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formelementtray.php';
//require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/form.php';

/**
 * Renders a form for setting module specific group permissions
 *
 * @author     Kazumi Ono <onokazu@myweb.ne.jp>
 * @copyright  copyright (c) 2000-2003 XOOPS.org
 */
class XoopsGroupPermForm extends XoopsForm
{
    /**
     * Module ID
     *
     * @var int
     */
    public $_modid;
    /**
     * Tree structure of items
     *
     * @var array
     */
    public $_itemTree;
    /**
     * Name of permission
     *
     * @var string
     */
    public $_permName;
    /**
     * Description of permission
     *
     * @var string
     */
    public $_permDesc;

    /**
     * Constructor
     * @param        $title
     * @param        $modid
     * @param        $permname
     * @param        $permdesc
     * @param string $url
     */
    public function __construct($title, $modid, $permname, $permdesc, $url = '')
    {
        parent::__construct($title, 'groupperm_form', XOOPS_URL . '/modules/system/admin/groupperm.php', 'post');
        $this->_modid    = (int)$modid;
        $this->_permName = $permname;
        $this->_permDesc = $permdesc;
        $this->addElement(new XoopsFormHidden('modid', $this->_modid));
        if ('' !== $url) {
            $this->addElement(new XoopsFormHidden('redirect_url', $url));
        }
    }

    /**
     * Adds an item to which permission will be assigned
     *
     * @param string $itemName
     * @param int    $itemId
     * @param int    $itemParent
     */
    public function addItem($itemId, $itemName, $itemParent = 0): void
    {
        $this->_itemTree[$itemParent]['children'][] = $itemId;
        $this->_itemTree[$itemId]['parent']         = $itemParent;
        $this->_itemTree[$itemId]['name']           = $itemName;
        $this->_itemTree[$itemId]['id']             = $itemId;
    }

    /**
     * Loads all child ids for an item to be used in javascript
     *
     * @param int   $itemId
     * @param array $childIds
     */
    public function _loadAllChildItemIds($itemId, &$childIds): void
    {
        if (!empty($this->_itemTree[$itemId]['children'])) {
            $first_child = $this->_itemTree[$itemId]['children'];
            foreach ($first_child as $fcid) {
                $childIds[] = $fcid;
                if (!empty($this->_itemTree[$fcid]['children'])) {
                    foreach ($this->_itemTree[$fcid]['children'] as $_fcid) {
                        $childIds[] = $_fcid;
                        $this->_loadAllChildItemIds($_fcid, $childIds);
                    }
                }
            }
        }
    }

    /**
     * Renders the form
     *
     * @return string
     */
    public function render()
    {
        // load all child ids for javascript codes
        foreach (\array_keys($this->_itemTree) as $item_id) {
            $this->_itemTree[$item_id]['allchild'] = [];
            $this->_loadAllChildItemIds($item_id, $this->_itemTree[$item_id]['allchild']);
        }
        $grouppermHandler = \xoops_getHandler('groupperm');
        $memberHandler    = \xoops_getHandler('member');
        $glist            = $memberHandler->getGroupList();
        foreach (\array_keys($glist) as $i) {
            // get selected item id(s) for each group
            $selected = $grouppermHandler->getItemIds($this->_permName, $i, $this->_modid);
            $ele      = new XoopsGroupFormCheckBox($glist[$i], 'perms[' . $this->_permName . ']', $i, $selected);
            $ele->setOptionTree($this->_itemTree);
            $this->addElement($ele);
            unset($ele);
        }
        $tray = new XoopsFormElementTray('');
        $tray->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
        $tray->addElement(new XoopsFormButton('', 'reset', _CANCEL, 'reset'));
        $this->addElement($tray);

        $ret      = '<h4>' . $this->getTitle() . '</h4>' . $this->_permDesc . '<br>';
        $ret      .= "<form name='" . $this->getName() . "' id='" . $this->getName() . "' action='" . $this->getAction() . "' method='" . $this->getMethod() . "'" . $this->getExtra() . ">\n<table width='100%' class='outer' cellspacing='1' valign='top'>\n";
        $elements = &$this->getElements();
        $hidden   = '';
        foreach (\array_keys($elements) as $i) {
            if (!\is_object($elements[$i])) {
                $ret .= $elements[$i];
            } elseif (!$elements[$i]->isHidden()) {
                $ret .= "<tr valign='top' align='left'><td class='head'>" . $elements[$i]->getCaption();
                if ('' !== $elements[$i]->getDescription()) {
                    $ret .= '<br><br><span style="font-weight: normal;">' . $elements[$i]->getDescription() . '</span>';
                }
                $ret .= "</td>\n<td class='even'>\n" . $elements[$i]->render() . "\n</td></tr>\n";
            } else {
                $hidden .= $elements[$i]->render();
            }
        }
        $ret .= "</table>$hidden</form>";
        $ret .= $this->renderValidationJS(true);

        return $ret;
    }
}
