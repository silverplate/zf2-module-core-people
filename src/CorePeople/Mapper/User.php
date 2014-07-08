<?php

namespace CorePeople\Mapper;

use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\Exception;
use CoreApplication\Mapper\AbstractMapper;

class User extends AbstractMapper
{
    /**
     * @param \CorePeople\Entity\User[] $users
     * @return \CorePeople\Entity\User[]
     */
    public function fetchPersons($users)
    {
        $personIds = array();
        foreach ($users as $user) {
            if ($user->getPersonId()) {
                $personIds[] = $user->getPersonId();
            }
        }

        if (count($personIds) > 0) {
            $l = $this->getPersonMapper()
                      ->fetchAll(array('person_id' => array_unique($personIds)))
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
     * @return Person
     */
    public function getPersonMapper()
    {
        return $this->srv('\CorePeople\Mapper\Person');
    }

    /**
     * @param \CorePeople\Entity\User $_user
     * @return bool
     */
    public function isUserUnique($_user)
    {
        $sql = new Sql($this->getAdapter());
        $select = $sql->select($this->getTable());

        $select->where->addPredicate(new Expression(
            'LOWER(`login`) = ?',
            \Ext\String::toLower($_user->getLogin())
        ));

        if ($_user->getId()) {
            $select->where(array('`user_id` != ?' => $_user->getId()));
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
                $this->getPersonMapper()->deleteEntity($user->getPerson());
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
            $this->getPersonMapper()->updateEntity($_user->getPerson());
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
            $this->getPersonMapper()->deleteEntity($_user->getPerson());
        }

        return parent::deleteEntity($_user);
    }
}
