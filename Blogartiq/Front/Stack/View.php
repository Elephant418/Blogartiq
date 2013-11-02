<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Blogartiq\Front\Stack;

class View extends View\__Parent
{


    /* OVERRIDABLE METHODS
     *************************************************************************/
    protected function extendTwig()
    {
        parent::extendTwig();

        $formatDateFilter = new \Twig_SimpleFilter('formatDate', function(\DateTime $date) {
            $time = $date->getTimestamp();
            return date('n-j-Y', $time);
        });
        $this->twig->addFilter($formatDateFilter);
    }

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
