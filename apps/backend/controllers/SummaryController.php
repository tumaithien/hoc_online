<?php

namespace Learncom\Backend\Controllers;


use Learncom\Models\LearnClass;
use Learncom\Models\LearnScore;
use Learncom\Models\LearnSubject;
use Learncom\Models\LearnUser;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Test;
use Learncom\Repositories\User;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Travelnercom\PHPExcel\PHPExcel;
require_once __DIR__ ."/../../library/PHPExcel/PHPExcel.php";
class SummaryController extends ControllerBase
{
    public function readAction() {
        $arrClass = LearnClass::find();
        $arrSucject = LearnSubject::find('subject_parent_id = 0');
        $this->view->setVars([
            'arrClass' => $arrClass,
            'arrSucject' => $arrSucject
        ]);
    }
    public function viewAction() {
        $class_id = $this->request->get('classId');
        $subject_id = $this->request->get('subjectId');
        $arrUser = User::findByClassAndSubject($class_id,$subject_id);
        $class_name = ClassSubject::getNameByClassAndSubject($class_id,$subject_id);
        $this->view->arrUser = $arrUser;
        $this->view->name = $class_name;
    }
    public function testAction() {
        $class_id = $this->request->get('classId');
        $subject_id = $this->request->get('subjectId');
        $arrTest = Test::findByClassAndSubject($class_id,$subject_id);
        $this->view->arrTest = $arrTest;
        $this->view->name = ClassSubject::getNameByClassAndSubject($class_id,$subject_id);
    }
    public function detailtestAction() {
        $user_id = $this->request->get('id');
        $user = LearnUser::findFirst([
            'user_id = :id: AND user_role="user"',
            'bind' => ['id' => $user_id]
        ]);
        if (!$user) {
            return $this->response->redirect('notfound');
        }
        $arrScore = LearnScore::find([
            'score_user_id = :id:',
            'bind' => ['id' => $user_id],
            'order' => 'score_id ASC'
        ]);
        $this->view->arrScore = $arrScore;
        $this->view->name = $user->getUserName();
    }
    public function listtestAction() {
        $test_id = $this->request->get('id');
        $test_model = Test::findFirstById($test_id);
        if (!$test_model) {
            $this->response->redirect("/notfound");
            return;
        }
        $this->view->test_id = $test_id;
        if ($this->request->isPost()) {
            $score_id = $this->request->getPost("sbmSave", array('string', 'trim'));
            $score_score = $this->request->getPost("txtScore".$score_id, array('string', 'trim'));
            $score_score_choose = $this->request->getPost("txtScoreIsChose".$score_id, array('string', 'trim'));
            $score_model = LearnScore::findFirst([
                'score_id = :id:',
                'bind' => ['id' => $score_id]
            ]);
            if ($score_model) {
                $score_model->setScoreScore($score_score);
                $score_model->setScoreScoreChoose($score_score_choose);
                if ($score_model->save()) {
                    $msg_result = [
                        'status' => "success",
                        'message' => "Lưu điểm thành công"
                        ];
                } else {
                    $msg_result = [
                        'status' => "error",
                        'message' => "Lưu điểm thất bại"
                    ];
                }
                $this->view->msg_result = $msg_result;
            }
        }
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        $arrTestParent = Test::getArrTestParentId($test_id);
        $arrScore = LearnScore::find([
            'FIND_IN_SET(score_test_id,:arr_test:)',
            'bind' => ['arr_test' => implode(',',$arrTestParent)],
            'order' => "score_id DESC"
        ]);
        /*
        $btn_export = $this->request->getPost("sbmCSV");
        if(isset($btn_export)){
            $this->view->disable();
            $USER = new User();
//            $objPHPExcel = new \PHPExcel();
//            $row = 1;
//            $objPHPExcel->setActiveSheetIndex(0)
//                ->setCellValue('A' . $row, 'Tên')
//                ->setCellValue('B' . $row, 'Lớp' )
//                ->setCellValue('C' . $row, 'Điểm' )
//                ->setCellValue('D' . $row, 'Thời gian xong' )
//                ->setCellValue('E' . $row, 'Thời gian làm bài' );
//            foreach ($arrScore as $score) {
//                $row ++;
//                $class = ClassSubject::getNameByClassAndSubject($test_model->getTestClassId(),$test_model->getTestSubjectId());
//                if ($score) {
//                    $objPHPExcel->setActiveSheetIndex(0)
//                        ->setCellValue('A' . $row, $USER->getNameByID($score->getScoreUserId()))
//                        ->setCellValue('B' . $row, $class )
//                        ->setCellValue('C' . $row, $score->getScoreScore() )
//                        ->setCellValue('D' . $row, $score->getScoreTime() )
//                        ->setCellValue('E' . $row, $this->my->formatDateTime($score->getScoreInsertTime()) );
//                }
//            }
//            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//            $excelFileName = "KET_QUA_KT".'.html';
//            $excelLogFilePath = $excelFileName;
////            header('Content-type: application/vnd.ms-excel');
////            header('Content-Disposition: attachment; filename="KET_QUA_KT.xlsx"');
////            header("Expires: 0");
//            $objWriter->save($excelLogFilePath);
//            header('Content-Description: File Transfer');
//            header("Content-Type: text/html");
//            header('Content-Disposition: attachment; filename="'.basename($excelLogFilePath).'"');
//            header('Expires: 0');
//            header('Cache-Control: must-revalidate');
//            header('Pragma: public');
//            header('Content-Length: ' . filesize($excelLogFilePath));
//            flush(); // Flush system output buffer
//            readfile($excelLogFilePath);
//            unlink($excelLogFilePath);



            $results[] = array(
                "\xEF\xBB\xBF".'Tên','Lớp','Điểm','Thời gian xong','Thời gian làm bài'
            );
            foreach ($arrScore as $score)
            {
                $class = ClassSubject::getNameByClassAndSubject($test_model->getTestClassId(),$test_model->getTestSubjectId());
                $test = [
                    $USER->getNameByID($score->getScoreUserId()),
                    $class,
                    $score->getScoreScore(),
                    $score->getScoreTime(),
                    $this->my->formatDateTime($score->getScoreInsertTime())
                ];
                $results[] = $test;
            }
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=user_'.time().'.csv');
            echo "\xEF\xBB\xBF";
            $out = fopen('php://output', 'w+',"");
            foreach ($results as $fields) {
                fputcsv($out, $fields);
            }
            fclose($out);
            die();
//            rename($excelFileName, "KET_QUA_KT");
//            header('Content-Description: File Transfer');
//            header('Content-Type: application/octet-stream');
//            header('Content-Disposition: attachment; filename="'.basename($excelFileName).'"');
//            header('Expires: 0');
//            header('Cache-Control: must-revalidate');
//            header('Pragma: public');
//            header('Content-Length: ' . filesize($excelFileName));
//            flush(); // Flush system output buffer
//            readfile("KET_QUA_KT");
           // die();
        }
        */
        $this->view->arrScore = $arrScore;
        $this->view->testName = $test_model->getTestName();
        $this->view->test_model = $test_model;
    }
    public function detailtestuserAction() {
        $test_id = $this->request->get('id');
        $user_id = $this->request->get('user-id');
        $score_model = Test::findFirstScoreByIdAndTest($user_id,$test_id);
        $test_model = Test::findFirstById($test_id);
        if (!$test_model || !$score_model) {
            return $this->response->redirect('/notfound');
        }
        $test_parent_id = $test_model->getTestParentId() == 0 ? $test_id : $test_model->getTestParentId();
        $test_parent_model = Test::findFirstById($test_parent_id);

        $class_id = $test_model->getTestClassId();
        $subject_id = $test_model->getTestSubjectId();
        $test_answer = $test_model->getTestAnswer();
        $arrTestAnswer = json_decode($test_answer,true);
        $arrTestScore = json_decode($score_model->getScoreData(),true);
        if (!$arrTestAnswer) $arrTestAnswer = [];
        if (!$arrTestScore) $arrTestScore = [];
        $total_success = $score_model->getScoreScore()*10/$test_model->getTestNumberQuestion();
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
    public function deletescoreAction() {
        $id = $this->request->get('id');
        $test_id = $this->request->get('testId');

        $msg_result = array('status' => '', 'message' => '');
        $test_model = LearnScore::findFirst([
            'score_id = :score:',
            'bind' => ['score' => $id]
        ]);
        if ($test_model) {
            if($test_model->delete() == false){
                $msg_result['message'] .= 'Bạn Không thể xóa bài làm vừa rồi ';
                $msg_result['status'] = 'danger';
            }
            else {
                $message = 'Xóa bài làm thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';
            }
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/detail-list-test?id='.$test_id);
    }
    public function getcsvAction() {
        $test_id = $this->request->getPost('id');
        $test_model = Test::findFirstById($test_id);
        if (!$test_model) {
            $this->response->redirect("/notfound");
            return;
        }
        $arrTestParent = Test::getArrTestParentId($test_id);
        $arrScore = LearnScore::find([
            'FIND_IN_SET(score_test_id,:arr_test:)',
            'bind' => ['arr_test' => implode(',',$arrTestParent)],
            'order' => "score_id DESC"
        ]);
        $USER = new User();
        $results = [];
        $t = 0;
        foreach ($arrScore as $score)
        {
            $class = ClassSubject::getNameByClassAndSubject($test_model->getTestClassId(),$test_model->getTestSubjectId());
            $results[$t]['Tên'] =  $USER->getNameByID($score->getScoreUserId());
             $results[$t]['Lớp'] =  $class;
             $results[$t]['Điểm'] =  $score->getScoreScore();
             $results[$t]['Thời gian xong'] =  $score->getScoreTime();
             $results[$t]['Thời gian làm bài'] =  $this->my->formatDateTime($score->getScoreInsertTime());
            $t ++;
        }
        die(json_encode($results));
    }
    public static function convertkeyword($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        $str = strtolower($str);
        $str = preg_replace("/(\“|\”|\‘|\’|\,|\!|\&|\;|\@|\#|\%|\~|\`|\=|\_|\'|\]|\[|\}|\{|\)|\(|\+|\^)/", '-', $str);
        $str = preg_replace('/[^A-Za-z0-9-\s]/', '-', $str);
        $str = preg_replace("/( )/", '-', $str);
        $str = preg_replace("/(--|---)/", '-', $str);
        $pos =strrpos($str,'-');
        if (($pos !== false)&&($pos === strlen($str)-1)){
            $str= substr($str,0, -1);
        }
        return $str;
    }
}