<?php

namespace Learncom\Frontend\Controllers;

use Learncom\Models\LearnDgnl;
use Learncom\Models\LearnTypeDgnl;
use Learncom\Repositories\CacheHistory;
use Learncom\Repositories\CacheRepo;
use Learncom\Repositories\Page;

class DgnlController extends ControllerBase
{
    public $video_id;

    public function indexAction()
    {
        $parent_keyword = 'khoahoc';
        $this->type = "video";
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('khoahoc', 'Course');
        $cache = new CacheRepo("list_dgnl");
        $typeDgnl = $cache->getCache();
        if (empty($typeDgnl)) {
            $arType = LearnTypeDgnl::find([
                'order' => "type_order ASC",
            ])->toArray();
            $typeDgnl = [];
            foreach ($arType as $type) {
                $count_video = LearnDgnl::count([
                    'dgnl_type_id = :type_id:',
                    'bind' => [
                        'type_id' => $type['type_id']
                    ]
                ]);
                $count_video = $count_video ?: 0;
                $type['total_video'] = $count_video;
                $typeDgnl[] = $type;
            }
            $cache->setCache($typeDgnl);
        } 

        $this->view->setVars([
            // 'type' => $this->type,
            'parent_keyword' => $parent_keyword,
            'arType' => $typeDgnl,
        ]);
    }
    public function viewAction()
    {
        $video_id = $this->request->get('video_id');

        if (!in_array($this->dgnl_select, $this->dgnl_type_id)) {
            return $this->response->redirect("/permission");
        }
        $key = "log_history_dgnl" . $this->auth['id'];
        $cache = new CacheHistory($key);
        $is_continue = false;
        if (!$video_id) {
            $video_id = $cache->setLogDgnl($this->dgnl_select, $video_id, false);
            if ($video_id) {
                $is_continue = true;
            }
        } else {
            $video_id = $cache->setLogDgnl($this->dgnl_select, $video_id, true);
        }

        $parent_keyword = 'khoahoc';
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('khoahoc', 'Course');

        $result = $this->formatToCouse($video_id);
        if (empty($result)) {
            $this->view->setVars([
                'link' => "",
                'chapters' => [],
                'lessons' => [],
                'video_selected' => $video_id,
            ]);
        } else {
            $this->view->setVars($result);
        }
        $this->view->is_continue = $is_continue;

        $this->view->pick("course/index");

    }
    public function formatToCouse($video_id)
    {
        $cache = new CacheRepo('dgnl_'. $this->dgnl_select);
        $arDgnlVideo = $cache->getCache();
        if (empty($arDgnlVideo)) {
            $arDgnlVideo = LearnDgnl::find([
                'dgnl_type_id = :type:',
                'bind' => [
                    'type' => $this->dgnl_select
                ],
                'order' => "learn_category ASC,dgnl_order ASC"
            ])->toArray();
            $cache->setCache($arDgnlVideo);
        }
   
        $dgnlModel = [];
        if (!$video_id) {
            $video_id = $arDgnlVideo[0]['dgnl_id'] ?? "";
        }

        $dgnlModel = array_filter($arDgnlVideo, function ($dgnl) use ($video_id) {
            return $dgnl['dgnl_id'] == $video_id;
        }, ARRAY_FILTER_USE_BOTH);
        $dgnlModel = reset($dgnlModel);

        $chapters = [];
        $lessons = [];
        if (empty($dgnlModel)) {
            return [];
        }
        $link = $dgnlModel['dgnl_link'] ?? "";
        //format lại: chapter: video/bài giảng, lessons: list video
        $chapters = [
            'video' => [
                'id' => 'video',
                'name' => "Video",
                'active' => $dgnlModel['learn_category'] == "video" ? "active" : ""
            ],
            'khoahoc' => [
                'id' => 'khoahoc',
                'name' => "Bài Giảng",
                'active' => $dgnlModel['learn_category'] == "khoahoc" ? "active" : ""
            ]
        ];
        foreach ($arDgnlVideo as $video) {
            $active_lesson = $video['dgnl_id'] == $video_id ? "active" : "";
            $lessons[$video['learn_category']][] = [
                'id' => $video['dgnl_id'],
                'name' => $video['dgnl_name'],
                'link' => $video['dgnl_link'],
                'active' => $active_lesson,
                'href' => "/dgnl/{$video['dgnl_type_id']}?video_id={$video['dgnl_id']}"
            ];
        }
        return [
            'link' => $link,
            'chapters' => $chapters,
            'lessons' => $lessons,
            'video_selected' => $video_id,
        ];

    }

}