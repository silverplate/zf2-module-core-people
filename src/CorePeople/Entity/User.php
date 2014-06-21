<?php

namespace CorePeople\Entity;

use Zend\Crypt\Password\Bcrypt;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Stdlib\ArraySerializableInterface;
use Zend\Validator;
use CoreApplication\Entity\IdTrait;
use CoreApplication\Entity\ExchangeArrayTrait;

class User
implements InputFilterAwareInterface, ArraySerializableInterface
{
    use IdTrait;
    use ExchangeArrayTrait {
        exchangeArray as traitExchangeArray;
        getArrayCopy as traitGetArrayCopy;
    }

    const DISABLED = 0;
    const ACTIVE   = 1;

    /** @var int */
    protected $_personId;

    /** @var Person */
    protected $_person;

    /** @var int */
    protected $_statusId = self::DISABLED;

    /** @var int */
    protected $_creationTime;

    /** @var int */
    protected $_lastVisit;

    /** @var string */
    protected $_login;

    /** @var string */
    protected $_password;

    /** @var string */
    protected $_reminderKey;

    /** @var int */
    protected $_reminderTime;

    /** @var InputFilter */
    protected $_inputFilter;

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
     * @param int $_lastVisit
     */
    public function setLastVisit($_lastVisit)
    {
        $this->_lastVisit = $_lastVisit;
    }

    /**
     * @return int
     */
    public function getLastVisit()
    {
        return $this->_lastVisit;
    }

    /**
     * @param string $_login
     */
    public function setLogin($_login)
    {
        $this->_login = $_login;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->_login;
    }

    /**
     * @param string $_password
     */
    public function setPassword($_password)
    {
        $bcrypt = new Bcrypt();
        $this->_password = $bcrypt->create($_password);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * @param Person $_person
     */
    public function setPerson($_person)
    {
        $this->_person = $_person;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->_person;
    }

    /**
     * @param int $_personId
     */
    public function setPersonId($_personId)
    {
        $this->_personId = $_personId;
    }

    /**
     * @return int
     */
    public function getPersonId()
    {
        return $this->_personId;
    }

    /**
     * @param string $_reminderKey
     */
    public function setReminderKey($_reminderKey)
    {
        $this->_reminderKey = $_reminderKey;
    }

    /**
     * @return string
     */
    public function getReminderKey()
    {
        return $this->_reminderKey;
    }

    /**
     * @param int $_reminderTime
     */
    public function setReminderTime($_reminderTime)
    {
        $this->_reminderTime = $_reminderTime;
    }

    /**
     * @return int
     */
    public function getReminderTime()
    {
        return $this->_reminderTime;
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
                (bool) $_isPublished ? self::ACTIVE : self::DISABLED
            );
        }

        return $this->getStatusId() == self::ACTIVE;
    }

    /**
     * Set input filter
     *
     * @param  InputFilterInterface $_inputFilter
     * @return InputFilterAwareInterface
     */
    public function setInputFilter(InputFilterInterface $_inputFilter)
    {
        $this->_inputFilter = $_inputFilter;
    }

    public function getUserInputFilter()
    {
        $filter = new InputFilter();

        $input = new Input('login');
        $input->getValidatorChain()->attach(new Validator\NotEmpty());
        $filter->add($input);

        if (!$this->getId()) {
            $input = new Input('password');
            $input->getValidatorChain()->attach(new Validator\NotEmpty());
            $filter->add($input);
        }

        return $filter;
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
            $filter->add($this->getUserInputFilter(), 'user');

            $person = new Person;
            $filter->add($person->getInputFilter(), 'person');

            $this->setInputFilter($filter);
        }

        return $this->_inputFilter;
    }

    public static function getExchangeAttrs()
    {
        return array(
            'login',
            'password',
            'person_id',
            'is_published',
        );
    }

    public function exchangeArray(array $data)
    {
        if (!empty($data['user'])) {
            if (
                array_key_exists('password', $data['user']) &&
                $data['user']['password'] === ''
            ) {
                unset($data['user']['password']);
            }

            $this->traitExchangeArray($data['user']);
        }

        if (!empty($data['person'])) {
            $this->getPerson()->exchangeArray($data['person']);
        }
    }

    public function getArrayCopy()
    {
        return array(
            'user' => $this->traitGetArrayCopy(),
            'person' => $this->getPerson()
                      ? $this->getPerson()->getArrayCopy()
                      : array()
        );
    }

    public function getTitle()
    {
        return $this->getPerson()
             ? $this->getPerson()->getTitle()
             : $this->getLogin();
    }
}
