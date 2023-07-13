<?php

namespace Learncom\Repositories;


use Learncom\Models\LearnExam;
use Learncom\Models\LearnTestFolder;
use Learncom\Models\LearnVideo;
use Phalcon\Mvc\User\Component;

class Upload extends Component
{
   public static function uploadFile(&$uploadFiles,&$messages, $name_upload = "fileUpload") {
       if ($_SERVER['REQUEST_METHOD'] == "POST") {
           $error = array();
           $target_dir = __DIR__."/../../public/frontend/upload/images/";
           $target_file = $target_dir . basename($_FILES[$name_upload]['name']);
           $type_file = pathinfo($_FILES[$name_upload]['name'], PATHINFO_EXTENSION);

           $type_fileAllow = array('png', 'jpg', 'jpeg', 'gif');
           if (!in_array(strtolower($type_file), $type_fileAllow)) {
               $error[$name_upload] = "File bạn vừa chọn hệ thống không hỗ trợ, bạn vui lòng chọn hình ảnh";
           }
           $size_file = $_FILES[$name_upload]['size'];
           if ($size_file > 5242880) {
               $error[$name_upload] = "File bạn chọn không được quá 5MB";
           }
           if (file_exists($target_file)) {
               unlink($target_file);
           }
           if (empty($error)) {
               if (move_uploaded_file($_FILES[$name_upload]["tmp_name"], $target_file)) {
                   $messages = [
                       "type" => "success",
                       "message" =>  "Bạn đã upload file thành công",
                   ];
                   $uploadFiles[] = array(
                       "file_name" => $_FILES[$name_upload]['name'],
                       "file_size" => $_FILES[$name_upload]['size'],
                       "file_type" => $type_file,
                       "file_url" => "/frontend/upload/images/".$_FILES[$name_upload]['name']
                   );
                   $flag = true;
               } else {
                   $messages = [
                       "type" => "error",
                       "message" =>  "File bạn vừa upload gặp sự cố",
                   ];
               }
           } else {
               $messages = [
                   "type" => "error",
                   "message" =>  $error[$name_upload],
               ];
           }
       }
   }
    public static function uploadFileScore(&$uploadFiles,&$messages,$t) {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $error = array();
            $start_time = time();
            $target_dir = __DIR__."/../../public/frontend/upload/images/".$start_time;
            $target_file = $target_dir . basename($_FILES['fileUpload']['name'][$t]);
            $type_file = pathinfo($_FILES['fileUpload']['name'][$t], PATHINFO_EXTENSION);

            $type_fileAllow = array('png', 'jpg', 'jpeg', 'gif');
            if (!in_array(strtolower($type_file), $type_fileAllow)) {
                $error['fileUpload'] = "File bạn vừa chọn hệ thống không hỗ trợ, bạn vui lòng chọn hình ảnh";
            }
            $size_file = $_FILES['fileUpload']['size'][$t];
            if ($size_file > 5242880) {
                $error['fileUpload'][$t] = "File bạn chọn không được quá 5MB";
            }
            if (file_exists($target_file)) {
                unlink($target_file);
            }
            if (empty($error)) {
                if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"][$t], $target_file)) {
                    $messages = [
                        "type" => "success",
                        "message" =>  "Bạn đã upload file thành công",
                    ];
                    $uploadFiles[] = array(
                        "file_name" => $_FILES['fileUpload']['name'][$t],
                        "file_size" => $_FILES['fileUpload']['size'][$t],
                        "file_type" => $type_file,
                        "file_url" => "/frontend/upload/images/".$start_time.$_FILES['fileUpload']['name'][$t]
                    );
                    $flag = true;
                } else {
                    $messages = [
                        "type" => "error",
                        "message" =>  "File bạn vừa upload gặp sự cố",
                    ];
                }
            } else {
                $messages = [
                    "type" => "error",
                    "message" =>  $error['fileUpload'],
                ];
            }
        }
    }

    public static function uploadFileMulti(&$uploadFiles,&$messages,$item,$id) {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $error = array();
            $target_dir = __DIR__."/../../public/test/".$id;
            if (!is_dir($target_dir)) {
                /* mkdir($folder, 0777, TRUE,NULL); */
                mkdir($target_dir, 0777,TRUE);
            }
            $target_file = $target_dir ."/". basename($_FILES['fileUpload']['name'][$item]);
            $type_file = pathinfo($_FILES['fileUpload']['name'][$item], PATHINFO_EXTENSION);

            $type_fileAllow = array('png', 'jpg', 'jpeg', 'gif');
            if (!in_array(strtolower($type_file), $type_fileAllow)) {
                $error['fileUpload'] = "File bạn vừa chọn hệ thống không hỗ trợ, bạn vui lòng chọn hình ảnh";
            }
            $size_file = $_FILES['fileUpload']['size'][$item];
            if ($size_file > 5242880) {
                $error['fileUpload'] = "File bạn chọn không được quá 5MB";
            }
            if (file_exists($target_file)) {
                unlink($target_file);
            }
            $status = substr($_FILES["fileUpload"]["name"][$item],3,3);
            if (empty($error)) {
                if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"][$item], $target_file)) {
                    $new_file = new LearnTestFolder();
                    $new_file->setFolderTestId($id);
                    $new_file->setFolderLink("/public/test/".$id."/".$_FILES["fileUpload"]["name"][$item]);
                    $new_file->setFolderStatus($status);
                    $new_file->setFolderLinkComment("");
                    //set comment
                    if (isset($_FILES["fileUploadComment"]["name"][$item])) {
                        foreach ($_FILES["fileUploadComment"]["name"] as $item_comment => $value) {
                            if ($value == $_FILES["fileUpload"]["name"][$item]) {
                                $target_dir_comment = __DIR__."/../../public/comment/".$id;
                                if (!is_dir($target_dir_comment)) {
                                    /* mkdir($folder, 0777, TRUE,NULL); */
                                    mkdir($target_dir_comment, 0777,TRUE);
                                }
                                $target_file_comment = $target_dir_comment ."/". basename($_FILES['fileUploadComment']['name'][$item_comment]);
                                move_uploaded_file($_FILES["fileUploadComment"]["tmp_name"][$item_comment], $target_file_comment);
                                $new_file->setFolderLinkComment("/public/comment/".$id."/".$_FILES["fileUpload"]["name"][$item]);
                                break;
                            }
                        }
                    }
                    if ($new_file->save()) {
                        $messages = [
                            "type" => "success",
                            "message" =>  "Bạn đã upload file thành công",
                        ];
                    } else {
                        $messages = [
                            "type" => "error",
                            "message" =>  "File bạn vừa upload gặp sự cố",
                        ];
                    }

                } else {
                    $messages = [
                        "type" => "error",
                        "message" =>  "File bạn vừa upload gặp sự cố",
                    ];
                }
            } else {
                $messages = [
                    "type" => "error",
                    "message" =>  $error['fileUpload'],
                ];
            }
        }
    }
    public static function uploadFileMultiForExam(&$arrID,&$messages,$item,$id,$arrAnswer) {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $error = array();
            $target_dir = __DIR__."/../../public/Khode/".$id;
            if (!is_dir($target_dir)) {
                /* mkdir($folder, 0777, TRUE,NULL); */
                mkdir($target_dir, 0777,TRUE);
            }
            $start_time = time();
            $target_file = $target_dir ."/". basename($start_time.$_FILES['fileUpload']['name'][$item]);
            $type_file = pathinfo($_FILES['fileUpload']['name'][$item], PATHINFO_EXTENSION);

            $type_fileAllow = array('png', 'jpg', 'jpeg', 'gif');
            if (!in_array(strtolower($type_file), $type_fileAllow)) {
                $error['fileUpload'] = "File bạn vừa chọn hệ thống không hỗ trợ, bạn vui lòng chọn hình ảnh";
            }
            $size_file = $_FILES['fileUpload']['size'][$item];
            if ($size_file > 5242880) {
                $error['fileUpload'] = "File bạn chọn không được quá 5MB";
            }
            if (file_exists($target_file)) {
                unlink($target_file);
            }
            $status = substr($_FILES["fileUpload"]["name"][$item],3,3);
            $status = intval($status);
            if (empty($error)) {
                if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"][$item], $target_file)) {
                    $aswer = isset($arrAnswer[$status]) ? $arrAnswer[$status] : "";
                    $new_file = new LearnExam();
                    $new_file->setExamLinkTest("/public/Khode/".$id."/".$start_time.$_FILES["fileUpload"]["name"][$item]);
                    if ($aswer) {
                        $new_file->setExamAnswer($aswer);
                    }
                    $new_file->setExamGroupId($id);
                    //set comment
                    if (isset($_FILES["fileUploadComment"]["name"][$item])) {
                        foreach ($_FILES["fileUploadComment"]["name"] as $item_comment => $value) {
                            if ($value == $_FILES["fileUpload"]["name"][$item]) {
                                $target_dir_comment = __DIR__."/../../public/loigiaikhode/".$id;
                                if (!is_dir($target_dir_comment)) {
                                    /* mkdir($folder, 0777, TRUE,NULL); */
                                    mkdir($target_dir_comment, 0777,TRUE);
                                }
                                $target_file_comment = $target_dir_comment ."/". basename($start_time.$_FILES['fileUploadComment']['name'][$item_comment]);
                                move_uploaded_file($_FILES["fileUploadComment"]["tmp_name"][$item_comment], $target_file_comment);
                                $new_file->setExamLinkAnswer("/public/loigiaikhode/".$id."/".$start_time.$_FILES["fileUpload"]["name"][$item]);
                                break;
                            }
                        }
                    }
                    if ($new_file->save()) {
                        array_push($arrID,$new_file->getExamId());
                        $messages = [
                            "type" => "success",
                            "message" =>  "Bạn đã upload file thành công",
                        ];
                    } else {
                        $messages = [
                            "type" => "error",
                            "message" =>  "File bạn vừa upload gặp sự cố",
                        ];
                    }

                } else {
                    $messages = [
                        "type" => "error",
                        "message" =>  "File bạn vừa upload gặp sự cố",
                    ];
                }
            } else {
                $messages = [
                    "type" => "error",
                    "message" =>  $error['fileUpload'],
                ];
            }
        }
    }
    public static function uploadFileTest(&$uploadFiles,&$messages,$test_id,$is_test) {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $error = array();
            $start_time = time();
            if ($is_test) {
                $target_dir = __DIR__."/../../public/test/".$test_id;
                $file = $_FILES['fileUpload'];
                $link = "/public/test/".$test_id."/".$start_time.$file['name'];
            } else {
                $target_dir = __DIR__."/../../public/comment/".$test_id;
                $file = $_FILES['fileUploadComment'];
                $link = "/public/comment/".$test_id."/".$start_time.$file['name'];
            }
            if (!is_dir($target_dir)) {
                /* mkdir($folder, 0777, TRUE,NULL); */
                mkdir($target_dir, 0777,TRUE);
            }
            $target_file = $target_dir ."/".$start_time. basename($file['name']);
            $type_file = pathinfo($file['name'], PATHINFO_EXTENSION);

            $type_fileAllow = array('png', 'jpg', 'jpeg', 'gif');
            if (!in_array(strtolower($type_file), $type_fileAllow)) {
                $error['fileUpload'] = "File bạn vừa chọn hệ thống không hỗ trợ, bạn vui lòng chọn hình ảnh";
            }
            $size_file = $file['size'];
            if ($size_file > 5242880) {
                $error['fileUpload'] = "File bạn chọn không được quá 5MB";
            }
            if (file_exists($target_file)) {
                unlink($target_file);
            }
            if (empty($error)) {
                if (move_uploaded_file($file["tmp_name"], $target_file)) {
                    $messages = [
                        "type" => "success",
                        "message" =>  "Bạn đã upload file thành công",
                    ];
                    $uploadFiles[] = array(
                        "file_name" => $file['name'],
                        "file_size" => $file['size'],
                        "file_type" => $type_file,
                        "file_url" => $link
                    );
                    $flag = true;
                } else {
                    $messages = [
                        "type" => "error",
                        "message" =>  "File bạn vừa upload gặp sự cố",
                    ];
                }
            } else {
                $messages = [
                    "type" => "error",
                    "message" =>  $error['fileUpload'],
                ];
            }
        }
    }
    public static function deleteDirTest($id) {
        $dir = __DIR__."/../../public/test/".$id;
        $target_dir = $dir."/*";
        if (is_dir($dir)) {
            $arrDir = glob($target_dir);
            foreach ($arrDir as $file ) {
                if(is_file($file))
                    unlink($file);
            }
            rmdir($dir);
        }
        $dir = __DIR__."/../../public/comment/".$id;
        $target_dir = $dir."/*";
        if (is_dir($dir)) {
            $arrDir = glob($target_dir);
            foreach ($arrDir as $file ) {
                if(is_file($file))
                    unlink($file);
            }
            rmdir($dir);
        }
    }

    public static function deleteDirExam($test_link,$answer_link = "",$check_exam = false) {
       if (!$check_exam && (strpos('Khode',$test_link) || strpos('Khode',$answer_link))) {
           return;
       }
        $dir_test = __DIR__."/../..".$test_link;
        $dir_aswer = __DIR__."/../..".$answer_link;
        if ($test_link) {
            unlink($dir_test);
        }
        if ($answer_link) {
            unlink($dir_aswer);
        }
    }
    public static function deleteSchoolScore($test_link,$answer_link = "",$check_exam = false) {
        if (!$check_exam && (strpos('Khode',$test_link) || strpos('Khode',$answer_link))) {
            return;
        }
        $dir_test = __DIR__."/../../public/".$test_link;
        $dir_aswer = __DIR__."/../..".$answer_link;
        if ($test_link) {
            unlink($dir_test);
        }
        if ($answer_link) {
            unlink($dir_aswer);
        }
    }
    public static function deleteFile($name) {

    }
}



