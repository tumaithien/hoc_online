<?php

namespace Learncom\Backend\Controllers;
use Learncom\Repositories\Upload;
use Learncom\Utils\Validator;
require __DIR__ ."/../../library/aws/aws-autoloader.php";

class ClouduploadController extends ControllerBase
{

    public function indexAction()
    {
        $uploadFiles = array();
        $error = [];
        $messages = [];
        // Check if the user has uploaded files
        if ($this->request->hasFiles() == true)
        {
            Upload::uploadFile($uploadFiles,$messages);
        }
        $this->view->message = $messages;
        $this->view->uploadFiles = $uploadFiles;
    }
}