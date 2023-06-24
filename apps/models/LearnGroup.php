<?php

namespace Learncom\Models;

class LearnGroup extends BaseModelCache
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $group_id;
    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $group_type;
    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $group_class_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $group_subject_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $group_name;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $group_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $group_active;

    /**
     * Method to set the value of field group_id
     *
     * @param integer $group_id
     * @return $this
     */
    public function setGroupId($group_id)
    {
        $this->group_id = $group_id;

        return $this;
    }

    /**
     * Method to set the value of field group_class_id
     *
     * @param integer $group_class_id
     * @return $this
     */
    public function setGroupClassId($group_class_id)
    {
        $this->group_class_id = $group_class_id;

        return $this;
    }

    /**
     * Method to set the value of field group_subject_id
     *
     * @param integer $group_subject_id
     * @return $this
     */
    public function setGroupSubjectId($group_subject_id)
    {
        $this->group_subject_id = $group_subject_id;

        return $this;
    }
    /**
     * Method to set the value of field group_type
     *
     * @param string $group_type
     * @return $this
     */
    public function setGroupType($group_type)
    {
        $this->group_type = $group_type;

        return $this;
    }
    /**
     * Method to set the value of field group_name
     *
     * @param string $group_name
     * @return $this
     */
    public function setGroupName($group_name)
    {
        $this->group_name = $group_name;

        return $this;
    }

    /**
     * Method to set the value of field group_order
     *
     * @param integer $group_order
     * @return $this
     */
    public function setGroupOrder($group_order)
    {
        $this->group_order = $group_order;

        return $this;
    }

    /**
     * Method to set the value of field group_active
     *
     * @param string $group_active
     * @return $this
     */
    public function setGroupActive($group_active)
    {
        $this->group_active = $group_active;

        return $this;
    }

    /**
     * Returns the value of field group_id
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    /**
     * Returns the value of field group_class_id
     *
     * @return integer
     */
    public function getGroupClassId()
    {
        return $this->group_class_id;
    }

    /**
     * Returns the value of field group_subject_id
     *
     * @return integer
     */
    public function getGroupSubjectId()
    {
        return $this->group_subject_id;
    }
    /**
     * Returns the value of field group_type
     *
     * @return string
     */
    public function getGroupType()
    {
        return $this->group_type;
    }
    /**
     * Returns the value of field group_name
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->group_name;
    }

    /**
     * Returns the value of field group_order
     *
     * @return integer
     */
    public function getGroupOrder()
    {
        return $this->group_order;
    }

    /**
     * Returns the value of field group_active
     *
     * @return string
     */
    public function getGroupActive()
    {
        return $this->group_active;
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
        return 'learn_group';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnGroup[]|LearnGroup
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnGroup
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
