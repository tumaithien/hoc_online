<?php


namespace Learncom\Models;


use Learncom\Repositories\CacheRepo;
use Phalcon\Mvc\Model;

class BaseModel extends Model
{
    public function initialize()
    {
        //  var_dump($this->beforeValidationOnCreate());exit;
    }
   
}