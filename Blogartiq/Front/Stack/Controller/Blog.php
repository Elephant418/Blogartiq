<?php

namespace Blogartiq\Front\Stack\Controller;

class Blog extends Blog\__Parent
{

    public function actionHome() {
        $view = new \Stack\View\Blog\Home();
        $view['home'] = (new \Stack\Entity\Page)->fetchById('home');
        $view['articles'] = $this->getLastArticles();
        return $view;
    }

    public function actionArchive() {
        $view = new \Stack\View\Blog\Archive();
        $view['articles'] = $this->getGroupedArticles();
        return $view;
    }

    public function actionFeed() {
        $setting = (new \Stack\Setting)
            ->parse('Blog')
            ->get('rss');

        $items = [];
        foreach ($this->getLastArticles() as $article) {
            $item = [];
            $item['title'] = $article->label;
            $item['url'] = 'http://' . $_SERVER['SERVER_NAME'] . \Staq\Util::getPublicUrl($article->id);
            $item['date'] = date('r', $article->date->getTimestamp());
            $item['intro'] = $article->intro;
            $item['author'] = $setting['author'];
            $items[] = $item;
        }

        $RSS = new \Blogartiq\Front\RSSWriter();
        $RSS->title = $setting['title'];
        $RSS->description = $setting['description'];
        $RSS->siteURL = 'http://' . $_SERVER['SERVER_NAME'] . \Staq\Util::getControllerUrl($this, 'home');
        $RSS->feedURL = 'http://' . $_SERVER['SERVER_NAME'] . \Staq\Util::getControllerUrl($this, 'feed');
        $RSS->output($items);

        return TRUE;
    }



    /* PROTECTED METHODS
     ***********************************************************/
    protected function getLastArticles($limit=10) {
        $articles = $this->getAllArticles();
        usort($articles, function($a, $b){
            return $b->date->getTimestamp() - $a->date->getTimestamp();
        });
        return array_slice($articles, 0, $limit);
    }

    protected function getGroupedArticles() {
        $articles = $this->getAllArticles();
        $articles = $this->groupArticles($articles, 'section');
        foreach (array_keys($articles) as $section) {
            $articles[$section]['subsections'] = $this->groupArticles($articles[$section]['articles'], 'subsection');
            unset($articles[$section]['articles']);
        }
        return $articles;
    }

    protected function groupArticles($articles, $index) {
        \UArray::doGroupBy($articles, $index);
        foreach ($articles as $key => $groupedArticles) {
            $articles[$key] = [];
            $articles[$key]['id'] = \UString::stripSpecialChar($key);
            $articles[$key]['name'] = $key;
            $articles[$key]['articles'] = $groupedArticles;
        }
        return $articles;
    }

    protected function getAllArticles() {
        $articles = (new \Stack\Entity\Article)->fetchAll();
        $current = time();
        foreach ($articles as $key => $article) {
            if (! $article->date || $article->date->getTimestamp() > $current) {
                unset($articles[$key]);
            }
        }
        return $articles;
    }
}