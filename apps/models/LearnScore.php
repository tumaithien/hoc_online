<?php

namespace Learncom\Models;

class LearnScore extends BaseModelCache
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false)
     */
    protected $score_id;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $score_user_id;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $score_test_id;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $score_time;

    /**
     *
     * @var double
     * @Column(type="double", nullable=false)
     */
    protected $score_score;

    /**
     *
     * @var double
     * @Column(type="double", nullable=false)
     */
    protected $score_score_choose;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $score_insert_time;
    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $score_update_time;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $score_data;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $score_data_not_choose;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $score_total_error;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $score_log;
    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $score_status;
    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $score_user_ip;
    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $score_user_device;

    /**
     * Method to set the value of field score_id
     *
     * @param integer $score_id
     * @return $this
     */
    public function setScoreId($score_id)
    {
        $this->score_id = $score_id;

        return $this;
    }

    /**
     * Method to set the value of field score_user_id
     *
     * @param integer $score_user_id
     * @return $this
     */
    public function setScoreUserId($score_user_id)
    {
        $this->score_user_id = $score_user_id;

        return $this;
    }

    /**
     * Method to set the value of field score_test_id
     *
     * @param integer $score_test_id
     * @return $this
     */
    public function setScoreTestId($score_test_id)
    {
        $this->score_test_id = $score_test_id;

        return $this;
    }

    /**
     * Method to set the value of field score_time
     *
     * @param integer $score_time
     * @return $this
     */
    public function setScoreTime($score_time)
    {
        $this->score_time = $score_time;

        return $this;
    }

    /**
     * Method to set the value of field score_score
     *
     * @param double $score_score
     * @return $this
     */
    public function setScoreScore($score_score)
    {
        $this->score_score = $score_score;

        return $this;
    }

    /**
     * Method to set the value of field score_score_choose
     *
     * @param double $score_score_choose
     * @return $this
     */
    public function setScoreScoreChoose($score_score_choose)
    {
        $this->score_score_choose = $score_score_choose;

        return $this;
    }

    /**
     * Method to set the value of field score_insert_time
     *
     * @param integer $score_insert_time
     * @return $this
     */
    public function setScoreInsertTime($score_insert_time)
    {
        $this->score_insert_time = $score_insert_time;

        return $this;
    }
    /**
     * Method to set the value of field score_update_time
     *
     * @param integer $score_update_time
     * @return $this
     */
    public function setScoreUpdateTime($score_update_time)
    {
        $this->score_update_time = $score_update_time;

        return $this;
    }
    /**
     * Method to set the value of field score_data
     *
     * @param string $score_data
     * @return $this
     */
    public function setScoreData($score_data)
    {
        $this->score_data = $score_data;

        return $this;
    }

    /**
     * Method to set the value of field score_data_not_choose
     *
     * @param string $score_data_not_choose
     * @return $this
     */
    public function setScoreDataNotChoose($score_data_not_choose)
    {
        $this->score_data_not_choose = $score_data_not_choose;

        return $this;
    }

    /**
     * Method to set the value of field score_total_error
     *
     * @param integer $score_total_error
     * @return $this
     */
    public function setScoreTotalError($score_total_error)
    {
        $this->score_total_error = $score_total_error;

        return $this;
    }

    /**
     * Method to set the value of field score_log
     *
     * @param string $score_log
     * @return $this
     */
    public function setScoreLog($score_log)
    {
        $this->score_log = $score_log;

        return $this;
    }
    /**
     * Method to set the value of field score_status
     *
     * @param string $score_status
     * @return $this
     */
    public function setScoreStatus($score_status)
    {
        $this->score_status = $score_status;

        return $this;
    }
    /**
     * Method to set the value of field score_user_ip
     *
     * @param string $score_user_ip
     * @return $this
     */
    public function setScoreUserIp($score_user_ip)
    {
        $this->score_user_ip = $score_user_ip;

        return $this;
    }
    /**
     * Method to set the value of field score_user_device
     *
     * @param string $score_user_device
     * @return $this
     */
    public function setScoreUserDevice($score_user_device)
    {
        $this->score_user_device = $score_user_device;

        return $this;
    }
    /**
     * Returns the value of field score_id
     *
     * @return integer
     */
    public function getScoreId()
    {
        return $this->score_id;
    }

    /**
     * Returns the value of field score_user_id
     *
     * @return integer
     */
    public function getScoreUserId()
    {
        return $this->score_user_id;
    }

    /**
     * Returns the value of field score_test_id
     *
     * @return integer
     */
    public function getScoreTestId()
    {
        return $this->score_test_id;
    }

    /**
     * Returns the value of field score_time
     *
     * @return integer
     */
    public function getScoreTime()
    {
        return $this->score_time;
    }

    /**
     * Returns the value of field score_score
     *
     * @return double
     */
    public function getScoreScore()
    {
        return $this->score_score;
    }

    /**
     * Returns the value of field score_score_choose
     *
     * @return double
     */
    public function getScoreScoreChoose()
    {
        return $this->score_score_choose;
    }

    /**
     * Returns the value of field score_insert_time
     *
     * @return integer
     */
    public function getScoreInsertTime()
    {
        return $this->score_insert_time;
    }
    /**
     * Returns the value of field score_update_time
     *
     * @return integer
     */
    public function getScoreUpdateTime()
    {
        return $this->score_update_time;
    }
    /**
     * Returns the value of field score_data
     *
     * @return string
     */
    public function getScoreData()
    {
        return $this->score_data;
    }

    /**
     * Returns the value of field score_data_not_choose
     *
     * @return string
     */
    public function getScoreDataNotChoose()
    {
        return $this->score_data_not_choose;
    }

    /**
     * Returns the value of field score_total_error
     *
     * @return integer
     */
    public function getScoreTotalError()
    {
        return $this->score_total_error;
    }

    /**
     * Returns the value of field score_log
     *
     * @return string
     */
    public function getScoreLog()
    {
        return $this->score_log;
    }
    /**
     * Returns the value of field score_status
     *
     * @return string
     */
    public function getScoreStatus()
    {
        return $this->score_status;
    }
    /**
     * Returns the value of field score_user_ip
     *
     * @return string
     */
    public function getScoreUserIp()
    {
        return $this->score_user_ip;
    }
    /**
     * Returns the value of field score_user_device
     *
     * @return string
     */
    public function getScoreUserdevice()
    {
        return $this->score_user_device;
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
        return 'learn_score';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnScore[]|LearnScore
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnScore
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
