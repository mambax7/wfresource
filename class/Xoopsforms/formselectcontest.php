<?php
/**
 * base class
 */
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formselect.php';

/**
 * Class XoopsFormSelectContest
 */
class XoopsFormSelectContest extends XoopsFormSelect
{
    /**
     * @param      $caption
     * @param      $name
     * @param null $value
     * @param int  $size
     */
    public function __construct($caption, $name, $value = null, $size = 1)
    {
        global $contestOptionHandler;

        $Handler        = &wfcon_gethandler('contestant');
        $wfcon_cont_obj = $Handler->getClientsArray();
        /*
        *
        */
        parent::__construct($caption, $name, $value, $size);
        $this->addOption(0, '--------------');
        $this->addOptionArray($wfcon_cont_obj);
    }
}
