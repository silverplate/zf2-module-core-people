<?php

namespace CorePeople\Form;

use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\FormInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Hydrator\ArraySerializable;
use CoreControl\Form\AbstractForm;

class User extends AbstractForm implements ServiceLocatorAwareInterface
{
    /** @var ServiceLocatorInterface */
    protected $_serviceLocator;

    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $_serviceLocator)
    {
        $this->_serviceLocator = $_serviceLocator;
    }

    /**
     * @param ServiceManager $_sm
     * @param null $_name
     * @param null $_object
     */
    public function __construct(ServiceManager $_sm, $_name = null, $_object = null)
    {
        $this->setServiceLocator($_sm);
        $this->setHydrator(new ArraySerializable);

        parent::__construct($_name, $_object);
    }

    public function bind($_object, $_fi = FormInterface::VALUES_NORMALIZED)
    {
        if ($_object->getId()) {
            $password = $this->get('user')->get('password');
            $options = $password->getOptions();

            if ($options && array_key_exists('is_required', $options)) {
                unset($options['is_required']);
                $password->setOptions($options);
            }
        }

        return parent::bind($_object, $_fi);
    }

    public function createElements()
    {
        // Person

// @todo Is it worth to add hole form instead of adding each field?
//        $personF = new Person('person', $this->getObject()->getPerson());
//        $personF->setLabel('Пользователь');
//        $this->add($personF);

        $personFs = new Fieldset('person');
        $personFs->setLabel('Пользователь');
        $this->add($personFs);

        $personF = new Person;
        foreach ($personF->getElements() as $element) {
            $personFs->add($element);
        }


        // Access

        $accessFs = new Fieldset('user');
        $accessFs->setLabel('Доступ');
        $this->add($accessFs);

        $ele = new Element\Text('login');
        $ele->setOptions(array('is_required' => true));
        $ele->setLabel('Логин');
        $accessFs->add($ele);

        $ele = new Element\Password('password');
        $ele->setOptions(array('is_required' => true));
        $ele->setLabel('Пароль');
        $accessFs->add($ele);

        $ele = new Element\Checkbox('is_published');
        $ele->setLabel('Активный?');
        $accessFs->add($ele);
    }

    protected function _getUser()
    {
        return $this->getObject();
    }

    protected function _isUserUnique()
    {
        /** @var \CorePeople\Mapper\User $mapper */
        $mapper = $this->getServiceLocator()->get('\CorePeople\Mapper\User');

        if (!$mapper->isUserUnique($this->_getUser())) {
            $login = $this->get('user')->get('login');
            $messages = $login->getMessages();
            if (!is_array($messages)) $messages = array();
            $messages[] = 'Value is not unique';
            $login->setMessages($messages);

            return false;
        }

        return true;
    }

    public function isValid()
    {
        if (parent::isValid()) return $this->_isUserUnique();

        Person::validateName($this, 'person');

        return parent::isValid() && $this->_isUserUnique();
    }
}
