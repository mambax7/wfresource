<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

require_once XOOPS_ROOT_PATH . '/class/xoopsform/formbutton.php';

/**
 * XoopsFormButtonTray
 *
 * @author    John
 * @copyright Copyright (c) 2007
 */
class XoopsFormButtonTray extends XoopsFormElement
{
    /**
     * Value
     *
     * @var string
     */
    public $_value;
    /**
     * Type of the button. This could be either "button", "submit", or "reset"
     *
     * @var string
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
        $this->_type       = !empty($type) ? $type : 'submit';
        $this->_showDelete = $showDelete;
        if ($onclick) {
            $this->setExtra($onclick);
        } else {
            $this->setExtra('');
        }
    }

    /**
     * XoopsFormButtonTray::getValue()
     */
    public function getValue(): string
    {
        return $this->_value;
    }

    /**
     * XoopsFormButtonTray::setValue()
     *
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->_value = $value;
    }

    /**
     * XoopsFormButtonTray::getType()
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * XoopsFormButtonTray::render()
     * @return string
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
