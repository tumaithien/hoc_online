<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnChapter;
use Learncom\Models\LearnExamGroup;
use Learncom\Models\LearnExamType;
use Learncom\Models\LearnGroup;
use Phalcon\Mvc\User\Component;

class ExamGroup extends Component
{
    public static function findFirstById($id) {
        return LearnExamGroup::findFirst([
            'group_id  = :id:',
            'bind' => ['id' => $id]
        ]);
    }
    public static function findFirstByClassSubjectType($class_id,$subject_id,$type_id) {
        return LearnExamGroup::find([
            'group_class_id  = :class_id: AND group_subject_id  = :subject_id: AND group_type_id  = :type_id:',
            'bind' => ['class_id' => $class_id,'subject_id' => $subject_id,'type_id' => $type_id]
        ]);
    }
    public static function deleteExam($exam_id) {
        $exam_group = LearnExamGroup::findFirst([
            'FIND_IN_SET(:exam_id:,group_data_test)',
            'bind' => ['exam_id' => $exam_id]
        ]);
        $arrId = explode(',',$exam_group->getGroupDataTest());
        foreach ($arrId as $key => $value) {
            if ($value == $exam_id) {
                unset($arrId[$key]);
                break;
            }
        }
        $exam_group->setGroupDataTest(implode(',',$arrId));
        $exam_group->save();
    }
    public static function findFirstByAll($class_id,$subject_id,$exam_subject_id,$chapter_id,$lesson_id,$type_id) {
        return LearnExamGroup::findFirst([
            'group_class_id  = :class_id: AND group_subject_id = :subject_id: AND group_exam_subject_id= :exam_subject_id: AND group_chapter_id= :chapter_id: AND group_lesson_id = :lesson_id: AND group_type_id  = :type_id:',
            'bind' => [
                'class_id' => $class_id,
                'subject_id' => $subject_id,
                'type_id' => $type_id,
                'exam_subject_id' => $exam_subject_id,
                'chapter_id' => $chapter_id,
                'lesson_id' => $lesson_id
            ]
        ]);
    }
    public static function findByExamSubject($exam_subject_id) {
        return LearnExamGroup::find([
            'group_exam_subject_id = :subject_id:',
            'bind' => ['subject_id' => $exam_subject_id]
        ]);
    }
    public static function findByExamChapter($chapter_id) {
        return LearnExamGroup::find([
            'group_chapter_id = :subject_id:',
            'bind' => ['subject_id' => $chapter_id]
        ]);
    }
    public static function findByExamLesson($lesson_id) {
        return LearnExamGroup::find([
            'group_lesson_id = :subject_id:',
            'bind' => ['subject_id' => $lesson_id]
        ]);
    }
    public static function findByExamSubjectAndType($exam_subject_id,$type_id) {
        return LearnExamGroup::find([
            'group_exam_subject_id = :subject_id: AND group_type_id = :type_id:',
            'bind' => ['subject_id' => $exam_subject_id,'type_id' => $type_id]
        ]);
    }
    public static function findByChapterAndType($chapter_id,$type_id) {
        return LearnExamGroup::find([
            'group_chapter_id = :subject_id: AND group_type_id = :type_id:',
            'bind' => ['subject_id' => $chapter_id,'type_id' => $type_id]
        ]);
    }
    public static function findByLessonAndType($lesson_id,$type_id) {
        return LearnExamGroup::find([
            'group_lesson_id = :subject_id: AND group_type_id = :type_id:',
            'bind' => ['subject_id' => $lesson_id,'type_id' => $type_id]
        ]);
    }
}



