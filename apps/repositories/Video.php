<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnClass;
use Learncom\Models\LearnSubject;
use Learncom\Models\LearnVideo;
use Phalcon\Mvc\User\Component;
use \Learncom\Models\LearnArticle;

class Video extends Component
{
    public static function count()
    {
        $cache = new CacheRepo("video_count_video");
        $total = $cache->getCache();
        if (!$total) {
            $total = LearnVideo::count([
            ]);
            $total = $cache->setCache($total);
        }
        return $total;

    }
    public static function findFirstById($id)
    {
        return LearnVideo::findFirst([
            'video_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }

    public static function findByClassAndSubjectAndTypeAndChapter($class_id, $subject_id, $type, $chapter_id)
    {
        return LearnVideo::find([
            'video_class_id = :class_id: AND video_subject_id = :subject: AND video_active = "Y" AND video_type = :type: AND video_chapter_id = :chapter_id:',
            'bind' => ['class_id' => $class_id, 'subject' => $subject_id, 'type' => $type, 'chapter_id' => $chapter_id],
            'order' => 'video_order ASC'
        ]);
    }

    public static function findByClassAndSubjectAndTypeAndGroup($class_id, $subject_id, $type, $group_id)
    {
        return LearnVideo::find([
            'video_class_id = :class_id: AND video_subject_id = :subject: AND video_active = "Y" AND video_type = :type: AND video_group_id = :group_id:',
            'bind' => ['class_id' => $class_id, 'subject' => $subject_id, 'type' => $type, 'group_id' => $group_id],
            'order' => 'video_order ASC'
        ]);
    }
    public static function findAndCache($class_id, $subject_id, $type, $group_id)
    {
        $key = "video_findByClassAndSubjectAndTypeAndGroupAndCache_{$class_id}_{$subject_id}_{$type}_{$group_id}";
        $cache = new CacheRepo($key);
        $data = $cache->getCache();
        if (empty($data)) {
            $model = LearnVideo::find([
                'video_class_id = :class_id: AND video_subject_id = :subject: AND video_active = "Y" AND video_type = :type: AND video_group_id = :group_id:',
                'bind' => ['class_id' => $class_id, 'subject' => $subject_id, 'type' => $type, 'group_id' => $group_id],
                'order' => 'video_order ASC'
            ]);
            $data = $cache->setCache($model->toArray());
        }
        return $data;
    }
    public static function findHomeVideo($subject_id, $arrClass)
    {
        $cache = new CacheRepo("vieo_findHomeVideo4", 1);
        $data = $cache->getCache();
        if (!$data) {
            $data = LearnVideo::find([
                'video_subject_id = :subject_id: AND  FIND_IN_SET(video_class_id,:arrClassId:)',
                'bind' => [
                    'subject_id' => $subject_id,
                    'arrClassId' => implode(",",array_column($arrClass,"class_id"))
                ],
                'order' => 'video_subject_id DESC,video_insert_time DESC, video_order ASC'
            ])->toArray();
            $data = $cache->setCache($data);
        }
        return $data;
    }
}