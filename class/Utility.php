<?php namespace Xoopsmodules\wfresource;

use Xmf\Request;
use Xoopsmodules\wfresource;
use Xoopsmodules\wfresource\common;

/**
 * Class Utility
 */
class Utility
{
    use common\VersionChecks; //checkVerXoops, checkVerPhp Traits

    use common\ServerStats; // getServerStats Trait

    use common\FilesManagement; // Files Management Trait

    //--------------- Custom module methods -----------------------------

}
