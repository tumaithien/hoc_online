<?php
namespace Learncom\Frontend\Controllers;

use Learncom\Repositories\Page;
use Learncom\Repositories\SendError;

class NotfoundController extends ControllerBase
{
    public function indexAction()
    {
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('notfound','404','notfound');
        $message = "";
        $sent_error = new SendError();
        $sent_error->sendErrorNotfound($message);
    }
    public function alertAction() {

    }
    public function nodataAction() {

    }
    public function timeAction() {

    }
    public function errorAction() {

    }
}