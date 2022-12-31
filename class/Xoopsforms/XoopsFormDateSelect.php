<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

/**
 * XoopsFormDateSelect
 *
 * @author    John
 * @copyright Copyright (c) 2007
 **/
class XoopsFormDateSelect extends XoopsFormText
{
    /**
     * XoopsFormDateSelect::XoopsFormDateSelect()
     *
     * @param mixed $caption
     * @param mixed $name
     * @param int   $size
     * @param mixed $value
     * @param mixed $dotime
     **/
    public function __construct($caption, $name, $size = 20, $value = null, $dotime = true)
    {
        if (\is_numeric($value) && (int)$value > 4000) {
            $value = \date('Y-m-d H:i:s', (int)$value);
        } else {
            $value = $dotime ? \date('Y-m-d H:i:s', \time()) : null;
        }
        parent::__construct($caption, $name, $size, 25, $value);
    }

    /**
     * XoopsFormDateSelect::render()
     */
    public function render(): string
    {
        $jstime = \formatTimestamp($this->getValue(), 'F j Y, H:i:s');
        require_once XOOPS_ROOT_PATH . '/modules/wfresource/include/calendarjs.php';

        return "<input type='text' name='"
               . $this->getName()
               . "' id='"
               . $this->getName()
               . "' size='"
               . $this->getSize()
               . "' maxlength='"
               . $this->getMaxlength()
               . "' value='"
               . $this->getValue()
               . "'"
               . $this->getExtra()
               . ">&nbsp;<input type='reset' value=' ... ' onclick='return showCalendar(\""
               . $this->getName()
               . "\");'>";
    }
}
