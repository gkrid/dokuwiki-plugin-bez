<?php

class renderer_plugin_bez_xhtmlmail extends Doku_Renderer_xhtml {

    /** @var array Settings, control the behavior of the renderer */
    public $info = array(
        'cache' => true, // may the rendered result cached?
        'toc'   => true, // render the TOC?
        'img'   => array(), //images to attach in mail
    );

    /**
     * Our own format
     *
     * @return string
     */
    function getFormat() {
        return 'bez_xhtmlmail';
    }

    /**
     * Unique media file cid used by Mailer to identify images
     *
     * @param string $mediaId
     * @param string $rev
     * @param bool   $delimiters wrap emded with '%%'
     * @return string
     */
    private function embed($mediaId, $rev='', $delimiters=false) {
        $embed = $mediaId . $rev;
        if ($delimiters) $embed = '%%' . $embed . '%%';
        return $embed;
    }

    /**
     * Render an internal media file
     *
     * @param string $src       media ID
     * @param string $title     descriptive text
     * @param string $align     left|center|right
     * @param int    $width     width of media in pixel
     * @param int    $height    height of media in pixel
     * @param string $cache     cache|recache|nocache
     * @param string $linking   linkonly|detail|nolink
     * @param bool   $return    return HTML instead of adding to $doc
     * @return void|string writes to doc attribute or returns html depends on $return
     */
    function internalmedia($src, $title = null, $align = null, $width = null,
                           $height = null, $cache = null, $linking = null, $return = false) {
        global $ID;
        list($src, $hash) = explode('#', $src, 2);
        resolve_mediaid(getNS($ID), $src, $exists, $this->date_at, true);

        $noLink = false;
        $render = ($linking == 'linkonly') ? false : true;
        $link   = $this->_getMediaLinkConf($src, $title, $align, $width, $height, $cache, $render);
        if ($exists) {
            $rev = $this->_getLastMediaRevisionAt($src);
            $path = mediaFN($src, $rev);
            list($ext, $mime) = mimetype($src);

            $this->info['img'][] = array(
                'path'  => $path,
                'mime'  => $mime,
                'name'  => $title,
                'embed' => $this->embed($src, $rev)
            );
        }

        list($ext, $mime) = mimetype($src, false);
        if(substr($mime, 0, 5) == 'image' && $render) {
            $link['url'] = ml($src, array('id' => $ID, 'cache' => $cache, 'rev'=>$this->_getLastMediaRevisionAt($src)),
                ($linking == 'direct'), '&amp;', true);
        } elseif(($mime == 'application/x-shockwave-flash' || media_supportedav($mime)) && $render) {
            // don't link movies
            $noLink = true;
        } else {
            // add file icons
            $class = preg_replace('/[^_\-a-z0-9]+/i', '_', $ext);
            $link['class'] .= ' mediafile mf_'.$class;
            $link['url'] = ml($src, array('id' => $ID, 'cache' => $cache , 'rev'=>$this->_getLastMediaRevisionAt($src)),
                              true, '&amp;', true);
            if($exists) $link['title'] .= ' ('.filesize_h(filesize(mediaFN($src))).')';
        }

        if($hash) $link['url'] .= '#'.$hash;

        //markup non existing files
        if(!$exists) {
            $link['class'] .= ' wikilink2';
        }

        //output formatted
        if($return) {
            if($linking == 'nolink' || $noLink) return $link['name'];
            else return $this->_formatLink($link);
        } else {
            if($linking == 'nolink' || $noLink) $this->doc .= $link['name'];
            else $this->doc .= $this->_formatLink($link);
        }
    }

    /**
     * Renders internal and external media
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @param string $src       media ID
     * @param string $title     descriptive text
     * @param string $align     left|center|right
     * @param int    $width     width of media in pixel
     * @param int    $height    height of media in pixel
     * @param string $cache     cache|recache|nocache
     * @param bool   $render    should the media be embedded inline or just linked
     * @return string
     */
    function _media($src, $title = null, $align = null, $width = null,
                    $height = null, $cache = null, $render = true) {

        $ret = '';

        list($ext, $mime) = mimetype($src);
        if(substr($mime, 0, 5) == 'image') {
            // first get the $title
            if(!is_null($title)) {
                $title = $this->_xmlEntities($title);
            } elseif($ext == 'jpg' || $ext == 'jpeg') {
                //try to use the caption from IPTC/EXIF
                require_once(DOKU_INC.'inc/JpegMeta.php');
                $jpeg = new JpegMeta(mediaFN($src));
                if($jpeg !== false) $cap = $jpeg->getTitle();
                if(!empty($cap)) {
                    $title = $this->_xmlEntities($cap);
                }
            }
            if(!$render) {
                // if the picture is not supposed to be rendered
                // return the title of the picture
                if(!$title) {
                    // just show the sourcename
                    $title = $this->_xmlEntities(utf8_basename(noNS($src)));
                }
                return $title;
            }
            //add image tag
            $rev = $this->_getLastMediaRevisionAt($src);
            $ret .= '<img src="' . $this->embed($src, $rev, true) . '"';
            $ret .= ' class="media'.$align.'"';

            if($title) {
                $ret .= ' title="'.$title.'"';
                $ret .= ' alt="'.$title.'"';
            } else {
                $ret .= ' alt=""';
            }

            if(!is_null($width))
                $ret .= ' width="'.$this->_xmlEntities($width).'"';

            if(!is_null($height))
                $ret .= ' height="'.$this->_xmlEntities($height).'"';

            $ret .= ' />';

        } elseif(media_supportedav($mime, 'video') || media_supportedav($mime, 'audio')) {
            // first get the $title
            $title = !is_null($title) ? $this->_xmlEntities($title) : false;
            if(!$render) {
                // if the file is not supposed to be rendered
                // return the title of the file (just the sourcename if there is no title)
                return $title ? $title : $this->_xmlEntities(utf8_basename(noNS($src)));
            }

            $att          = array();
            $att['class'] = "media$align";
            if($title) {
                $att['title'] = $title;
            }

            if(media_supportedav($mime, 'video')) {
                //add video
                $ret .= $this->_video($src, $width, $height, $att);
            }
            if(media_supportedav($mime, 'audio')) {
                //add audio
                $ret .= $this->_audio($src, $att);
            }

        } elseif($mime == 'application/x-shockwave-flash') {
            if(!$render) {
                // if the flash is not supposed to be rendered
                // return the title of the flash
                if(!$title) {
                    // just show the sourcename
                    $title = utf8_basename(noNS($src));
                }
                return $this->_xmlEntities($title);
            }

            $att          = array();
            $att['class'] = "media$align";
            if($align == 'right') $att['align'] = 'right';
            if($align == 'left') $att['align'] = 'left';
            $ret .= html_flashobject(
                ml($src, array('cache' => $cache), true, '&'), $width, $height,
                array('quality' => 'high'),
                null,
                $att,
                $this->_xmlEntities($title)
            );
        } elseif($title) {
            // well at least we have a title to display
            $ret .= $this->_xmlEntities($title);
        } else {
            // just show the sourcename
            $ret .= $this->_xmlEntities(utf8_basename(noNS($src)));
        }

        return $ret;
    }
}