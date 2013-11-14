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

        $RSS = new \Feedify\Writer();
        $RSS->title = $setting['title'];
        $RSS->description = $setting['description'];
        $RSS->siteURL = 'http://' . $_SERVER['SERVER_NAME'] . \Staq\Util::getControllerUrl($this, 'home');
        $RSS->feedURL = 'http://' . $_SERVER['SERVER_NAME'] . \Staq\Util::getControllerUrl($this, 'feed');
        $RSS->items = $this->getLastArticles();
        $RSS->addAttributeMap('title', 'label');
        $RSS->addAttributeFormatter('url', function($article){
            return 'http://' . $_SERVER['SERVER_NAME'] . \Staq\Util::getPublicUrl($article->id);
        });
        $RSS->addAttribute('date');
        $RSS->addAttribute('intro');
        $RSS->addAttribute('author');
        $RSS->output();

        return TRUE;
    }

    public function actionSiteMap() {
        $siteMap = new \Feedify\Writer();
        $siteMap->addItems($this->getAllPages());
        $siteMap->addItems(array(array('url'=>'http://' . $_SERVER['SERVER_NAME'] . \Staq\Util::getControllerUrl($this, 'archive'))));
        $siteMap->addItems($this->getAllArticles());
        $siteMap->addAttributeFormatter('url', function($article){
            if (isset($article->id)) {
                $path = $article->id;
                if ($path=='index') $path = '';
                return 'http://' . $_SERVER['SERVER_NAME'] . \Staq\Util::getPublicUrl($path);
            }
            if (isset($article['url'])) {
                return $article['url'];
            }
        });
        $siteMap->addAttribute('date');
        $siteMap->output(\Feedify\Writer::SITEMAP_FORMAT);

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

    protected function getAllPages() {
        return (new \Stack\Entity\Page)->fetchAll();
    }
}