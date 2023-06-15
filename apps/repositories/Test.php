<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnClass;
use Learncom\Models\LearnScore;
use Learncom\Models\LearnSubject;
use Learncom\Models\LearnTest;
use Learncom\Models\LearnTestFolder;
use Learncom\Models\LearnVideo;
use Phalcon\Mvc\User\Component;
use \Learncom\Models\LearnArticle;

class Test extends Component
{
    public static function findFirstById($id) {
        return LearnTest::findFirst([
            'test_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }

    public static function stringToArrAnswer($string) {
       $arrAnswer = explode(',',$string);
       $arrAnswerDB = [];
       foreach ($arrAnswer as $answer) {
           $arAnswerQuestion = explode(':',$answer);
           if (!$arAnswerQuestion[0] || !$arAnswerQuestion[1] || !is_numeric($arAnswerQuestion[0]) || !in_array($arAnswerQuestion[1],['A','B','C','D'])) {
               return [];
           }
           $arrAnswerDB[trim($arAnswerQuestion[0])] = trim($arAnswerQuestion[1]);
       }
       return $arrAnswerDB;
   }
    public static function arrToStringAnswer($arr) {
        $arAnswerQuestion = [];
        foreach ($arr as $question => $answer) {
            $arAnswerQuestion[] = $question.":".$answer;
        }
        $string = implode(',',$arAnswerQuestion);
        return $string;
    }
    public static function findByClassAndSubject($class_id,$subject_id) {
        return LearnTest::find([
            'test_class_id = :class_id: AND test_subject_id = :subject: AND test_parent_id = 0',
            'bind' => ['class_id' => $class_id,'subject'=>$subject_id],
            'order' => 'test_order ASC'
        ]);
    }
    public static function findFirstScoreByIdAndTest($user_id,$test_id) {
        return LearnScore::findFirst([
            'score_test_id = :id: AND score_user_id = :user_id:',
            'bind' => ['id' => $test_id,"user_id" => $user_id]
        ]);
    }
    public static function findByGroupId($id,$is_old = true) {
        if ($is_old) {
            return LearnTest::find([
                'test_group_id = :id: AND test_parent_id = 0 AND test_type = "old"',
                'bind' => ['id' => $id],
                'order' => 'test_order ASC'
            ]);
        } else {
            return LearnTest::find([
                'test_group_id = :id: AND test_parent_id = 0 AND test_type != "old"',
                'bind' => ['id' => $id],
                'order' => 'test_order ASC'
            ]);
        }

    }
    public static function findFirstByScoreId($id) {
        return LearnScore::findFirst([
            'score_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }
    public static function getTestName($id) {
        $model = self::findFirstById($id);
        return $model ? $model->getTestName() : "";
    }
    public static function getTestClass($id) {
        $model = self::findFirstById($id);
        $name = "";
        if ($model) {
            $name = ClassSubject::getNameByClassAndSubject($model->getTestClassId(),$model->getTestSubjectId());
        }
        return $name;
    }
    public static function findNoGroup($class_id,$subject_id,$is_old = true) {
        if ($is_old) {
            return LearnTest::find([
                'test_class_id = :class_id: AND test_subject_id = :subject: AND test_group_id = 0 AND test_parent_id = 0 AND test_type = "old"',
                'bind' => ['class_id' => $class_id,'subject'=>$subject_id],
                'order' => 'test_order ASC'
            ]);
        } else {
            return LearnTest::find([
                'test_class_id = :class_id: AND test_subject_id = :subject: AND test_group_id = 0 AND test_parent_id = 0 AND test_type != "old"',
                'bind' => ['class_id' => $class_id,'subject'=>$subject_id],
                'order' => 'test_order ASC'
            ]);
        }

    }
    public static function findBySubject($subject_id) {
        return LearnTest::find([
            ' test_subject_id = :subject: AND test_parent_id = 0',
            'bind' => ['subject'=>$subject_id],
            'order' => 'test_id DESC',
            'limt' => 5
        ]);
    }
    public static function findScoreByClassAndSubject($class_id,$subject_id) {
        return LearnScore::query()
            ->columns('t.test_id,t.test_name,t.test_time,score_id,score_score,score_time,score_insert_time,t.test_number_question')
            ->innerJoin(LearnTest::class,'score_test_id = t.test_id','t')
            ->where('t.test_class_id = :class: AND t.test_subject_id = :subject:',['class' => $class_id,'subject' => $subject_id])
            ->orderBy('t.test_order ASC')->execute()->toArray();
    }
    public static function deleteParentId($id) {
        $arr = LearnTest::find([
            'test_parent_id = :id:',
            'bind' => ['id' => $id]
        ]);
        $arr->delete();
    }
    public static function deleteFolder($id) {
        $arr = LearnTestFolder::find([
            'folder_test_id = :id:',
            'bind' => ['id' => $id]
        ]);
        $arr->delete();
    }
    public static function getArrTestParent($id,$parent = -1) {
        if ($parent == 0) {
            $parent = -1;
        }
        return LearnTest::find([
            'test_parent_id = :id: OR test_id = :id: OR test_parent_id = :parent: OR test_id = :parent: AND test_type = "old"',
            'bind' => ['id' => $id,'parent' => $parent]
        ]);
    }
    public static function getArrTestParentId($id,$parent_id = -1) {
        $arrParent = self::getArrTestParent($id,$parent_id);
        $arrId = [];
        foreach ($arrParent as $parent) {
            array_push($arrId,$parent->getTestId());
        }
        return $arrId;
    }
    public static function findSchoolTest($class_id,$subject_id,$school_class_id) {
        return LearnTest::find([
            'test_class_id = :class_id: AND test_subject_id = :subject_id: AND test_parent_id = 0 AND test_type !="old"
             AND FIND_IN_SET(:school_class_id:,test_school_class_ids) AND test_active = "Y"',
            'bind' => [
                'class_id' => $class_id,
                'subject_id' => $subject_id,
                'school_class_id' => $school_class_id
            ],
            'order' => "test_time_start DESC"
        ]);
    }
    public  function createTest(&$data,&$arrTestId,$offline = false,$isHard = false) {
        $total_number = $this->request->getPost("txtNumberQuestion", array('string', 'trim'));

        $data['test_number_question'] = 0;
        $data['test_answer'] = json_encode([],true);

        $msg_result = array();
        $new_device = new LearnTest();
        $result = $new_device->save($data);
        if ($result === true) {
            //nạp dữ liệu
            $test_answer = [];
            $test_array = [];
            $arrExamChose = [];
            $arrExamNotChose = [];
            $number_chose = 1;
            $number_not_chose = $total_number;
            $arrExamIdSum = [];
            if ($offline == true) {
                $arrTestSubject = $_POST['txtTestSubject'];
                $arrTestChapter = $_POST['txtTestChapter'];
                $arrTestLesson = $_POST['txtTestLesson'];
            } else {
                $arTem = json_decode($data['test_array'],true);
                $arrTestSubject = $arTem['txtTestSubject'];
                $arrTestChapter = $arTem['txtTestChapter'];
                $arrTestLesson = $arTem['txtTestLesson'];
            }
            $color = "";
            if (isset($arrTestSubject)) {
                foreach ($arrTestSubject as $type_id => $items) {
                    if ($isHard) {
                        $color = $type_id;
                    }
                    foreach ($items as  $exam_subject_id=>$number_test) {
                        if ($number_test > 0) {
                            $arrGroup = ExamGroup::findByExamSubjectAndType($exam_subject_id,$type_id);
                            $data['test_number_question'] += $number_test;
                            $test_group = "";
                            foreach ($arrGroup as $group) {
                                $test_group = implode(',',[$test_group,$group->getGroupDataTest()]);
                            }
                            $arrExamId = explode(',',$test_group);
                            if (count($arrExamId) > $number_test) {
                                $this->addTest($number_test,$arrExamId,$new_device,$number_chose,$number_not_chose,$arrExamIdSum,$arrExamNotChose,$test_array,$test_answer,$arrExamChose,$color);
                            }
                        }

                    }
                }
            }
            if (isset($arrTestChapter)) {
                foreach ($arrTestChapter as $type_id => $items) {
                    if ($isHard) {
                        $color = $type_id;
                    }
                    foreach ($items as $chapter_id => $number_test) {
                        if ($number_test > 0) {
                            $arrGroup = ExamGroup::findByChapterAndType($chapter_id, $type_id);
                            $data['test_number_question'] += $number_test;
                            $test_group = "";
                            foreach ($arrGroup as $group) {
                                $test_group = implode(',', [$test_group, $group->getGroupDataTest()]);
                            }
                            $arrExamId = explode(',', $test_group);
                            if (count($arrExamId) > $number_test) {
                                $this->addTest($number_test, $arrExamId, $new_device, $number_chose, $number_not_chose, $arrExamIdSum, $arrExamNotChose, $test_array, $test_answer, $arrExamChose,$color);
                            }
                        }
                    }
                }
            }
            if (isset($arrTestLesson)) {
                foreach ($arrTestLesson as $type_id => $items) {
                    if ($isHard) {
                        $color = $type_id;
                    }
                    foreach ($items as $lesson => $number_test) {
                        if ($number_test > 0) {
                            $arrGroup = ExamGroup::findByLessonAndType($lesson, $type_id);
                            $data['test_number_question'] += $number_test;
                            $test_group = "";
                            foreach ($arrGroup as $group) {
                                $test_group = implode(',', [$test_group, $group->getGroupDataTest()]);
                            }
                            $arrExamId = explode(',', $test_group);
                            if (count($arrExamId) > $number_test) {
                                $this->addTest($number_test, $arrExamId, $new_device, $number_chose, $number_not_chose, $arrExamIdSum, $arrExamNotChose, $test_array, $test_answer, $arrExamChose,$color);
                            }
                        }
                    }
                }
            }
            $new_device->setTestArray(json_encode($test_array,true));
            $new_device->setTestAnswer(json_encode($test_answer,true));
            $new_device->setTestNumberQuestion($data['test_number_question']);
            $result = $new_device->save();
            if ($result) {
                array_push($arrTestId,$new_device->getTestId());
            }
            //trộn dề
            $data_new = $data;
            $data_new['test_parent_id'] = $new_device->getTestId();
            for ($t=1;$t<$data['test_number_exam'];$t++) {
                $data_new['test_answer'] = json_encode($test_answer,true);
                $data_new['test_name'] = $data['test_name']."- MÃ ".$t;
                $arrTempOf = $arrExamChose;
                $arrNewOf = [];
                $v = 1;
                while ($arrTempOf || $v < $number_chose) {
                    $k = array_rand($arrTempOf);
                    $arrNewOf[$v] = $arrTempOf[$k];
                    unset($arrTempOf[$k]);
                    $v++;
                }
                $arrDataTestNew = [];
                for ($j = 1;$j<=$data['test_number_question'];$j++) {
                    if (isset($arrNewOf[$j]) && $arrNewOf[$j] != "") {
                        $arrDataTestNew[$j] = $arrNewOf[$j];
                    } else {
                        $arrDataTestNew[$j] = $j;
                    }
                }
                $data_new['test_array'] = json_encode($arrDataTestNew,true);
                $new_test = new LearnTest();
                $new_test->save($data_new);
            }
            $msg_result = array('status' => 'success', 'message' => 'Tạo Đề thi thành công ');

        } else {
            $message = "We can't store your info now: \n";
            foreach ($new_device->getMessages() as $msg) {
                $message .= $msg . "\n";
            }
            $msg_result['status'] = 'error';
            $msg_result['message'] = $message;
        }
        return $msg_result;
    }
    private function addTest(&$numberQuestion,&$arrExamId,&$new_device,&$number_chose,&$number_not_chose,
                             &$arrExamIdSum,&$arrExamNotChose,&$test_array,&$test_answer,&$arrExamChose,$color = "") {
        foreach ($arrExamId as $key => $exam) {
            if (!$exam) {
                unset($arrExamId[$key]);
            }
        }
        for ($k = 1; $k<=$numberQuestion;$k++ ) {
            $t = array_rand($arrExamId);
            $exam_id = $arrExamId[$t];
            $check = false;
            while ($check == false) {
                $t = array_rand($arrExamId);
                $exam_id = $arrExamId[$t];
                if (!in_array($exam_id,$arrExamIdSum)) {
                    $check = true;
                }
            }
            array_push($arrExamIdSum,$exam_id);
            $exam = Exam::findFirstById($exam_id);
            if ($exam) {
                //cho trắc nghiệm lên trước, tự luận ra sau
                if ($exam->getExamAnswer()) {
                    $folder = new LearnTestFolder();
                    $folder->setFolderTestId($new_device->getTestId());
                    $folder->setFolderStatus($number_chose);
                    $folder->setFolderLink($exam->getExamLinkTest());
                    $folder->setFolderLinkComment($exam->getExamLinkAnswer());
                    if ($color) {
                        $folder->setFolderColor($color);
                    }
                    $folder->save();
                    $test_array[$number_chose]=$number_chose;
                    $test_answer[$number_chose] = $exam->getExamAnswer();
                    $arrExamChose[$number_chose] = $number_chose;
                    $number_chose++;
                } else {
                    $folder = new LearnTestFolder();
                    $folder->setFolderTestId($new_device->getTestId());
                    $folder->setFolderStatus($number_not_chose);
                    $folder->setFolderLink($exam->getExamLinkTest());
                    $folder->setFolderLinkComment($exam->getExamLinkAnswer());
                    if ($color) {
                        $folder->setFolderColor($color);
                    }
                    $folder->save();
                    $test_array[$number_not_chose]=$number_not_chose;
                    $arrExamNotChose[$number_not_chose] = $number_not_chose;
                    $number_not_chose--;
                }

            }
        }
    }

}



