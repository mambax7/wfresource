<?php
defined('XOOPS_ROOT_PATH') || exit('You do not have permission to access this file!');

require_once XOOPS_ROOT_PATH . '/class/xoopsform/formbutton.php';

/**
 * XoopsFormButtonTray
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2007
 * @access    public
 */
class XoopsFormButtonTray extends XoopsFormElement
{
    /**
     * Value
     *
     * @var string
     * @access private
     */
    public $_value;

    /**
     * Type of the button. This could be either "button", "submit", or "reset"
     *
     * @var string
     * @access private
     */
    public $_type;

    /**
     * XoopsFormButtonTray::XoopsFormButtonTray()
     *
     * @param mixed  $name
     * @param string $value
     * @param string $type
     * @param string $onclick
     * @param bool   $showDelete
     */
    public function __construct($name, $value = '', $type = '', $onclick = '', $showDelete = false)
    {
        parent::__construct();
        $this->setName($name);
        $this->setValue($value);
        $this->_type       = (!empty($type)) ? $type : 'submit';
        $this->_showDelete = $showDelete;
        if ($onclick) {
            $this->setExtra($onclick);
        } else {
            $this->setExtra('');
        }
    }

    /**
     * XoopsFormButtonTray::getValue()
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * XoopsFormButtonTray::setValue()
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * XoopsFormButtonTray::getType()
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * XoopsFormButtonTray::render()
     * @return string|void
     */
    public function render()
    {
        // onclick="this.form.elements.op.value=\'delfile\';
        $ret = '';
        if ($this->_showDelete) {
            $ret .= '<input type="submit" class="formbutton" name="delete" id="delete" value="' . _DELETE . '" onclick="this.form.elements.op.value=\'delete\'">&nbsp;';
        }
        $ret .= '<input type="button" value="'
                . _CANCEL
                . '" onClick="history.go(-1);return true;">&nbsp;<input type="reset" class="formbutton"  name="reset"  id="reset" value="'
                . _RESET
                . '">&nbsp;<input type="'
                . $this->getType()
                . '" class="formbutton"  name="'
                . $this->getName()
                . '"  id="'
                . $this->getName()
                . '" value="'
                . $this->getValue()
                . '"'
                . $this->getExtra()
                . ' >';

        return $ret;
    }
}
