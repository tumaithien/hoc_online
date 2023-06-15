<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnChapter;
use Learncom\Models\LearnGroup;
use Learncom\Models\LearnScore;
use Learncom\Models\LearnTest;
use Phalcon\Mvc\User\Component;

class SchooManage extends Component
{
    const arrIcon = [
        0 => "https://d1q96dymhl7bnj.cloudfront.net/uploads/play-solid-1635345422.svg",//bắt đầu
        1 => "/backoffice/img/exchange-alt-solid.svg",//chuyển câu, xanh da trời
        2 => "/backoffice/img/check-solid.svg",//Chọn đáp án, xanh lá
        3 => "/backoffice/img/reply-solid-red.svg",//rời tab, đỏ
        4 => "/backoffice/img/reply-solid.svg",//trở lại, xanh lá
        5 => "/backoffice/img/sign-out-alt-solid.svg",//tắt full, đỏ
        6 => "/backoffice/img/sign-in-alt-solid.svg",//bật full, xanh lá
        7 => "/backoffice/img/undo-alt-solid.svg",//upload, xanh da trời
    ];
   public  function getHtmlTestNo($arrTest,$arTest2)
   {
       $student = "learncom\Models\LearnUser";
       $test = "learncom\Models\LearnTest";
       $score = "learncom\Models\LearnScore";
       $html = "";
       $arrID = [];
       foreach ($arrTest as $user) {
           if (in_array($user->$student->user_id,$arrID)) {
               continue;
           }
           array_push($arrID,$user->$student->user_id);
           $html .= '<a class="nav-link">';
           $html .=  '<span class="info-student">'.$user->$student->user_name.'</span>';
           $html .= '<span class="info-student">'.$user->$score->score_user_ip.'</span>';
           $html .= '<span class="info-student">'.$user->$score->score_user_device.'</span>';
       }
       foreach ($arTest2 as $user) {
           if (in_array($user->user_id,$arrID)) {
               continue;
           }
           array_push($arrID,$user->user_id);
           $html .= '<a class="nav-link">';
           $html .=  '<span class="info-student">'.$user->user_name.'</span>';
           $html .= '<span class="info-student"></span>';
           $html .= '<span class="info-student"></span>';
       }
       return $html;
   }
    public  function getHtmlTestPending($arrTest,$tab_2_active)
    {
        $student = "learncom\Models\LearnUser";
        $test = "learncom\Models\LearnTest";
        $score = "learncom\Models\LearnScore";
        $html = "";
        foreach ($arrTest as $user) {
            $arrAction = json_decode($user->$score->score_log,true);
            $last_action = end($arrAction);
            $active = "";
            if ($tab_2_active == $user->$student->user_id) {
                $active = "active";
            }
            $arrActionNew = explode(',',$last_action);
            $html .=  '<a  class="nav-link tab-2 '.$active.'" title="'.$user->$student->user_id.'" id="hoc-sinh-'.$user->$student->user_id.'-tab" data-toggle="pill" href="#hoc-sinh-'.$user->$student->user_id.'" role="tab" aria-controls="hoc-sinh-'.$user->$student->user_id.'" aria-selected="true">
                                        <span class="info-student">'.$user->$student->user_name.'</span>
                                        <span class="info-student">'.$user->$score->score_user_ip.'</span>
                                        <span class="info-student">'.$user->$score->score_user_device.'</span>
                                        <span class="info-student">'.$user->$score->score_total_error.'</span>
                                        <span class="info-student">'.$user->$score->score_score_choose.'</span>
                                        <span class="info-student"><img src="'.self::arrIcon[$arrActionNew[1]].'" style="padding-right:10px" alt="" class="mr-10">'.$this->my->formatDateTimeTest($arrActionNew[0]).": ".$arrActionNew[2].'</span>
                                    </a>';
        }
        return $html;
    }
    public  function getHtmlTestSuccess($arrTest,$tab_2_active)
    {
        $student = "learncom\Models\LearnUser";
        $test = "learncom\Models\LearnTest";
        $score = "learncom\Models\LearnScore";
        $html = "";
        foreach ($arrTest as $user) {
            $active = "";
            if ($tab_2_active == $user->$student->user_id) {
                $active = "active";
            }
            $html .=  '<a class="nav-link '.$active.'">
                                        <span class="info-student">'.$user->$student->user_name.'</span>
                                        <span class="info-student">'.$user->$score->score_time.'</span>
                                        <span class="info-student" style="flex: 0 0 30%">'.$user->$score->score_score_choose.'</span>
                                        <span class="info-student">'.$user->$score->score_total_error.'</span>
                                    </a>';
        }
        return $html;
    }
    public  function getListAlert($arrTest,$tab_2_active)
    {
        $student = "learncom\Models\LearnUser";
        $test = "learncom\Models\LearnTest";
        $score = "learncom\Models\LearnScore";
        $html = "";
        foreach ($arrTest as $user) {
            $arrAction = json_decode($user->$score->score_log,true);
            $arrAction = array_reverse($arrAction);
            $active = "";
            if ($tab_2_active == $user->$student->user_id) {
                $active = "active in";
            }
            $html .= '<div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade '.$active.'" id="hoc-sinh-'.$user->$student->user_id.'" role="tabpanel" aria-labelledby="hoc-sinh-'.$user->$student->user_id.'-tab">
                        <div class="list-screen-follow-student">
                            <h2>'.$user->$student->user_name.'</h2>
                            <div class="history-follow-student">';
            $total = 0;
            foreach ($arrAction as $action) {
                $arrActionNew = explode(',',$action);
                $total++;
                if($total > 10) {
                    break;
                }
                $html .= '<span class="item-history-follow-student">
                                              <img src="'.self::arrIcon[$arrActionNew[1]].'" alt="" style="padding-right:10px" class="mr-10">
                '.$this->my->formatDateTimeTest($arrActionNew[0]).": ". $arrActionNew[2] .'
                                            </span>';
            }
            $html .= '    </div>
                        </div>
                    </div>

                </div>';
        }
        return $html;
    }
    public static function updateScoreStatus($id) {
       $arrTest = LearnTest::find([
           'test_id = :id: OR test_parent_id =:id:',
           'bind' => ['id' => $id]
       ]);
        foreach ($arrTest as $test) {
            $test_id = $test->getTestId();
           $arrScore = LearnScore::find("score_test_id = $test_id");
           foreach ($arrScore as $score) {
               if ($score->getScoreScoreChoose() == 0) {
                   $dataScore = $score->getScoreData();
                   $dataScore = json_decode($dataScore,true);
                   $test_model = Test::findFirstById($score->getScoreTestId());
                   $score_choose = self::updateScore($test_model,$dataScore);
                   $score->setScoreScoreChoose($score_choose);
                   $score->setScoreScore($score_choose);
               }
               $score->setScoreStatus("S");
               $score->update();
           }
        }
    }
    public static function updateScore($test_model,$dataScore) {
        $total_success = 0;
        $dataTest = json_decode($test_model->getTestArray(),true);
        $dataAnswer = json_decode($test_model->getTestAnswer(),true);
        foreach ($dataTest as $key => $test) {
            if(isset($dataScore[$key]) && isset($dataAnswer[$test])) {
                if ($dataScore[$key] == $dataAnswer[$test]) {
                    $total_success++;
                }
            }
        }
        $score_choose = round(($total_success/$test_model->getTestNumberQuestion()) * $test_model->getTestScoreChoose(),2);
        return $score_choose;
    }

}



