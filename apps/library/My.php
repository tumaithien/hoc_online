<?php
/**
 * My
 *
 * My Common functions
 */
class My extends Phalcon\Mvc\User\Component
{
    //convert time to local site time
    public function localTime($time)
    {
        return $time + $this->globalVariable->timeZone;
    }

    //convert local site time to UTC0
    public function UTCTime($time)
    {
        return $time - $this->globalVariable->timeZone;
    }
    function formatDateTime($time)
    {
        $time2 = $this->localTime($time);
        $time_format = strftime('%d/%m/%Y | %H:%M:%S', $time2);
        return $time_format;
    }
    function formatDateTimeTest($time)
    {
        $time2 = $this->localTime($time);
        $time_format = strftime('%H:%M:%S', $time2);
        return $time_format;
    }

    function formatDateTimeBirthday($time)
    {
        $time2 = $this->localTime($time);
        $time_format = strftime('%d/%m/%Y', $time2);
        return $time_format;
    }
    function getIdFromFormatID($formattedId, $hasSub = false)
    {
        if (intval($formattedId) != 0 ) {
            if ($hasSub && strlen($formattedId) > 6) {
                return intval(substr($formattedId, 3));
            } else if (strlen($formattedId) > 5){
                return intval(substr($formattedId, 2));
            }
            return $formattedId;
        }else{
            return $formattedId;
        }
    }
    //format Date
    function formatDate($unixTime, $showWeekDay = false){
        if ($showWeekDay){
            return date('l, M d Y', $unixTime);
        }
        return date('M d Y', $unixTime);
    }
    //format Time
    function formatTime($unixTime, $showSecond = true){
        if ($showSecond){
            return date('h:i:s A',$unixTime);
        }
        return date('h:i A',$unixTime);
    }

    function formatTimeArDetail($time)
    {
        return "Updated time: ".date("M j, Y ",$this->localTime($time)).date(", H:i ",$this->localTime($time)) ." (UTC-06:00)";
    }

    public function ssIpInfo()
    {
//        $_SERVER['REMOTE_ADDR'] = "14.161.2.3";
//        $_SERVER['REMOTE_ADDR'] = "109.169.6.152";
        if (!$this->session->has("ssIpInfo")) {
            $ipInfo = \Learncom\Utils\IpApi::info_ip($_SERVER['REMOTE_ADDR']); //$_SERVER['REMOTE_ADDR']
            if ($ipInfo->status == 'success') {
                $this->session->set("ssIpInfo", serialize($ipInfo));
            }
        }
    }

    //send email
    public function sendEmail($frommail,$tomail,$subject,$message,$fromfullname, $tofullname, $reply_to_email, $reply_to_name)
    {
        if (defined('EMAIL_TEST_MODE') && EMAIL_TEST_MODE && defined('EMAIL_TEST_EMAIL')) {
            $tomail = EMAIL_TEST_EMAIL;
        }
        /** @var PHPMailer $mail */
        $mail = $this->myMailer;
        $result = array();
        try {
            //reply to
            $mail->AddReplyTo($reply_to_email, $reply_to_name);
            //
            $mail->SetFrom($frommail, $fromfullname); //from (verified email address)
            $mail->Subject = (defined('EMAIL_SUBJECT_PREFIX') ? EMAIL_SUBJECT_PREFIX : '') .$subject; //subject
            //message
            $body = $message;
            //$body = preg_replace("/\\/i",'',$body);
            $mail->MsgHTML($body);
            //
            //recipient
            $mail->AddAddress($tomail, $tofullname);

            // add bbc
            //$mail->AddBCC($bbc_email, $bbc_name);
            //Success
            $isSent = $mail->Send();
            if ($isSent) {
                $result['success'] = true;
                $result['message'] = "Message sent!";
            }
            else
            {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $mail->ErrorInfo;
            }
        } catch (phpmailerException $e) {
            $result['success'] = false;
            $result['message'] = "Mailer Error: " . $e->errorMessage();//Pretty error messages from PHPMailer
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = "Mailer Error: " . $e->getMessage();//Boring error messages from anything else!
        }
        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
        $mail->ClearAttachments();
        return $result;
    }
    public function renderPagination($url, $page, $totalPages, $limit = 0, $attributes = array())
    {
        if ($page < 1) {
            $page = 1;
        }
        if ($totalPages < 1) {
            $totalPages = 1;
        }

        $disablePrevious = $page <= 1;
        $disableNext = $page >= $totalPages;
        $showLeftDot = ($limit != 0) && ($page - $limit > 2);
        $showRightDot = ($limit != 0) && ($page + $limit < $totalPages - 1);
        $showFirstPage = ($limit != 0) && ($page - $limit >= 2);
        $showLastPage = ($limit != 0) && ($page + $limit <= $totalPages - 1);

        $attributeString = '';
        foreach ($attributes as $key => $value) {
            $key = strtolower(trim((string)$key));
            if ($key != 'class') {
                $attributeString .= $key . '="' . $value . ' ';
            }
        }

        $html = '<ul class="pagination ' . (isset($attributes['class']) ? $attributes['class'] : '') . '" ' . $attributeString . '>';

        if ($disablePrevious) {
            $html .= '<li class="disabled"><span aria-hidden="true">&laquo;</span></li>';
            $html .= '<li class="disabled"><span aria-hidden="true">Previous</span></li>';
        } else {
            $html .= '<li><a href="' . $url . (1) . '"><span aria-hidden="true">&laquo;</span></a></li>';
            $html .= '<li><a href="' . $url . ($page - 1) . '"><span aria-hidden="true">Previous</span></a></li>';
        }

        if ($limit == 0) {
            for ($i = 1; $i <= $totalPages; $i++) {
                if ($page == $i) {
                    $html .= '<li class="active"><span>' . $i . '</span></li>';
                } else {
                    $html .= '<li><a href="' . $url . $i . '">' . $i . '</a></li>';
                }
            }
        } else {
            if ($showFirstPage) {
                $html .= '<li><a href="' . $url . '1">1</a></li>';
            }
            if ($showLeftDot) {
                $html .= '<li><span>&hellip;</span></li>';
            }
            for ($i = $page - $limit; $i <= $page + $limit; $i++) {
                if ($i < 1 || $i > $totalPages) continue;
                if ($page == $i) {
                    $html .= '<li class="active"><span>' . $i . '</span></li>';
                } else {
                    $html .= '<li><a href="' . $url . $i . '">' . $i . '</a></li>';
                }
            }
            if ($showRightDot) {
                $html .= '<li><span>&hellip;</span></li>';
            }
            if ($showLastPage) {
                $html .= '<li><a href="' . $url . $totalPages . '">' . $totalPages . '</a></li>';
            }
        }

        if ($disableNext) {
            $html .= '<li class="disabled"><span aria-hidden="true">Next</span></li>';
            $html .= '<li class="disabled"><span aria-hidden="true">&raquo;</span></li>';
        } else {
            $html .= '<li><a href="' . $url . ($page + 1) . '"><span aria-hidden="true">Next</span></a></li>';
            $html .= '<li><a href="' . $url . ($totalPages) . '"><span aria-hidden="true">&raquo;</span></a></li>';
        }

        $html .= '</ul>';

        return $html;
    }
    public function renderPaginationNew($url, $page, $totalPages, $limit = 0, $attributes = array())
    {
        if ($page < 1) $page = 1;
        if ($totalPages < 1) $totalPages = 1;

        $disablePrevious = $page <= 1;
        $disableNext = $page >= $totalPages;
        $showLeftDot = ($limit != 0) && ($page - $limit > 2);
        $showRightDot = ($limit != 0) && ($page + $limit < $totalPages - 1);
        $showFirstPage = ($limit != 0) && ($page - $limit >= 2);
        $showLastPage = ($limit != 0) && ($page + $limit <= $totalPages - 1);

        $attributeString = '';
        foreach ($attributes as $key => $value) {
            $key = strtolower(trim((string)$key));
            if ($key != 'class') {
                $attributeString .= $key . '="' . $value . ' ';
            }
        }

        $html = '<ul class="pagination end ' . (isset($attributes['class']) ? $attributes['class'] : '') . '" ' . $attributeString . '>';

        if ($disablePrevious) {
            $html .= '<li class="disabled"><span class="item">&laquo;</span></li>';
            $html .= '<li class="disabled"><span class="item">Previous</span></li>';
        } else {
            $html .= '<li><a class="item" href="' . $url . (1) . '" >&laquo;</a></li>';
            $html .= '<li><a class="item" href="' . $url . ($page - 1) . '" >Previous</a></li>';
        }

        if ($limit == 0) {
            for ($i = 1; $i <= $totalPages; $i++) {
                if ($page == $i) {
                    $html .= '<li class="active"><span class="item">' . $i . '</span></li>';
                } else {
                    $html .= '<li><a class="item" href="' . $url . $i . '" >' . $i . '</a></li>';
                }
            }
        } else {
            if ($showFirstPage) $html .= '<li><a class="item" href="' . $url . (1) . '" >1</a></li>';
            if ($showLeftDot) $html .= '<li><a class="item">&hellip;</a></li>';
            for ($i = $page - $limit; $i <= $page + $limit; $i++) {
                if ($i < 1 || $i > $totalPages) continue;
                if ($page == $i) {
                    $html .= '<li class="active"><span class="item">' . $i . '</span></li>';
                } else {
                    $html .= '<li><a class="item" href="' . $url . $i . '" >' . $i . '</a></li>';
                }
            }
            if ($showRightDot) $html .= '<li><a class="item">&hellip;</a></li>';
            if ($showLastPage) $html .= '<li><a class="item" href="' . $url . $totalPages . '" >' . $totalPages . '</a></li>';
        }

        if ($disableNext) {
            $html .= '<li class="disabled"><span class="item">Next</span></li>';
            $html .= '<li class="disabled"><span class="item">&raquo;</span></li>';
        } else {
            $html .= '<li><a class="item" href="' . $url . ($page + 1) . '" >Next</a></li>';
            $html .= '<li><a class="item" href="' . $url . ($totalPages) . '" >&raquo;</a></li>';
        }

        $html .= '</ul>';
        return $html;
    }
    //format ID
    public function formatID($idType, $insertTime, $id, $suffix='')
    {
        $insertYear = date('Y', $insertTime);
        $y = substr($insertYear, strlen($insertYear)-1);
        return sprintf("%s%s%04d%s",$idType,$y,$id,$suffix);
    }
    //format Contact ID
    public function formatContactID($insertTime, $id)
    {
        return $this->formatID(4,$insertTime, $id);
    }

    public function redirectToNotFoundPage()
    {
        $this->response->redirect('/notfound');
    }
}