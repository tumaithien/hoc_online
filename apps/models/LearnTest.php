<?php

namespace Learncom\Models;

class LearnTest extends BaseModelCache
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false)
     */
    protected $test_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $test_type;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $test_school_class_ids;
    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $test_time_start;


    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $test_parent_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $test_array;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $test_is_public_answer;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $test_name;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $test_class_id;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $test_subject_id;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=true)
     */
    protected $test_group_id;

    /**
     *
     * @var double
     * @Column(type="double", nullable=true)
     */
    protected $test_score_choose;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $test_link;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $test_link_comment;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $test_active;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $test_status;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $test_time;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $test_number_exam;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $test_number_question;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $test_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $test_only_one;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $test_public_score;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $test_description;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $test_answer;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $test_answer_decode;

    /**
     * Method to set the value of field test_id
     *
     * @param integer $test_id
     * @return $this
     */
    public function setTestId($test_id)
    {
        $this->test_id = $test_id;

        return $this;
    }

    /**
     * Method to set the value of field test_type
     *
     * @param string $test_type
     * @return $this
     */
    public function setTestType($test_type)
    {
        $this->test_type = $test_type;

        return $this;
    }

    /**
     * Method to set the value of field test_school_class_ids
     *
     * @param string $test_school_class_ids
     * @return $this
     */
    public function setTestSchoolClassIds($test_school_class_ids)
    {
        $this->test_school_class_ids = $test_school_class_ids;

        return $this;
    }
    /**
     * Method to set the value of field test_time_start
     *
     * @param string $test_time_start
     * @return $this
     */
    public function setTestTimeStart($test_time_start)
    {
        $this->test_time_start = $test_time_start;

        return $this;
    }
    /**
     * Method to set the value of field test_parent_id
     *
     * @param integer $test_parent_id
     * @return $this
     */
    public function setTestParentId($test_parent_id)
    {
        $this->test_parent_id = $test_parent_id;

        return $this;
    }

    /**
     * Method to set the value of field test_array
     *
     * @param string $test_array
     * @return $this
     */
    public function setTestArray($test_array)
    {
        $this->test_array = $test_array;

        return $this;
    }

    /**
     * Method to set the value of field test_is_public_answer
     *
     * @param string $test_is_public_answer
     * @return $this
     */
    public function setTestIsPublicAnswer($test_is_public_answer)
    {
        $this->test_is_public_answer = $test_is_public_answer;

        return $this;
    }

    /**
     * Method to set the value of field test_name
     *
     * @param string $test_name
     * @return $this
     */
    public function setTestName($test_name)
    {
        $this->test_name = $test_name;

        return $this;
    }

    /**
     * Method to set the value of field test_class_id
     *
     * @param integer $test_class_id
     * @return $this
     */
    public function setTestClassId($test_class_id)
    {
        $this->test_class_id = $test_class_id;

        return $this;
    }

    /**
     * Method to set the value of field test_subject_id
     *
     * @param integer $test_subject_id
     * @return $this
     */
    public function setTestSubjectId($test_subject_id)
    {
        $this->test_subject_id = $test_subject_id;

        return $this;
    }

    /**
     * Method to set the value of field test_group_id
     *
     * @param integer $test_group_id
     * @return $this
     */
    public function setTestGroupId($test_group_id)
    {
        $this->test_group_id = $test_group_id;

        return $this;
    }

    /**
     * Method to set the value of field test_score_choose
     *
     * @param double $test_score_choose
     * @return $this
     */
    public function setTestScoreChoose($test_score_choose)
    {
        $this->test_score_choose = $test_score_choose;

        return $this;
    }

    /**
     * Method to set the value of field test_link
     *
     * @param string $test_link
     * @return $this
     */
    public function setTestLink($test_link)
    {
        $this->test_link = $test_link;

        return $this;
    }

    /**
     * Method to set the value of field test_link_comment
     *
     * @param string $test_link_comment
     * @return $this
     */
    public function setTestLinkComment($test_link_comment)
    {
        $this->test_link_comment = $test_link_comment;

        return $this;
    }

    /**
     * Method to set the value of field test_active
     *
     * @param string $test_active
     * @return $this
     */
    public function setTestActive($test_active)
    {
        $this->test_active = $test_active;

        return $this;
    }
    /**
     * Method to set the value of field test_status
     *
     * @param string $test_status
     * @return $this
     */
    public function setTestStatus($test_status)
    {
        $this->test_status = $test_status;

        return $this;
    }
    /**
     * Method to set the value of field test_time
     *
     * @param integer $test_time
     * @return $this
     */
    public function setTestTime($test_time)
    {
        $this->test_time = $test_time;

        return $this;
    }

    /**
     * Method to set the value of field test_number_exam
     *
     * @param integer $test_number_exam
     * @return $this
     */
    public function setTestNumberExam($test_number_exam)
    {
        $this->test_number_exam = $test_number_exam;

        return $this;
    }

    /**
     * Method to set the value of field test_number_question
     *
     * @param integer $test_number_question
     * @return $this
     */
    public function setTestNumberQuestion($test_number_question)
    {
        $this->test_number_question = $test_number_question;

        return $this;
    }

    /**
     * Method to set the value of field test_order
     *
     * @param integer $test_order
     * @return $this
     */
    public function setTestOrder($test_order)
    {
        $this->test_order = $test_order;

        return $this;
    }

    /**
     * Method to set the value of field test_only_one
     *
     * @param string $test_only_one
     * @return $this
     */
    public function setTestOnlyOne($test_only_one)
    {
        $this->test_only_one = $test_only_one;

        return $this;
    }

    /**
     * Method to set the value of field test_public_score
     *
     * @param string $test_public_score
     * @return $this
     */
    public function setTestPublicScore($test_public_score)
    {
        $this->test_public_score = $test_public_score;

        return $this;
    }

    /**
     * Method to set the value of field test_description
     *
     * @param string $test_description
     * @return $this
     */
    public function setTestDescription($test_description)
    {
        $this->test_description = $test_description;

        return $this;
    }

    /**
     * Method to set the value of field test_answer
     *
     * @param string $test_answer
     * @return $this
     */
    public function setTestAnswer($test_answer)
    {
        $this->test_answer = $test_answer;

        return $this;
    }

    /**
     * Method to set the value of field test_answer_decode
     *
     * @param string $test_answer_decode
     * @return $this
     */
    public function setTestAnswerDecode($test_answer_decode)
    {
        $this->test_answer_decode = $test_answer_decode;

        return $this;
    }

    /**
     * Returns the value of field test_id
     *
     * @return integer
     */
    public function getTestId()
    {
        return $this->test_id;
    }

    /**
     * Returns the value of field test_type
     *
     * @return string
     */
    public function getTestType()
    {
        return $this->test_type;
    }

    /**
     * Returns the value of field test_school_class_ids
     *
     * @return string
     */
    public function getTestSchoolClassIds()
    {
        return $this->test_school_class_ids;
    }
    /**
     * Returns the value of field test_time_start
     *
     * @return string
     */
    public function getTestTimeStart()
    {
        return $this->test_time_start;
    }

    /**
     * Returns the value of field test_parent_id
     *
     * @return integer
     */
    public function getTestParentId()
    {
        return $this->test_parent_id;
    }

    /**
     * Returns the value of field test_array
     *
     * @return string
     */
    public function getTestArray()
    {
        return $this->test_array;
    }

    /**
     * Returns the value of field test_is_public_answer
     *
     * @return string
     */
    public function getTestIsPublicAnswer()
    {
        return $this->test_is_public_answer;
    }

    /**
     * Returns the value of field test_name
     *
     * @return string
     */
    public function getTestName()
    {
        return $this->test_name;
    }

    /**
     * Returns the value of field test_class_id
     *
     * @return integer
     */
    public function getTestClassId()
    {
        return $this->test_class_id;
    }

    /**
     * Returns the value of field test_subject_id
     *
     * @return integer
     */
    public function getTestSubjectId()
    {
        return $this->test_subject_id;
    }

    /**
     * Returns the value of field test_group_id
     *
     * @return integer
     */
    public function getTestGroupId()
    {
        return $this->test_group_id;
    }

    /**
     * Returns the value of field test_score_choose
     *
     * @return double
     */
    public function getTestScoreChoose()
    {
        return $this->test_score_choose;
    }

    /**
     * Returns the value of field test_link
     *
     * @return string
     */
    public function getTestLink()
    {
        return $this->test_link;
    }

    /**
     * Returns the value of field test_link_comment
     *
     * @return string
     */
    public function getTestLinkComment()
    {
        return $this->test_link_comment;
    }

    /**
     * Returns the value of field test_active
     *
     * @return string
     */
    public function getTestActive()
    {
        return $this->test_active;
    }
    /**
     * Returns the value of field test_status
     *
     * @return string
     */
    public function getTestStatus()
    {
        return $this->test_status;
    }

    /**
     * Returns the value of field test_time
     *
     * @return integer
     */
    public function getTestTime()
    {
        return $this->test_time;
    }

    /**
     * Returns the value of field test_number_exam
     *
     * @return integer
     */
    public function getTestNumberExam()
    {
        return $this->test_number_exam;
    }

    /**
     * Returns the value of field test_number_question
     *
     * @return integer
     */
    public function getTestNumberQuestion()
    {
        return $this->test_number_question;
    }

    /**
     * Returns the value of field test_order
     *
     * @return integer
     */
    public function getTestOrder()
    {
        return $this->test_order;
    }

    /**
     * Returns the value of field test_only_one
     *
     * @return string
     */
    public function getTestOnlyOne()
    {
        return $this->test_only_one;
    }

    /**
     * Returns the value of field test_public_score
     *
     * @return string
     */
    public function getTestPublicScore()
    {
        return $this->test_public_score;
    }

    /**
     * Returns the value of field test_description
     *
     * @return string
     */
    public function getTestDescription()
    {
        return $this->test_description;
    }

    /**
     * Returns the value of field test_answer
     *
     * @return string
     */
    public function getTestAnswer()
    {
        return $this->test_answer;
    }

    /**
     * Returns the value of field test_answer_decode
     *
     * @return string
     */
    public function getTestAnswerDecode()
    {
        return $this->test_answer_decode;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany(
            'test_id',
            'Learncom\Models\LearnScore',
            'score_test_id',
            [
                'alias' => 'score'
            ]
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'learn_test';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnTest[]|LearnTest
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnTest
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}