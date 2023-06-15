<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnExamChapter;
use Phalcon\Mvc\User\Component;

class ExamChapter extends Component
{
    public static function findFirstById($id) {
        return LearnExamChapter::findFirst([
            'chapter_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }
    public static function getCombobox($chapter_id,$subject_id,$class_id) {
        $data = LearnExamChapter::find([
            'chapter_class_id = :class: AND chapter_subject_id= :subject:',
            'bind' => ['class' => $class_id,'subject' => $subject_id]
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

        return "";
    }

    public static function findByClassAndSubject($class_id,$subject_id) {
        return LearnExamChapter::find([
            'chapter_class_id = :class_id: AND chapter_subject_id = :subject: AND chapter_active = "Y"',
            'bind' => ['class_id' => $class_id,'subject'=>$subject_id],
            'order' => 'chapter_order ASC'
        ]);
    }

    public static function getNameById($id) {
        $chapter_model = self::findFirstById($id);
        return $chapter_model ? $chapter_model->getChapterName() : "";
    }


}



