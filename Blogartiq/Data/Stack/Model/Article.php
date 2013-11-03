<?php

namespace Blogartiq\Data\Stack\Model;

class Article extends Article\__Parent
{


    /* ATTRIBUTES
     *************************************************************************/
    public $title;



    /* CONSTRUCTOR
     *************************************************************************/
    protected function initialize()
    {

        /* Retreat content */
        if (trim($this->content)) {
            $doc = new \DOMDocument();
            $doc->loadHTML('<?xml encoding="UTF-8">' . $this->content);

            /* H1 */
            $h1s = $doc->getElementsByTagName('h1');
            if ($h1s->length) {
                $this->title = $h1s->item(0)->textContent;
            }
        }

        if (! $this->label) {
            $this->label = $this->title;
        }

        parent::initialize();
    }
}
