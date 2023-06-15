<?php

namespace Learncom\Models;

class LearnExamGroup extends \Phalcon\Mvc\Model
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
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $group_exam_subject_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $group_chapter_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $group_lesson_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $group_type_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $group_data_test;

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
     * Method to set the value of field group_exam_subject_id
     *
     * @param integer $group_exam_subject_id
     * @return $this
     */
    public function setGroupExamSubjectId($group_exam_subject_id)
    {
        $this->group_exam_subject_id = $group_exam_subject_id;

        return $this;
    }

    /**
     * Method to set the value of field group_chapter_id
     *
     * @param integer $group_chapter_id
     * @return $this
     */
    public function setGroupChapterId($group_chapter_id)
    {
        $this->group_chapter_id = $group_chapter_id;

        return $this;
    }

    /**
     * Method to set the value of field group_lesson_id
     *
     * @param integer $group_lesson_id
     * @return $this
     */
    public function setGroupLessonId($group_lesson_id)
    {
        $this->group_lesson_id = $group_lesson_id;

        return $this;
    }

    /**
     * Method to set the value of field group_type_id
     *
     * @param integer $group_type_id
     * @return $this
     */
    public function setGroupTypeId($group_type_id)
    {
        $this->group_type_id = $group_type_id;

        return $this;
    }

    /**
     * Method to set the value of field group_data_test
     *
     * @param string $group_data_test
     * @return $this
     */
    public function setGroupDataTest($group_data_test)
    {
        $this->group_data_test = $group_data_test;

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
     * Returns the value of field group_exam_subject_id
     *
     * @return integer
     */
    public function getGroupExamSubjectId()
    {
        return $this->group_exam_subject_id;
    }

    /**
     * Returns the value of field group_chapter_id
     *
     * @return integer
     */
    public function getGroupChapterId()
    {
        return $this->group_chapter_id;
    }

    /**
     * Returns the value of field group_lesson_id
     *
     * @return integer
     */
    public function getGroupLessonId()
    {
        return $this->group_lesson_id;
    }

    /**
     * Returns the value of field group_type_id
     *
     * @return integer
     */
    public function getGroupTypeId()
    {
        return $this->group_type_id;
    }

    /**
     * Returns the value of field group_data_test
     *
     * @return string
     */
    public function getGroupDataTest()
    {
        return $this->group_data_test;
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
        return 'learn_exam_group';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnExamGroup[]|LearnExamGroup
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnExamGroup
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
