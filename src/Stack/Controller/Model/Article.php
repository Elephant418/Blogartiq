<?php

namespace Blogartiq\Stack\Controller\Model;

class Article extends Article\__Parent
{

    public function actionView($id) {
        $article = (new \Stack\Entity\Article)->fetchById($id);
        if (! $article->exists()) {
            return null;
        }

        $view = $this->createView();
        $view['article'] = $article;
        return $view;
    }
}