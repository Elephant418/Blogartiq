<?php

namespace Tzi\Blog;

class RSSWriter
{

    public $title;
    public $description;
    public $feedURL;
    public $siteURL;
    public $items= [];

    public function output($items=[]) {
        header('Content-Type: application/rss+xml; charset=utf-8');
        $this->items = $items;
        $loader = new \Twig_Loader_Filesystem(__DIR__);
        $twig = new \Twig_Environment($loader);
        $view = $twig->loadTemplate('RSSWriter.twig');
        echo $view->render(['rss'=>$this]);
    }
}




