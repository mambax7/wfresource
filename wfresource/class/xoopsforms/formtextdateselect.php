<?php
// $Id: formtextdateselect.php 8181 2011-11-07 01:14:53Z beckmi $
// ------------------------------------------------------------------------ //
// Xoops - PHP Content Management System                      			//
// Copyright (c) 2007 Xoops                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.xoops.com 												//
// Project: Xoops Project                                               //
// -------------------------------------------------------------------------//
defined('XOOPS_ROOT_PATH') || exit('You do not have permission to access this file!');
/**
 *
 * @package    kernel
 * @subpackage form
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.xoops.com
 */

/**
 * A text field with calendar popup
 *
 * @package    kernel
 * @subpackage form
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.xoops.com
 */
class XoopsFormTextDateSelect extends XoopsFormCalendar
{
    /**
     * XoopsFormTextDateSelect::XoopsFormTextDateSelect()
     *
     * @param mixed   $caption
     * @param mixed   $name
     * @param integer $size
     * @param string  $value
     * @param mixed   $showtime
     */
    public function XoopsFormTextDateSelect($caption, $name, $size = 30, $value = '', $showtime = true)
    {
        $calendar_options['showsTime'] = $showtime;
        $field_attributes['size']      = $size;
        $value                         = ($value == 0) ? ($showtime == true) ? time() : '' : $value;
        if ($value != '' || $value > 0) {
            $field_attributes['value'] = (is_numeric($value)) ? strftime('%m/%d/%Y %H:%M', $value) : $value;
        } else {
            $field_attributes['value'] = '';
        }
        $this->XoopsFormCalendar($caption, $name, $value, $calendar_options, $field_attributes);
    }
}
