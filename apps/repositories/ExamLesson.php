<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnClass;
use Learncom\Models\LearnExamLesson;
use Learncom\Models\LearnSubject;
use Learncom\Models\LearnVideo;
use Phalcon\Mvc\User\Component;
use \Learncom\Models\LearnArticle;

class ExamLesson extends Component
{
    public static function findFirstById($id) {
        return LearnExamLesson::findFirst([
            'lesson_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }

    public static function findByClassAndSubjectAndChapter($class_id,$subject_id,$chapter_id) {
        return LearnExamLesson::find([
            'lesson_class_id = :class_id: AND lesson_subject_id = :subject: AND lesson_active = "Y" AND lesson_chapter_id = :chapter_id:',
            'bind' => ['class_id' => $class_id,'subject'=>$subject_id, 'chapter_id' => $chapter_id],
            'order' => 'lesson_order ASC'
        ]);
    }
    public static function getCombobox($lesson_id,$chapter_id,$class_id) {
        $data = LearnExamLesson::find([
            'lesson_class_id = :class: AND lesson_chapter_id= :chapter:',
            'bind' => ['class' => $class_id,'chapter' => $chapter_id]
        ]);
        $output = '';
        foreach ($data as $value) {
            $selected = '';
            if ($value->getLessonId() == $lesson_id) {
                $selected = 'selected';
            }
            $output .= "<option " . $selected . " value='" . $value->getLessonId() . "'>" . $value->getLessonName() . "</option>";
        }
        return $output;
    }
    public static function getNameById($id) {
        $model = self::findFirstById($id);
        return $model ? $model->getLessonName() : "";
    }
}



