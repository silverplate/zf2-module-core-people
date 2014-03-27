<?php

namespace CorePeople\Entity;

use Zend\I18n\Validator\Int;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Stdlib\ArraySerializableInterface;
use Zend\Validator;
use CoreApplication\Entity\ExchangeArrayTrait;
use CoreApplication\Entity\IdTrait;
use CoreGeo\Entity\City;

abstract class AbstractPerson
implements InputFilterAwareInterface, ArraySerializableInterface
{
    use IdTrait;
    use ExchangeArrayTrait;

    const DISABLED = 0;
    const ACTIVE   = 1;

    const UNKNOWN = 0;
    const MR      = 1;
    const MRS     = 2;
    const MISS    = 3;
    const UNISEX  = 4;

    /** @var array[] */
    protected static $_sexItems;

    /** @var int */
    protected $_objectTypeId;

    /** @var int */
    protected $_objectId;

    /** @var int */
    protected $_cityId;

    /** @var City */
    protected $_city;

    /** @var int */
    protected $_companyId;

    /** @var int */
    protected $_statusId = self::DISABLED;

    /** @var int */
    protected $_creationTime;

    /** @var int */
    protected $_sexId = self::UNKNOWN;

    /** @var string */
    protected $_lastName;

    /** @var string */
    protected $_firstName;

    /** @var string */
    protected $_middleName;

    /** @var string */
    protected $_nickname;

    /** @var string */
    protected $_position;

    /**
     * @var string
     * @todo Is it needed here or implement through sources?
     */
    protected $_email;

    /** @var string */
    protected $_birthDay;

    /** @var string */
    protected $_birthYear;

    /** @var string */
    protected $_about;

    /** @var int */
    protected $_rating;

    /** @var InputFilter */
    protected $_inputFilter;

    /**
     * @param int $_objectId
     */
    public function setObjectId($_objectId)
    {
        $this->_objectId = $_objectId;
    }

    /**
     * @return int
     */
    public function getObjectId()
    {
        return $this->_objectId;
    }

    /**
     * @param int $_objectTypeId
     */
    public function setObjectTypeId($_objectTypeId)
    {
        $this->_objectTypeId = $_objectTypeId;
    }

    /**
     * @return int
     */
    public function getObjectTypeId()
    {
        return $this->_objectTypeId;
    }

    /**
     * @param string $_about
     */
    public function setAbout($_about)
    {
        $this->_about = $_about;
    }

    /**
     * @return string
     */
    public function getAbout()
    {
        return $this->_about;
    }

    /**
     * @param string $_birthDay
     */
    public function setBirthDay($_birthDay)
    {
        $this->_birthDay = $_birthDay;
    }

    /**
     * @return string
     */
    public function getBirthDay()
    {
        return $this->_birthDay;
    }

    /**
     * @param string $_birthYear
     */
    public function setBirthYear($_birthYear)
    {
        $this->_birthYear = $_birthYear;
    }

    /**
     * @return string
     */
    public function getBirthYear()
    {
        return $this->_birthYear;
    }

    /**
     * @param int $_creationTime
     */
    public function setCreationTime($_creationTime)
    {
        $this->_creationTime = $_creationTime;
    }

    /**
     * @return int
     */
    public function getCreationTime()
    {
        return $this->_creationTime;
    }

    /**
     * @param string $_email
     */
    public function setEmail($_email)
    {
        $this->_email = $_email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * @param string $_firstName
     */
    public function setFirstName($_firstName)
    {
        $this->_firstName = $_firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->_firstName;
    }

    /**
     * @param string $_lastName
     */
    public function setLastName($_lastName)
    {
        $this->_lastName = $_lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->_lastName;
    }

    /**
     * @param string $_middleName
     */
    public function setMiddleName($_middleName)
    {
        $this->_middleName = $_middleName;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->_middleName;
    }

    public function getTitle()
    {
        $name = trim($this->getLastName() . ' ' . $this->getFirstName());
        $nickname = $this->getNickname();

        if ($name == '' && $nickname == '') return $this->getId();
        else if ($name != '') return $name;
        else return $nickname;
    }

    /**
     * @param string $_nickname
     */
    public function setNickname($_nickname)
    {
        $this->_nickname = $_nickname;
    }

    /**
     * @return string
     */
    public function getNickname()
    {
        return $this->_nickname;
    }

    /**
     * @param string $_position
     */
    public function setPosition($_position)
    {
        $this->_position = $_position;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->_position;
    }

    /**
     * @param int $_sexId
     */
    public function setSexId($_sexId)
    {
        $this->_sexId = $_sexId;
    }

    /**
     * @return int
     */
    public function getSexId()
    {
        return $this->_sexId;
    }

    /**
     * @param int $_statusId
     */
    public function setStatusId($_statusId)
    {
        $this->_statusId = $_statusId;
    }

    /**
     * @return int
     */
    public function getStatusId()
    {
        return $this->_statusId;
    }

    /**
     * @param bool $_isPublished
     * @return bool
     */
    public function isPublished($_isPublished = null)
    {
        if (null !== $_isPublished) {
            $this->setStatusId(
                (bool) $_isPublished ? static::ACTIVE : static::DISABLED
            );
        }

        return $this->getStatusId() == static::ACTIVE;
    }

    /**
     * Especialy for form hydration (Zend\Stdlib\Hydrator\ClassMethods)
     *
     * @param bool $_isPublished
     * @return bool
     */
    public function setIsPublished($_isPublished)
    {
        return $this->isPublished((bool) $_isPublished);
    }

    /**
     * @param int $_objectTypeId
     */
    public function __construct($_objectTypeId)
    {
        $this->setObjectTypeId($_objectTypeId);
    }

    /**
     * Set input filter
     *
     * @param InputFilterInterface $_inputFilter
     * @return InputFilterAwareInterface
     */
    public function setInputFilter(InputFilterInterface $_inputFilter)
    {
        $this->_inputFilter = $_inputFilter;
    }

    /**
     * Retrieve input filter
     *
     * @return InputFilterInterface
     */
    public function getInputFilter()
    {
        if (null == $this->_inputFilter) {
            $filter = new InputFilter();

            /**
             * @todo Gather in collection for one validation to avoid Form::isValid overriding?
             */
            $input = new Input('first_name');
            $input->getValidatorChain()->attach(new Validator\NotEmpty());
            $filter->add($input);

            $input = new Input('last_name');
            $input->getValidatorChain()->attach(new Validator\NotEmpty());
            $filter->add($input);

            $input = new Input('nickname');
            $input->getValidatorChain()->attach(new Validator\NotEmpty());
            $filter->add($input);

            $input = new Input('email');
            $input->setRequired(false);
            $filter->add($input);

            $input = new Input('birth_year');
            $input->getValidatorChain()->attach(new Int());
            $input->setRequired(false);
            $filter->add($input);

            $input = new Input('rating');
            $input->setRequired(false);
            $filter->add($input);

            $this->setInputFilter($filter);
        }

        return $this->_inputFilter;
    }

    /**
     * @return array[]
     */
    public static function getSexItems()
    {
        if (!isset(static::$_sexItems)) {
            static::$_sexItems = array(
                static::UNKNOWN => array('title' => 'не известно'),
                static::MR      => array('title' => 'мистер'),
                static::MRS     => array('title' => 'миссис'),
                static::MISS    => array('title' => 'мисс'),
                static::UNISEX  => array('title' => 'юнисекс')
            );

            foreach (array_keys(static::$_sexItems) as $id) {
                static::$_sexItems[$id]['id'] = $id;
            }
        }

        return static::$_sexItems;
    }

    /**
     * @param \CoreGeo\Entity\City $_city
     */
    public function setCity($_city)
    {
        $this->_city = $_city;
    }

    /**
     * @return \CoreGeo\Entity\City
     */
    public function getCity()
    {
        return $this->_city;
    }

    /**
     * @param int $_cityId
     */
    public function setCityId($_cityId)
    {
        $this->_cityId = $_cityId;
    }

    /**
     * @return int
     */
    public function getCityId()
    {
        return $this->_cityId;
    }

    /**
     * @param int $_companyId
     */
    public function setCompanyId($_companyId)
    {
        $this->_companyId = $_companyId;
    }

    /**
     * @return int
     */
    public function getCompanyId()
    {
        return $this->_companyId;
    }

    /**
     * @param int $_rating
     */
    public function setRating($_rating)
    {
        $this->_rating = $_rating;
    }

    /**
     * @return int
     */
    public function getRating()
    {
        return $this->_rating;
    }

    public static function getExchangeAttrs()
    {
        return array(
            'first_name',
            'last_name',
            'middle_name',
            'city_id',
            'about',
            'birth_day',
            'birth_year',
            'company_id',
            'email',
            'nickname',
            'object_id',
            'object_type_id',
            'position',
            'rating',
            'sex_id',
            'is_published', // Alias to status_id
        );
    }
}
