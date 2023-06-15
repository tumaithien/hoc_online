<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnChapter;
use Learncom\Models\LearnExam;
use Learncom\Models\LearnExamGroup;
use Learncom\Models\LearnExamType;
use Learncom\Models\LearnGroup;
use Phalcon\Mvc\User\Component;

class Exam extends Component
{
    public static function findFirstById($id) {
        return LearnExam::findFirst([
            'exam_id  = :id:',
            'bind' => ['id' => $id]
        ]);
    }

}



