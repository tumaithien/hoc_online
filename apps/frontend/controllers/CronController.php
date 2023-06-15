<?php
namespace Learncom\Frontend\Controllers;

use Learncom\Models\LearnScore;

class CronController extends ControllerBase
{
    public function updatesuccesstestAction()
    {
        $this->view->disable();
        //check ctoken
        $token = $this->request->get('ctoken');
        if($token!=$this->globalVariable->serect_key)
        {
            echo "Invalid token!";
            return;
        }
        $startTime = $this->globalVariable->curTime;
        $total = 0;
        echo "\r\n--------- CRON BEGIN AT ".$this->my->formatDateTime($startTime)."------\r\n";
        $arrScoreP = LearnScore::find('score_status = "P"');
        foreach ($arrScoreP as $score) {
            if (($startTime - $score->getScoreUpdateTime()) > 30*60) {
                $score->setScoreUpdateTime($startTime);
                $score->setScoreStatus("S");
                $result = $score->save();
                if ($result) {
                    echo $score->getScoreId();
                    $total ++;
                }
            }
        }
        $runTime = microtime(true) - $startTime;
        echo "----------------------------------------------------------------------------\r\n";
        echo "\r\nUpdate Status Score success in " . $runTime . "\r\n";
        echo "---Total Success: ".$total."\r\n";
    }
}