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
            $total = LearnVideo::count([]);
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
    public static function findHomeVideo($arrSubject, $arrClass)
    {
        $cache = new CacheRepo("vieo_findHomeVideo13");
        $data = $cache->getCache();
        $image = "";
        if (!$data) {
            $data = [];
            foreach ($arrSubject as $subject) {
                $class_end = 0;
                $id = 0;
                foreach ($arrClass as $class) {
                    $dataTem = LearnVideo::findFirst([
                        'video_subject_id = :subject_id: AND  video_class_id = :class_id: ',
                        'bind' => [
                            'subject_id' => $subject['subject_id'],
                            'class_id' => $class['class_id']
                        ],
                        'order' => 'video_insert_time DESC, video_order ASC'
                    ]);
                    if ($dataTem) {
                        $class_end = $class['class_id'];
                        $id = $dataTem->toArray()['video_id'];
                        $data[$subject['subject_id']][$class['class_id']] = $dataTem->toArray();
                        $data[$subject['subject_id']][$class['class_id']]['link_image'] = $image = self::addImageByNameClass($class['class_name'], $subject['subject_name']);
                    }
                }
                if (count($data[$subject['subject_id']]) < 4 && $class_end) {
                    $dataTem = LearnVideo::findFirst([
                        'video_subject_id = :subject_id: AND  video_class_id = :class_id: AND video_id != :id:',
                        'bind' => [
                            'subject_id' => $subject['subject_id'],
                            'class_id' => $class_end,
                            'id' => $id
                        ],
                        'order' => 'video_insert_time DESC, video_order ASC'
                    ]);
                    if ($dataTem) {
                        $data[$subject['subject_id']][999] = $dataTem->toArray();
                        $data[$subject['subject_id']][999]['link_image'] = $image;
                    }
                }
            }

            $data = $cache->setCache($data);
        }
        return $data;
    }
    private static function addImageByNameClass($class_name, $subject_name)
    {
        $c_s = $class_name . $subject_name;
        $c_s = strtolower($c_s);
        $image = "/frontend/images/image-subject/";
        switch (true) {
            case strpos($c_s, "hóa"):
                $image .= "hoa";
                break;
            case strpos($c_s, "lí"):
                $image .= "ly";
                break;
            case strpos($c_s, "toán"):
                $image .= "toan";
                break;
        }
        switch (true) {
            case strpos($c_s, "10"):
                $image .= "-10";
                break;
            case strpos($c_s, "11"):
                $image .= "-11";
                break;
            case strpos($c_s, "12"):
                $image .= "-12";
                break;
            default:
                $image = "/frontend/images/image-subject/default";
        }
        return $image . ".jpg";
    }
}
