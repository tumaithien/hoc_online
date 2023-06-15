<?php

namespace Learncom\Repositories;

use Phalcon\Mvc\User\Component;

class Log extends Component
{
    public function writefile($actionName, $userName ){
        //$name =$date->format('Y-m-d H:i:s')

        date_default_timezone_set('UTC');
        $curTime = time()+6.5*3600;
        $name= date('Y-m-d',$curTime);
        //$myDateTime = DateTime::createFromFormat('Y-m-d', $curTime);
        //$formattedweddingdate = $myDateTime->format('d-m-Y');
        $myFile =__DIR__ ."/../logs/".$name.".txt";
        $folder = __DIR__ ."/../logs";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, TRUE);
        }
        //chmod($myFile,0755);
        $hourMinuteSeconds = "Time:    ".date('H:i:s',$curTime)."<br>";

        $ua=$this->getBrowser();
        $browser= "Agent user: Browser: " . $ua['name'] . " " . $ua['version'] . " on " .$ua['platform'];
        $ip = "----Ip:".getHostByName(php_uname('n'))."<br>";
        $userName = "User Name: ".$userName."<br>";
        $action   = "Action:   ".$actionName."<br>";
        $end ="=====================================================================";
        $message = $hourMinuteSeconds.$browser.$ip.$userName.$action.$end;
        if (file_exists($myFile)) {
            $fh = fopen($myFile, 'a+');
            fwrite($fh, $message."<br>");
        } else {
            $fh = fopen($myFile, 'w+');
            fwrite($fh, $message."<br>");
        }
        fclose($fh);
        return $myFile;
    }
    /*Create Write Library */
    public function editfile($actionName, $userName,$id){
        //$name =$date->format('Y-m-d H:i:s')

        date_default_timezone_set('UTC');
        $curTime = time()+6.5*3600;
        $name= date('Y-m-d',$curTime);
        //$myDateTime = DateTime::createFromFormat('Y-m-d', $curTime);
        //$formattedweddingdate = $myDateTime->format('d-m-Y');
        $myFile =__DIR__ ."/../logs/".$name.".txt";
        $folder = __DIR__ ."/../logs";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, TRUE);
        }
        //chmod($myFile,0755);
        $hourMinuteSeconds = "Time:    ".date('H:i:s',$curTime)."<br>";

        $ua=$this->getBrowser();
        $browser= "Agent user: Browser: " . $ua['name'] . " " . $ua['version'] . " on " .$ua['platform'];
        $ip = "----Ip:".getHostByName(php_uname('n'))."<br>";
        $userName = "User Name: ".$userName."<br>";
        $action   = "Action:   ".$actionName."<br>";
        $key    = "ID:". $id."<br>";
        $end ="=====================================================================";
        $message = $hourMinuteSeconds.$browser.$ip.$userName.$action.$key.$end;
        if (file_exists($myFile)) {
            $fh = fopen($myFile, 'a+');
            fwrite($fh, $message."<br>");
        } else {
            $fh = fopen($myFile, 'w+');
            fwrite($fh, $message."<br>");
        }
        fclose($fh);

        return $myFile;
    }
    public function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i',$u_agent))
        {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i',$u_agent))
        {
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif(preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    }
    public function readLog($fromDate, $toDate){
        $dir=__DIR__ ."/../logs/";
        $fromTime = strtotime($fromDate);
        $toTime =strtotime($toDate);
        $arrFile = "";
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'txt')
                    {
                        $file = strtolower(substr($file,0, strrpos($file, '.')));
                        $fileTime = strtotime($file);
                        if($file != NULL ){
                            if(($fromDate !=NULL) &&($toDate !=NULL)){
                                if(($toTime >= $fileTime)&&($fromTime <= $fileTime)){
                                    $arrFile .=$file.";";
                                }
                            }else {
                                $arrFile .=$file.";";
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
        return $arrFile;

    }


}