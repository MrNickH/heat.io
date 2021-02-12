<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 01/02/19
 * Time: 09:10
 */

namespace Model\Utilities;

use DOMNode;
use Model\General\Log;
use \DOMDocument;


class HTML
{
 public static function sanitizeHTML(String $html, String $itemType = 'unspecified item.', $anyHTMLAllowed = true){
    // Build our DOMDocument, and load our HTML

     $html = mb_convert_encoding($html, 'utf-8', mb_detect_encoding($html));
     // if you have not escaped entities use
     $html = mb_convert_encoding($html, 'html-entities', 'utf-8');
     $doc = new DOMDocument('1.0', 'UTF-8');
     $doc->loadHTML($html, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);

     //Dangerous Atrributes Remove
     self::nodeWalker($doc);

     //Dangerous Tags Remove
     foreach(self::dangerousTags() as $tagName){
         $tagInstances = $doc->getElementsByTagName(strtolower($tagName));
         if($tagInstances->length != 0) {
             foreach($tagInstances as $tag){
                 $tag->parentNode->removeChild($tag);
             }
             Log::LogData('Security', 'User attempted to use dangerous tag "' . $tagName . '" in ' . $itemType);
         }
     }
     $html = $doc->saveHTML();
     $content = trim(strip_tags($html));

     if(!$content && !stristr($html, "<img src=")){
         Log::LogData('Security', 'User attempted to post empty '.$itemType. ".");
         return false;
     }

     return $anyHTMLAllowed ? $html : $content;
 }

 private static function nodeWalker(DOMNode &$domNode){
     foreach ($domNode->childNodes as $node) {
         /**@var $node DOMNode*/
         if($node->hasAttributes()){
             foreach($node->attributes as $attribute){
                if(in_array(strtolower($attribute->name),self::dangerousAttributes())){
                    $node->removeAttribute($attribute->name);
                    Log::LogData('Security', 'User attempted to use danegerous attribute "'.$attribute->name.'" in '.$itemType);
                }
             }
         }
         if($node->hasChildNodes()) {
             self::nodeWalker($node);
         }
     }
 }

 public static function dangerousTags(){
     return [
        "FORM",
        "NOFRAMES",
        "NOSCRIPT",
        "MARQUEE",
        "PLAINTEXT",
        "REPLACE",
        "STYLE",
        "BUTTON",
        "INPUT",
        "TEXTAREA",
        "FRAMESET",
        "SELECT",
        "BLINK",
        "IMAGE",
        "XML",
        "BASE",
        "HTML",
        "HEAD",
        "TITLE",
        "BODY",
        "APPLET",
        "SCRIPT",
        "SVG",
        "OBJECT",
        "EMBED",
        //"IFRAME", -- Allowed for video Embed
        "FRAME",
        "LAYER",
        "ILAYER",
        "META",
        "BGSOUND",
        "LINK",
        "ISINDEX",
        "NEXTID"
     ];
 }


 public static function dangerousAttributes()
 {
     return [
         "background",
         "dynsrc",
         "lowsrc",
         "datasrc",
         "data",
         "srcdoc",
         "onclick",
         "ondblclick",
         "onmousedown",
         "onmousemove",
         "onmouseover",
         "onmouseout",
         "onmouseup",
         "onratechange",
         "onfilterchange",
         "onerror",
         "onanimationstart",
         "onwebkittransitionend",
         "onkeydown",
         "ontoggle",
         "onkeypress",
         "onpageshow",
         "onkeyup",
         "onscroll",
         "onchange",
         "onsubmit",
         "onreset",
         "onselect",
         "onblur",
         "onfocus",
         "onload",
         "onunload",
         "xlink"
     ];
 }
}