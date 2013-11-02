<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Blogartiq\Stack;

class View extends View\__Parent
{


    /* OVERRIDABLE METHODS
     *************************************************************************/
    protected function addVariables()
    {
        $this['blog'] = (new \Stack\Setting)->parse('Blog');
        $pages = (new \Stack\Entity\Page)->fetchAll();
        usort($pages, function($a, $b){
            return $b->order - $a->order;
        });
        $this['pages'] = $pages;
    }
}
