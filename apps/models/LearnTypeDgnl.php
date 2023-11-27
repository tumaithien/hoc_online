<?php
namespace Learncom\Models;

class LearnTypeDgnl extends BaseModelCache
{
    public $type_id;
    public $type_name;
    public $type_teacher;
    public $type_descriptiont;
    public $type_image;
    public $type_icon;
    public $type_content;

    public $type_order;


    public static function getCheckboxDGNL($arrChecked, $id = -1)
    {
        $output = '';
        $dataService = LearnTypeDgnl::find();
        foreach ($dataService as $value) {
            $checked = '';
            if (in_array($value->getId(), $arrChecked)) {
                $checked = 'checked';
            }
            if ($id != -1) {
                $output .= "<input class='check_class check_box search_group' type='checkbox' " . $checked . " id='class_" . $id . "_" . $value->getId() . "' name='slcType[" . $id . "][]' value='" . $value->getId() . "'>" . " " . addslashes(preg_replace("/\r|\n/", "", $value->getName())) . "<br>";
            } else {
                $output .= "<input class='check_class check_box search_group' type='checkbox' " . $checked . " id='class_" . $value->getId() . "' name='slcType[]' value='" . $value->getId() . "'>" . " " . addslashes(preg_replace("/\r|\n/", "", $value->getName())) . "<br>";
            }
        }
        return $output;
    }
    public static function getNameById($id)
    {
        $model = self::findFirstById($id);
        return $model ? $model->getName() : "";

    }
    public static function findFirstById($id)
    {
        return self::findFirst([
            'type_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }
    public static function getComboboxClass($type)
    {
        $data = self::find();
        $output = '';
        foreach ($data as $value) {
            $selected = '';
            if ($value->getId() == $type) {
                $selected = 'selected';
            }
            $output .= "<option " . $selected . " value='" . $value->getId() . "'>" . $value->getName() . "</option>";
        }
        return $output;
    }
    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'learn_type_dgnl';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnTypeDgnl[]|LearnTypeDgnl
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnTypeDgnl
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function getId()
    {
        return $this->type_id;
    }

    public function getName()
    {
        return $this->type_name;
    }

    public function setName($name)
    {
        $this->type_name = $name;
    }

    public function getTeacher()
    {
        return $this->type_teacher;
    }

    public function setTeacher($teacher)
    {
        $this->type_teacher = $teacher;
    }

    public function getDescription()
    {
        return $this->type_descriptiont;
    }

    public function setDescription($description)
    {
        $this->type_descriptiont = $description;
    }

    public function getImage()
    {
        return $this->type_image;
    }

    public function setImage($image)
    {
        $this->type_image = $image;
    }

    public function getIcon()
    {
        return $this->type_icon;
    }

    public function setIcon($icon)
    {
        $this->type_icon = $icon;
    }

    public function getContent()
    {
        return $this->type_content;
    }

    public function setContent($content)
    {
        $this->type_content = $content;
    }
    public function getOrder()
    {
        return $this->type_order;
    }

    public function setOrder($order)
    {
        $this->type_order = $order;
    }
}