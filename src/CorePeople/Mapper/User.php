<?php

namespace CorePeople\Mapper;

use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\Exception;
use CoreApplication\Mapper\AbstractMapper;

class User extends AbstractMapper
{
    protected $table = 'user';

    /**
     * @param \CorePeople\Entity\User[] $users
     * @param int $_personObjectTypeId
     * @return \CorePeople\Entity\User[]
     */
    public function fetchPersons($users, $_personObjectTypeId = null)
    {
        $personIds = [];
        foreach ($users as $user)
            if ($user->getPersonId())
                $personIds[] = $user->getPersonId();

        if (count($personIds) > 0) {
            $l = $this->getPersonMapper($_personObjectTypeId)
                      ->fetchAll(['person_id' => array_unique($personIds)])
                      ->asArray();

            foreach ($users as $user) {
                if (
                    $user->getPersonId() &&
                    array_key_exists($user->getPersonId(), $l)
                ) {
                    $user->setPerson($l[$user->getPersonId()]);
                }
            }
        }

        return $users;
    }

    public function getList($_where = null)
    {
        return $this->fetchPersons(parent::getList($_where));
    }

    /**
     * @param int $_objectTypeId
     * @return Person
     */
    public function getPersonMapper($_objectTypeId = null)
    {
        if ($_objectTypeId === null) {
            return $this->srv('\CorePeople\Mapper\Person');

        } else {
            return new \CorePeople\Mapper\Person($_objectTypeId);
        }
    }

    /**
     * @param \CorePeople\Entity\User $_user
     * @return bool
     */
    public function isUserUnique($_user)
    {
        return $this->isUnique($_user->getLogin(), $_user->getId());
    }

    /**
     * @param string $_login
     * @param null|int $_id
     * @return bool
     */
    public function isUnique($_login, $_id = null)
    {
        $sql = new Sql($this->getAdapter());
        $select = $sql->select($this->getTable());

        $select->where->addPredicate(new Expression(
            'LOWER(`login`) = ?',
            \Ext\String::toLower($_login)
        ));

        if ($_id !== null) {
            $select->where(array('`user_id` != ?' => $_id));
        }

        return $sql->prepareStatementForSqlObject($select)->execute()->count() == 0;

    }

    /**
     * @param \CorePeople\Entity\User $user
     * @return int
     * @throws \Exception
     */
    public function insertEntity($user)
    {
        $personCreation = false;

        if (!$user->getPersonId() && $user->getPerson()) {
            $personCreation = true;
            $this->getPersonMapper()->insertEntity($user->getPerson());
            $user->setPersonId($user->getPerson()->getId());
        }

        try {
            $res = parent::insertEntity($user);

        } catch (\Exception $e) {
            if ($personCreation) {
                $this->getPersonMapper(
                    $user->getPerson()->getObjectTypeId()
                )->deleteEntity($user->getPerson());
                $user->setPersonId(null);
            }

            throw $e;
        }

        return $res;
    }

    /**
     * @param \CorePeople\Entity\User $_user
     * @return int
     */
    public function updateEntity($_user)
    {
        if ($_user->getPerson()) {
            $this->getPersonMapper(
                $_user->getPerson()->getObjectTypeId()
            )->updateEntity($_user->getPerson());
        }

        return parent::updateEntity($_user);
    }

    /**
     * @param \CorePeople\Entity\User $_user
     * @return int
     */
    public function deleteEntity($_user)
    {
        if ($_user->getPerson()) {
            $this->getPersonMapper(
                $_user->getPerson()->getObjectTypeId()
            )->deleteEntity($_user->getPerson());
        }

        return parent::deleteEntity($_user);
    }
}
