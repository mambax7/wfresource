<?php

/* HTMLCleaner 1.2 [stable] (c) 2007-2013 Lucian Sabo
  HTML source code cleaner (great help for cleaning MS Word content)
  luciansabo@gmail.com

  Contributors:
  Nadir Boussoukaia <boussou@gmail.com>

  Licenced under Creative Commons Attribution-Noncommercial-Share Alike 3.0 Unported (http://creativecommons.org/licenses/by-nc-sa/3.0/)
  for personal, non-commercial use
  -------- */
define('TAG_WHITELIST', 0);
define('TAG_BLACKLIST', 1);
define('ATTRIB_WHITELIST', 0);
define('ATTRIB_BLACKLIST', 1);

error_reporting(0);

/**
 * Class wfp_Htmltidy
 */
class wfp_Htmltidy
{
    public $options;
    public $tag_whitelist = '<table><tbody><thead><tfoot><tr><th><td><colgroup><col>
        <p><br><hr><blockquote>
        <b><i><u><sub><sup><strong><em><tt><var>
        <code><xmp><cite><pre><abbr><acronym><address><samp>
        <fieldset><legend>
        <a><img>
        <h1><h2><h3><h4><h4><h5><h6>
        <ul><ol><li><dl><dt>
        <frame><frameset>
        <form><input><select><option><optgroup><button><textarea>';
    // add <html><head><meta><title> to generate proper page
    // don't forget to remove <body strip below

    public $attrib_blacklist = 'id|on[\w]+';
    public $cleanUpTags      = array(
        'a',
        'span',
        'b',
        'i',
        'u',
        'strong',
        'em',
        'big',
        'small',
        'tt',
        'var',
        'code',
        'xmp',
        'cite',
        'pre',
        'abbr',
        'acronym',
        'address',
        'q',
        'samp',
        'sub',
        'sup'
    ); //array of inline tags that can be merged
    public $tidyConfig;
    public $encoding         = 'utf8';
    public $version          = '1.2';

    /**
     *
     */
    public function __construct()
    {
        $this->options = array(
            'RemoveStyles'        => true, // removes style definitions like style and class
            'IsWord'              => true, // Microsoft Word flag - specific operations may occur
            'UseTidy'             => true, // uses the tidy engine also to cleanup the source (reccomended)
            'TidyBefore'          => true, // apply Tidy first (not reccomended as tidy messes up sometimes legitimate spaces
            'CleaningMethod'      => array(TAG_WHITELIST, ATTRIB_BLACKLIST), // cleaning methods
            'OutputXHTML'         => true, // converts to XHTML by using TIDY.
            'FillEmptyTableCells' => true, // fills empty cells with non-breaking spaces
            'DropEmptyParas'      => true, // drops empty paragraphs
            'Optimize'            => true, // Optimize code - merge tags
            'Compress'            => true
        ); //trims all spaces (line breaks, tabs) between tags and between words.

        // Specify TIDY configuration
        $this->tidyConfig = array(
            'indent'                      => true, // a bit slow
            'fix-bad-comments'            => false, //from Catzwolf?
            'output-xhtml'                => true, //Outputs the data in XHTML format
            'word-2000'                   => false, //Removes all proprietary data when an MS Word document has been saved as HTML
            'bare'                        => false, //from Catzwolf?
            //'clean'                       => true, /*too slow*/
            'drop-proprietary-attributes' => true, //Removes all attributes that are not part of a web standard
            'drop-empty-paras'            => true, //from Catzwolf?
            'hide-comments'               => true, //Strips all comments
            'preserve-entities'           => false, // preserve the well-formed entitites as found in the input
            'quote-ampersand'             => true, //output unadorned & characters as &amp;.
            'show-body-only'              => true,
            'wrap'                        => 200
        ); //Sets the number of characters allowed before a line is soft-wrapped
    }

    /**
     * -----------------------------------------------------------------------------
     * @param $attribs
     */
    public function removeBlacklistedAttributes($attribs)
    {
        // the attribute _must_ have a line-break or a space before
        $this->html = preg_replace('/[\s]+(' . $attribs . ')=[\s]*("[^"]*"|\'[^\']*\')/i', '', $this->html); //double and single quoted
        $this->html = preg_replace('/[\s]+(' . $attribs . ')=[\s]*[^ |^>]*/i', '', $this->html);  //not quoted
    }

    /* ----------------------------------------------------------------------------- */

    public function tidyClean()
    {
        if (!class_exists('tidy')) {
            if (function_exists('tidy_parse_string')) {
                //use procedural style for compatibility with PHP 4.3
                tidy_set_encoding($this->encoding);

                foreach ($this->tidyConfig as $key => $value) {
                    tidy_setopt($key, $value);
                }

                tidy_parse_string($this->html, $array);
                tidy_clean_repair();
                $this->html = tidy_get_output();
            } else {
                print("<b>No tidy support. Please enable it in your php.ini.\r\nOnly basic cleaning is beeing applied\r\n</b>");
            }
        } else {
            //PHP 5 only !!!
            $tidy = new tidy;
            $tidy->parseString($this->html, $this->tidyConfig, $this->encoding);
            $tidy->cleanRepair();
            $this->html = $tidy;
        }
    }

    /**
     * -----------------------------------------------------------------------------
     * @param  string       $encoding
     * @return mixed|string
     */

    public function cleanUp($encoding = null)
    {
        if (!empty($encoding)) {
            $this->encoding = $encoding;
        }

        //++++
        if ($this->options['IsWord']) {
            $this->tidyConfig['word-2000']                   = true;
            $this->tidyConfig['drop-proprietary-attributes'] = true;
        } else {
            $this->tidyConfig['word-2000'] = false;
        }

        //++++
        if ($this->options['OutputXHTML']) {
            $this->options['UseTidy']         = true;
            $this->tidyConfig['output-xhtml'] = true;
        } else {
            $this->tidyConfig['output-xhtml'] = false;
        }

        //++++
        // Tidy
        if ($this->options['UseTidy']) {
            if ($this->options['TidyBefore']) {
                $this->tidyClean();
            }
        }

        // remove escape slashes
        $this->html = stripslashes($this->html);

        //++++
        if ($this->options['CleaningMethod'][0] === TAG_WHITELIST) {
            // trim everything before the body tag right away, leaving possibility for body attributes
            if (preg_match('/<body/i', "$this->html")) {
                $this->html = stristr($this->html, '<body');
            }

            //patch strip_tags bugs with the lt char (ex: <p>1<2</p> shows <p>1</p>)
            // strip tags, still leaving attributes, second variable is allowable tags
            $this->html = strip_tags($this->html, $this->Tag_whitelist);
        }

        //++++
        if ($this->options['RemoveStyles']) {
            //remove class and style definitions from tidied result
            $this->removeBlacklistedAttributes('class|style');
        }

        //++++
        if ($this->options['IsWord']) {
            $this->removeBlacklistedAttributes('lang|[ovwxp]:\w+');
        }

        //++++
        if ($this->options['CleaningMethod'][1] === ATTRIB_BLACKLIST) {
            if (!empty($this->Attrib_blacklist)) {
                $this->removeBlacklistedAttributes($this->Attrib_blacklist);
            }
        }

        //++++
        if ($this->options['Optimize']) {
            //Optimize until nothing can be done for PHP 5, twice for PHP 4
            if ((int)phpversion() >= 5) {
                $repl = 1;
                while ($repl) {
                    $repl = 0;
                    foreach ($this->CleanUpTags as $tag) {
                        $this->html = preg_replace("/<($tag)[^>]*>[\s]*([(&nbsp;)]*)[\s]*<\/($tag)>/i", "\\2", $this->html, -1, $count); //strip empty inline tags (must be on top of merge inline tags)
                        $repl += $count;
                        $this->html = preg_replace("/<\/($tag)[^>]*>[\s]*([(&nbsp;)]*)[\s]*<($tag)>/i", "\\2", $this->html, -1, $count); //merge inline tags
                        $repl += $count;
                    }
                }
            } else {
                //PHP 4
                $repl = 1;
                while ($repl) {
                    $repl = 0;
                    foreach ($this->CleanUpTags as $tag) {
                        $count = preg_match("/<($tag)[^>]*>[\s]*([(&nbsp;)]*)[\s]*<\/($tag)>/i", $this->html);
                        $repl += $count;
                        $this->html = preg_replace("/<($tag)[^>]*>[\s]*([(&nbsp;)]*)[\s]*<\/($tag)>/i", "\\2", $this->html); //strip empty inline tags (must be on top of merge inline tags)

                        $count = preg_match("/<\/($tag)[^>]*>[\s]*([(&nbsp;)]*)[\s]*<($tag)>/i", $this->html);
                        $repl += $count;
                        $this->html = preg_replace("/<\/($tag)[^>]*>[\s]*([(&nbsp;)]*)[\s]*<($tag)>/i", "\\2", $this->html); //merge inline tags
                    }
                }
            } //end php version test
            // drop empty paras after merging tags
            if ($this->options['DropEmptyParas']) {
                $this->html = preg_replace('/<(p|h[1-6]{1})([^>]*)>[\s]*[(&nbsp;)]*[\s]*<\/(p|h[1-6]{1})>/i', "\r\n", $this->html);
            }

            //trim extra spaces only if tidy is not set to indent
            if (!$this->tidyConfig['indent']) {
                $this->html = preg_replace('/([^<>])[\s]+([^<>])/u', "\\1 \\2", $this->html); //trim spaces between words
                $this->html = preg_replace('/[\n|\r|\r\n|][\n|\r|\r\n|]+</i', '<', $this->html); //trim excess spaces before tags
            }
        } // end optimize
        //++++
        // must be on top of    FillEmptyTableCells, because it can strip nbsp enclosed in paras
        if ($this->options['DropEmptyParas'] && !$this->options['Optimize']) {
            $this->html = preg_replace('/<(p|h[1-6]{1})([^>]*)>[\s]*[(&nbsp;)]*[\s]*<\/(p|h[1-6]{1})>/i', "\r\n", $this->html);
        }

        //++++

        if ($this->options['FillEmptyTableCells']) {
            $this->html = preg_replace("/<td([^>]*)>[\s]*<\/td>/u", "<td\\1>&nbsp;</td>", $this->html);
        }

        //++++

        if ($this->options['Compress']) {
            $this->html = preg_replace('/>[\s]{2,}/', '>', $this->html); //trim spaces after tags
            $this->html = preg_replace('/[\s]{2,}<\//', '</', $this->html); //trim spaces before end tags
            $this->html = preg_replace('/[\s]{2,}</', '<', $this->html); //trim spaces before tags
            $this->html = preg_replace('/([^<>])[\s]+([^<>])/u', "\\1 \\2", $this->html); //trim spaces between words
        }

        //++++
        // Tidy
        if ($this->options['UseTidy']) {
            if (!$this->options['TidyBefore']) {
                $this->tidyClean();
            }
        }

        return $this->html;
    }

    //end cleanup
    /* ----------------------------------------------------------------------------- */
}
