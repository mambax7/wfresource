<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Pdf;

/**
 * Cpdf
 *
 * https://www.ros.co.nz/pdf
 *
 * A PHP class to provide the basic functionality to create a pdf document without
 * any requirement for additional addons.
 *
 * Note that they companion class CezPdf can be used to extend this class and dramatically
 * simplify the creation of documents.
 *
 * IMPORTANT NOTE
 * there is no warranty, implied or otherwise with this software.
 *
 * LICENCE
 * This code has been placed in the Public Domain for all to enjoy.
 *
 * @author        Wayne Munro <pdf@ros.co.nz>
 * @version       009
 */
class Cpdf
{
    /**
     * the current number of pdf objects in the document
     */
    public $numObj = 0;
    /**
     * this array contains all of the pdf objects, ready for final assembly
     */
    public $objects = [];
    /**
     * the objectId (number within the objects array) of the document catalog
     */
    public $catalogId;
    /**
     * array carrying information about the fonts that the system currently knows about
     * used to ensure that a font is not loaded twice, among other things
     */
    public $fonts = [];
    /**
     * a record of the current font
     */
    public $currentFont = '';
    /**
     * the current base font
     */
    public $currentBaseFont = '';
    /**
     * the number of the current font within the font array
     */
    public $currentFontNum = 0;
    public $currentNode;
    /**
     * object number of the current page
     */
    public $currentPage;
    /**
     * object number of the currently active contents block
     */
    public $currentContents;
    /**
     * number of fonts within the system
     */
    public $numFonts = 0;
    /**
     * current colour for fill operations, defaults to inactive value, all three components should be between 0 and 1 inclusive when active
     */
    public $currentColour = ['r' => -1, 'g' => -1, 'b' => -1];
    /**
     * current colour for stroke operations (lines etc.)
     */
    public $currentStrokeColour = ['r' => -1, 'g' => -1, 'b' => -1];
    /**
     * current style that lines are drawn in
     */
    public $currentLineStyle = '';
    /**
     * an array which is used to save the state of the document, mainly the colours and styles
     * it is used to temporarily change to another state, the change back to what it was before
     */
    public $stateStack = [];
    /**
     * number of elements within the state stack
     */
    public $nStateStack = 0;
    /**
     * number of page objects within the document
     */
    public $numPages = 0;
    /**
     * object Id storage stack
     */
    public $stack = [];
    /**
     * number of elements within the object Id storage stack
     */
    public $nStack = 0;
    /**
     * an array which contains information about the objects which are not firmly attached to pages
     * these have been added with the addObject function
     */
    public $looseObjects = [];
    /**
     * array contains infomation about how the loose objects are to be added to the document
     */
    public $addLooseObjects = [];
    /**
     * the objectId of the information object for the document
     * this contains authorship, title etc.
     */
    public $infoObject = 0;
    /**
     * number of images being tracked within the document
     */
    public $numImages = 0;
    /**
     * an array containing options about the document
     * it defaults to turning on the compression of the objects
     */
    public $options = ['compression' => 1];
    /**
     * the objectId of the first page of the document
     */
    public $firstPageId;
    /**
     * used to track the last used value of the inter-word spacing, this is so that it is known
     * when the spacing is changed.
     */
    public $wordSpaceAdjust = 0;
    /**
     * the object Id of the procset object
     */
    public $procsetObjectId;
    /**
     * store the information about the relationship between font families
     * this used so that the code knows which font is the bold version of another font, etc.
     * the value of this array is initialised in the constuctor function.
     */
    public $fontFamilies = [];
    /**
     * track if the current font is bolded or italicised
     */
    public $currentTextState = '';
    /**
     * messages are stored here during processing, these can be selected afterwards to give some useful debug information
     */
    public $messages = '';
    /**
     * the ancryption array for the document encryption is stored here
     */
    public $arc4 = '';
    /**
     * the object Id of the encryption information
     */
    public $arc4_objnum = 0;
    /**
     * the file identifier, used to uniquely identify a pdf document
     */
    public $fileIdentifier = '';
    /**
     * a flag to say if a document is to be encrypted or not
     */
    public $encrypted = 0;
    /**
     * the ancryption key for the encryption of all the document content (structure is not encrypted)
     */
    public $encryptionKey = '';
    /**
     * array which forms a stack to keep track of nested callback functions
     */
    public $callback = [];
    /**
     * the number of callback functions in the callback array
     */
    public $nCallback = 0;
    /**
     * store label->id pairs for named destinations, these will be used to replace internal links
     * done this way so that destinations can be defined after the location that links to them
     */
    public $destinations = [];
    /**
     * store the stack for the transaction commands, each item in here is a record of the values of all the
     * variables within the class, so that the user can rollback at will (from each 'start' command)
     * note that this includes the objects array, so these can be large.
     */
    public $checkpoint = '';

    /**
     * class constructor
     * this will start a new document
     *
     * @param mixed $pageSize
     */
    public function __construct($pageSize = [0, 0, 612, 792])
    {
        $this->newDocument($pageSize);

        // also initialize the font families that are known about already
        $this->setFontFamily('init');
        //  $this->fileIdentifier = md5('wfresourcexxx'.time());
    }

    /**
     * Document object methods (internal use only)
     *
     * There is about one object method for each type of object in the pdf document
     * Each function has the same call list ($id,$action,$options).
     * $id = the object ID of the object, or what it is to be if it is being created
     * $action = a string specifying the action to be performed, though ALL must support:
     *           'new' - create the object with the id $id
     *           'out' - produce the output for the pdf object
     * $options = optional, a string or array containing the various parameters for the object
     *
     * These, in conjunction with the output function are the ONLY way for output to be produced
     * within the pdf 'file'.
     * @param mixed $id
     * @param mixed $action
     * @param mixed $options
     */

    /**
     * destination object, used to specify the location for the user to jump to, presently on opening
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oDestination($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                $this->objects[$id] = ['t' => 'destination', 'info' => []];
                $tmp                = '';
                switch ($options['type']) {
                    case 'XYZ':
                    case 'FitR':
                        $tmp = ' ' . $options['p3'] . $tmp;
                        break;
                    case 'FitH':
                    case 'FitV':
                    case 'FitBH':
                    case 'FitBV':
                        $tmp = ' ' . $options['p1'] . ' ' . $options['p2'] . $tmp;
                        break;
                    case 'Fit':
                    case 'FitB':
                        $tmp                                  = $options['type'] . $tmp;
                        $this->objects[$id]['info']['string'] = $tmp;
                        $this->objects[$id]['info']['page']   = $options['page'];
                }
                break;
            case 'out':
                $tmp = $o['info'];
                $res = "\n" . $id . " 0 obj\n" . '[' . $tmp['page'] . ' 0 R /' . $tmp['string'] . "]\nendobj\n";

                return $res;
                break;
        }
    }

    /**
     * set the viewer preferences
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oViewerPreferences($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                $this->objects[$id] = ['t' => 'viewerPreferences', 'info' => []];
                break;
            case 'add':
                foreach ($options as $k => $v) {
                    switch ($k) {
                        case 'HideToolbar':
                        case 'HideMenubar':
                        case 'HideWindowUI':
                        case 'FitWindow':
                        case 'CenterWindow':
                        case 'NonFullScreenPageMode':
                        case 'Direction':
                            $o['info'][$k] = $v;
                            break;
                    }
                }
                break;
            case 'out':
                $res = "\n" . $id . " 0 obj\n" . '<< ';
                foreach ($o['info'] as $k => $v) {
                    $res .= "\n/" . $k . ' ' . $v;
                }
                $res .= "\n>>\n";

                return $res;
                break;
        }
    }

    /**
     * define the document catalog, the overall controller for the document
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oCatalog($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                $this->objects[$id] = ['t' => 'catalog', 'info' => []];
                $this->catalogId    = $id;
                break;
            case 'outlines':
            case 'pages':
            case 'openHere':
                $o['info'][$action] = $options;
                break;
            case 'viewerPreferences':
                if (!isset($o['info']['viewerPreferences'])) {
                    $this->numObj++;
                    $this->oViewerPreferences($this->numObj, 'new');
                    $o['info']['viewerPreferences'] = $this->numObj;
                }
                $vp = $o['info']['viewerPreferences'];
                $this->oViewerPreferences($vp, 'add', $options);
                break;
            case 'out':
                $res = "\n" . $id . " 0 obj\n" . '<< /Type /Catalog';
                foreach ($o['info'] as $k => $v) {
                    switch ($k) {
                        case 'outlines':
                            $res .= "\n" . '/Outlines ' . $v . ' 0 R';
                            break;
                        case 'pages':
                            $res .= "\n" . '/Pages ' . $v . ' 0 R';
                            break;
                        case 'viewerPreferences':
                            $res .= "\n" . '/ViewerPreferences ' . $o['info']['viewerPreferences'] . ' 0 R';
                            break;
                        case 'openHere':
                            $res .= "\n" . '/OpenAction ' . $o['info']['openHere'] . ' 0 R';
                            break;
                    }
                }
                $res .= " >>\nendobj";

                return $res;
                break;
        }
    }

    /**
     * object which is a parent to the pages in the document
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oPages($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                $this->objects[$id] = ['t' => 'pages', 'info' => []];
                $this->oCatalog($this->catalogId, 'pages', $id);
                break;
            case 'page':
                if (!\is_array($options)) {
                    // then it will just be the id of the new page
                    $o['info']['pages'][] = $options;
                } else {
                    // then it should be an array having 'id','rid','pos', where rid=the page to which this one will be placed relative
                    // and pos is either 'before' or 'after', saying where this page will fit.
                    if (isset($options['id']) && isset($options['rid']) && isset($options['pos'])) {
                        $i = \array_search($options['rid'], $o['info']['pages'], true);
                        if (isset($o['info']['pages'][$i]) && $o['info']['pages'][$i] == $options['rid']) {
                            // then there is a match
                            // make a space
                            switch ($options['pos']) {
                                case 'before':
                                    $k = $i;
                                    break;
                                case 'after':
                                    $k = $i + 1;
                                    break;
                                default:
                                    $k = -1;
                                    break;
                            }
                            if ($k >= 0) {
                                for ($j = \count($o['info']['pages']) - 1; $j >= $k; $j--) {
                                    $o['info']['pages'][$j + 1] = $o['info']['pages'][$j];
                                }
                                $o['info']['pages'][$k] = $options['id'];
                            }
                        }
                    }
                }
                break;
            case 'procset':
                $o['info']['procset'] = $options;
                break;
            case 'mediaBox':
                $o['info']['mediaBox'] = $options; // which should be an array of 4 numbers
                break;
            case 'font':
                $o['info']['fonts'][] = ['objNum' => $options['objNum'], 'fontNum' => $options['fontNum']];
                break;
            case 'xObject':
                $o['info']['xObjects'][] = ['objNum' => $options['objNum'], 'label' => $options['label']];
                break;
            case 'out':
                if (\count($o['info']['pages'])) {
                    $res = "\n" . $id . " 0 obj\n<< /Type /Pages\n/Kids [";
                    foreach ($o['info']['pages'] as $k => $v) {
                        $res .= $v . " 0 R\n";
                    }
                    $res .= "]\n/Count " . \count($this->objects[$id]['info']['pages']);
                    if (isset($o['info']['procset']) || (isset($o['info']['fonts']) && \count($o['info']['fonts']))) {
                        $res .= "\n/Resources <<";
                        if (isset($o['info']['procset'])) {
                            $res .= "\n/ProcSet " . $o['info']['procset'] . ' 0 R';
                        }
                        if (isset($o['info']['fonts']) && \count($o['info']['fonts'])) {
                            $res .= "\n/Font << ";
                            foreach ($o['info']['fonts'] as $finfo) {
                                $res .= "\n/F" . $finfo['fontNum'] . ' ' . $finfo['objNum'] . ' 0 R';
                            }
                            $res .= ' >>';
                        }
                        if (isset($o['info']['xObjects']) && \count($o['info']['xObjects'])) {
                            $res .= "\n/XObject << ";
                            foreach ($o['info']['xObjects'] as $finfo) {
                                $res .= "\n/" . $finfo['label'] . ' ' . $finfo['objNum'] . ' 0 R';
                            }
                            $res .= ' >>';
                        }
                        $res .= "\n>>";
                        if (isset($o['info']['mediaBox'])) {
                            $tmp = $o['info']['mediaBox'];
                            $res .= "\n/MediaBox [" . \sprintf('%.3f', $tmp[0]) . ' ' . \sprintf('%.3f', $tmp[1]) . ' ' . \sprintf('%.3f', $tmp[2]) . ' ' . \sprintf('%.3f', $tmp[3]) . ']';
                        }
                    }
                    $res .= "\n >>\nendobj";
                } else {
                    $res = "\n" . $id . " 0 obj\n<< /Type /Pages\n/Count 0\n>>\nendobj";
                }

                return $res;
                break;
        }
    }

    /**
     * define the outlines in the doc, empty for now
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oOutlines($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                $this->objects[$id] = ['t' => 'outlines', 'info' => ['outlines' => []]];
                $this->oCatalog($this->catalogId, 'outlines', $id);
                break;
            case 'outline':
                $o['info']['outlines'][] = $options;
                break;
            case 'out':
                if (\count($o['info']['outlines'])) {
                    $res = "\n" . $id . " 0 obj\n<< /Type /Outlines /Kids [";
                    foreach ($o['info']['outlines'] as $k => $v) {
                        $res .= $v . ' 0 R ';
                    }
                    $res .= '] /Count ' . \count($o['info']['outlines']) . " >>\nendobj";
                } else {
                    $res = "\n" . $id . " 0 obj\n<< /Type /Outlines /Count 0 >>\nendobj";
                }

                return $res;
                break;
        }
    }

    /**
     * an object to hold the font description
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oFont($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                $this->objects[$id]                    = [
                    't'    => 'font',
                    'info' => ['name' => $options['name'], 'SubType' => 'Type1'],
                ];
                $fontNum                               = $this->numFonts;
                $this->objects[$id]['info']['fontNum'] = $fontNum;
                // deal with the encoding and the differences
                if (isset($options['differences'])) {
                    // then we'll need an encoding dictionary
                    $this->numObj++;
                    $this->oFontEncoding($this->numObj, 'new', $options);
                    $this->objects[$id]['info']['encodingDictionary'] = $this->numObj;
                } elseif (isset($options['encoding'])) {
                    // we can specify encoding here
                    switch ($options['encoding']) {
                        case 'WinAnsiEncoding':
                        case 'MacRomanEncoding':
                        case 'MacExpertEncoding':
                            $this->objects[$id]['info']['encoding'] = $options['encoding'];
                            break;
                        case 'none':
                            break;
                        default:
                            $this->objects[$id]['info']['encoding'] = 'WinAnsiEncoding';
                            break;
                    }
                } else {
                    $this->objects[$id]['info']['encoding'] = 'WinAnsiEncoding';
                }
                // also tell the pages node about the new font
                $this->oPages($this->currentNode, 'font', ['fontNum' => $fontNum, 'objNum' => $id]);
                break;
            case 'add':
                foreach ($options as $k => $v) {
                    switch ($k) {
                        case 'BaseFont':
                            $o['info']['name'] = $v;
                            break;
                        case 'FirstChar':
                        case 'LastChar':
                        case 'Widths':
                        case 'FontDescriptor':
                        case 'SubType':
                            $this->addMessage('oFont ' . $k . ' : ' . $v);
                            $o['info'][$k] = $v;
                            break;
                    }
                }
                break;
            case 'out':
                $res = "\n" . $id . " 0 obj\n<< /Type /Font\n/Subtype /" . $o['info']['SubType'] . "\n";
                $res .= '/Name /F' . $o['info']['fontNum'] . "\n";
                $res .= '/BaseFont /' . $o['info']['name'] . "\n";
                if (isset($o['info']['encodingDictionary'])) {
                    // then place a reference to the dictionary
                    $res .= '/Encoding ' . $o['info']['encodingDictionary'] . " 0 R\n";
                } elseif (isset($o['info']['encoding'])) {
                    // use the specified encoding
                    $res .= '/Encoding /' . $o['info']['encoding'] . "\n";
                }
                if (isset($o['info']['FirstChar'])) {
                    $res .= '/FirstChar ' . $o['info']['FirstChar'] . "\n";
                }
                if (isset($o['info']['LastChar'])) {
                    $res .= '/LastChar ' . $o['info']['LastChar'] . "\n";
                }
                if (isset($o['info']['Widths'])) {
                    $res .= '/Widths ' . $o['info']['Widths'] . " 0 R\n";
                }
                if (isset($o['info']['FontDescriptor'])) {
                    $res .= '/FontDescriptor ' . $o['info']['FontDescriptor'] . " 0 R\n";
                }
                $res .= ">>\nendobj";

                return $res;
                break;
        }
    }

    /**
     * a font descriptor, needed for including additional fonts
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oFontDescriptor($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                $this->objects[$id] = ['t' => 'fontDescriptor', 'info' => $options];
                break;
            case 'out':
                $res = "\n" . $id . " 0 obj\n<< /Type /FontDescriptor\n";
                foreach ($o['info'] as $label => $value) {
                    switch ($label) {
                        case 'Ascent':
                        case 'CapHeight':
                        case 'Descent':
                        case 'Flags':
                        case 'ItalicAngle':
                        case 'StemV':
                        case 'AvgWidth':
                        case 'Leading':
                        case 'MaxWidth':
                        case 'MissingWidth':
                        case 'StemH':
                        case 'XHeight':
                        case 'CharSet':
                            if (mb_strlen($value)) {
                                $res .= '/' . $label . ' ' . $value . "\n";
                            }
                            break;
                        case 'FontFile':
                        case 'FontFile2':
                        case 'FontFile3':
                            $res .= '/' . $label . ' ' . $value . " 0 R\n";
                            break;
                        case 'FontBBox':
                            $res .= '/' . $label . ' [' . $value[0] . ' ' . $value[1] . ' ' . $value[2] . ' ' . $value[3] . "]\n";
                            break;
                        case 'FontName':
                            $res .= '/' . $label . ' /' . $value . "\n";
                            break;
                    }
                }
                $res .= ">>\nendobj";

                return $res;
                break;
        }
    }

    /**
     * the font encoding
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oFontEncoding($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                // the options array should contain 'differences' and maybe 'encoding'
                $this->objects[$id] = ['t' => 'fontEncoding', 'info' => $options];
                break;
            case 'out':
                $res = "\n" . $id . " 0 obj\n<< /Type /Encoding\n";
                if (!isset($o['info']['encoding'])) {
                    $o['info']['encoding'] = 'WinAnsiEncoding';
                }
                if ('none' !== $o['info']['encoding']) {
                    $res .= '/BaseEncoding /' . $o['info']['encoding'] . "\n";
                }
                $res  .= "/Differences \n[";
                $onum = -100;
                foreach ($o['info']['differences'] as $num => $label) {
                    if ($num !== $onum + 1) {
                        // we cannot make use of consecutive numbering
                        $res .= "\n" . $num . ' /' . $label;
                    } else {
                        $res .= ' /' . $label;
                    }
                    $onum = $num;
                }
                $res .= "\n]\n>>\nendobj";

                return $res;
                break;
        }
    }

    /**
     * the document procset, solves some problems with printing to old PS printers
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oProcset($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                $this->objects[$id] = ['t' => 'procset', 'info' => ['PDF' => 1, 'Text' => 1]];
                $this->oPages($this->currentNode, 'procset', $id);
                $this->procsetObjectId = $id;
                break;
            case 'add':
                // this is to add new items to the procset list, despite the fact that this is considered
                // obselete, the items are required for printing to some postscript printers
                switch ($options) {
                    case 'ImageB':
                    case 'ImageC':
                    case 'ImageI':
                        $o['info'][$options] = 1;
                        break;
                }
                break;
            case 'out':
                $res = "\n" . $id . " 0 obj\n[";
                foreach ($o['info'] as $label => $val) {
                    $res .= '/' . $label . ' ';
                }
                $res .= "]\nendobj";

                return $res;
                break;
        }
    }

    /**
     * define the document information
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oInfo($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                $this->infoObject   = $id;
                $date               = 'D:' . \date('Ymd');
                $this->objects[$id] = [
                    't'    => 'info',
                    'info' => [
                        'Creator'      => 'R and OS php pdf writer, https://www.ros.co.nz',
                        'CreationDate' => $date,
                    ],
                ];
                break;
            case 'Title':
            case 'Author':
            case 'Subject':
            case 'Keywords':
            case 'Creator':
            case 'Producer':
            case 'CreationDate':
            case 'ModDate':
            case 'Trapped':
                $o['info'][$action] = $options;
                break;
            case 'out':
                if ($this->encrypted) {
                    $this->encryptInit($id);
                }
                $res = "\n" . $id . " 0 obj\n<<\n";
                foreach ($o['info'] as $k => $v) {
                    $res .= '/' . $k . ' (';
                    if ($this->encrypted) {
                        $res .= $this->filterText($this->ARC4($v));
                    } else {
                        $res .= $this->filterText($v);
                    }
                    $res .= ")\n";
                }
                $res .= ">>\nendobj";

                return $res;
                break;
        }
    }

    /**
     * an action object, used to link to URLS initially
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oAction($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                if (\is_array($options)) {
                    $this->objects[$id] = ['t' => 'action', 'info' => $options, 'type' => $options['type']];
                } else {
                    // then assume a URI action
                    $this->objects[$id] = ['t' => 'action', 'info' => $options, 'type' => 'URI'];
                }
                break;
            case 'out':
                if ($this->encrypted) {
                    $this->encryptInit($id);
                }
                $res = "\n" . $id . " 0 obj\n<< /Type /Action";
                switch ($o['type']) {
                    case 'ilink':
                        // there will be an 'label' setting, this is the name of the destination
                        $res .= "\n/S /GoTo\n/D " . $this->destinations[(string)$o['info']['label']] . ' 0 R';
                        break;
                    case 'URI':
                        $res .= "\n/S /URI\n/URI (";
                        if ($this->encrypted) {
                            $res .= $this->filterText($this->ARC4($o['info']));
                        } else {
                            $res .= $this->filterText($o['info']);
                        }
                        $res .= ')';
                        break;
                }
                $res .= "\n>>\nendobj";

                return $res;
                break;
        }
    }

    /**
     * an annotation object, this will add an annotation to the current page.
     * initially will support just link annotations
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oAnnotation($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                // add the annotation to the current page
                $pageId = $this->currentPage;
                $this->oPage($pageId, 'annot', $id);
                // and add the action object which is going to be required
                switch ($options['type']) {
                    case 'link':
                        $this->objects[$id] = ['t' => 'annotation', 'info' => $options];
                        $this->numObj++;
                        $this->oAction($this->numObj, 'new', $options['url']);
                        $this->objects[$id]['info']['actionId'] = $this->numObj;
                        break;
                    case 'ilink':
                        // this is to a named internal link
                        $label              = $options['label'];
                        $this->objects[$id] = ['t' => 'annotation', 'info' => $options];
                        $this->numObj++;
                        $this->oAction($this->numObj, 'new', ['type' => 'ilink', 'label' => $label]);
                        $this->objects[$id]['info']['actionId'] = $this->numObj;
                        break;
                }
                break;
            case 'out':
                $res = "\n" . $id . " 0 obj\n<< /Type /Annot";
                switch ($o['info']['type']) {
                    case 'link':
                    case 'ilink':
                        $res .= "\n/Subtype /Link";
                        break;
                }
                $res .= "\n/A " . $o['info']['actionId'] . ' 0 R';
                $res .= "\n/Border [0 0 0]";
                $res .= "\n/H /I";
                $res .= "\n/Rect [ ";
                foreach ($o['info']['rect'] as $v) {
                    $res .= \sprintf('%.4f ', $v);
                }
                $res .= ']';
                $res .= "\n>>\nendobj";

                return $res;
                break;
        }
    }

    /**
     * a page object, it also creates a contents object to hold its contents
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oPage($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                $this->numPages++;
                $this->objects[$id] = [
                    't'    => 'page',
                    'info' => [
                        'parent'  => $this->currentNode,
                        'pageNum' => $this->numPages,
                    ],
                ];
                if (\is_array($options)) {
                    // then this must be a page insertion, array shoudl contain 'rid','pos'=[before|after]
                    $options['id'] = $id;
                    $this->oPages($this->currentNode, 'page', $options);
                } else {
                    $this->oPages($this->currentNode, 'page', $id);
                }
                $this->currentPage = $id;
                //make a contents object to go with this page
                $this->numObj++;
                $this->oContents($this->numObj, 'new', $id);
                $this->currentContents                    = $this->numObj;
                $this->objects[$id]['info']['contents']   = [];
                $this->objects[$id]['info']['contents'][] = $this->numObj;
                $match                                    = (($this->numPages % 2) ? 'odd' : 'even');
                foreach ($this->addLooseObjects as $oId => $target) {
                    if ('all' === $target || $match === $target) {
                        $this->objects[$id]['info']['contents'][] = $oId;
                    }
                }
                break;
            case 'content':
                $o['info']['contents'][] = $options;
                break;
            case 'annot':
                // add an annotation to this page
                if (!isset($o['info']['annot'])) {
                    $o['info']['annot'] = [];
                }
                // $options should contain the id of the annotation dictionary
                $o['info']['annot'][] = $options;
                break;
            case 'out':
                $res = "\n" . $id . " 0 obj\n<< /Type /Page";
                $res .= "\n/Parent " . $o['info']['parent'] . ' 0 R';
                if (isset($o['info']['annot'])) {
                    $res .= "\n/Annots [";
                    foreach ($o['info']['annot'] as $aId) {
                        $res .= ' ' . $aId . ' 0 R';
                    }
                    $res .= ' ]';
                }
                $count = \count($o['info']['contents']);
                if (1 === $count) {
                    $res .= "\n/Contents " . $o['info']['contents'][0] . ' 0 R';
                } elseif ($count > 1) {
                    $res .= "\n/Contents [\n";
                    foreach ($o['info']['contents'] as $cId) {
                        $res .= $cId . " 0 R\n";
                    }
                    $res .= ']';
                }
                $res .= "\n>>\nendobj";

                return $res;
                break;
        }
    }

    /**
     * the contents objects hold all of the content which appears on pages
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oContents($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                $this->objects[$id] = ['t' => 'contents', 'c' => '', 'info' => []];
                if ((int)$options && mb_strlen($options)) {
                    // then this contents is the primary for a page
                    $this->objects[$id]['onPage'] = $options;
                } elseif ('raw' === $options) {
                    // then this page contains some other type of system object
                    $this->objects[$id]['raw'] = 1;
                }
                break;
            case 'add':
                // add more options to the decleration
                foreach ($options as $k => $v) {
                    $o['info'][$k] = $v;
                }
                break;
            case 'out':
                $tmp = $o['c'];
                $res = "\n" . $id . " 0 obj\n";
                if (isset($this->objects[$id]['raw'])) {
                    $res .= $tmp;
                } else {
                    $res .= '<<';
                    if ($this->options['compression'] && \function_exists('gzcompress')) {
                        // then implement ZLIB based compression on this content stream
                        $res .= ' /Filter /FlateDecode';
                        $tmp = \gzcompress($tmp);
                    }
                    if ($this->encrypted) {
                        $this->encryptInit($id);
                        $tmp = $this->ARC4($tmp);
                    }
                    foreach ($o['info'] as $k => $v) {
                        $res .= "\n/" . $k . ' ' . $v;
                    }
                    $res .= "\n/Length " . mb_strlen($tmp) . " >>\nstream\n" . $tmp . "\nendstream";
                }
                $res .= "\nendobj\n";

                return $res;
                break;
        }
    }

    /**
     * an image object, will be an XObject in the document, includes description and data
     * @param         $id
     * @param         $action
     * @param string  $options
     * @return string
     */
    public function oImage($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                // make the new object
                $this->objects[$id]                    = [
                    't'    => 'image',
                    'data' => $options['data'],
                    'info' => [],
                ];
                $this->objects[$id]['info']['Type']    = '/XObject';
                $this->objects[$id]['info']['Subtype'] = '/Image';
                $this->objects[$id]['info']['Width']   = $options['iw'];
                $this->objects[$id]['info']['Height']  = $options['ih'];
                if (!isset($options['type']) || 'jpg' === $options['type']) {
                    if (!isset($options['channels'])) {
                        $options['channels'] = 3;
                    }
                    switch ($options['channels']) {
                        case 1:
                            $this->objects[$id]['info']['ColorSpace'] = '/DeviceGray';
                            break;
                        default:
                            $this->objects[$id]['info']['ColorSpace'] = '/DeviceRGB';
                            break;
                    }
                    $this->objects[$id]['info']['Filter']           = '/DCTDecode';
                    $this->objects[$id]['info']['BitsPerComponent'] = 8;
                } elseif ('png' === $options['type']) {
                    $this->objects[$id]['info']['Filter']      = '/FlateDecode';
                    $this->objects[$id]['info']['DecodeParms'] = '<< /Predictor 15 /Colors ' . $options['ncolor'] . ' /Columns ' . $options['iw'] . ' /BitsPerComponent ' . $options['bitsPerComponent'] . '>>';
                    if (mb_strlen($options['pdata'])) {
                        $tmp = ' [ /Indexed /DeviceRGB ' . (mb_strlen($options['pdata']) / 3 - 1) . ' ';
                        $this->numObj++;
                        $this->oContents($this->numObj, 'new');
                        $this->objects[$this->numObj]['c']        = $options['pdata'];
                        $tmp                                      .= $this->numObj . ' 0 R';
                        $tmp                                      .= ' ]';
                        $this->objects[$id]['info']['ColorSpace'] = $tmp;
                        if (isset($options['transparency'])) {
                            switch ($options['transparency']['type']) {
                                case 'indexed':
                                    $tmp                                = ' [ ' . $options['transparency']['data'] . ' ' . $options['transparency']['data'] . '] ';
                                    $this->objects[$id]['info']['Mask'] = $tmp;
                                    break;
                            }
                        }
                    } else {
                        $this->objects[$id]['info']['ColorSpace'] = '/' . $options['color'];
                    }
                    $this->objects[$id]['info']['BitsPerComponent'] = $options['bitsPerComponent'];
                }
                // assign it a place in the named resource dictionary as an external object, according to
                // the label passed in with it.
                $this->oPages($this->currentNode, 'xObject', ['label' => $options['label'], 'objNum' => $id]);
                // also make sure that we have the right procset object for it.
                $this->oProcset($this->procsetObjectId, 'add', 'ImageC');
                break;
            case 'out':
                $tmp = $o['data'];
                $res = "\n" . $id . " 0 obj\n<<";
                foreach ($o['info'] as $k => $v) {
                    $res .= "\n/" . $k . ' ' . $v;
                }
                if ($this->encrypted) {
                    $this->encryptInit($id);
                    $tmp = $this->ARC4($tmp);
                }
                $res .= "\n/Length " . mb_strlen($tmp) . " >>\nstream\n" . $tmp . "\nendstream\nendobj\n";

                return $res;
                break;
        }
    }

    /**
     * encryption object.
     * @param         $id
     * @param         $action
     * @param string|array  $options
     * @return string
     */
    public function oEncryption($id, $action, $options = ''): ?string
    {
        if ('new' !== $action) {
            $o = &$this->objects[$id];
        }
        switch ($action) {
            case 'new':
                // make the new object
                $this->objects[$id] = ['t' => 'encryption', 'info' => $options];
                $this->arc4_objnum  = $id;
                // figure out the additional paramaters required
                $pad = \chr(0x28)
                       . \chr(0xBF)
                       . \chr(0x4E)
                       . \chr(0x5E)
                       . \chr(0x4E)
                       . \chr(0x75)
                       . \chr(0x8A)
                       . \chr(0x41)
                       . \chr(0x64)
                       . \chr(0x00)
                       . \chr(0x4E)
                       . \chr(0x56)
                       . \chr(0xFF)
                       . \chr(0xFA)
                       . \chr(0x01)
                       . \chr(0x08)
                       . \chr(0x2E)
                       . \chr(0x2E)
                       . \chr(0x00)
                       . \chr(0xB6)
                       . \chr(0xD0)
                       . \chr(0x68)
                       . \chr(0x3E)
                       . \chr(0x80)
                       . \chr(0x2F)
                       . \chr(0x0C)
                       . \chr(0xA9)
                       . \chr(0xFE)
                       . \chr(0x64)
                       . \chr(0x53)
                       . \chr(0x69)
                       . \chr(0x7A);
                $len = mb_strlen($options['owner']);
                if ($len > 32) {
                    $owner = mb_substr($options['owner'], 0, 32);
                } elseif ($len < 32) {
                    $owner = $options['owner'] . mb_substr($pad, 0, 32 - $len);
                } else {
                    $owner = $options['owner'];
                }
                $len = mb_strlen($options['user']);
                if ($len > 32) {
                    $user = mb_substr($options['user'], 0, 32);
                } elseif ($len < 32) {
                    $user = $options['user'] . mb_substr($pad, 0, 32 - $len);
                } else {
                    $user = $options['user'];
                }
                $tmp  = $this->md5_16($owner);
                $okey = mb_substr($tmp, 0, 5);
                $this->ARC4_init($okey);
                $ovalue                          = $this->ARC4($user);
                $this->objects[$id]['info']['O'] = $ovalue;
                // now make the u value, phew.
                $tmp  = $this->md5_16($user . $ovalue . \chr((int)$options['p']) . \chr(255) . \chr(255) . \chr(255) . $this->fileIdentifier);
                $ukey = mb_substr($tmp, 0, 5);

                $this->ARC4_init($ukey);
                $this->encryptionKey = $ukey;
                $this->encrypted     = 1;
                $uvalue              = $this->ARC4($pad);

                $this->objects[$id]['info']['U'] = $uvalue;
                $this->encryptionKey             = $ukey;

                // initialize the arc4 array
                break;
            case 'out':
                $res = "\n" . $id . " 0 obj\n<<";
                $res .= "\n/Filter /Standard";
                $res .= "\n/V 1";
                $res .= "\n/R 2";
                $res .= "\n/O (" . $this->filterText($o['info']['O']) . ')';
                $res .= "\n/U (" . $this->filterText($o['info']['U']) . ')';
                // and the p-value needs to be converted to account for the twos-complement approach
                $o['info']['p'] = (($o['info']['p'] ^ 255) + 1) * -1;
                $res            .= "\n/P " . $o['info']['p'];
                $res            .= "\n>>\nendobj\n";

                return $res;
                break;
        }
    }

    /**
     * ARC4 functions
     * A series of function to implement ARC4 encoding in PHP
     * @param mixed $string
     */

    /**
     * calculate the 16 byte version of the 128 bit md5 digest of the string
     * @param string $string
     */
    public function md5_16($string): string
    {
        $tmp = \md5($string);
        $out = '';
        for ($i = 0; $i <= 30; $i += 2) {
            $out .= \chr(\intval(mb_substr($tmp, $i, 2), 16));
        }

        return $out;
    }

    /**
     * initialize the encryption for processing a particular object
     * @param $id
     */
    public function encryptInit($id): void
    {
        $tmp = $this->encryptionKey;
        $hex = \dechex($id);
        if (mb_strlen($hex) < 6) {
            $hex = mb_substr('000000', 0, 6 - mb_strlen($hex)) . $hex;
        }
        $tmp .= \chr(\intval(mb_substr($hex, 4, 2), 16)) . \chr(\intval(mb_substr($hex, 2, 2), 16)) . \chr(\intval(mb_substr($hex, 0, 2), 16)) . \chr(0) . \chr(0);
        $key = $this->md5_16($tmp);
        $this->ARC4_init(mb_substr($key, 0, 10));
    }

    /**
     * initialize the ARC4 encryption
     * @param string $key
     */
    public function ARC4_init($key = ''): void
    {
        $this->arc4 = '';
        // setup the control array
        if (0 === mb_strlen($key)) {
            return;
        }
        $k = '';
        while (mb_strlen($k) < 256) {
            $k .= $key;
        }
        $k = mb_substr($k, 0, 256);
        for ($i = 0; $i < 256; ++$i) {
            $this->arc4 .= \chr($i);
        }
        $j = 0;
        for ($i = 0; $i < 256; ++$i) {
            $t              = $this->arc4[$i];
            $j              = ($j + \ord($t) + \ord($k[$i])) % 256;
            $this->arc4[$i] = $this->arc4[$j];
            $this->arc4[$j] = $t;
        }
    }

    /**
     * ARC4 encrypt a text string
     * @param $text
     */
    public function ARC4($text): string
    {
        $len = mb_strlen($text);
        $a   = 0;
        $b   = 0;
        $c   = $this->arc4;
        $out = '';
        for ($i = 0; $i < $len; ++$i) {
            $a     = ($a + 1) % 256;
            $t     = $c[$a];
            $b     = ($b + \ord($t)) % 256;
            $c[$a] = $c[$b];
            $c[$b] = $t;
            $k     = \ord($c[(\ord($c[$a]) + \ord($c[$b])) % 256]);
            $out   .= \chr(\ord($text[$i]) ^ $k);
        }

        return $out;
    }

    /**
     * functions which can be called to adjust or add to the document
     * @param mixed $url
     * @param mixed $x0
     * @param mixed $y0
     * @param mixed $x1
     * @param mixed $y1
     */

    /**
     * add a link in the document to an external URL
     * @param $url
     * @param $x0
     * @param $y0
     * @param $x1
     * @param $y1
     */
    public function addLink($url, $x0, $y0, $x1, $y1): void
    {
        $this->numObj++;
        $info = ['type' => 'link', 'url' => $url, 'rect' => [$x0, $y0, $x1, $y1]];
        $this->oAnnotation($this->numObj, 'new', $info);
    }

    /**
     * add a link in the document to an internal destination (ie. within the document)
     * @param $label
     * @param $x0
     * @param $y0
     * @param $x1
     * @param $y1
     */
    public function addInternalLink($label, $x0, $y0, $x1, $y1): void
    {
        $this->numObj++;
        $info = ['type' => 'ilink', 'label' => $label, 'rect' => [$x0, $y0, $x1, $y1]];
        $this->oAnnotation($this->numObj, 'new', $info);
    }

    /**
     * set the encryption of the document
     * can be used to turn it on and/or set the passwords which it will have.
     * also the functions that the user will have are set here, such as print, modify, add
     * @param string $userPass
     * @param string $ownerPass
     */
    public function setEncryption($userPass = '', $ownerPass = '', array $pc = null): void
    {
        $p = \bindec('11000000');

        $options = [
            'print'  => 4,
            'modify' => 8,
            'copy'   => 16,
            'add'    => 32,
        ];
        foreach ($pc as $k => $v) {
            if ($v && isset($options[$k])) {
                $p += $options[$k];
            } elseif (isset($options[$v])) {
                $p += $options[$v];
            }
        }
        // implement encryption on the document
        if (0 === $this->arc4_objnum) {
            // then the block does not exist already, add it.
            $this->numObj++;
            if (0 === mb_strlen($ownerPass)) {
                $ownerPass = $userPass;
            }
            $this->oEncryption($this->numObj, 'new', ['user' => $userPass, 'owner' => $ownerPass, 'p' => $p]);
        }
    }

    /**
     * should be used for internal checks, not implemented as yet
     */
    public function checkAllHere(): void
    {
    }

    /**
     * return the pdf stream as a string returned from the function
     * @param int $debug
     */
    public function output($debug = 0): string
    {
        if ($debug) {
            // turn compression off
            $this->options['compression'] = 0;
        }

        if ($this->arc4_objnum) {
            $this->ARC4_init($this->encryptionKey);
        }

        $this->checkAllHere();

        $xref    = [];
        $content = "%PDF-1.3\n%âãÏÓ\n";
        //  $content="%PDF-1.3\n";
        $pos = mb_strlen($content);
        foreach ($this->objects as $k => $v) {
            $tmp     = 'o_' . $v['t'];
            $cont    = $this->$tmp($k, 'out');
            $content .= $cont;
            $xref[]  = $pos;
            $pos     += mb_strlen($cont);
        }
        $content .= "\nxref\n0 " . (\count($xref) + 1) . "\n0000000000 65535 f \n";
        foreach ($xref as $p) {
            $content .= mb_substr('0000000000', 0, 10 - mb_strlen($p)) . $p . " 00000 n \n";
        }
        $content .= "\ntrailer\n  << /Size " . (\count($xref) + 1) . "\n     /Root 1 0 R\n     /Info " . $this->infoObject . " 0 R\n";
        // if encryption has been applied to this document then add the marker for this dictionary
        if ($this->arc4_objnum > 0) {
            $content .= '/Encrypt ' . $this->arc4_objnum . " 0 R\n";
        }
        if (mb_strlen($this->fileIdentifier)) {
            $content .= '/ID[<' . $this->fileIdentifier . '><' . $this->fileIdentifier . ">]\n";
        }
        $content .= "  >>\nstartxref\n" . $pos . "\n%%EOF\n";

        return $content;
    }

    /**
     * intialize a new document
     * if this is called on an existing document results may be unpredictable, but the existing document would be lost at minimum
     * this function is called automatically by the constructor function
     */
    public function newDocument(array $pageSize = [0, 0, 612, 792]): void
    {
        $this->numObj  = 0;
        $this->objects = [];

        $this->numObj++;
        $this->oCatalog($this->numObj, 'new');

        $this->numObj++;
        $this->oOutlines($this->numObj, 'new');

        $this->numObj++;
        $this->oPages($this->numObj, 'new');

        $this->oPages($this->numObj, 'mediaBox', $pageSize);
        $this->currentNode = 3;

        $this->numObj++;
        $this->oProcset($this->numObj, 'new');

        $this->numObj++;
        $this->oInfo($this->numObj, 'new');

        $this->numObj++;
        $this->oPage($this->numObj, 'new');

        // need to store the first page id as there is no way to get it to the user during
        // startup
        $this->firstPageId = $this->currentContents;
    }

    /**
     * open the font file and return a php structure containing it.
     * first check if this one has been done before and saved in a form more suited to php
     * note that if a php serialized version does not exist it will try and make one, but will
     * require_once write access to the directory to do it... it is MUCH faster to have these serialized
     * files.
     *
     * @param $font
     */
    public function openFont($font): void
    {
        // assume that $font contains both the path and perhaps the extension to the file, split them
        $pos = mb_strrpos($font, '/');
        if (false === $pos) {
            $dir  = './';
            $name = $font;
        } else {
            $dir  = mb_substr($font, 0, $pos + 1);
            $name = mb_substr($font, $pos + 1);
        }

        if ('.afm' === mb_substr($name, -4)) {
            $name = mb_substr($name, 0, -4);
        }
        $this->addMessage('openFont: ' . $font . ' - ' . $name);
        if (\is_file($dir . 'php_' . $name . '.afm')) {
            $this->addMessage('openFont: php file exists ' . $dir . 'php_' . $name . '.afm');
            $tmp                = \file($dir . 'php_' . $name . '.afm');
            $this->fonts[$font] = \unserialize($tmp[0]);
            if (!isset($this->fonts[$font]['_version_']) || $this->fonts[$font]['_version_'] < 1) {
                // if the font file is old, then clear it out and prepare for re-creation
                $this->addMessage('openFont: clear out, make way for new version.');
                unset($this->fonts[$font]);
            }
        }
        if (!isset($this->fonts[$font]) && \is_file($dir . $name . '.afm')) {
            // then rebuild the php_<font>.afm file from the <font>.afm file
            $this->addMessage('openFont: build php file from ' . $dir . $name . '.afm');
            $data = [];
            $file = \file($dir . $name . '.afm');
            foreach ($file as $rowA) {
                $row = \trim($rowA);
                $pos = mb_strpos($row, ' ');
                if ($pos) {
                    // then there must be some keyword
                    $key = mb_substr($row, 0, $pos);
                    switch ($key) {
                        case 'FontName':
                        case 'FullName':
                        case 'FamilyName':
                        case 'Weight':
                        case 'ItalicAngle':
                        case 'IsFixedPitch':
                        case 'CharacterSet':
                        case 'UnderlinePosition':
                        case 'UnderlineThickness':
                        case 'Version':
                        case 'EncodingScheme':
                        case 'CapHeight':
                        case 'XHeight':
                        case 'Ascender':
                        case 'Descender':
                        case 'StdHW':
                        case 'StdVW':
                        case 'StartCharMetrics':
                            $data[$key] = \trim(mb_substr($row, $pos));
                            break;
                        case 'FontBBox':
                            $data[$key] = \explode(' ', \trim(mb_substr($row, $pos)));
                            break;
                        case 'C':
                            //C 39 ; WX 222 ; N quoteright ; B 53 463 157 718 ;
                            $bits = \explode(';', \trim($row));
                            $dtmp = [];
                            foreach ($bits as $bit) {
                                $bits2 = \explode(' ', \trim($bit));
                                if (mb_strlen($bits2[0])) {
                                    if (\count($bits2) > 2) {
                                        $dtmp[$bits2[0]] = [];
                                        $arrayCount      = \count($bits2);
                                        for ($i = 1; $i < $arrayCount; ++$i) {
                                            $dtmp[$bits2[0]][] = $bits2[$i];
                                        }
                                    } elseif (2 === \count($bits2)) {
                                        $dtmp[$bits2[0]] = $bits2[1];
                                    }
                                }
                            }
                            if ($dtmp['C'] >= 0) {
                                $data['C'][$dtmp['C']] = $dtmp;
                                $data['C'][$dtmp['N']] = $dtmp;
                            } else {
                                $data['C'][$dtmp['N']] = $dtmp;
                            }
                            break;
                        case 'KPX':
                            //KPX Adieresis yacute -40
                            $bits                            = \explode(' ', \trim($row));
                            $data['KPX'][$bits[1]][$bits[2]] = $bits[3];
                            break;
                    }
                }
            }
            $data['_version_']  = 1;
            $this->fonts[$font] = $data;
            $fp                 = \fopen($dir . 'php_' . $name . '.afm', 'wb');
            \fwrite($fp, \serialize($data));
            \fclose($fp);
        } elseif (!isset($this->fonts[$font])) {
            $this->addMessage('openFont: no font file found');
            //    echo 'Font not Found '.$font;
        }
    }

    /**
     * if the font is not loaded then load it and make the required object
     * else just make it the current font
     * the encoding array can contain 'encoding'=> 'none','WinAnsiEncoding','MacRomanEncoding' or 'MacExpertEncoding'
     * note that encoding='none' will need to be used for symbolic fonts
     * and 'differences' => an array of mappings between numbers 0->255 and character names.
     * @param         $fontName
     * @param string  $encoding
     * @param int     $set
     */
    public function selectFont($fontName, $encoding = '', $set = 1): int
    {
        if (!isset($this->fonts[$fontName])) {
            // load the file
            $this->openFont($fontName);
            if (isset($this->fonts[$fontName])) {
                $this->numObj++;
                $this->numFonts++;
                $pos = mb_strrpos($fontName, '/');
                //      $dir=substr($fontName,0,$pos+1);
                $name = mb_substr($fontName, $pos + 1);
                if ('.afm' === mb_substr($name, -4)) {
                    $name = mb_substr($name, 0, -4);
                }
                $options = ['name' => $name];
                if (\is_array($encoding)) {
                    // then encoding and differences might be set
                    if (isset($encoding['encoding'])) {
                        $options['encoding'] = $encoding['encoding'];
                    }
                    if (isset($encoding['differences'])) {
                        $options['differences'] = $encoding['differences'];
                    }
                } elseif (mb_strlen($encoding)) {
                    // then perhaps only the encoding has been set
                    $options['encoding'] = $encoding;
                }
                $fontObj = $this->numObj;
                $this->oFont($this->numObj, 'new', $options);
                $this->fonts[$fontName]['fontNum'] = $this->numFonts;
                // if this is a '.afm' font, and there is a '.pfa' file to go with it ( as there
                // should be for all non-basic fonts), then load it into an object and put the
                // references into the font object
                $basefile = mb_substr($fontName, 0, -4);
                $fbtype   = '';
                if (\file_exists($basefile . '.pfb')) {
                    $fbtype = 'pfb';
                } elseif (\file_exists($basefile . '.ttf')) {
                    $fbtype = 'ttf';
                }
                $fbfile = $basefile . '.' . $fbtype;

                //      $pfbfile = substr($fontName,0,strlen($fontName)-4).'.pfb';
                //      $ttffile = substr($fontName,0,strlen($fontName)-4).'.ttf';
                $this->addMessage('selectFont: checking for - ' . $fbfile);
                if ('.afm' === mb_substr($fontName, -4) && mb_strlen($fbtype)) {
                    $adobeFontName = $this->fonts[$fontName]['FontName'];
                    //        $fontObj = $this->numObj;
                    $this->addMessage('selectFont: adding font file - ' . $fbfile . ' - ' . $adobeFontName);
                    // find the array of fond widths, and put that into an object.
                    $firstChar = -1;
                    $lastChar  = 0;
                    $widths    = [];
                    foreach ($this->fonts[$fontName]['C'] as $num => $d) {
                        if ((int)$num > 0 || '0' === $num) {
                            if ($lastChar > 0 && $num > $lastChar + 1) {
                                for ($i = $lastChar + 1; $i < $num; ++$i) {
                                    $widths[] = 0;
                                }
                            }
                            $widths[] = $d['WX'];
                            if (-1 === $firstChar) {
                                $firstChar = $num;
                            }
                            $lastChar = $num;
                        }
                    }
                    // also need to adjust the widths for the differences array
                    if (isset($options['differences'])) {
                        foreach ($options['differences'] as $charNum => $charName) {
                            if ($charNum > $lastChar) {
                                for ($i = $lastChar + 1; $i <= $charNum; ++$i) {
                                    $widths[] = 0;
                                }
                                $lastChar = $charNum;
                            }
                            if (isset($this->fonts[$fontName]['C'][$charName])) {
                                $widths[$charNum - $firstChar] = $this->fonts[$fontName]['C'][$charName]['WX'];
                            }
                        }
                    }
                    $this->addMessage('selectFont: FirstChar=' . $firstChar);
                    $this->addMessage('selectFont: LastChar=' . $lastChar);
                    $this->numObj++;
                    $this->oContents($this->numObj, 'new', 'raw');
                    $this->objects[$this->numObj]['c'] .= '[';
                    foreach ($widths as $width) {
                        $this->objects[$this->numObj]['c'] .= ' ' . $width;
                    }
                    $this->objects[$this->numObj]['c'] .= ' ]';
                    $widthid                           = $this->numObj;

                    // load the pfb file, and put that into an object too.
                    // note that pdf supports only binary format type 1 font files, though there is a
                    // simple utility to convert them from pfa to pfb.
                    $fp = \fopen($fbfile, 'rb');
                    //                    $tmp = @get_magic_quotes_runtime();
                    //                    @set_magic_quotes_runtime(0);
                    $data = \fread($fp, \filesize($fbfile));
                    //                    @set_magic_quotes_runtime($tmp);
                    \fclose($fp);

                    // create the font descriptor
                    $this->numObj++;
                    $fontDescriptorId = $this->numObj;
                    $this->numObj++;
                    $pfbid = $this->numObj;
                    // determine flags (more than a little flakey, hopefully will not matter much)
                    $flags = 0;
                    if (0 !== $this->fonts[$fontName]['ItalicAngle']) {
                        $flags += 2 ** 6;
                    }
                    if ('true' === $this->fonts[$fontName]['IsFixedPitch']) {
                        ++$flags;
                    }
                    $flags += 2 ** 5; // assume non-sybolic

                    $list  = [
                        'Ascent'      => 'Ascender',
                        'CapHeight'   => 'CapHeight',
                        'Descent'     => 'Descender',
                        'FontBBox'    => 'FontBBox',
                        'ItalicAngle' => 'ItalicAngle',
                    ];
                    $fdopt = [
                        'Flags'    => $flags,
                        'FontName' => $adobeFontName,
                        'StemV'    => 100,  // don't know what the value for this should be!
                    ];
                    foreach ($list as $k => $v) {
                        if (isset($this->fonts[$fontName][$v])) {
                            $fdopt[$k] = $this->fonts[$fontName][$v];
                        }
                    }

                    if ('pfb' === $fbtype) {
                        $fdopt['FontFile'] = $pfbid;
                    } elseif ('ttf' === $fbtype) {
                        $fdopt['FontFile2'] = $pfbid;
                    }
                    $this->oFontDescriptor($fontDescriptorId, 'new', $fdopt);

                    // embed the font program
                    $this->oContents($this->numObj, 'new');
                    $this->objects[$pfbid]['c'] .= $data;
                    // determine the cruicial lengths within this file
                    if ('pfb' === $fbtype) {
                        $l1 = mb_strpos($data, 'eexec') + 6;
                        $l2 = mb_strpos($data, '00000000') - $l1;
                        $l3 = mb_strlen($data) - $l2 - $l1;
                        $this->oContents($this->numObj, 'add', ['Length1' => $l1, 'Length2' => $l2, 'Length3' => $l3]);
                    } elseif ('ttf' === $fbtype) {
                        $l1 = mb_strlen($data);
                        $this->oContents($this->numObj, 'add', ['Length1' => $l1]);
                    }

                    // tell the font object about all this new stuff
                    $tmp = [
                        'BaseFont'       => $adobeFontName,
                        'Widths'         => $widthid,
                        'FirstChar'      => $firstChar,
                        'LastChar'       => $lastChar,
                        'FontDescriptor' => $fontDescriptorId,
                    ];
                    if ('ttf' === $fbtype) {
                        $tmp['SubType'] = 'TrueType';
                    }
                    $this->addMessage('adding extra info to font.(' . $fontObj . ')');
                    foreach ($tmp as $fk => $fv) {
                        $this->addMessage($fk . ' : ' . $fv);
                    }
                    $this->oFont($fontObj, 'add', $tmp);
                } else {
                    $this->addMessage('selectFont: pfb or ttf file not found, ok if this is one of the 14 standard fonts');
                }

                // also set the differences here, note that this means that these will take effect only the
                //first time that a font is selected, else they are ignored
                if (isset($options['differences'])) {
                    $this->fonts[$fontName]['differences'] = $options['differences'];
                }
            }
        }
        if ($set && isset($this->fonts[$fontName])) {
            // so if for some reason the font was not set in the last one then it will not be selected
            $this->currentBaseFont = $fontName;
            // the next line means that if a new font is selected, then the current text state will be
            // applied to it as well.
            $this->setCurrentFont();
        }

        return $this->currentFontNum;
    }

    /**
     * sets up the current font, based on the font families, and the current text state
     * note that this system is quite flexible, a <b><i> font can be completely different to a
     * <i><b> font, and even <br><br> will have to be defined within the family to have meaning
     * This function is to be called whenever the currentTextState is changed, it will update
     * the currentFont setting to whatever the appropriatte family one is.
     * If the user calls selectFont themselves then that will reset the currentBaseFont, and the currentFont
     * This function will change the currentFont to whatever it should be, but will not change the
     * currentBaseFont.
     */
    public function setCurrentFont(): void
    {
        if (0 === mb_strlen($this->currentBaseFont)) {
            // then assume an initial font
            $this->selectFont('./fonts/Helvetica.afm');
        }
        $cf = mb_substr($this->currentBaseFont, mb_strrpos($this->currentBaseFont, '/') + 1);
        if (isset($this->fontFamilies[$cf]) && mb_strlen($this->currentTextState)
            && isset($this->fontFamilies[$cf][$this->currentTextState])) {
            // then we are in some state or another
            // and this font has a family, and the current setting exists within it
            // select the font, then return it
            $nf = mb_substr($this->currentBaseFont, 0, mb_strrpos($this->currentBaseFont, '/') + 1) . $this->fontFamilies[$cf][$this->currentTextState];
            $this->selectFont($nf, '', 0);
            $this->currentFont    = $nf;
            $this->currentFontNum = $this->fonts[$nf]['fontNum'];
        } else {
            // the this font must not have the right family member for the current state
            // simply assume the base font
            $this->currentFont    = $this->currentBaseFont;
            $this->currentFontNum = $this->fonts[$this->currentFont]['fontNum'];
        }
    }

    /**
     * function for the user to find out what the ID is of the first page that was created during
     * startup - useful if they wish to add something to it later.
     */
    public function getFirstPageId()
    {
        return $this->firstPageId;
    }

    /**
     * add content to the currently active object
     *
     * @param $content
     */
    public function addContent($content): void
    {
        $this->objects[$this->currentContents]['c'] .= $content;
    }

    /**
     * sets the colour for fill operations
     * @param     $r
     * @param     $g
     * @param     $b
     * @param int $force
     */
    public function setColor($r, $g, $b, $force = 0): void
    {
        if ($r >= 0
            && ($force || $r !== $this->currentColour['r'] || $g !== $this->currentColour['g']
                || $b !== $this->currentColour['b'])) {
            $this->objects[$this->currentContents]['c'] .= "\n" . \sprintf('%.3f', $r) . ' ' . \sprintf('%.3f', $g) . ' ' . \sprintf('%.3f', $b) . ' rg';
            $this->currentColour                        = ['r' => $r, 'g' => $g, 'b' => $b];
        }
    }

    /**
     * sets the colour for stroke operations
     * @param     $r
     * @param     $g
     * @param     $b
     * @param int $force
     */
    public function setStrokeColor($r, $g, $b, $force = 0): void
    {
        if ($r >= 0
            && ($force || $r !== $this->currentStrokeColour['r'] || $g !== $this->currentStrokeColour['g']
                || $b !== $this->currentStrokeColour['b'])) {
            $this->objects[$this->currentContents]['c'] .= "\n" . \sprintf('%.3f', $r) . ' ' . \sprintf('%.3f', $g) . ' ' . \sprintf('%.3f', $b) . ' RG';
            $this->currentStrokeColour                  = ['r' => $r, 'g' => $g, 'b' => $b];
        }
    }

    /**
     * draw a line from one set of coordinates to another
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     */
    public function line($x1, $y1, $x2, $y2): void
    {
        $this->objects[$this->currentContents]['c'] .= "\n" . \sprintf('%.3f', $x1) . ' ' . \sprintf('%.3f', $y1) . ' m ' . \sprintf('%.3f', $x2) . ' ' . \sprintf('%.3f', $y2) . ' l S';
    }

    /**
     * draw a bezier curve based on 4 control points
     * @param $x0
     * @param $y0
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     * @param $x3
     * @param $y3
     */
    public function curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3): void
    {
        // in the current line style, draw a bezier curve from (x0,y0) to (x3,y3) using the other two points
        // as the control points for the curve.
        $this->objects[$this->currentContents]['c'] .= "\n" . \sprintf('%.3f', $x0) . ' ' . \sprintf('%.3f', $y0) . ' m ' . \sprintf('%.3f', $x1) . ' ' . \sprintf('%.3f', $y1);
        $this->objects[$this->currentContents]['c'] .= ' ' . \sprintf('%.3f', $x2) . ' ' . \sprintf('%.3f', $y2) . ' ' . \sprintf('%.3f', $x3) . ' ' . \sprintf('%.3f', $y3) . ' c S';
    }

    /**
     * draw a part of an ellipse
     * @param     $x0
     * @param     $y0
     * @param     $astart
     * @param     $afinish
     * @param     $r1
     * @param int $r2
     * @param int $angle
     * @param int $nSeg
     */
    public function partEllipse($x0, $y0, $astart, $afinish, $r1, $r2 = 0, $angle = 0, $nSeg = 8): void
    {
        $this->ellipse($x0, $y0, $r1, $r2, $angle, $nSeg, $astart, $afinish, 0);
    }

    /**
     * draw a filled ellipse
     * @param     $x0
     * @param     $y0
     * @param     $r1
     * @param int $r2
     * @param int $angle
     * @param int $nSeg
     * @param int $astart
     * @param int $afinish
     */
    public function filledEllipse($x0, $y0, $r1, $r2 = 0, $angle = 0, $nSeg = 8, $astart = 0, $afinish = 360): void
    {
        return $this->ellipse($x0, $y0, $r1, $r2 = 0, $angle, $nSeg, $astart, $afinish, 1, 1);
    }

    /**
     * draw an ellipse
     * note that the part and filled ellipse are just special cases of this function
     *
     * draws an ellipse in the current line style
     * centered at $x0,$y0, radii $r1,$r2
     * if $r2 is not set, then a circle is drawn
     * nSeg is not allowed to be less than 2, as this will simply draw a line (and will even draw a
     * pretty crappy shape at 2, as we are approximating with bezier curves.
     * @param     $x0
     * @param     $y0
     * @param     $r1
     * @param int $r2
     * @param int $angle
     * @param int $nSeg
     * @param int $astart
     * @param int $afinish
     * @param int $close
     * @param int $fill
     */
    public function ellipse(
        $x0,
        $y0,
        $r1,
        $r2 = 0,
        $angle = 0,
        $nSeg = 8,
        $astart = 0,
        $afinish = 360,
        $close = 1,
        $fill = 0
    ): void {
        if (0 === $r1) {
            return;
        }
        if (0 === $r2) {
            $r2 = $r1;
        }
        if ($nSeg < 2) {
            $nSeg = 2;
        }

        $astart     = \deg2rad((float)$astart);
        $afinish    = \deg2rad((float)$afinish);
        $totalAngle = $afinish - $astart;

        $dt  = $totalAngle / $nSeg;
        $dtm = $dt / 3;

        if (0 !== $angle) {
            $a                                          = -1 * \deg2rad((float)$angle);
            $tmp                                        = "\n q ";
            $tmp                                        .= \sprintf('%.3f', \cos($a)) . ' ' . \sprintf('%.3f', -1.0 * \sin($a)) . ' ' . \sprintf('%.3f', \sin($a)) . ' ' . \sprintf('%.3f', \cos($a)) . ' ';
            $tmp                                        .= \sprintf('%.3f', $x0) . ' ' . \sprintf('%.3f', $y0) . ' cm';
            $this->objects[$this->currentContents]['c'] .= $tmp;
            $x0                                         = 0;
            $y0                                         = 0;
        }

        $t1 = $astart;
        $a0 = $x0 + $r1 * \cos($t1);
        $b0 = $y0 + $r2 * \sin($t1);
        $c0 = -$r1 * \sin($t1);
        $d0 = $r2 * \cos($t1);

        $this->objects[$this->currentContents]['c'] .= "\n" . \sprintf('%.3f', $a0) . ' ' . \sprintf('%.3f', $b0) . ' m ';
        for ($i = 1; $i <= $nSeg; ++$i) {
            // draw this bit of the total curve
            $t1                                         = $i * $dt + $astart;
            $a1                                         = $x0 + $r1 * \cos($t1);
            $b1                                         = $y0 + $r2 * \sin($t1);
            $c1                                         = -$r1 * \sin($t1);
            $d1                                         = $r2 * \cos($t1);
            $this->objects[$this->currentContents]['c'] .= "\n" . \sprintf('%.3f', $a0 + $c0 * $dtm) . ' ' . \sprintf('%.3f', $b0 + $d0 * $dtm);
            $this->objects[$this->currentContents]['c'] .= ' ' . \sprintf('%.3f', $a1 - $c1 * $dtm) . ' ' . \sprintf('%.3f', $b1 - $d1 * $dtm) . ' ' . \sprintf('%.3f', $a1) . ' ' . \sprintf('%.3f', $b1) . ' c';
            $a0                                         = $a1;
            $b0                                         = $b1;
            $c0                                         = $c1;
            $d0                                         = $d1;
        }
        if ($fill) {
            $this->objects[$this->currentContents]['c'] .= ' f';
        } else {
            if ($close) {
                $this->objects[$this->currentContents]['c'] .= ' s'; // small 's' signifies closing the path as well
            } else {
                $this->objects[$this->currentContents]['c'] .= ' S';
            }
        }
        if (0 !== $angle) {
            $this->objects[$this->currentContents]['c'] .= ' Q';
        }
    }

    /**
     * this sets the line drawing style.
     * width, is the thickness of the line in user units
     * cap is the type of cap to put on the line, values can be 'butt','round','square'
     *    where the diffference between 'square' and 'butt' is that 'square' projects a flat end past the
     *    end of the line.
     * join can be 'miter', 'round', 'bevel'
     * dash is an array which sets the dash pattern, is a series of length values, which are the lengths of the
     *   on and off dashes.
     *   (2) represents 2 on, 2 off, 2 on , 2 off ...
     *   (2,1) is 2 on, 1 off, 2 on, 1 off.. etc
     * phase is a modifier on the dash pattern which is used to shift the point at which the pattern starts.
     * @param int    $width
     * @param string $cap
     * @param string $join
     * @param string $dash
     * @param int    $phase
     */
    public function setLineStyle($width = 1, $cap = '', $join = '', $dash = '', $phase = 0): void
    {
        // this is quite inefficient in that it sets all the parameters whenever 1 is changed, but will fix another day
        $string = '';
        if ($width > 0) {
            $string .= $width . ' w';
        }
        $ca = ['butt' => 0, 'round' => 1, 'square' => 2];
        if (isset($ca[$cap])) {
            $string .= ' ' . $ca[$cap] . ' J';
        }
        $ja = ['miter' => 0, 'round' => 1, 'bevel' => 2];
        if (isset($ja[$join])) {
            $string .= ' ' . $ja[$join] . ' j';
        }
        if (\is_array($dash)) {
            $string .= ' [';
            foreach ($dash as $len) {
                $string .= ' ' . $len;
            }
            $string .= ' ] ' . $phase . ' d';
        }
        $this->currentLineStyle                     = $string;
        $this->objects[$this->currentContents]['c'] .= "\n" . $string;
    }

    /**
     * draw a polygon, the syntax for this is similar to the GD polygon command
     * @param     $p
     * @param     $np
     * @param int $f
     */
    public function polygon($p, $np, $f = 0): void
    {
        $this->objects[$this->currentContents]['c'] .= "\n";
        $this->objects[$this->currentContents]['c'] .= \sprintf('%.3f', $p[0]) . ' ' . \sprintf('%.3f', $p[1]) . ' m ';
        for ($i = 2; $i < $np * 2; $i += 2) {
            $this->objects[$this->currentContents]['c'] .= \sprintf('%.3f', $p[$i]) . ' ' . \sprintf('%.3f', $p[$i + 1]) . ' l ';
        }
        if (1 === $f) {
            $this->objects[$this->currentContents]['c'] .= ' f';
        } else {
            $this->objects[$this->currentContents]['c'] .= ' S';
        }
    }

    /**
     * a filled rectangle, note that it is the width and height of the rectangle which are the secondary paramaters, not
     * the coordinates of the upper-right corner
     * @param $x1
     * @param $y1
     * @param $width
     * @param $height
     */
    public function filledRectangle($x1, $y1, $width, $height): void
    {
        $this->objects[$this->currentContents]['c'] .= "\n" . \sprintf('%.3f', $x1) . ' ' . \sprintf('%.3f', $y1) . ' ' . \sprintf('%.3f', $width) . ' ' . \sprintf('%.3f', $height) . ' re f';
    }

    /**
     * draw a rectangle, note that it is the width and height of the rectangle which are the secondary paramaters, not
     * the coordinates of the upper-right corner
     * @param $x1
     * @param $y1
     * @param $width
     * @param $height
     */
    public function rectangle($x1, $y1, $width, $height): void
    {
        $this->objects[$this->currentContents]['c'] .= "\n" . \sprintf('%.3f', $x1) . ' ' . \sprintf('%.3f', $y1) . ' ' . \sprintf('%.3f', $width) . ' ' . \sprintf('%.3f', $height) . ' re S';
    }

    /**
     * add a new page to the document
     * this also makes the new page the current active object
     * @param int    $insert
     * @param int    $id
     * @param string $pos
     * @return mixed
     */
    public function newPage($insert = 0, $id = 0, $pos = 'after')
    {
        // if there is a state saved, then go up the stack closing them
        // then on the new page, re-open them with the right setings

        if ($this->nStateStack) {
            for ($i = $this->nStateStack; $i >= 1; $i--) {
                $this->restoreState($i);
            }
        }

        $this->numObj++;
        if ($insert) {
            // the id from the ezPdf class is the od of the contents of the page, not the page object itself
            // query that object to find the parent
            $rid = $this->objects[$id]['onPage'];
            $opt = ['rid' => $rid, 'pos' => $pos];
            $this->oPage($this->numObj, 'new', $opt);
        } else {
            $this->oPage($this->numObj, 'new');
        }
        // if there is a stack saved, then put that onto the page
        if ($this->nStateStack) {
            for ($i = 1; $i <= $this->nStateStack; ++$i) {
                $this->saveState($i);
            }
        }
        // and if there has been a stroke or fill colour set, then transfer them
        if ($this->currentColour['r'] >= 0) {
            $this->setColor($this->currentColour['r'], $this->currentColour['g'], $this->currentColour['b'], 1);
        }
        if ($this->currentStrokeColour['r'] >= 0) {
            $this->setStrokeColor($this->currentStrokeColour['r'], $this->currentStrokeColour['g'], $this->currentStrokeColour['b'], 1);
        }

        // if there is a line style set, then put this in too
        if (mb_strlen($this->currentLineStyle)) {
            $this->objects[$this->currentContents]['c'] .= "\n" . $this->currentLineStyle;
        }

        // the call to the oPage object set currentContents to the present page, so this can be returned as the page id
        return $this->currentContents;
    }

    /**
     * output the pdf code, streaming it to the browser
     * the relevant headers are set so that hopefully the browser will recognise it
     * @param string $options
     */
    public function stream($options = ''): void
    {
        // setting the options allows the adjustment of the headers
        // values at the moment are:
        // 'Content-Disposition'=>'filename'  - sets the filename, though not too sure how well this will
        //        work as in my trial the browser seems to use the filename of the php file with .pdf on the end
        // 'Accept-Ranges'=>1 or 0 - if this is not set to 1, then this header is not included, off by default
        //    this header seems to have caused some problems despite tha fact that it is supposed to solve
        //    them, so I am leaving it off by default.
        // 'compress'=> 1 or 0 - apply content stream compression, this is on (1) by default
        if (!\is_array($options)) {
            $options = [];
        }
        if (isset($options['compress']) && 0 === $options['compress']) {
            $tmp = $this->output(1);
        } else {
            $tmp = $this->output();
        }
        \header('Content-type: application/pdf');
        \header('Content-Length: ' . mb_strlen(\ltrim($tmp)));
        $fileName = ($options['Content-Disposition'] ?? 'file.pdf');
        \header('Content-Disposition: inline; filename=' . $fileName);
        if (isset($options['Accept-Ranges']) && 1 === $options['Accept-Ranges']) {
            \header('Accept-Ranges: ' . mb_strlen(\ltrim($tmp)));
        }
        echo \ltrim($tmp);
    }

    /**
     * return the height in units of the current font in the given size
     * @param $size
     * @return float
     */
    public function getFontHeight($size)
    {
        if (!$this->numFonts) {
            $this->selectFont('./fonts/Helvetica');
        }
        // for the current font, and the given size, what is the height of the font in user units
        $h = $this->fonts[$this->currentFont]['FontBBox'][3] - $this->fonts[$this->currentFont]['FontBBox'][1];

        return $size * $h / 1000;
    }

    /**
     * return the font decender, this will normally return a negative number
     * if you add this number to the baseline, you get the level of the bottom of the font
     * it is in the pdf user units
     * @param $size
     * @return float
     */
    public function getFontDecender($size)
    {
        // note that this will most likely return a negative value
        if (!$this->numFonts) {
            $this->selectFont('./fonts/Helvetica');
        }
        $h = $this->fonts[$this->currentFont]['FontBBox'][1];

        return $size * $h / 1000;
    }

    /**
     * filter the text, this is applied to all text just before being inserted into the pdf document
     * it escapes the various things that need to be escaped, and so on
     *
     * @param $text
     * @return array|string|string[]
     */
    public function filterText($text)
    {
        $text = \str_replace('\\', '\\\\', $text);
        $text = \str_replace('(', '\(', $text);
        $text = \str_replace(')', '\)', $text);
        $text = \str_replace('&lt;', '<', $text);
        $text = \str_replace('&gt;', '>', $text);
        $text = \str_replace('&#039;', '\'', $text);
        $text = \str_replace('&quot;', '"', $text);
        $text = \str_replace('&amp;', '&', $text);

        return $text;
    }

    /**
     * given a start position and information about how text is to be laid out, calculate where
     * on the page the text will end
     *
     * @param $x
     * @param $y
     * @param $angle
     * @param $size
     * @param $wa
     * @param $text
     */
    private function privGetTextPosition($x, $y, $angle, $size, $wa, $text): array
    {
        // given this information return an array containing x and y for the end position as elements 0 and 1
        $w = $this->getTextWidth($size, $text);
        // need to adjust for the number of spaces in this text
        $words   = \explode(' ', $text);
        $nspaces = \count($words) - 1;
        $w       += $wa * $nspaces;
        $a       = \deg2rad((float)$angle);

        return [\cos($a) * $w + $x, -\sin($a) * $w + $y];
    }

    /**
     * wrapper function for privCheckTextDirective1
     *
     * @param $text
     * @param $i
     * @param $f
     * @return bool|int
     */
    private function privCheckTextDirective($text, $i, &$f)
    {
        $x = 0;
        $y = 0;

        return $this->privCheckTextDirective1($text, $i, $f, 0, $x, $y);
    }

    /**
     * checks if the text stream contains a control directive
     * if so then makes some changes and returns the number of characters involved in the directive
     * this has been re-worked to include everything neccesary to fins the current writing point, so that
     * the location can be sent to the callback function if required
     * if the directive does not require_once a font change, then $f should be set to 0
     *
     * @param           $text
     * @param           $i
     * @param           $final
     * @param int       $size
     * @param int       $angle
     * @param int       $wordSpaceAdjust
     * @return bool|int
     */
    private function privCheckTextDirective1(
        $text,
        $i,
        &$f,
        $final,
        &$x,
        &$y,
        $size = 0,
        $angle = 0,
        $wordSpaceAdjust = 0
    ) {
        $directive = 0;
        $j         = $i;
        if ('<' === $text[$j]) {
            ++$j;
            switch ($text[$j]) {
                case '/':
                    ++$j;
                    if (mb_strlen($text) <= $j) {
                        return $directive;
                    }
                    switch ($text[$j]) {
                        case 'b':
                        case 'i':
                            ++$j;
                            if ('>' === $text[$j]) {
                                $p = mb_strrpos($this->currentTextState, $text[$j - 1]);
                                if (false !== $p) {
                                    // then there is one to remove
                                    $this->currentTextState = mb_substr($this->currentTextState, 0, $p) . mb_substr($this->currentTextState, $p + 1);
                                }
                                $directive = $j - $i + 1;
                            }
                            break;
                        case 'c':
                            // this this might be a callback function
                            ++$j;
                            $k = mb_strpos($text, '>', $j);
                            if (false !== $k && ':' === $text[$j]) {
                                // then this will be treated as a callback directive
                                $directive = $k - $i + 1;
                                $f         = 0;
                                // split the remainder on colons to get the function name and the paramater
                                $tmp = mb_substr($text, $j + 1, $k - $j - 1);
                                $b1  = mb_strpos($tmp, ':');
                                if (false !== $b1) {
                                    $func = mb_substr($tmp, 0, $b1);
                                    $parm = mb_substr($tmp, $b1 + 1);
                                } else {
                                    $func = $tmp;
                                    $parm = '';
                                }
                                if (null === $func || !mb_strlen(\trim($func))) {
                                    $directive = 0;
                                } else {
                                    // only call the function if this is the final call
                                    if ($final) {
                                        // need to assess the text position, calculate the text width to this point
                                        // can use getTextWidth to find the text width I think
                                        $tmp  = $this->privGetTextPosition($x, $y, $angle, $size, $wordSpaceAdjust, mb_substr($text, 0, $i));
                                        $info = [
                                            'x'         => $tmp[0],
                                            'y'         => $tmp[1],
                                            'angle'     => $angle,
                                            'status'    => 'end',
                                            'p'         => $parm,
                                            'nCallback' => $this->nCallback,
                                        ];
                                        $x    = $tmp[0];
                                        $y    = $tmp[1];
                                        $ret  = $this->$func($info);
                                        if (\is_array($ret)) {
                                            // then the return from the callback function could set the position, to start with, later will do font colour, and font
                                            foreach ($ret as $rk => $rv) {
                                                switch ($rk) {
                                                    case 'x':
                                                    case 'y':
                                                        $$rk = $rv;
                                                        break;
                                                }
                                            }
                                        }
                                        // also remove from to the stack
                                        // for simplicity, just take from the end, fix this another day
                                        $this->nCallback--;
                                        if ($this->nCallback < 0) {
                                            $this->nCallBack = 0;
                                        }
                                    }
                                }
                            }
                            break;
                    }
                    break;
                case 'b':
                case 'i':
                    ++$j;
                    if ('>' === $text[$j]) {
                        $this->currentTextState .= $text[$j - 1];
                        $directive              = $j - $i + 1;
                    }
                    break;
                case 'C':
                    $noClose = 1;
                    break;
                case 'c':
                    // this might be a callback function
                    ++$j;
                    $k = mb_strpos($text, '>', $j);
                    if (false !== $k && ':' === $text[$j]) {
                        // then this will be treated as a callback directive
                        $directive = $k - $i + 1;
                        $f         = 0;
                        // split the remainder on colons to get the function name and the paramater
                        //          $bits = explode(':',substr($text,$j+1,$k-$j-1));
                        $tmp = mb_substr($text, $j + 1, $k - $j - 1);
                        $b1  = mb_strpos($tmp, ':');
                        if (false !== $b1) {
                            $func = mb_substr($tmp, 0, $b1);
                            $parm = mb_substr($tmp, $b1 + 1);
                        } else {
                            $func = $tmp;
                            $parm = '';
                        }
                        if (null === $func || !mb_strlen(\trim($func))) {
                            $directive = 0;
                        } else {
                            // only call the function if this is the final call, ie, the one actually doing printing, not measurement
                            if ($final) {
                                // need to assess the text position, calculate the text width to this point
                                // can use getTextWidth to find the text width I think
                                // also add the text height and decender
                                $tmp  = $this->privGetTextPosition($x, $y, $angle, $size, $wordSpaceAdjust, mb_substr($text, 0, $i));
                                $info = [
                                    'x'        => $tmp[0],
                                    'y'        => $tmp[1],
                                    'angle'    => $angle,
                                    'status'   => 'start',
                                    'p'        => $parm,
                                    'f'        => $func,
                                    'height'   => $this->getFontHeight($size),
                                    'decender' => $this->getFontDecender($size),
                                ];
                                $x    = $tmp[0];
                                $y    = $tmp[1];
                                if (null === $noClose || !$noClose) {
                                    // only add to the stack if this is a small 'c', therefore is a start-stop pair
                                    $this->nCallback++;
                                    $info['nCallback']                = $this->nCallback;
                                    $this->callback[$this->nCallback] = $info;
                                }
                                $ret = $this->$func($info);
                                if (\is_array($ret)) {
                                    // then the return from the callback function could set the position, to start with, later will do font colour, and font
                                    foreach ($ret as $rk => $rv) {
                                        switch ($rk) {
                                            case 'x':
                                            case 'y':
                                                $$rk = $rv;
                                                break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
            }
        }

        return $directive;
    }

    /**
     * add text to the document, at a specified location, size and angle on the page
     * @param     $x
     * @param     $y
     * @param     $size
     * @param     $text
     * @param int $angle
     * @param int $wordSpaceAdjust
     */
    public function addText($x, $y, $size, $text, $angle = 0, $wordSpaceAdjust = 0): void
    {
        if (!$this->numFonts) {
            $this->selectFont('./fonts/Helvetica');
        }

        // if there are any open callbacks, then they should be called, to show the start of the line
        if ($this->nCallback > 0) {
            for ($i = $this->nCallback; $i > 0; $i--) {
                // call each function
                $info = [
                    'x'         => $x,
                    'y'         => $y,
                    'angle'     => $angle,
                    'status'    => 'sol',
                    'p'         => $this->callback[$i]['p'],
                    'nCallback' => $this->callback[$i]['nCallback'],
                    'height'    => $this->callback[$i]['height'],
                    'decender'  => $this->callback[$i]['decender'],
                ];
                $func = $this->callback[$i]['f'];
                $this->$func($info);
            }
        }
        if (0 === $angle) {
            $this->objects[$this->currentContents]['c'] .= "\n" . 'BT ' . \sprintf('%.3f', $x) . ' ' . \sprintf('%.3f', $y) . ' Td';
        } else {
            $a                                          = \deg2rad((float)$angle);
            $tmp                                        = "\n" . 'BT ';
            $tmp                                        .= \sprintf('%.3f', \cos($a)) . ' ' . \sprintf('%.3f', -1.0 * \sin($a)) . ' ' . \sprintf('%.3f', \sin($a)) . ' ' . \sprintf('%.3f', \cos($a)) . ' ';
            $tmp                                        .= \sprintf('%.3f', $x) . ' ' . \sprintf('%.3f', $y) . ' Tm';
            $this->objects[$this->currentContents]['c'] .= $tmp;
        }
        if (0 !== $wordSpaceAdjust || $wordSpaceAdjust !== $this->wordSpaceAdjust) {
            $this->wordSpaceAdjust                      = $wordSpaceAdjust;
            $this->objects[$this->currentContents]['c'] .= ' ' . \sprintf('%.3f', $wordSpaceAdjust) . ' Tw';
        }
        $len   = mb_strlen($text);
        $start = 0;
        for ($i = 0; $i < $len; ++$i) {
            $f         = 1;
            $directive = $this->privCheckTextDirective($text, $i, $f);
            if ($directive) {
                // then we should write what we need to
                if ($i > $start) {
                    $part                                       = mb_substr($text, $start, $i - $start);
                    $this->objects[$this->currentContents]['c'] .= ' /F' . $this->currentFontNum . ' ' . \sprintf('%.1f', $size) . ' Tf ';
                    $this->objects[$this->currentContents]['c'] .= ' (' . $this->filterText($part) . ') Tj';
                }
                if ($f) {
                    // then there was nothing drastic done here, restore the contents
                    $this->setCurrentFont();
                } else {
                    $this->objects[$this->currentContents]['c'] .= ' ET';
                    $f                                          = 1;
                    $xp                                         = $x;
                    $yp                                         = $y;
                    $directive                                  = $this->privCheckTextDirective1($text, $i, $f, 1, $xp, $yp, $size, $angle, $wordSpaceAdjust);

                    // restart the text object
                    if (0 === $angle) {
                        $this->objects[$this->currentContents]['c'] .= "\n" . 'BT ' . \sprintf('%.3f', $xp) . ' ' . \sprintf('%.3f', $yp) . ' Td';
                    } else {
                        $a                                          = \deg2rad((float)$angle);
                        $tmp                                        = "\n" . 'BT ';
                        $tmp                                        .= \sprintf('%.3f', \cos($a)) . ' ' . \sprintf('%.3f', -1.0 * \sin($a)) . ' ' . \sprintf('%.3f', \sin($a)) . ' ' . \sprintf('%.3f', \cos($a)) . ' ';
                        $tmp                                        .= \sprintf('%.3f', $xp) . ' ' . \sprintf('%.3f', $yp) . ' Tm';
                        $this->objects[$this->currentContents]['c'] .= $tmp;
                    }
                    if (0 !== $wordSpaceAdjust || $wordSpaceAdjust !== $this->wordSpaceAdjust) {
                        $this->wordSpaceAdjust                      = $wordSpaceAdjust;
                        $this->objects[$this->currentContents]['c'] .= ' ' . \sprintf('%.3f', $wordSpaceAdjust) . ' Tw';
                    }
                }
                // and move the writing point to the next piece of text
                $i     = $i + $directive - 1;
                $start = $i + 1;
            }
        }
        if ($start < $len) {
            $part                                       = mb_substr($text, $start);
            $this->objects[$this->currentContents]['c'] .= ' /F' . $this->currentFontNum . ' ' . \sprintf('%.1f', $size) . ' Tf ';
            $this->objects[$this->currentContents]['c'] .= ' (' . $this->filterText($part) . ') Tj';
        }
        $this->objects[$this->currentContents]['c'] .= ' ET';

        // if there are any open callbacks, then they should be called, to show the end of the line
        if ($this->nCallback > 0) {
            for ($i = $this->nCallback; $i > 0; $i--) {
                // call each function
                $tmp  = $this->privGetTextPosition($x, $y, $angle, $size, $wordSpaceAdjust, $text);
                $info = [
                    'x'         => $tmp[0],
                    'y'         => $tmp[1],
                    'angle'     => $angle,
                    'status'    => 'eol',
                    'p'         => $this->callback[$i]['p'],
                    'nCallback' => $this->callback[$i]['nCallback'],
                    'height'    => $this->callback[$i]['height'],
                    'decender'  => $this->callback[$i]['decender'],
                ];
                $func = $this->callback[$i]['f'];
                $this->$func($info);
            }
        }
    }

    /**
     * calculate how wide a given text string will be on a page, at a given size.
     * this can be called externally, but is alse used by the other class functions
     * @param $size
     * @param $text
     * @return float
     */
    public function getTextWidth($size, $text)
    {
        // this function should not change any of the settings, though it will need to
        // track any directives which change during calculation, so copy them at the start
        // and put them back at the end.
        $store_currentTextState = $this->currentTextState;

        if (!$this->numFonts) {
            $this->selectFont('./fonts/Helvetica');
        }

        // converts a number or a float to a string so it can get the width
        $text = (string)$text;

        // hmm, this is where it all starts to get tricky - use the font information to
        // calculate the width of each character, add them up and convert to user units
        $w   = 0;
        $len = mb_strlen($text);
        $cf  = $this->currentFont;
        for ($i = 0; $i < $len; ++$i) {
            $f         = 1;
            $directive = $this->privCheckTextDirective($text, $i, $f);
            if ($directive) {
                if ($f) {
                    $this->setCurrentFont();
                    $cf = $this->currentFont;
                }
                $i = $i + $directive - 1;
            } else {
                $char = \ord($text[$i]);
                if (isset($this->fonts[$cf]['differences'][$char])) {
                    // then this character is being replaced by another
                    $name = $this->fonts[$cf]['differences'][$char];
                    if (isset($this->fonts[$cf]['C'][$name]['WX'])) {
                        $w += $this->fonts[$cf]['C'][$name]['WX'];
                    }
                } elseif (isset($this->fonts[$cf]['C'][$char]['WX'])) {
                    $w += $this->fonts[$cf]['C'][$char]['WX'];
                }
            }
        }

        $this->currentTextState = $store_currentTextState;
        $this->setCurrentFont();

        return $w * $size / 1000;
    }

    /**
     * do a part of the calculation for sorting out the justification of the text
     *
     * @param $text
     * @param $actual
     * @param $width
     * @param $x
     * @param $adjust
     * @param $justification
     */
    private function privAdjustWrapText($text, $actual, $width, &$x, &$adjust, $justification): void
    {
        switch ($justification) {
            case 'left':
                return;
                break;
            case 'right':
                $x += $width - $actual;
                break;
            case 'center':
            case 'centre':
                $x += ($width - $actual) / 2;
                break;
            case 'full':
                // count the number of words
                $words   = \explode(' ', $text);
                $nspaces = \count($words) - 1;
                $adjust  = 0;
                if ($nspaces > 0) {
                    $adjust = ($width - $actual) / $nspaces;
                }
                break;
        }
    }

    /**
     * add text to the page, but ensure that it fits within a certain width
     * if it does not fit then put in as much as possible, splitting at word boundaries
     * and return the remainder.
     * justification and angle can also be specified for the text
     * @param         $x
     * @param         $y
     * @param         $width
     * @param         $size
     * @param         $text
     * @param string  $justification
     * @param int     $angle
     * @param int     $test
     */
    public function addTextWrap($x, $y, $width, $size, $text, $justification = 'left', $angle = 0, $test = 0): string
    {
        // this will display the text, and if it goes beyond the width $width, will backtrack to the
        // previous space or hyphen, and return the remainder of the text.

        // $justification can be set to 'left','right','center','centre','full'

        // need to store the initial text state, as this will change during the width calculation
        // but will need to be re-set before printing, so that the chars work out right
        $store_currentTextState = $this->currentTextState;

        if (!$this->numFonts) {
            $this->selectFont('./fonts/Helvetica');
        }
        if ($width <= 0) {
            // error, pretend it printed ok, otherwise risking a loop
            return '';
        }
        $w          = 0;
        $break      = 0;
        $breakWidth = 0;
        $len        = mb_strlen($text);
        $cf         = $this->currentFont;
        $tw         = $width / $size * 1000;
        for ($i = 0; $i < $len; ++$i) {
            $f         = 1;
            $directive = $this->privCheckTextDirective($text, $i, $f);
            if ($directive) {
                if ($f) {
                    $this->setCurrentFont();
                    $cf = $this->currentFont;
                }
                $i = $i + $directive - 1;
            } else {
                $cOrd  = \ord($text[$i]);
                $cOrd2 = $this->fonts[$cf]['differences'][$cOrd] ?? $cOrd;

                if (isset($this->fonts[$cf]['C'][$cOrd2]['WX'])) {
                    $w += $this->fonts[$cf]['C'][$cOrd2]['WX'];
                }
                if ($w > $tw) {
                    // then we need to truncate this line
                    if ($break > 0) {
                        // then we have somewhere that we can split :)
                        if (' ' === $text[$break]) {
                            $tmp = mb_substr($text, 0, $break);
                        } else {
                            $tmp = mb_substr($text, 0, $break + 1);
                        }
                        $adjust = 0;
                        $this->privAdjustWrapText($tmp, $breakWidth, $width, $x, $adjust, $justification);

                        // reset the text state
                        $this->currentTextState = $store_currentTextState;
                        $this->setCurrentFont();
                        if (!$test) {
                            $this->addText($x, $y, $size, $tmp, $angle, $adjust);
                        }

                        return mb_substr($text, $break + 1);
                    }
                    // just split before the current character
                    $tmp    = mb_substr($text, 0, $i);
                    $adjust = 0;
                    $ctmp   = $this->fonts[$cf]['differences'][$ctmp] ?? \ord($text[$i]);
                    $tmpw   = ($w - $this->fonts[$cf]['C'][$ctmp]['WX']) * $size / 1000;
                    $this->privAdjustWrapText($tmp, $tmpw, $width, $x, $adjust, $justification);
                    // reset the text state
                    $this->currentTextState = $store_currentTextState;
                    $this->setCurrentFont();
                    if (!$test) {
                        $this->addText($x, $y, $size, $tmp, $angle, $adjust);
                    }

                    return mb_substr($text, $i);
                }
                if ('-' === $text[$i]) {
                    $break      = $i;
                    $breakWidth = $w * $size / 1000;
                }
                if (' ' === $text[$i]) {
                    $break      = $i;
                    $ctmp       = $this->fonts[$cf]['differences'][$ctmp] ?? \ord($text[$i]);
                    $breakWidth = ($w - $this->fonts[$cf]['C'][$ctmp]['WX']) * $size / 1000;
                }
            }
        }
        // then there was no need to break this line
        if ('full' === $justification) {
            $justification = 'left';
        }
        $adjust = 0;
        $tmpw   = $w * $size / 1000;
        $this->privAdjustWrapText($text, $tmpw, $width, $x, $adjust, $justification);
        // reset the text state
        $this->currentTextState = $store_currentTextState;
        $this->setCurrentFont();
        if (!$test) {
            $this->addText($x, $y, $size, $text, $angle, $adjust);
        }

        return '';
    }

    /**
     * this will be called at a new page to return the state to what it was on the
     * end of the previous page, before the stack was closed down
     * This is to get around not being able to have open 'q' across pages
     * @param int $pageEnd
     */
    public function saveState($pageEnd = 0): void
    {
        if ($pageEnd) {
            // this will be called at a new page to return the state to what it was on the
            // end of the previous page, before the stack was closed down
            // This is to get around not being able to have open 'q' across pages
            $opt = $this->stateStack[$pageEnd]; // ok to use this as stack starts numbering at 1
            $this->setColor($opt['col']['r'], $opt['col']['g'], $opt['col']['b'], 1);
            $this->setStrokeColor($opt['str']['r'], $opt['str']['g'], $opt['str']['b'], 1);
            $this->objects[$this->currentContents]['c'] .= "\n" . $opt['lin'];
            //    $this->currentLineStyle = $opt['lin'];
        } else {
            $this->nStateStack++;
            $this->stateStack[$this->nStateStack] = [
                'col' => $this->currentColour,
                'str' => $this->currentStrokeColour,
                'lin' => $this->currentLineStyle,
            ];
        }
        $this->objects[$this->currentContents]['c'] .= "\nq";
    }

    /**
     * restore a previously saved state
     * @param int $pageEnd
     */
    public function restoreState($pageEnd = 0): void
    {
        if (!$pageEnd) {
            $n                                          = $this->nStateStack;
            $this->currentColour                        = $this->stateStack[$n]['col'];
            $this->currentStrokeColour                  = $this->stateStack[$n]['str'];
            $this->objects[$this->currentContents]['c'] .= "\n" . $this->stateStack[$n]['lin'];
            $this->currentLineStyle                     = $this->stateStack[$n]['lin'];
            unset($this->stateStack[$n]);
            $this->nStateStack--;
        }
        $this->objects[$this->currentContents]['c'] .= "\nQ";
    }

    /**
     * make a loose object, the output will go into this object, until it is closed, then will revert to
     * the current one.
     * this object will not appear until it is included within a page.
     * the function will return the object number
     */
    public function openObject(): int
    {
        $this->nStack++;
        $this->stack[$this->nStack] = ['c' => $this->currentContents, 'p' => $this->currentPage];
        // add a new object of the content type, to hold the data flow
        $this->numObj++;
        $this->oContents($this->numObj, 'new');
        $this->currentContents             = $this->numObj;
        $this->looseObjects[$this->numObj] = 1;

        return $this->numObj;
    }

    /**
     * open an existing object for editing
     * @param $id
     */
    public function reopenObject($id): void
    {
        $this->nStack++;
        $this->stack[$this->nStack] = ['c' => $this->currentContents, 'p' => $this->currentPage];
        $this->currentContents      = $id;
        // also if this object is the primary contents for a page, then set the current page to its parent
        if (isset($this->objects[$id]['onPage'])) {
            $this->currentPage = $this->objects[$id]['onPage'];
        }
    }

    /**
     * close an object
     */
    public function closeObject(): void
    {
        // close the object, as long as there was one open in the first place, which will be indicated by
        // an objectId on the stack.
        if ($this->nStack > 0) {
            $this->currentContents = $this->stack[$this->nStack]['c'];
            $this->currentPage     = $this->stack[$this->nStack]['p'];
            $this->nStack--;
            // easier to probably not worry about removing the old entries, they will be overwritten
            // if there are new ones.
        }
    }

    /**
     * stop an object from appearing on pages from this point on
     * @param $id
     */
    public function stopObject($id): void
    {
        // if an object has been appearing on pages up to now, then stop it, this page will
        // be the last one that could contian it.
        if (isset($this->addLooseObjects[$id])) {
            $this->addLooseObjects[$id] = '';
        }
    }

    /**
     * after an object has been created, it wil only show if it has been added, using this function.
     * @param        $id
     * @param string $options
     */
    public function addObject($id, $options = 'add'): void
    {
        // add the specified object to the page
        if ($this->currentContents !== $id && isset($this->looseObjects[$id])) {
            // then it is a valid object, and it is not being added to itself
            switch ($options) {
                case 'all':
                    // then this object is to be added to this page (done in the next block) and
                    // all future new pages.
                    $this->addLooseObjects[$id] = 'all';
                    break;
                case 'add':
                    if (isset($this->objects[$this->currentContents]['onPage'])) {
                        // then the destination contents is the primary for the page
                        // (though this object is actually added to that page)
                        $this->oPage($this->objects[$this->currentContents]['onPage'], 'content', $id);
                    }
                    break;
                case 'even':
                    $this->addLooseObjects[$id] = 'even';
                    $pageObjectId               = $this->objects[$this->currentContents]['onPage'];
                    if (0 === $this->objects[$pageObjectId]['info']['pageNum'] % 2) {
                        $this->addObject($id); // hacky huh :)
                    }
                    break;
                case 'odd':
                    $this->addLooseObjects[$id] = 'odd';
                    $pageObjectId               = $this->objects[$this->currentContents]['onPage'];
                    if (1 === $this->objects[$pageObjectId]['info']['pageNum'] % 2) {
                        $this->addObject($id); // hacky huh :)
                    }
                    break;
                case 'next':
                    $this->addLooseObjects[$id] = 'all';
                    break;
                case 'nexteven':
                    $this->addLooseObjects[$id] = 'even';
                    break;
                case 'nextodd':
                    $this->addLooseObjects[$id] = 'odd';
                    break;
            }
        }
    }

    /**
     * add content to the documents info object
     * @param     $label
     * @param int $value
     */
    public function addInfo($label, $value = 0): void
    {
        // this will only work if the label is one of the valid ones.
        // modify this so that arrays can be passed as well.
        // if $label is an array then assume that it is key=>value pairs
        // else assume that they are both scalar, anything else will probably error
        if (\is_array($label)) {
            foreach ($label as $l => $v) {
                $this->oInfo($this->infoObject, $l, $v);
            }
        } else {
            $this->oInfo($this->infoObject, $label, $value);
        }
    }

    /**
     * set the viewer preferences of the document, it is up to the browser to obey these.
     * @param     $label
     * @param int $value
     */
    public function setPreferences($label, $value = 0): void
    {
        // this will only work if the label is one of the valid ones.
        if (\is_array($label)) {
            foreach ($label as $l => $v) {
                $this->oCatalog($this->catalogId, 'viewerPreferences', [$l => $v]);
            }
        } else {
            $this->oCatalog($this->catalogId, 'viewerPreferences', [$label => $value]);
        }
    }

    /**
     * extract an integer from a position in a byte stream
     *
     * @param $data
     * @param $pos
     * @param $num
     */
    private function privGetBytes($data, $pos, $num): int
    {
        // return the integer represented by $num bytes from $pos within $data
        $ret = 0;
        for ($i = 0; $i < $num; ++$i) {
            //            $ret = $ret * 256;
            $ret *= 256;
            $ret += \ord($data[$pos + $i]);
        }

        return $ret;
    }

    /**
     * add a PNG image into the document, from a file
     * this should work with remote files
     * @param     $file
     * @param     $x
     * @param     $y
     * @param int $w
     * @param int $h
     */
    public function addPngFromFile($file, $x, $y, $w = 0, $h = 0): void
    {
        // read in a png file, interpret it, then add to the system
        $error = 0;
        //        $tmp   = @get_magic_quotes_runtime();
        //        @set_magic_quotes_runtime(0);
        //        $fp = @fopen($file, 'rb');
        if (false !== ($fp = \fopen($file, 'rb'))) {
            $data = '';
            while (!\feof($fp)) {
                $data .= \fread($fp, 1024);
            }
            \fclose($fp);
        } else {
            $error    = 1;
            $errormsg = 'trouble opening file: ' . $file;
        }
        //        @set_magic_quotes_runtime($tmp);

        if (!$error) {
            $header = \chr(137) . \chr(80) . \chr(78) . \chr(71) . \chr(13) . \chr(10) . \chr(26) . \chr(10);
            if (0 !== mb_strpos($data, $header)) {
                $error    = 1;
                $errormsg = 'this file does not have a valid header';
            }
        }

        if (!$error) {
            // set pointer
            $p   = 8;
            $len = mb_strlen($data);
            // cycle through the file, identifying chunks
            $haveHeader = 0;
            $info       = [];
            $idata      = '';
            $pdata      = '';
            while ($p < $len) {
                $chunkLen  = $this->privGetBytes($data, $p, 4);
                $chunkType = mb_substr($data, $p + 4, 4);
                //      echo $chunkType.' - '.$chunkLen.'<br>';

                switch ($chunkType) {
                    case 'IHDR':
                        // this is where all the file information comes from
                        $info['width']             = $this->privGetBytes($data, $p + 8, 4);
                        $info['height']            = $this->privGetBytes($data, $p + 12, 4);
                        $info['bitDepth']          = \ord($data[$p + 16]);
                        $info['colorType']         = \ord($data[$p + 17]);
                        $info['compressionMethod'] = \ord($data[$p + 18]);
                        $info['filterMethod']      = \ord($data[$p + 19]);
                        $info['interlaceMethod']   = \ord($data[$p + 20]);
                        //print_r($info);
                        $haveHeader = 1;
                        if (0 !== $info['compressionMethod']) {
                            $error    = 1;
                            $errormsg = 'unsupported compression method';
                        }
                        if (0 !== $info['filterMethod']) {
                            $error    = 1;
                            $errormsg = 'unsupported filter method';
                        }
                        break;
                    case 'PLTE':
                        $pdata .= mb_substr($data, $p + 8, $chunkLen);
                        break;
                    case 'IDAT':
                        $idata .= mb_substr($data, $p + 8, $chunkLen);
                        break;
                    case 'tRNS':
                        //this chunk can only occur once and it must occur after the PLTE chunk and before IDAT chunk
                        //print "tRNS found, color type = ".$info['colorType']."<br>";
                        $transparency = [];
                        if (3 === $info['colorType']) { // indexed color, rbg
                            /* corresponding to entries in the plte chunk
          Alpha for palette index 0: 1 byte
          Alpha for palette index 1: 1 byte
          ...etc...
          */ // there will be one entry for each palette entry. up until the last non-opaque entry.
                            // set up an array, stretching over all palette entries which will be o (opaque) or 1 (transparent)
                            $transparency['type'] = 'indexed';
                            $numPalette           = mb_strlen($pdata) / 3;
                            $trans                = 0;
                            for ($i = $chunkLen; $i >= 0; $i--) {
                                if (0 === \ord($data[$p + 8 + $i])) {
                                    $trans = $i;
                                }
                            }
                            $transparency['data'] = $trans;
                        } elseif (0 === $info['colorType']) { // grayscale
                            /* corresponding to entries in the plte chunk
          Gray: 2 bytes, range 0 .. (2^bitdepth)-1
          */
                            //            $transparency['grayscale']=$this->privGetBytes($data,$p+8,2); // g = grayscale
                            $transparency['type'] = 'indexed';
                            $transparency['data'] = \ord($data[$p + 8 + 1]);
                        } elseif (2 === $info['colorType']) { // truecolor
                            /* corresponding to entries in the plte chunk
          Red: 2 bytes, range 0 .. (2^bitdepth)-1
          Green: 2 bytes, range 0 .. (2^bitdepth)-1
          Blue: 2 bytes, range 0 .. (2^bitdepth)-1
          */
                            $transparency['r'] = $this->privGetBytes($data, $p + 8, 2); // r from truecolor
                            $transparency['g'] = $this->privGetBytes($data, $p + 10, 2); // g from truecolor
                            $transparency['b'] = $this->privGetBytes($data, $p + 12, 2); // b from truecolor
                        }
                        //unsupported transparency type

                        // KS End new code
                        break;
                    default:
                        break;
                }

                $p += $chunkLen + 12;
            }

            if (!$haveHeader) {
                $error    = 1;
                $errormsg = 'information header is missing';
            }
            if (isset($info['interlaceMethod']) && $info['interlaceMethod']) {
                $error    = 1;
                $errormsg = 'There appears to be no support for interlaced images in pdf.';
            }
        }

        if (!$error && $info['bitDepth'] > 8) {
            $error    = 1;
            $errormsg = 'only bit depth of 8 or less is supported';
        }

        if (!$error) {
            if (2 !== $info['colorType'] && 0 !== $info['colorType'] && 3 !== $info['colorType']) {
                $error    = 1;
                $errormsg = 'transparancey alpha channel not supported, transparency only supported for palette images.';
            } else {
                switch ($info['colorType']) {
                    case 3:
                        $color  = 'DeviceRGB';
                        $ncolor = 1;
                        break;
                    case 2:
                        $color  = 'DeviceRGB';
                        $ncolor = 3;
                        break;
                    case 0:
                        $color  = 'DeviceGray';
                        $ncolor = 1;
                        break;
                }
            }
        }
        if ($error) {
            $this->addMessage('PNG error - (' . $file . ') ' . $errormsg);

            return;
        }
        if (0 === $w) {
            $w = $h / $info['height'] * $info['width'];
        }
        if (0 === $h) {
            $h = $w * $info['height'] / $info['width'];
        }
        //print_r($info);
        // so this image is ok... add it in.
        $this->numImages++;
        $im    = $this->numImages;
        $label = 'I' . $im;
        $this->numObj++;
        //  $this->oImage($this->numObj,'new',array('label'=>$label,'data'=>$idata,'iw'=>$w,'ih'=>$h,'type'=>'png','ic'=>$info['width']));
        $options = [
            'label'            => $label,
            'data'             => $idata,
            'bitsPerComponent' => $info['bitDepth'],
            'pdata'            => $pdata,
            'iw'               => $info['width'],
            'ih'               => $info['height'],
            'type'             => 'png',
            'color'            => $color,
            'ncolor'           => $ncolor,
        ];
        if (null !== $transparency) {
            $options['transparency'] = $transparency;
        }
        $this->oImage($this->numObj, 'new', $options);

        $this->objects[$this->currentContents]['c'] .= "\nq";
        $this->objects[$this->currentContents]['c'] .= "\n" . \sprintf('%.3f', $w) . ' 0 0 ' . \sprintf('%.3f', $h) . ' ' . \sprintf('%.3f', $x) . ' ' . \sprintf('%.3f', $y) . ' cm';
        $this->objects[$this->currentContents]['c'] .= "\n/" . $label . ' Do';
        $this->objects[$this->currentContents]['c'] .= "\nQ";
    }

    /**
     * add a JPEG image into the document, from a file
     * @param     $img
     * @param     $x
     * @param     $y
     * @param int $w
     * @param int $h
     */
    public function addJpegFromFile($img, $x, $y, $w = 0, $h = 0): void
    {
        // attempt to add a jpeg image straight from a file, using no GD commands
        // note that this function is unable to operate on a remote file.

        if (!\is_file($img)) {
            return;
        }

        $tmp         = \getimagesize($img);
        $imageWidth  = $tmp[0];
        $imageHeight = $tmp[1];

        $channels = $tmp['channels'] ?? 3;

        if ($w <= 0 && $h <= 0) {
            $w = $imageWidth;
        }
        if (0 === $w) {
            $w = $h / $imageHeight * $imageWidth;
        }
        if (0 === $h) {
            $h = $w * $imageHeight / $imageWidth;
        }

        $fp = \fopen($img, 'rb');

        //        $tmp = @get_magic_quotes_runtime();
        //        @set_magic_quotes_runtime(0);
        $data = \fread($fp, \filesize($img));
        //        @set_magic_quotes_runtime($tmp);

        \fclose($fp);

        $this->addJpegImage_common($data, $x, $y, $w, $h, $imageWidth, $imageHeight, $channels);
    }

    /**
     * add an image into the document, from a GD object
     * this function is not all that reliable, and I would probably encourage people to use
     * the file based functions
     * @param     $img
     * @param     $x
     * @param     $y
     * @param int $w
     * @param int $h
     * @param int $quality
     */
    public function addImage($img, $x, $y, $w = 0, $h = 0, $quality = 75): void
    {
        // add a new image into the current location, as an external object
        // add the image at $x,$y, and with width and height as defined by $w & $h

        // note that this will only work with full colour images and makes them jpg images for display
        // later versions could present lossless image formats if there is interest.

        // there seems to be some problem here in that images that have quality set above 75 do not appear
        // not too sure why this is, but in the meantime I have restricted this to 75.
        if ($quality > 75) {
            $quality = 75;
        }

        // if the width or height are set to zero, then set the other one based on keeping the image
        // height/width ratio the same, if they are both zero, then give up :)
        $imageWidth  = \imagesx($img);
        $imageHeight = \imagesy($img);

        if ($w <= 0 && $h <= 0) {
            return;
        }
        if (0 === $w) {
            $w = $h / $imageHeight * $imageWidth;
        }
        if (0 === $h) {
            $h = $w * $imageHeight / $imageWidth;
        }

        // gotta get the data out of the img..

        // so I write to a temp file, and then read it back.. soo ugly, my apologies.
        $tmpDir  = '/tmp';
        $tmpName = \tempnam($tmpDir, 'img');
        \imagejpeg($img, $tmpName, $quality);
        $fp = \fopen($tmpName, 'rb');

        //        $tmp = @get_magic_quotes_runtime();
        //        @set_magic_quotes_runtime(0);
        //        $fp = @fopen($tmpName, 'rb');
        if (false !== ($fp = \fopen($tmpName, 'rb'))) {
            $data = '';
            while (!\feof($fp)) {
                $data .= \fread($fp, 1024);
            }
            \fclose($fp);
        } else {
            $error    = 1;
            $errormsg = 'trouble opening file';
        }
        //  $data = fread($fp,filesize($tmpName));
        //        @set_magic_quotes_runtime($tmp);
        //  fclose($fp);
        \unlink($tmpName);
        $this->addJpegImage_common($data, $x, $y, $w, $h, $imageWidth, $imageHeight);
    }

    /**
     * common code used by the two JPEG adding functions
     *
     * @param     $data
     * @param     $x
     * @param     $y
     * @param int $w
     * @param int $h
     * @param     $imageWidth
     * @param     $imageHeight
     * @param int $channels
     */
    public function addJpegImage_common($data, $x, $y, $w, $h, $imageWidth, $imageHeight, $channels = 3): void
    {
        // note that this function is not to be called externally
        // it is just the common code between the GD and the file options
        $this->numImages++;
        $im    = $this->numImages;
        $label = 'I' . $im;
        $this->numObj++;
        $this->oImage($this->numObj, 'new', [
            'label'    => $label,
            'data'     => $data,
            'iw'       => $imageWidth,
            'ih'       => $imageHeight,
            'channels' => $channels,
        ]);

        $this->objects[$this->currentContents]['c'] .= "\nq";
        $this->objects[$this->currentContents]['c'] .= "\n" . \sprintf('%.3f', $w) . ' 0 0 ' . \sprintf('%.3f', $h) . ' ' . \sprintf('%.3f', $x) . ' ' . \sprintf('%.3f', $y) . ' cm';
        $this->objects[$this->currentContents]['c'] .= "\n/" . $label . ' Do';
        $this->objects[$this->currentContents]['c'] .= "\nQ";
    }

    /**
     * specify where the document should open when it first starts
     * @param     $style
     * @param int $a
     * @param int $b
     * @param int $c
     */
    public function openHere($style, $a = 0, $b = 0, $c = 0): void
    {
        // this function will open the document at a specified page, in a specified style
        // the values for style, and the required paramters are:
        // 'XYZ'  left, top, zoom
        // 'Fit'
        // 'FitH' top
        // 'FitV' left
        // 'FitR' left,bottom,right
        // 'FitB'
        // 'FitBH' top
        // 'FitBV' left
        $this->numObj++;
        $this->oDestination($this->numObj, 'new', ['page' => $this->currentPage, 'type' => $style, 'p1' => $a, 'p2' => $b, 'p3' => $c]);
        $id = $this->catalogId;
        $this->oCatalog($id, 'openHere', $this->numObj);
    }

    /**
     * create a labelled destination within the document
     * @param     $label
     * @param     $style
     * @param int $a
     * @param int $b
     * @param int $c
     */
    public function addDestination($label, $style, $a = 0, $b = 0, $c = 0): void
    {
        // associates the given label with the destination, it is done this way so that a destination can be specified after
        // it has been linked to
        // styles are the same as the 'openHere' function
        $this->numObj++;
        $this->oDestination($this->numObj, 'new', ['page' => $this->currentPage, 'type' => $style, 'p1' => $a, 'p2' => $b, 'p3' => $c]);
        $id = $this->numObj;
        // store the label->idf relationship, note that this means that labels can be used only once
        $this->destinations[(string)$label] = $id;
    }

    /**
     * define font families, this is used to initialize the font families for the default fonts
     * and for the user to add new ones for their fonts. The default bahavious can be overridden should
     * that be desired.
     * @param        $family
     * @param string $options
     */
    public function setFontFamily($family, $options = ''): void
    {
        if (!\is_array($options)) {
            if ('init' === $family) {
                // set the known family groups
                // these font families will be used to enable bold and italic markers to be included
                // within text streams. html forms will be used... <b></b> <i></i>
                $this->fontFamilies['Helvetica.afm']   = [
                    'b'  => 'Helvetica-Bold.afm',
                    'i'  => 'Helvetica-Oblique.afm',
                    'bi' => 'Helvetica-BoldOblique.afm',
                    'ib' => 'Helvetica-BoldOblique.afm',
                ];
                $this->fontFamilies['Courier.afm']     = [
                    'b'  => 'Courier-Bold.afm',
                    'i'  => 'Courier-Oblique.afm',
                    'bi' => 'Courier-BoldOblique.afm',
                    'ib' => 'Courier-BoldOblique.afm',
                ];
                $this->fontFamilies['Times-Roman.afm'] = [
                    'b'  => 'Times-Bold.afm',
                    'i'  => 'Times-Italic.afm',
                    'bi' => 'Times-BoldItalic.afm',
                    'ib' => 'Times-BoldItalic.afm',
                ];
            }
        } else {
            // the user is trying to set a font family
            // note that this can also be used to set the base ones to something else
            if (mb_strlen($family)) {
                $this->fontFamilies[$family] = $options;
            }
        }
    }

    /**
     * used to add messages for use in debugging
     * @param string $message
     */
    public function addMessage($message): void
    {
        $this->messages .= $message . "\n";
    }

    /**
     * a few functions which should allow the document to be treated transactionally.
     * @param $action
     */
    public function transaction($action): void
    {
        switch ($action) {
            case 'start':
                // store all the data away into the checkpoint variable
                $data             = \get_object_vars($this);
                $this->checkpoint = $data;
                unset($data);
                break;
            case 'commit':
                if (\is_array($this->checkpoint) && isset($this->checkpoint['checkpoint'])) {
                    $tmp              = $this->checkpoint['checkpoint'];
                    $this->checkpoint = $tmp;
                    unset($tmp);
                } else {
                    $this->checkpoint = '';
                }
                break;
            case 'rewind':
                // do not destroy the current checkpoint, but move us back to the state then, so that we can try again
                if (\is_array($this->checkpoint)) {
                    // can only abort if were inside a checkpoint
                    $tmp = $this->checkpoint;
                    foreach ($tmp as $k => $v) {
                        if ('checkpoint' !== $k) {
                            $this->$k = $v;
                        }
                    }
                    unset($tmp);
                }
                break;
            case 'abort':
                if (\is_array($this->checkpoint)) {
                    // can only abort if were inside a checkpoint
                    $tmp = $this->checkpoint;
                    foreach ($tmp as $k => $v) {
                        $this->$k = $v;
                    }
                    unset($tmp);
                }
                break;
        }
    }
}
// end of class
