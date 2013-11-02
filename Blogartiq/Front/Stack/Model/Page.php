<?php

namespace Blogartiq\Front\Stack\Model;

class Page extends Article
{


    /* ATTRIBUTES
     *************************************************************************/
    public $preTitle;



    /* CONSTRUCTOR
     *************************************************************************/
    protected function initialize()
    {
        parent::initialize();

        if (\UString::isStartWith($this->title, '(')) {
            \UString::doSubstrAfter($this->title, '(');
            $this->preTitle = \UString::substrBefore($this->title, ')');
            \UString::doSubstrAfter($this->title, ')');
        }
    }
}
