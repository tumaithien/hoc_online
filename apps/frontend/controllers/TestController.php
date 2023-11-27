<?php
namespace Learncom\Frontend\Controllers;

use Learncom\Models\LearnScore;
use Learncom\Repositories\Chapter;
use Learncom\Repositories\Group;
use Learncom\Repositories\Test;

class TestController extends ControllerBase
{
    protected  $arrGroup;
    protected $arrTestNoGroup;
    public function setview($class_id,$subject_id) {
        $this->arrGroup = Chapter::findByType($class_id,$subject_id,'test');
        $this->arrTestNoGroup = Test::findNoGroup($class_id,$subject_id);
        $this->view->setVars([
            'arrTestNoGroup' => $this->arrTestNoGroup,
            'arrGroup' => $this->arrGroup,
        ]);
    }
    public function startAction()
    {
        $class_id = $this->request->get('classId');
        $subject_id = $this->request->get('subjectId');
        $this->setview($class_id,$subject_id);
        $test_id = $this->request->get('testId');

        if (!in_array($class_id,$this->class_id) || !in_array($subject_id,$this->subject_id)  ) {
            return $this->response->redirect('/permission');
        }
        $arrTest = Test::findByClassAndSubject($class_id,$subject_id);
        if (count($arrTest) == 0) {
            return $this->response->redirect('/nodata');
        }
        $test_model = Test::findFirstById($test_id);
        if(!$test_model) {
            $test_model = $arrTest[0];
        }
        $arrTestParent = Test::getArrTestParent($test_id);
        foreach ($arrTestParent as $test_parent) {
            $score_model = Test::findFirstScoreByIdAndTest($this->auth['id'],$test_parent->getTestId());
            if ($score_model) {
                return $this->response->redirect('/finish-test?testId='.$test_parent->getTestId());
            }
        }
        $this->view->setVars([
            'arrTest' => $arrTest,
            'test_model' => $test_model,
            'class_id' => $class_id,
            'subject_id' => $subject_id,
            'test_id' => $test_id,
            'group_id' => $test_model->getTestGroupId(),
        ]);
    }
    public function indexAction()
    {
        $test_parent_id = $this->request->get('id');
        $test_parent_model = Test::findFirstById($test_parent_id);
        if (!$test_parent_model) {
            return $this->response->redirect('/permission');
        }
        $class_id = $test_parent_model->getTestClassId();
        $subject_id = $test_parent_model->getTestSubjectId();
        $this->setview($class_id,$subject_id);
        $arrTestParent = Test::getArrTestParent($test_parent_id);
        $temp = array_rand($arrTestParent->toArray(),1);
        $test_model = $arrTestParent[$temp];

        if (!in_array($class_id,$this->class_id) || !in_array($subject_id,$this->subject_id)  ) {
            return $this->response->redirect('/permission');
        }
        $score_model = Test::findFirstScoreByIdAndTest($this->auth['id'],$test_model->getTestId());
        if (!$score_model) {
            if ($test_model->getTestParentId() == 0) {
                $test_parent_id = 0;
            } else {
                $test_parent_id = $test_model->getTestParentId();
            }
            $arrTestParent = Test::getArrTestParent($test_parent_id);
            foreach ($arrTestParent as $test_parent) {
                $score_model = Test::findFirstScoreByIdAndTest($this->auth['id'],$test_parent->getTestId());
                if ($score_model) {
                    break;
                }
            }
            if (!$score_model) {
                $score_model = new LearnScore();
                $score_model->setScoreUserId($this->auth['id']);
                $score_model->setScoreTestId($test_model->getTestId());
                $score_model->setScoreTime(0);
                $score_model->setScoreScore(0);
                $score_model->setScoreInsertTime($this->globalVariable->curTime);
                $score_model->save();
            }
        }
        $this->session->set('time_start_test',time());
        $this->session->set('time_finish_test',$test_model->getTestTime());
        $hasNotChoose = count(json_decode($test_model->getTestArray(),true)) > count(json_decode($test_model->getTestAnswer(),true)) ? true : false;
     
        $this->view->setVars([
            'test_model' => $test_model,
            'test_parent_model' => $test_parent_model,
            'class_id' => $class_id,
            'subject_id' => $subject_id,
            'test_id' => $test_model->getTestId(),
            'group_id' => $test_model->getTestGroupId(),
            'hasNotChoose' => $hasNotChoose,
        ]);
    }
    public function finishAction()
    {
        $time = 0;
        $time_test = 0;
        if ($this->session->has('time_start_test')) {
            $time = time() - $this->session->get('time_start_test');
            $this->session->remove('time_start_test');
        }
        if ($this->session->has('time_finish_test')) {
            $this->session->remove('time_finish_test');
        }
        if ($this->session->has('test_time')) {
            $time_test = $_SESSION['test_time'];
            $this->session->remove('test_time');
        }
        $test_id = $this->request->get('testId');
        $test_model = Test::findFirstById($test_id);
        if (!$test_model) {
            return $this->response->redirect('/nodata');
        }
        $score_model = Test::findFirstScoreByIdAndTest($this->auth['id'],$test_id);

        $class_id = $test_model->getTestClassId();
        $subject_id = $test_model->getTestSubjectId();
        $test_answer = $test_model->getTestAnswer();
        $arrTestAnswer = json_decode($test_answer,true);
        $total_score = 0;
        if ($score_model) {
            $total_score = $score_model->getScoreScore();
        }
        $arrTestArray =  json_decode($test_model->getTestArray(),true);
        $total_success = 0;
        $answer = "";
        if ($this->request->isPost()) {
            $answer_not_choose = $this->request->getPost('txtNotChoose');
          if ($_POST['question']) {
             $answer = json_encode($_POST['question'],true);
             foreach ($arrTestArray as $key => $number_question) {
                 if (isset($_POST['question'][$key]) && isset($arrTestAnswer[$number_question])) {
                     if ($_POST['question'][$key] == $arrTestAnswer[$number_question]) {
                         $total_success++;
                     }
                 }
             }
          }
          $score_choose =  0;
          if (!empty($arrTestAnswer)) {
            $score_choose = round($total_success*$test_model->getTestScoreChoose()/count($arrTestAnswer),2);
          }
          if (!$score_model) {
              if ($test_model->getTestParentId() == 0) {
                  $test_parent_id = 0;
              } else {
                  $test_parent_id = $test_model->getTestParentId();
              }
              $arrTestParent = Test::getArrTestParent($test_parent_id);
              foreach ($arrTestParent as $test_parent) {
                  $score_model = Test::findFirstScoreByIdAndTest($this->auth['id'],$test_parent->getTestId());
                  if ($score_model) {
                      break;
                  }
              }
              if (!$score_model) {
                  $score_model = new LearnScore();
                  $score_model->setScoreUserId($this->auth['id']);
              }
          }
            $score_model->setScoreTestId($test_id);
            $score_model->setScoreTime(floor($time_test/60));
            $score_model->setScoreData($answer);
            $score_model->setScoreScore($score_choose);
            $score_model->setScoreScoreChoose($score_choose);
            $score_model->setScoreInsertTime($this->globalVariable->curTime);
            $score_model->setScoreDataNotChoose($answer_not_choose);
            $score_model->save();
            $total_score = $score_choose;
        }
        $test_choose = $test_model->getTestScoreChoose() ? $test_model->getTestScoreChoose() : 10;
        $total_success = round($score_model->getScoreScoreChoose()*count($arrTestAnswer)/$test_choose);
        $this->setview($class_id,$subject_id);
        $arrTest = Test::findByClassAndSubject($class_id,$subject_id);
        if (count($arrTest) == 0) {
            return $this->response->redirect('/nodata');
        }
        $this->view->setVars([
            'arrTest' => $arrTest,
            'class_id' => $class_id,
            'subject_id' => $subject_id,
            'test_id' => $test_id,
            'total_score' => $total_score,
            'total_success' => $total_success,
            'group_id' => $test_model->getTestGroupId(),
            'test_model' => $test_model,
            'arrTestArray' => $arrTestArray,
            'test_choose' => $test_choose,
            'total_choose' => count($arrTestAnswer),
        ]);
    }
    public function viewAction() {
        $test_id = $this->request->get('id');
        $score_model = Test::findFirstScoreByIdAndTest($this->auth['id'],$test_id);
        $test_model = Test::findFirstById($test_id);
        if (!$test_model || !$score_model) {
            return $this->response->redirect('/nodata');
        }
        $test_parent_id = $test_model->getTestParentId() == 0 ? $test_id : $test_model->getTestParentId();
        $test_parent_model = Test::findFirstById($test_parent_id);

        $class_id = $test_model->getTestClassId();
        $subject_id = $test_model->getTestSubjectId();
        $this->setview($class_id,$subject_id);
        $test_answer = $test_model->getTestAnswer();
        $arrTestAnswer = json_decode($test_answer,true);
        $arrTestScore = json_decode($score_model->getScoreData(),true);
        if (!$arrTestAnswer) $arrTestAnswer = [];
        if (!$arrTestScore) $arrTestScore = [];
        $test_choose = $test_model->getTestScoreChoose() ? $test_model->getTestScoreChoose() : 10;
        $total_success = round($score_model->getScoreScoreChoose()*$test_choose/$test_model->getTestNumberQuestion());
        $this->view->setVars([
            'class_id' => $class_id,
            'subject_id' => $subject_id,
            'test_id' => $test_id,
            'group_id' => $test_model->getTestGroupId(),
            'arrTestAnswer' => $arrTestAnswer,
            'arrTestScore' => $arrTestScore,
            'total_success' => $total_success,
            'test_parent_model' => $test_parent_model,
            'test_model' => $test_model,
            'test_parent_id' => $test_parent_id,
            'score_model' => $score_model
        ]);
    }
    public function checktimeAction() {
        $arr = [];
        if (isset($_SESSION['time_start_test']) && isset($_SESSION['time_finish_test'])) {
            $sum = time() - $_SESSION['time_start_test'];
            $exc = $_SESSION['time_finish_test'] * 60 - $sum;
            $arr['seconds'] = $exc % 60 >= 0 ? $exc % 60 : 0;
            $arr['minutes'] = floor($exc /60) >= 0 ? floor($exc /60) : 0;
            $_SESSION['test_time'] = $sum;
        }
        die(json_encode($arr));
    }
}