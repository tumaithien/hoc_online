<?php
/**
 * Created by PhpStorm.
 * User: Binmedia12
 * Date: 9/6/2017
 * Time: 1:46 PM
 */
namespace Learncom\Repositories;
use Phalcon\Mvc\User\Component;
use Learncom\Models\LearnArticle;
use Learncom\Models\LearnType;
class News extends Component
{
    public function find_all_news()
    {
        $type_news = $this->globalVariable->type_news;
        return LearnArticle::query()
            ->where("ar_type_id = '$type_news'")
            ->andwhere("ar_active = 'Y'")
            ->execute();
    }
    public function find_ar_news($ar_keyword)
    {
        $type_news = $this->globalVariable->type_news;
        return LearnArticle::findFirst(
            [
                'ar_type_id = :type_news: AND ar_keyword = :ar_keyword: AND ar_active = :ar_active:',
                "bind" => [
                    "type_news" => $type_news,
                    "ar_keyword" => "$ar_keyword",
                    "ar_active" =>"Y",
                ]
            ]
        );
    }
    public function find_related_ar_news($ar_keyword)
    {
        $type_news = $this->globalVariable->type_news;
        return LearnArticle::find(
            [
                'ar_type_id = :type_news: AND ar_keyword != :ar_keyword: AND ar_active = :ar_active:',
                "bind" => [
                    "type_news" => $type_news,
                    "ar_keyword" => $ar_keyword,
                    "ar_active" => "Y",
                ],
                "limit" => 6
            ]
        );
    }
}
