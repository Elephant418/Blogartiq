<?php

namespace Blogartiq\Front\Stack\Controller\Model;

class Article extends Article\__Parent
{

    public function actionView($id) {
        $query = strtolower($id);
        $article = (new \Stack\Entity\Article)->fetchById($query);
        if (! $article->exists()) {
            return null;
        }
        if ($id != $article->id) {
            \Staq\Util::httpRedirect(\Staq\Util::getModelControllerUrl($article), 301);
        }

        $view = $this->createView();
        $view['content'] = $article;
        $view['article'] = $article;
        return $view;
    }
}