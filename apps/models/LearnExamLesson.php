<?php

namespace Learncom\Models;

class LearnExamLesson extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $lesson_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $lesson_name;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $lesson_class_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $lesson_subject_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $lesson_chapter_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $lesson_active;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $lesson_order;

    /**
     * Method to set the value of field lesson_id
     *
     * @param integer $lesson_id
     * @return $this
     */
    public function setLessonId($lesson_id)
    {
        $this->lesson_id = $lesson_id;

        return $this;
    }

    /**
     * Method to set the value of field lesson_name
     *
     * @param string $lesson_name
     * @return $this
     */
    public function setLessonName($lesson_name)
    {
        $this->lesson_name = $lesson_name;

        return $this;
    }

    /**
     * Method to set the value of field lesson_class_id
     *
     * @param integer $lesson_class_id
     * @return $this
     */
    public function setLessonClassId($lesson_class_id)
    {
        $this->lesson_class_id = $lesson_class_id;

        return $this;
    }

    /**
     * Method to set the value of field lesson_subject_id
     *
     * @param integer $lesson_subject_id
     * @return $this
     */
    public function setLessonSubjectId($lesson_subject_id)
    {
        $this->lesson_subject_id = $lesson_subject_id;

        return $this;
    }

    /**
     * Method to set the value of field lesson_chapter_id
     *
     * @param integer $lesson_chapter_id
     * @return $this
     */
    public function setLessonChapterId($lesson_chapter_id)
    {
        $this->lesson_chapter_id = $lesson_chapter_id;

        return $this;
    }

    /**
     * Method to set the value of field lesson_active
     *
     * @param string $lesson_active
     * @return $this
     */
    public function setLessonActive($lesson_active)
    {
        $this->lesson_active = $lesson_active;

        return $this;
    }

    /**
     * Method to set the value of field lesson_order
     *
     * @param integer $lesson_order
     * @return $this
     */
    public function setLessonOrder($lesson_order)
    {
        $this->lesson_order = $lesson_order;

        return $this;
    }

    /**
     * Returns the value of field lesson_id
     *
     * @return integer
     */
    public function getLessonId()
    {
        return $this->lesson_id;
    }

    /**
     * Returns the value of field lesson_name
     *
     * @return string
     */
    public function getLessonName()
    {
        return $this->lesson_name;
    }

    /**
     * Returns the value of field lesson_class_id
     *
     * @return integer
     */
    public function getLessonClassId()
    {
        return $this->lesson_class_id;
    }

    /**
     * Returns the value of field lesson_subject_id
     *
     * @return integer
     */
    public function getLessonSubjectId()
    {
        return $this->lesson_subject_id;
    }

    /**
     * Returns the value of field lesson_chapter_id
     *
     * @return integer
     */
    public function getLessonChapterId()
    {
        return $this->lesson_chapter_id;
    }

    /**
     * Returns the value of field lesson_active
     *
     * @return string
     */
    public function getLessonActive()
    {
        return $this->lesson_active;
    }

    /**
     * Returns the value of field lesson_order
     *
     * @return integer
     */
    public function getLessonOrder()
    {
        return $this->lesson_order;
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
        return 'learn_exam_lesson';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnExamLesson[]|LearnExamLesson
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnExamLesson
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
