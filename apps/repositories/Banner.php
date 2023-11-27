<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnBanner;
use Phalcon\Mvc\User\Component;
use Learncom\Repositories\CacheRepo;

class Banner extends Component
{
    // Find Banner in Global
    public function findBanner()
    {
        $cache = new CacheRepo("banner_findbanner");
        $data = $cache->getCache();
        if (!$data) {
            $model = LearnBanner::query()
            ->where("banner_active = 'Y'")
            ->andWhere('banner_type = "mid"')
            ->orderBy('banner_order ASC')
            ->execute()->toArray();
            $data = $cache->setCache($model);
        }
        return $data;
    }
    
    public function findVideoIndex()
    {
        $cache = new CacheRepo("banner_findVideoIndex");
        $data = $cache->getCache();
        if (!$data) {
            $model = LearnBanner::query()
            ->where("banner_active = 'Y'")
            ->andWhere('banner_type = "video"')
            ->orderBy('banner_order ASC')
            ->execute()->toArray();
            $data = $cache->setCache($model);
        }
        return $data;
    }
    public function findById($id){
        return LearnBanner::findFirst("banner_id = $id");
    }

    public static function findBannerTop(){
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



