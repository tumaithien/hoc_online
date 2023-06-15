<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnArticle;
use Phalcon\Mvc\User\Component;
class Article extends Component
{
    public static function findFirstById($id) {
        return LearnArticle::findFirst([
            'article_id = :id: AND article_active = "Y"',
            'bind' => ['id' => $id]
        ]);
    }

    public function getByKey($keyword){
        return LearnArticle::findFirst([
            'article_keyword = :keyword: AND article_active = "Y"',
            'bind' => ['keyword' => $keyword]
        ]);
    }

    public function getRelatedArticle($id){
        return LearnArticle::query()
            ->where("article_active = 'Y'")
            ->andWhere("article_id != '$id'")
            ->orderBy('article_order ASC')
            ->limit(3)
            ->execute();
    }

    public function getHomeArticle(){
        return LearnArticle::query()
            ->where("article_active = 'Y'")
            ->orderBy('article_order ASC')
            ->limit(3)
            ->execute();
    }
}



