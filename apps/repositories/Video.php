<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnClass;
use Learncom\Models\LearnSubject;
use Learncom\Models\LearnVideo;
use Phalcon\Mvc\User\Component;
use \Learncom\Models\LearnArticle;

class Video extends Component
{
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
            $model =  LearnVideo::find([
                'video_class_id = :class_id: AND video_subject_id = :subject: AND video_active = "Y" AND video_type = :type: AND video_group_id = :group_id:',
                'bind' => ['class_id' => $class_id, 'subject' => $subject_id, 'type' => $type, 'group_id' => $group_id],
                'order' => 'video_order ASC'
            ]);
            $data = $cache->setCache($model->toArray());
        }
        return $data;
    }
}
