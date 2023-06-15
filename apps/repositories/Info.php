<?php

namespace Learncom\Repositories;

use Phalcon\Mvc\User\Component;
use \Learncom\Models\LearnArticle;

class Info extends Component
{

    public function find_ar($key)
	{
		$type_info  = $this->globalVariable->type_info;
		return LearnArticle::query()
            ->where("ar_type_id = '$type_info'")
			->andWhere("ar_active = 'Y'")
			->andWhere("ar_keyword = '$key'")
            ->execute();
	}

    public function find_ar_aboutus($ar_keyword)
    {
        $type_about = $this->globalVariable->type_about;
        return LearnArticle::findFirst(
            [
                'ar_type_id = :type_aboutus: AND ar_keyword = :ar_keyword: AND ar_active = :ar_active:',
                "bind" => [
                    "type_aboutus" => $type_about,
                    "ar_keyword" => "$ar_keyword",
                    "ar_active" =>"Y",
                ]
            ]
        );
    }
    public function find_related_aboutus($ar_keyword)
    {
        $type_about = $this->globalVariable->type_about;
        return LearnArticle::find(
            [
                'ar_type_id = :type_aboutus: AND ar_keyword != :ar_keyword: AND ar_active = :ar_active:',
                "bind" => [
                    "type_aboutus" => $type_about,
                    "ar_keyword" => $ar_keyword,
                    "ar_active" => "Y",
                ]
            ]
        );
    }

	
}



