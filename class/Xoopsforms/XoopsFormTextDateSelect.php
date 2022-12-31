<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

// ------------------------------------------------------------------------ //
// Xoops - PHP Content Management System                                //
// Copyright (c) 2007 Xoops                                         //
// //
// Authors:                                                                 //
// John Neill ( AKA Catzwolf )                                              //
// Raimondas Rimkevicius ( AKA Mekdrop )                                    //
// //
// URL: http:www.xoops.com                                              //
// Project: Xoops Project                                               //
// -------------------------------------------------------------------------//

/**
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.xoops.com
 */

/**
 * A text field with calendar popup
 *
 * @author     Kazumi Ono
 * @copyright  copyright (c) 2007 Xoops Project - http.www.xoops.com
 */
class XoopsFormTextDateSelect extends XoopsFormCalendar
{
    /**
     * XoopsFormTextDateSelect::__construct()
     *
     * @param mixed      $caption
     * @param mixed      $name
     * @param int        $size
     * @param string|int $value
     * @param mixed      $showtime
     */
    public function __construct($caption, $name, $size = 30, $value = '', $showtime = true)
    {
        $calendar_options['showsTime'] = $showtime;
        $field_attributes['size']      = $size;
        $value                         = (0 == $value) ? ((true === $showtime) ? \time() : '') : $value;
        if ('' !== $value || $value > 0) {
            $field_attributes['value'] = \is_numeric($value) ? \strftime('%m/%d/%Y %H:%M', $value) : $value;
        } else {
            $field_attributes['value'] = '';
        }
        parent::__construct($caption, $name, $value, $calendar_options, $field_attributes);
    }
}
