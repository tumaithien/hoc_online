<?php
namespace Learncom\Frontend\Controllers;

use Learncom\Backend\Controllers\CreatetestController;
use Learncom\Models\LearnExamType;
use Learncom\Models\LearnScore;
use Learncom\Models\LearnTest;
use Learncom\Repositories\ExamType;
use Learncom\Repositories\Group;
use Learncom\Repositories\SchooManage;
use Learncom\Repositories\Test;
use Learncom\Repositories\Upload;

class TestnewController extends ControllerBase
{
    protected  $arrGroup;
    protected $arrTestNoGroup;
    public function setview($class_id,$subject_id) {
        $this->arrGroup = Group::findByType($class_id,$subject_id,'test');
        $this->arrTestNoGroup = Test::findNoGroup($class_id,$subject_id,false);
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

        $arrTest = Test::findSchoolTest($class_id,$subject_id,$this->school_class_id);
        $sql_score = "SElECT * FROM Learncom\Models\LearnScore as s
                             INNER JOIN Learncom\Models\LearnTest as t ON s.score_test_id = t.test_id 
                             WHERE s.score_user_id = :user_id: AND t.test_class_id = :class_id: AND t.test_subject_id = :subject_id: AND t.test_type != 'old'
                             ORDER BY score_insert_time ASC";
        $param = [
           'user_id' => $this->auth['id'],
           'class_id' => $class_id,
           'subject_id' => $subject_id
        ];
        $arrScore = $this->modelsManager->executeQuery($sql_score,$param);
        $this->view->setVars([
            'arrTest' => $arrTest,
            'class_id' => $class_id,
            'subject_id' => $subject_id,
            'arrScore' => $arrScore
        ]);
    }

    public function indexAction()
    {
        $test_parent_id = $this->request->get('id');
        $user_school_id = $this->school_class_id;
        if (!$user_school_id) {
            return $this->response->redirect('/permission');
        }
        $time = $this->globalVariable->curTime;
        $test_parent_model = LearnTest::findFirst([
            'FIND_IN_SET(:user_school_id:,test_school_class_ids) AND (test_type = "new" OR test_type="exam") AND test_active = "Y" 
            AND test_id=:test_id: AND test_status = "P"',
            'bind' => ['user_school_id' => $user_school_id,'test_id'=>$test_parent_id]
        ]);
        if ($test_parent_model->getTestTimeStart() > $time) {
            return $this->response->redirect('/time');
        }
        if (!$test_parent_model) {
            return $this->response->redirect('/permission');
        }
        $class_id = $test_parent_model->getTestClassId();
        $subject_id = $test_parent_model->getTestSubjectId();
        $this->setview($class_id,$subject_id);
        $arrTestParent = Test::getArrTestParent($test_parent_id);

        foreach ($arrTestParent as $test_parent) {
            $score_model =  Test::findFirstScoreByIdAndTest($this->auth['id'],$test_parent->getTestId());
            if ($score_model) {
                if ($score_model->getScoreStatus() == "S" && $test_parent_model->getTestOnlyOne() == "Y") {
                    return $this->response->redirect('/ket-thuc?testId='.$test_parent->getTestId());
                } else if ($score_model->getScoreStatus() == "S") {
                    $test_model = $test_parent;
                    if ($score_model->getScoreStatus() == "S") {
                        $score_model->delete();
                    }
                    break;
                } else {
                    $test_model = $test_parent;
                    break;
                }

            }
        }
        if ($test_parent_model->getTestType() == "new") {
            if (!$test_model) {
                $temp = array_rand($arrTestParent->toArray(), 1);
                $test_model = $arrTestParent[$temp];
            }
        } elseif ($test_parent_model->getTestType() == "exam") {
            if (!$test_model) {
                $data = $test_parent_model->toArray();
                $data['test_parent_id'] = $test_parent_model->getTestId();
                unset($data['test_id']);
                $createtest = new Test();
                $arrTestId = [];
                $msg_result = $createtest->createTest($data,$arrTestId,false,true);
                if ($msg_result) {
                    $test_model = Test::findFirstById($arrTestId[0]);
                }
            }
        } else {
            return $this->response->redirect('/permission');
        }

//        if (!in_array($class_id,$this->class_id) || !in_array($subject_id,$this->subject_id)  ) {
//            return $this->response->redirect('/permission');
//        }
        $time_test = $test_model->getTestTime();

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
                $score_model->setScoreScoreChoose(0);

                $score_model->setScoreInsertTime($this->globalVariable->curTime);
                $dataLog = [0=>$this->globalVariable->curTime.",0,Học sinh bắt đầu làm bài"];
                $score_model->setScoreLog(json_encode($dataLog,true));
            }
        } else {
            $dataLog = $score_model->getScoreLog();
            $dataLog = json_decode($dataLog,true);
            if ($score_model->getScoreInsertTime() > 0) {
                $time_test_new = $time_test - (time() - $score_model->getScoreInsertTime())/60;
                if ($time_test_new <= 0) {
                    if ($score_model->getScoreStatus() == "P") {
                        $score_model->setScoreStatus("S");
                        $score_model->setScoreUpdateTime(time());
                        $score_model->save();
                    }

                    return $this->response->redirect('/ket-thuc?testId='.$test_model->getTestId());
                }
                $time_test = round($time_test_new);
            }
            $dataLog[] = $this->globalVariable->curTime.",5,Học sinh trở lại bài làm sau: ".round((($this->globalVariable->curTime - $score_model->getScoreUpdateTime()) / 60)). " phút rời mạng.";
            $score_model->setScoreLog(json_encode($dataLog,true));
        }
        $score_model->setScoreStatus("P");
        $score_model->setScoreUpdateTime(time());
        $score_model->setScoreUserIp($_SESSION['ip']);
        $device = $this->isMobile ? "Điện thoại" : "Máy tính";
        $score_model->setScoreUserDevice($device);
        $result = $score_model->save();
        if (!$result) {
            return $this->response->redirect('/alert');
        }

        $arrScore = [];
        if ($score_model->getScoreData()) {
            $arrScore = json_decode($score_model->getScoreData(),true);
        }
        $this->session->set('time_start_test',time());
        $this->session->set('time_finish_test',$test_model->getTestTime());
        $hasNotChoose = count(json_decode($test_model->getTestArray(),true)) > count(json_decode($test_model->getTestAnswer(),true)) ? true : false;
        $arrType = LearnExamType::find(['order' => 'type_order ASC']);
        $arrColor = ['#00FFFF','#FFD700','#FF8C00','#DC143C','#A0522D'];
        $arrColorReturn = [];
        $t = 0;
        foreach ($arrType as $type_exam) {
            $arrColorReturn[$type_exam->getTypeId()] = $arrColor[$t];
            $t++;
        }
        $this->view->setVars([
            'time_test' => $time_test,
            'test_model' => $test_model,
            'test_parent_model' => $test_parent_model,
            'class_id' => $class_id,
            'subject_id' => $subject_id,
            'hasNotChoose' => $hasNotChoose,
            'time_start' => $this->globalVariable->curTime,
            'arrScore' => $arrScore,
            'arrColorReturn' => $arrColorReturn
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
            $this->session->remove('test_time');
        }
        $test_id = $this->request->get('testId');
        $test_model = Test::findFirstById($test_id);
        if (!$test_model) {
            $test_model = LearnTest::findFirst([
                'test_parent_id = :id:',
                'bind' => ['id' => $test_id]
            ]);
            if (!$test_model) {
                return $this->response->redirect('/nodata');
            }
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
            $uploadFiles =[];
            if ($this->request->hasFiles() == true && $_FILES['fileUpload']['size'][0] > 0 )
            {
                $total = 0;
                foreach ($_FILES['fileUpload']['name'] as $item => $value) {
                    $messages_upload = [];
                    Upload::uploadFileScore($uploadFiles,$messages_upload,$item);
                    if ($messages_upload['type'] == "success") {
                        $total++;
                    }
                }
                if ($total < count($_FILES['fileUpload']['name'])) {
                    $message_image = "Upload hình ảnh không đủ";
                }
            }
            $arrLink = [];
            foreach ($uploadFiles as $file ){
                array_push($arrLink,$file['file_url']);
            }
            $score_choose = round($total_success*$test_model->getTestScoreChoose()/count($arrTestAnswer),2);
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
            $time_test = (time() - $score_model->getScoreInsertTime());

            $score_model->setScoreDataNotChoose(implode(',',$arrLink));
            $score_model->setScoreTestId($test_id);
            $score_model->setScoreTime(floor($time_test/60));
            $score_model->setScoreData($answer);
            $score_model->setScoreScore($score_choose);
            $score_model->setScoreUpdateTime(time());
            $score_model->setScoreScoreChoose($score_choose);
            $score_model->setScoreInsertTime($this->globalVariable->curTime);
            $score_model->setScoreStatus("S");
            $device = $this->isMobile ? "Điện thoại" : "Máy tính";
            $score_model->setScoreUserDevice($device);
            $score_model->save();
            $total_score = $score_choose;
        }
        $test_choose = $test_model->getTestScoreChoose() ? $test_model->getTestScoreChoose() : 10;
        if ($score_model) {
            $total_success = round($score_model->getScoreScoreChoose()*count($arrTestAnswer)/$test_choose);
        }
        $this->setview($class_id,$subject_id);
        $arrColor = ['#66CCFF','#FFFF00','#FF9900','#DD0000','#330000'];
        $arrType = LearnExamType::find(['order' => "type_order ASC"]);
        $t = 0;
        $arrColorReturn = [];
        foreach ($arrType as $type) {
            $arrColorReturn[$type->getTypeId()] = $arrColor[$t];
            $t++;
        }
        $this->view->setVars([
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
            'time_start' => $this->globalVariable->curTime,
            'arrColorReturn' => $arrColorReturn
        ]);
    }
    public function viewAction() {
        $test_id = $this->request->get('id');
        $score_model = Test::findFirstScoreByIdAndTest($this->auth['id'],$test_id);
        $test_model = Test::findFirstById($test_id);
        if (!$test_model || !$score_model) {
            return $this->response->redirect('/nodata');
        }
        if ($test_model->getTestType() == "exam") {
            $test_parent_id = $test_model->getTestId();
        } else {
            if ($test_model->getTestParentId() == 0){
                $test_parent_id = $test_model->getTestId();
            } else {
                $test_parent_id = $test_model->getTestParentId();
            }
        }        $test_parent_model = Test::findFirstById($test_parent_id);

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
    public function editdataAction() {
        $data = [
            'message' => $this->request->getPost('message', array('string', 'trim')),
            'action' => $this->request->getPost('action', array('string', 'trim')),
            'cauhoi' => $this->request->getPost('cauhoi', array('string', 'trim')),
            'dapan' => $this->request->getPost('dapan'), array('string', 'trim'),
            'time' => time(),
            'test_id' => $this->request->get('id', array('string', 'trim')),
            'time_start' => $this->request->get('time_start', array('string', 'trim')),
        ];
        $score = LearnScore::findFirst([
            'score_test_id = :test_id: AND score_user_id = :user_id:',
            'bind' => ['test_id' => $data['test_id'],'user_id' => $this->auth['id']]
        ]);
        $dataScore = [];
        $dataLog = [];
        $error = 0;
        if ($score) {
            if ($score->getScoreData()) {
                $dataScore = $score->getScoreData();
                $dataScore = json_decode($dataScore,true);
            }
            $dataLog = $score->getScoreLog();
            $dataLog = json_decode($dataLog,true);
            $error = $score->getScoreTotalError();
        }
        array_push($dataLog,$data['time'].",".$data['action'].",".$data['message']);

        if (!$score) {
            $score = new LearnScore();
            $score->setScoreTestId($data['test_id']);
            $score->setScoreUserId($this->auth['id']);
            $score->setScoreInsertTime($data['time_start']);
        }
        if ($data['cauhoi'] && $data['dapan']) {
            $dataScore[$data['cauhoi']] = $data['dapan'];
            $score->setScoreData(json_encode($dataScore,true));
        }
        if (in_array($data['action'],['3','5'])) {
            $error++;
        }
        $test_model = LearnTest::findFirst($score->getScoreTestId());
        $score_score = SchooManage::updateScore($test_model,$dataScore);
        $score->setScoreTotalError($error);
        $score->setScoreLog(json_encode($dataLog,true));
        $score->setScoreTime(round(($data['time']-$data['time_start'])/60));
        $score->setScoreScoreChoose($score_score);
        $score->setScoreScore($score_score);
        $score->setScoreUpdateTime($data['time']);
        $device = $this->isMobile ? "Điện thoại" : "Máy tính";
        $score->setScoreUserDevice($device);
        $score->save();
    }
}