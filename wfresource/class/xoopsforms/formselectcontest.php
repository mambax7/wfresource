<?php
/**
 * base class
 */
include_once XOOPS_ROOT_PATH . "/class/xoopsform/formselect.php";

class XoopsFormSelectContest extends XoopsFormSelect
{
    public function XoopsFormSelectContest($caption, $name, $value = null, $size = 1)
    {
        global $contestOption_handler;

        $_handler       = &wfcon_gethandler('contestant');
        $wfcon_cont_obj = $_handler->getClientsArray();
        /*
        *
        */
        $this->XoopsFormSelect($caption, $name, $value, $size);
        $this->addOption(0, '--------------');
        $this->addOptionArray($wfcon_cont_obj);
    }
}
