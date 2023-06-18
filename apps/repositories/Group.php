<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnChapter;
use Learncom\Models\LearnGroup;
use Phalcon\Mvc\User\Component;

class Group extends Component
{
    public static function findFirstById($id) {
        return LearnGroup::findFirst([
            'group_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }
    public static function getComboboxVideo($group_id,$subject_id,$class_id) {
        if ($subject_id) {
            $data = LearnGroup::find([
                'group_class_id = :class: AND group_subject_id= :subject: AND group_type = "video"',
                'bind' => ['class' => $class_id,'subject' => $subject_id]
            ]);
            $output = '';
            foreach ($data as $value) {
                $selected = '';
                if ($value->getGroupId() == $group_id) {
                    $selected = 'selected';
                }
                $output .= "<option " . $selected . " value='" . $value->getGroupId() . "'>" . $value->getGroupName() . "</option>";
            }
            return $output;
        }
        return "";
    }
    public static function getComboboxTest($group_id,$subject_id,$class_id) {
        if ($subject_id) {
            $data = LearnGroup::find([
                'group_class_id = :class: AND group_subject_id= :subject: AND group_type = "test"',
                'bind' => ['class' => $class_id,'subject' => $subject_id]
            ]);
            $output = '';
            foreach ($data as $value) {
                $selected = '';
                if ($value->getGroupId() == $group_id) {
                    $selected = 'selected';
                }
                $output .= "<option " . $selected . " value='" . $value->getGroupId() . "'>" . $value->getGroupName() . "</option>";
            }
            return $output;
        }
        return "";
    }
    public static function getNameById($id) {
        $model = self::findFirstById($id);
        return $model ? $model->getGroupName() : "";
    }

    public static function findByType($class_id,$subject_id,$type) {
        return  LearnGroup::find([
            'group_type = :type: AND group_class_id = :class_id: AND group_subject_id = :subject:',
            'bind' => ['type' => $type,
                'class_id' => $class_id,
                'subject' => $subject_id
            ],
            'order' => 'group_order ASC'
        ]);
    }
    public static function getComboboxType($type) {
        $data = ['test' => "Trắc Nghiệm",'video' => 'Khóa Học'];
        $output = '';
        foreach ($data as $value =>$item) {
            $selected = '';
            if ($value == $type) {
                $selected = 'selected';
            }
            $output .= "<option " . $selected . " value='" . $value . "'>" . $item. "</option>";
        }
        return $output;
    }

    public static function findByClassAndSubject($class_id,$subject_id,$type="video") {
        return LearnGroup::find([
            'group_class_id = :class_id: AND group_subject_id = :subject: AND group_active = "Y" AND group_type = :type:',
            'bind' => ['class_id' => $class_id,'subject'=>$subject_id,'type'=>$type],
            'order' => 'group_order ASC'
        ]);
    }
    public static function findByClassAndSubjectAndCache($class_id,$subject_id,$type="video") {
        $key = "group_findByClassAndSubjectAndCache_{$class_id}_{$subject_id}";
        $cache = new CacheRepo($key);
        $data = $cache->getCache();
        if (!$data) {
            $models =  LearnGroup::find([
                'group_class_id = :class_id: AND group_subject_id = :subject: AND group_active = "Y" AND group_type = :type:',
                'bind' => ['class_id' => $class_id,'subject'=>$subject_id,'type'=>$type],
                'order' => 'group_order ASC'
            ])->toArray();
            $data = $cache->setCache($models);
        }
        return $data;
    }
}



