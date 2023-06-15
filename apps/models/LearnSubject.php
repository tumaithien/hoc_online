<?php

namespace Learncom\Models;

class LearnSubject extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $subject_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $subject_parent_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $subject_name;
    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $subject_image;
    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $subject_order;

    /**
     * Method to set the value of field subject_id
     *
     * @param integer $subject_id
     * @return $this
     */
    public function setSubjectId($subject_id)
    {
        $this->subject_id = $subject_id;

        return $this;
    }
    /**
     * Method to set the value of field subject_parent_id
     *
     * @param integer $subject_parent_id
     * @return $this
     */
    public function setSubjectParentId($subject_parent_id)
    {
        $this->subject_parent_id = $subject_parent_id;

        return $this;
    }

    /**
     * Method to set the value of field subject_name
     *
     * @param string $subject_name
     * @return $this
     */
    public function setSubjectName($subject_name)
    {
        $this->subject_name = $subject_name;

        return $this;
    }
    /**
     * Method to set the value of field subject_image
     *
     * @param string $subject_image
     * @return $this
     */
    public function setSubjectImage($subject_image)
    {
        $this->subject_image = $subject_image;

        return $this;
    }
    /**
     * Method to set the value of field subject_order
     *
     * @param integer $subject_order
     * @return $this
     */
    public function setSubjectOrder($subject_order)
    {
        $this->subject_order = $subject_order;

        return $this;
    }
    /**
     * Returns the value of field subject_id
     *
     * @return integer
     */
    public function getSubjectId()
    {
        return $this->subject_id;
    }
    /**
     * Returns the value of field subject_parent_id
     *
     * @return integer
     */
    public function getSubjectParentId()
    {
        return $this->subject_parent_id;
    }

    /**
     * Returns the value of field subject_name
     *
     * @return string
     */
    public function getSubjectName()
    {
        return $this->subject_name;
    }
    /**
     * Returns the value of field subject_image
     *
     * @return string
     */
    public function getSubjectImage()
    {
        return $this->subject_image;
    }
    /**
     * Returns the value of field subject_order
     *
     * @return integer
     */
    public function getSubjectOrder()
    {
        return $this->subject_order;
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
        return 'learn_subject';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnSubject[]|LearnSubject
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnSubject
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
