<?php namespace XoopsModules\Wfresource;

use Xmf\Request;
use XoopsModules\Wfresource;
use XoopsModules\Wfresource\Common;

/**
 * Class Utility
 */
class Utility
{
    use Common\VersionChecks; //checkVerXoops, checkVerPhp Traits

    use Common\ServerStats; // getServerStats Trait

    use Common\FilesManagement; // Files Management Trait

    //--------------- Custom module methods -----------------------------
}
