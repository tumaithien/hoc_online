<?php

namespace Learncom\Models;

class LearnSchoolClass extends BaseModelCache
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false)
     */
    protected $class_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $class_name;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $class_order;

    /**
     * Method to set the value of field class_id
     *
     * @param integer $class_id
     * @return $this
     */
    public function setClassId($class_id)
    {
        $this->class_id = $class_id;

        return $this;
    }

    /**
     * Method to set the value of field class_name
     *
     * @param string $class_name
     * @return $this
     */
    public function setClassName($class_name)
    {
        $this->class_name = $class_name;

        return $this;
    }

    /**
     * Method to set the value of field class_order
     *
     * @param integer $class_order
     * @return $this
     */
    public function setClassOrder($class_order)
    {
        $this->class_order = $class_order;

        return $this;
    }

    /**
     * Returns the value of field class_id
     *
     * @return integer
     */
    public function getClassId()
    {
        return $this->class_id;
    }

    /**
     * Returns the value of field class_name
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * Returns the value of field class_order
     *
     * @return integer
     */
    public function getClassOrder()
    {
        return $this->class_order;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("hoc_truc_tuyen");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'learn_school_class';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnSchoolClass[]|LearnSchoolClass
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnSchoolClass
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function findFirstByClasId($id) {
        return self::findFirst([
            'class_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }
    public static function getNameById($id) {
        $model =  self::findFirstByClasId($id);
        return $model ? $model->getClassName() : "";
    }
    public static function getNameInTest($arrId) {
        $arrName = [];
        foreach ($arrId as $id) {
            array_push($arrName,self::getNameById($id));
        }
        return implode(', ',$arrName);
    }
    public static function getCombobox($class_id,$arrClass = "") {
        if (!empty($arrClass)) {
            $data = self::find([
                'FIND_IN_SET(class_id,:arrClass:)',
                'bind' => ['arrClass' => $arrClass]
            ]);
        } else {
            $data = self::find();
        }
        $output = '';
        $class_ids = explode(',',$class_id);
        foreach ($data as $value) {
            $selected = '';
            if (in_array($value->getClassId(),$class_ids)) {
                $selected = 'selected';
            }
            $output .= "<option " . $selected . " value='" . $value->getClassId() . "'>" . $value->getClassName() . "</option>";
        }
        return $output;
    }
    public static function getCheckboxSchoolClass ($arrChecked){
        $data = self::find(['order' => "class_name ASC"]);
        $output = "";
        if($data) {
            foreach ($data as $key => $value)
            {
                $checked = (in_array($value->getClassId(),$arrChecked)) ? 'checked' : '';
                $output.= "<div class='role_block country_block col-md-4'><label class='container_checkbox'> ".$value->getClassName()."
                            <input type='checkbox' 
                                    class='form-control check' 
                                    name='slcSchoolClass[]' 
                                     " . $checked. " 
                                     value='" . $value->getClassId() . "' />
                            <span class='checkmark_checkbox'></span>
                            </label>
                            </div> ";
            }
        }

        return $output;
    }
}
