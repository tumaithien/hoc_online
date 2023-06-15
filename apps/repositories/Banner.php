<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnBanner;
use Phalcon\Mvc\User\Component;

class Banner extends Component
{
    // Find Banner in Global
    public function findBanner()
    {
        return LearnBanner::query()
            ->where("banner_active = 'Y'")
            ->andWhere('banner_type = "mid"')
            ->orderBy('banner_order ASC')
            ->execute();
    }
    public function findById($id){
        return LearnBanner::findFirst("banner_id = $id");
    }

    public function findBannerTop(){
        return LearnBanner::findFirst([
            'banner_active = "Y" AND banner_type = "top"',
            'order' => ['banner_order ASC']
        ]);
    }

    public function findBannerBot(){
        return LearnBanner::findFirst([
            'banner_active = "Y" AND banner_type = "bot"',
            'order' => ['banner_order ASC']
        ]);
    }
	
}



