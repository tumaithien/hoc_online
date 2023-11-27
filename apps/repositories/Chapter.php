<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnChapter;
use Phalcon\Mvc\User\Component;

class Chapter extends Component
{
    public static function findFirstById($id)
    {
        return LearnChapter::findFirst([
            'chapter_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }
    public static function findByType($class_id,$subject_id,$type) {
        return  LearnChapter::find([
            'chapter_class_id = :class_id: AND chapter_subject_id = :subject:',
            'bind' => [
                'class_id' => $class_id,
                'subject' => $subject_id
            ],
            'order' => 'chapter_order ASC'
        ]);
    }
    public static function getCombobox($chapter_id, $subject_id, $class_id)
    {
        if ($subject_id) {
            $subject_model = ClassSubject::findFirstBySubjectId($subject_id);
            $parent_id = 0;
            if ($subject_model) {
                $parent_id = $subject_model->getSubjectParentId();

            }
            $data = LearnChapter::find([
                'chapter_class_id = :class: AND (chapter_subject_id= :subject: OR chapter_subject_id = :parent: )',
                'bind' => ['class' => $class_id, 'subject' => $subject_id, 'parent' => $parent_id]
            ]);
            $output = '';
            foreach ($data as $value) {
                $selected = '';
                if ($value->getChapterId() == $chapter_id) {
                    $selected = 'selected';
                }
                $output .= "<option " . $selected . " value='" . $value->getChapterId() . "'>" . $value->getChapterName() . "</option>";
            }
            return $output;
        }
        return "";
    }

    public static function findByClassAndSubject($class_id, $subject_id)
    {
        return LearnChapter::find([
            'chapter_class_id = :class_id: AND chapter_subject_id = :subject: AND chapter_active = "Y"',
            'bind' => ['class_id' => $class_id, 'subject' => $subject_id],
            'order' => 'chapter_order ASC'
        ]);
    }
    public static function findByClassAndSubjectAndCache($class_id, $subject_id)
    {
        $cache = new CacheRepo("Chapter_findByClassAndSubjectAndCache_{$class_id}_{$subject_id}");
        $data = $cache->getCache();
        if (!$data) {
            $data = LearnChapter::find([
                'chapter_class_id = :class_id: AND chapter_subject_id = :subject: AND chapter_active = "Y"',
                'bind' => ['class_id' => $class_id, 'subject' => $subject_id],
                'order' => 'chapter_order ASC'
            ])->toArray();
            $data = $cache->setCache($data);
        }

        return $data;
    }

    public static function getNameById($id)
    {
        $chapter_model = self::findFirstById($id);
        return $chapter_model ? $chapter_model->getChapterName() : "";
    }


}