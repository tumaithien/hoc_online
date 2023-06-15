<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnClass;
use Learncom\Models\LearnSubject;
use Learncom\Models\LearnVideo;
use Phalcon\Mvc\User\Component;
use \Learncom\Models\LearnArticle;

class Login extends Component
{
    public static function generateKey($length=50) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}



