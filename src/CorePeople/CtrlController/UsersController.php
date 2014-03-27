<?php

namespace CorePeople\CtrlController;

use CoreControl\Controller\AbstractController;
use CoreControl\Controller\SimpleFormTrait;
use CorePeople\Entity\Person;

class UsersController extends AbstractController
{
    use SimpleFormTrait {
        _getEntity as traitGetEntity;
    }

    protected $_route = 'ctrl-users';
    protected $_title = 'Пользователи';

    protected function _getMapper()
    {
        return $this->srv('\CorePeople\Mapper\User');
    }

    protected function _createForm()
    {
        /** @var \CorePeople\Form\User $form */
        $form = $this->srv('\CorePeople\Form\User');
        $form->bind($this->ent());

        return $form;
    }

    protected function _getEntity()
    {
        if (!$this->traitGetEntity()->getPerson()) {
            if ($this->traitGetEntity()->getPersonId()) {
                $this->_getMapper()->fetchPersons(array(
                    $this->traitGetEntity()
                ));

            } else {
                $this->traitGetEntity()->setPerson(new Person);
            }
        }

        return $this->traitGetEntity();
    }
}
