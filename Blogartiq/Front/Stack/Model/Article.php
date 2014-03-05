<?php

namespace Blogartiq\Front\Stack\Model;

class Article extends Article\__Parent
{


    /* ATTRIBUTES
     *************************************************************************/
    public $section;
    public $subsection;
    public $shortTitle;

    public $summary = [];
    public $intro;
    public $image;


    /* CONSTRUCTOR
     *************************************************************************/
    protected function initialize()
    {

        /* Id */
        if (substr_count($this->id, '/') == 2) {
            $this->section = \UString::substrBefore($this->id, '/');
            $this->subsection = \UString::substrBeforeLast( \UString::substrAfter($this->id, '/'), '/');
            $this->shortTitle = \UString::substrAfterLast($this->id, '/');
        }

        /* Retreat content */
        if (trim($this->content)) {
            $doc = new \DOMDocument();
            @$doc->loadHTML('<?xml encoding="UTF-8">' . $this->content);

            /* H1 */
            $h1List = $doc->getElementsByTagName('h1');
            if ($h1List->length) {
                $this->title = $h1List->item(0)->textContent;
            }
            foreach($h1List as $h1) {
                $h1->parentNode->removeChild($h1);
            }

            /* H2 */
            $h2List = $doc->getElementsByTagName('h2');
            foreach($h2List as $h2) {
                $title = $h2->textContent;
                if ($h2->hasAttribute('id')) {
                    $id = $h2->getAttribute('id');
                } else {
                    $id = \UString::stripSpecialChar($title);
                    $h2->setAttribute('id', $id);
                }
                $this->summary[$id] = $title;
                $h2->nodeValue = '';
                $link = $doc->createElement('a');
                $link->nodeValue = htmlentities($title);
                $link->setAttribute('href', '#'.$id);
                $h2->appendChild($link);
            }

            /* Image */
            $imageList = $doc->getElementsByTagName('img');
            if ($imageList->length) {
                $image = $imageList->item(0);
                if ($image->hasAttribute('src')) {
                    $this->image = $image->getAttribute('src');
                }
            }

            /* Intro */
            $ps = $doc->getElementsByTagName('p');
            if ($ps->length) {
                $firstP = $ps->item(0);
                $this->intro = $firstP->textContent;
                $this->introHTML = $doc->saveHTML($firstP);
                $firstP->parentNode->removeChild($firstP);
            }

            /* Content */
            $stripHTML = ['/^\<\!DOCTYPE.*?<html><body>/si', '!</body></html>$!si'];
            $this->content = preg_replace($stripHTML, '', $doc->saveHTML());
        }

        if (! $this->label) {
            $this->label = $this->title;
        }
    }
}
