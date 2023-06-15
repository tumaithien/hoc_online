<?php

namespace Learncom\Backend\Controllers;


use Learncom\Models\LearnClass;
use Learncom\Models\LearnSchoolClass;
use Learncom\Models\LearnScore;
use Learncom\Models\LearnSubject;
use Learncom\Models\LearnUser;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Test;
use Learncom\Repositories\User;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\NativeArray;
use Travelnercom\PHPExcel\PHPExcel;
require_once __DIR__ ."/../../library/PHPExcel/PHPExcel.php";
class SchoolscoreController extends ControllerBase
{

    public function indexAction() {
        $test_id = $this->request->get('id');
        $current_page = $this->request->get('page');

        $this->view->id = $test_id;
        $key_word= trim($this->request->get("txtSearchKeyword"));
        $school_class_id=trim($this->request->get("slcClass"));
        $school_class_id = $school_class_id ? $school_class_id : "all";
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
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
        ])->toArray();
        $user = new User();
        foreach ($arrScore as $key => $score) {
            if (empty($arrScore)) {
                break;
            }
            if ($school_class_id != "all") {
                $user_model = $user->findById($score['score_user_id']);
                if ($user_model->getUserSchoolClassId() !== $school_class_id) {
                    if (isset($arrScore[$key])) {
                        unset($arrScore[$key]);

                    }

                }
            }

        }
        $paginator = new NativeArray(array(
            'data'  => $arrScore,
            'limit' => 20,
            'page'  => $current_page,
        ));
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
        $select_school_class = LearnSchoolClass::getCombobox($school_class_id,$test_model->getTestSchoolClassIds());
        if ($this->request->isAjax()) {
            $USER = new User();
            $results = [];
            $t = 0;
            foreach ($arrScore as $score)
            {
                $results[$t]['ID'] =  $USER->getIDByID($score['score_user_id']);
                $results[$t]['Tên'] =  $USER->getNameByID($score['score_user_id']);
                $results[$t]['Lớp'] = $USER->getSchoolClassById($score['score_user_id']);
                $results[$t]['Điểm'] =  $score['score_score'];
                $results[$t]['Thời gian xong'] =  $score['score_time'];
                $results[$t]['Vi phạm'] =  $score['score_total_error'];
                $results[$t]['Thời gian làm bài'] =  $this->my->formatDateTime($score['score_insert_time']);
                $t ++;
            }
            die(json_encode($results));
        }

        $this->view->school_class_id = $school_class_id;
        $this->view->select_school_class  = $select_school_class;
        $this->view->list_item = $paginator->getPaginate();
        $this->view->testName = $test_model->getTestName();
        $this->view->test_model = $test_model;
    }
    public function viewAction() {
        $test_id = $this->request->get('id');
        $user_id = $this->request->get('user-id');
        $score_model = Test::findFirstScoreByIdAndTest($user_id,$test_id);
        $test_model = Test::findFirstById($test_id);
        if (!$test_model || !$score_model) {
            return $this->response->redirect('/notfound');
        }
        if ($test_model->getTestType() == "exam") {
            $test_parent_id = $test_id;
        } else {
            $test_parent_id = $test_model->getTestParentId() == 0 ? $test_id : $test_model->getTestParentId();
        }
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
    public function detailAction() {
        $test_id = $this->request->get('id');
        $user_id = $this->request->get('user-id');
        $score_model = Test::findFirstScoreByIdAndTest($user_id,$test_id);
        $test_model = Test::findFirstById($test_id);
        if (!$test_model || !$score_model) {
            return $this->response->redirect('/notfound');
        }
        if ($test_model->getTestType() == "exam") {
            $test_parent_id = $test_id;
        } else {
            $test_parent_id = $test_model->getTestParentId() == 0 ? $test_id : $test_model->getTestParentId();
        }
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
    public function deleteAction() {
        $items_checked = $this->request->getPost("item");
        $test_id = $this->request->get('id');
        $class_id = $this->request->get('slcClass');

        if (!empty($items_checked)) {
            $msg_result = array();
            $count_delete = 0;
            foreach ($items_checked as $id) {
                $item = LearnScore::findFirst([
                    'score_id = :score:',
                    'bind' => ['score' => $id]
                ]);

                if ($item) {
                    if ($item->delete() === false) {
                        $message_delete = 'Xóa Bài làm không thành công ID = ' . $item->getScoreId();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        $count_delete++;
                    }
                }
            }
            if ($count_delete > 0) {
                $message_delete = 'Xóa ' . $count_delete . ' bài làm thành công.';
                $msg_result['status'] = 'success';
                $msg_result['message'] = $message_delete;
            }
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect('/office/new-list-score?id='.$test_id."&slcClass=".$class_id);
        }
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