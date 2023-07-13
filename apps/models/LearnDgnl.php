<?php
namespace Learncom\Models;

class LearnDgnl extends BaseModelCache
{
    public $dgnl_id;
    public $dgnl_type_id;
    public $learn_category;
    public $dgnl_name;
    public $dgnl_link;
    public $dgnl_order;
    public $dgnl_description;

     /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'learn_dgnl';
    }

    public function getId()
    {
        return $this->dgnl_id;
    }

    public function getType()
    {
        return $this->dgnl_type_id;
    }

    public function setType($type)
    {
        $this->dgnl_type_id = $type;
    }

    public function getCategory()
    {
        return $this->learn_category;
    }

    public function setCategory($category)
    {
        $this->learn_category = $category;
    }

    public function getName()
    {
        return $this->dgnl_name;
    }

    public function setName($name)
    {
        $this->dgnl_name = $name;
    }

    public function getLink()
    {
        return $this->dgnl_link;
    }

    public function setLink($link)
    {
        $this->dgnl_link = $link;
    }

    public function getOrder()
    {
        return $this->dgnl_order;
    }

    public function setOrder($order)
    {
        $this->dgnl_order = $order;
    }

    public function getDescription()
    {
        return $this->dgnl_description;
    }

    public function setDescription($description)
    {
        $this->dgnl_description = $description;
    }
    
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnDgnl[]|LearnDgnl
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnDgnl
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
