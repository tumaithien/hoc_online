<?php

namespace Learncom\Models;

class LearnChapter extends BaseModelCache
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $chapter_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $chapter_class_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $chapter_subject_id;


    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $chapter_name;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $chapter_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $chapter_active;

    /**
     * Method to set the value of field chapter_id
     *
     * @param integer $chapter_id
     * @return $this
     */
    public function setChapterId($chapter_id)
    {
        $this->chapter_id = $chapter_id;

        return $this;
    }

    /**
     * Method to set the value of field chapter_class_id
     *
     * @param integer $chapter_class_id
     * @return $this
     */
    public function setChapterClassId($chapter_class_id)
    {
        $this->chapter_class_id = $chapter_class_id;

        return $this;
    }

    /**
     * Method to set the value of field chapter_subject_id
     *
     * @param integer $chapter_subject_id
     * @return $this
     */
    public function setChapterSubjectId($chapter_subject_id)
    {
        $this->chapter_subject_id = $chapter_subject_id;

        return $this;
    }


    /**
     * Method to set the value of field chapter_name
     *
     * @param string $chapter_name
     * @return $this
     */
    public function setChapterName($chapter_name)
    {
        $this->chapter_name = $chapter_name;

        return $this;
    }

    /**
     * Method to set the value of field chapter_order
     *
     * @param integer $chapter_order
     * @return $this
     */
    public function setChapterOrder($chapter_order)
    {
        $this->chapter_order = $chapter_order;

        return $this;
    }

    /**
     * Method to set the value of field chapter_active
     *
     * @param string $chapter_active
     * @return $this
     */
    public function setChapterActive($chapter_active)
    {
        $this->chapter_active = $chapter_active;

        return $this;
    }

    /**
     * Returns the value of field chapter_id
     *
     * @return integer
     */
    public function getChapterId()
    {
        return $this->chapter_id;
    }

    /**
     * Returns the value of field chapter_class_id
     *
     * @return integer
     */
    public function getChapterClassId()
    {
        return $this->chapter_class_id;
    }

    /**
     * Returns the value of field chapter_subject_id
     *
     * @return integer
     */
    public function getChapterSubjectId()
    {
        return $this->chapter_subject_id;
    }


    /**
     * Returns the value of field chapter_name
     *
     * @return string
     */
    public function getChapterName()
    {
        return $this->chapter_name;
    }

    /**
     * Returns the value of field chapter_order
     *
     * @return integer
     */
    public function getChapterOrder()
    {
        return $this->chapter_order;
    }

    /**
     * Returns the value of field chapter_active
     *
     * @return string
     */
    public function getChapterActive()
    {
        return $this->chapter_active;
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
        return 'learn_chapter';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnChapter[]|LearnChapter
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnChapter
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
