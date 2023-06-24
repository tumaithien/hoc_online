<?php

namespace Learncom\Models;
use Phalcon\Security;

class LearnUser extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $user_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $user_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $user_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $user_group_excluded_ids;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $user_email;

    /**
     *
     * @var string
     * @Column(type="string", length=12, nullable=true)
     */
    protected $user_birth_day;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=true)
     */
    protected $user_tel;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $user_address;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $user_gender;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $user_ip;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $user_token_cokie_1;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $user_token_cokie_2;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $user_token_online;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $user_role;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $user_password;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $user_active;
    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $user_special;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $user_insert_time;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $user_class_ids;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $user_subject_ids;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $user_school_class_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $user_group_ids;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $user_description;

    /**
     * Method to set the value of field user_id
     *
     * @param integer $user_id
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Method to set the value of field user_code
     *
     * @param string $user_code
     * @return $this
     */
    public function setUserCode($user_code)
    {
        $this->user_code = $user_code;

        return $this;
    }

    /**
     * Method to set the value of field user_name
     *
     * @param string $user_name
     * @return $this
     */
    public function setUserName($user_name)
    {
        $this->user_name = $user_name;

        return $this;
    }

    /**
     * Method to set the value of field user_group_excluded_ids
     *
     * @param string $user_group_excluded_ids
     * @return $this
     */
    public function setUserGroupExcludedIds($user_group_excluded_ids)
    {
        $this->user_group_excluded_ids = $user_group_excluded_ids;

        return $this;
    }

    /**
     * Method to set the value of field user_email
     *
     * @param string $user_email
     * @return $this
     */
    public function setUserEmail($user_email)
    {
        $this->user_email = $user_email;

        return $this;
    }

    /**
     * Method to set the value of field user_birth_day
     *
     * @param string $user_birth_day
     * @return $this
     */
    public function setUserBirthDay($user_birth_day)
    {
        $this->user_birth_day = $user_birth_day;

        return $this;
    }

    /**
     * Method to set the value of field user_tel
     *
     * @param integer $user_tel
     * @return $this
     */
    public function setUserTel($user_tel)
    {
        $this->user_tel = $user_tel;

        return $this;
    }

    /**
     * Method to set the value of field user_address
     *
     * @param string $user_address
     * @return $this
     */
    public function setUserAddress($user_address)
    {
        $this->user_address = $user_address;

        return $this;
    }

    /**
     * Method to set the value of field user_gender
     *
     * @param string $user_gender
     * @return $this
     */
    public function setUserGender($user_gender)
    {
        $this->user_gender = $user_gender;

        return $this;
    }

    /**
     * Method to set the value of field user_ip
     *
     * @param string $user_ip
     * @return $this
     */
    public function setUserIp($user_ip)
    {
        $this->user_ip = $user_ip;

        return $this;
    }

    /**
     * Method to set the value of field user_token_cokie_1
     *
     * @param string $user_token_cokie_1
     * @return $this
     */
    public function setUserTokenCokie1($user_token_cokie_1)
    {
        $this->user_token_cokie_1 = $user_token_cokie_1;

        return $this;
    }

    /**
     * Method to set the value of field user_token_cokie_2
     *
     * @param string $user_token_cokie_2
     * @return $this
     */
    public function setUserTokenCokie2($user_token_cokie_2)
    {
        $this->user_token_cokie_2 = $user_token_cokie_2;

        return $this;
    }

    /**
     * Method to set the value of field user_token_online
     *
     * @param string $user_token_online
     * @return $this
     */
    public function setUserTokenOnline($user_token_online)
    {
        $this->user_token_online = $user_token_online;

        return $this;
    }

    /**
     * Method to set the value of field user_role
     *
     * @param string $user_role
     * @return $this
     */
    public function setUserRole($user_role)
    {
        $this->user_role = $user_role;

        return $this;
    }

    /**
     * Method to set the value of field user_password
     *
     * @param string $user_password
     * @return $this
     */
    public function setUserPassword($user_password)
    {
        $security = new Security();
        $this->user_password = !empty($user_password) ? $security->hash($user_password) : '';

        return $this;
    }

    /**
     * Method to set the value of field user_active
     *
     * @param string $user_active
     * @return $this
     */
    public function setUserActive($user_active)
    {
        $this->user_active = $user_active;

        return $this;
    }
    /**
     * Method to set the value of field user_special
     *
     * @param string $user_special
     * @return $this
     */
    public function setUserSpecial($user_special)
    {
        $this->user_special = $user_special;

        return $this;
    }

    /**
     * Method to set the value of field user_insert_time
     *
     * @param integer $user_insert_time
     * @return $this
     */
    public function setUserInsertTime($user_insert_time)
    {
        $this->user_insert_time = $user_insert_time;

        return $this;
    }

    /**
     * Method to set the value of field user_class_ids
     *
     * @param string $user_class_ids
     * @return $this
     */
    public function setUserClassIds($user_class_ids)
    {
        $this->user_class_ids = $user_class_ids;

        return $this;
    }

    /**
     * Method to set the value of field user_subject_ids
     *
     * @param string $user_subject_ids
     * @return $this
     */
    public function setUserSubjectIds($user_subject_ids)
    {
        $this->user_subject_ids = $user_subject_ids;

        return $this;
    }

    /**
     * Method to set the value of field user_school_class_id
     *
     * @param integer $user_school_class_id
     * @return $this
     */
    public function setUserSchoolClassId($user_school_class_id)
    {
        $this->user_school_class_id = $user_school_class_id;

        return $this;
    }

    /**
     * Method to set the value of field user_group_ids
     *
     * @param string $user_group_ids
     * @return $this
     */
    public function setUserGroupIds($user_group_ids)
    {
        $this->user_group_ids = $user_group_ids;

        return $this;
    }

    /**
     * Method to set the value of field user_description
     *
     * @param string $user_description
     * @return $this
     */
    public function setUserDescription($user_description)
    {
        $this->user_description = $user_description;

        return $this;
    }

    /**
     * Returns the value of field user_id
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Returns the value of field user_code
     *
     * @return string
     */
    public function getUserCode()
    {
        return $this->user_code;
    }

    /**
     * Returns the value of field user_name
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * Returns the value of field user_group_excluded_ids
     *
     * @return string
     */
    public function getUserGroupExcludedIds()
    {
        return $this->user_group_excluded_ids;
    }

    /**
     * Returns the value of field user_email
     *
     * @return string
     */
    public function getUserEmail()
    {
        return $this->user_email;
    }

    /**
     * Returns the value of field user_birth_day
     *
     * @return string
     */
    public function getUserBirthDay()
    {
        return $this->user_birth_day;
    }

    /**
     * Returns the value of field user_tel
     *
     * @return integer
     */
    public function getUserTel()
    {
        return $this->user_tel;
    }

    /**
     * Returns the value of field user_address
     *
     * @return string
     */
    public function getUserAddress()
    {
        return $this->user_address;
    }

    /**
     * Returns the value of field user_gender
     *
     * @return string
     */
    public function getUserGender()
    {
        return $this->user_gender;
    }

    /**
     * Returns the value of field user_ip
     *
     * @return string
     */
    public function getUserIp()
    {
        return $this->user_ip;
    }

    /**
     * Returns the value of field user_token_cokie_1
     *
     * @return string
     */
    public function getUserTokenCokie1()
    {
        return $this->user_token_cokie_1;
    }

    /**
     * Returns the value of field user_token_cokie_2
     *
     * @return string
     */
    public function getUserTokenCokie2()
    {
        return $this->user_token_cokie_2;
    }

    /**
     * Returns the value of field user_token_online
     *
     * @return string
     */
    public function getUserTokenOnline()
    {
        return $this->user_token_online;
    }

    /**
     * Returns the value of field user_role
     *
     * @return string
     */
    public function getUserRole()
    {
        return $this->user_role;
    }

    /**
     * Returns the value of field user_password
     *
     * @return string
     */
    public function getUserPassword()
    {
        return $this->user_password;
    }

    /**
     * Returns the value of field user_active
     *
     * @return string
     */
    public function getUserActive()
    {
        return $this->user_active;
    }
    /**
     * Returns the value of field user_special
     *
     * @return string
     */
    public function getUserSpecial()
    {
        return $this->user_special;
    }
    /**
     * Returns the value of field user_insert_time
     *
     * @return integer
     */
    public function getUserInsertTime()
    {
        return $this->user_insert_time;
    }

    /**
     * Returns the value of field user_class_ids
     *
     * @return string
     */
    public function getUserClassIds()
    {
        return $this->user_class_ids;
    }

    /**
     * Returns the value of field user_subject_ids
     *
     * @return string
     */
    public function getUserSubjectIds()
    {
        return $this->user_subject_ids;
    }

    /**
     * Returns the value of field user_school_class_id
     *
     * @return integer
     */
    public function getUserSchoolClassId()
    {
        return $this->user_school_class_id;
    }

    /**
     * Returns the value of field user_group_ids
     *
     * @return string
     */
    public function getUserGroupIds()
    {
        return $this->user_group_ids;
    }

    /**
     * Returns the value of field user_description
     *
     * @return string
     */
    public function getUserDescription()
    {
        return $this->user_description;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany(
            'user_id',
            'Learncom\Models\LearnScore',
            'score_user_id',
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
        return 'learn_user';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnUser[]|LearnUser
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnUser
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function findFirstByUserCode($code)
    {
        return self::findFirst(array(
            'user_code = :user_code: AND user_active="Y"',
            'bind' => array('user_code' => $code)
        ));
    }
}
