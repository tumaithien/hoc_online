<?php


namespace Learncom\Models;

use Learncom\Models\BaseModel as ModelsBaseModel;
use Learncom\Repositories\CacheRepo;
use Phalcon\Mvc\Model;

class BaseModelCache extends BaseModel
{
    public function initialize()
    {
        //  var_dump($this->beforeValidationOnCreate());exit;
    }
    public function beforeValidationOnCreate()
    {
        $this->deleteCache();
    }
    public function beforeValidationOnUpdate()
    {
        $this->deleteCache();

    }
    public function beforeValidationOnDelete()
    {
        $this->deleteCache();

    }
    public function deleteCache()
    {
        $cache = new CacheRepo();
        $cache->deleteAllCache();
    }
}