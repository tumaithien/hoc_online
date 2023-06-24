<?php

namespace Learncom\Models;

class LearnExam extends BaseModelCache
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $exam_id;
    /**
     *
     * @var string
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $exam_group_id;
    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $exam_link_answer;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $exam_answer;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $exam_link_test;

    /**
     * Method to set the value of field exam_id
     *
     * @param integer $exam_id
     * @return $this
     */
    public function setExamId($exam_id)
    {
        $this->exam_id = $exam_id;

        return $this;
    }
    /**
     * Method to set the value of field exam_group_id
     *
     * @param integer $exam_group_id
     * @return $this
     */
    public function setExamGroupId($exam_group_id)
    {
        $this->exam_group_id = $exam_group_id;

        return $this;
    }
    /**
     * Method to set the value of field exam_link_answer
     *
     * @param string $exam_link_answer
     * @return $this
     */
    public function setExamLinkAnswer($exam_link_answer)
    {
        $this->exam_link_answer = $exam_link_answer;

        return $this;
    }

    /**
     * Method to set the value of field exam_answer
     *
     * @param string $exam_answer
     * @return $this
     */
    public function setExamAnswer($exam_answer)
    {
        $this->exam_answer = $exam_answer;

        return $this;
    }

    /**
     * Method to set the value of field exam_link_test
     *
     * @param string $exam_link_test
     * @return $this
     */
    public function setExamLinkTest($exam_link_test)
    {
        $this->exam_link_test = $exam_link_test;

        return $this;
    }

    /**
     * Returns the value of field exam_id
     *
     * @return integer
     */
    public function getExamId()
    {
        return $this->exam_id;
    }
    /**
     * Returns the value of field exam_group_id
     *
     * @return integer
     */
    public function getExamGroupId()
    {
        return $this->exam_group_id;
    }
    /**
     * Returns the value of field exam_link_answer
     *
     * @return string
     */
    public function getExamLinkAnswer()
    {
        return $this->exam_link_answer;
    }

    /**
     * Returns the value of field exam_answer
     *
     * @return string
     */
    public function getExamAnswer()
    {
        return $this->exam_answer;
    }

    /**
     * Returns the value of field exam_link_test
     *
     * @return string
     */
    public function getExamLinkTest()
    {
        return $this->exam_link_test;
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
        return 'learn_exam';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnExam[]|LearnExam
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnExam
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
