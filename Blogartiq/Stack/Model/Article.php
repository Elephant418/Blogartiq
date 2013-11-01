<?php

namespace Blogartiq\Stack\Model;

class Article extends Article\__Parent
{


    /* ATTRIBUTES
     *************************************************************************/
    public $section;
    public $subsection;
    public $shortTitle;

    public $title;
    public $summary = [];
    public $intro;


    /* CONSTRUCTOR
     *************************************************************************/
    protected function initialize()
    {
        /* Date */
        $this->dateTime = strtotime($this->date)+8*60*60;

        /* Id */
        $this->section = \UString::substrBefore($this->id, '/');
        $this->subsection = \UString::substrBeforeLast( \UString::substrAfter($this->id, '/'), '/');
        $this->shortTitle = \UString::substrAfterLast($this->id, '/');

        /* Retreat content */
        if (trim($this->content)) {
            $doc = new \DOMDocument();
            $doc->loadHTML($this->content);

            /* H1 */
            $h1s = $doc->getElementsByTagName('h1');
            if ($h1s->length) {
                $this->title = $h1s->item(0)->textContent;
            }
            foreach($h1s as $h1) {
                $h1->parentNode->removeChild($h1);
            }

            /* H2 */
            $h2s = $doc->getElementsByTagName('h2');
            foreach($h2s as $h2) {
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

            /* Intro */
            $ps = $doc->getElementsByTagName('p');
            if ($ps->length) {
                $this->intro = $ps->item(0)->textContent;
            }

            /* Content */
            $this->content = $doc->saveHTML();
        }

        parent::initialize();
    }
}
