<?php

namespace CorePeople\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ArraySerializable;
use CoreControl\Form\AbstractForm;
use CorePeople\Entity\AbstractPerson as Model;

abstract class AbstractPerson extends AbstractForm
{
    public function __construct($_name = null, $_object = null)
    {
        parent::__construct($_name, $_object);
        $this->setHydrator(new ArraySerializable);
    }

    public function isValid()
    {
        if (parent::isValid()) return true;

        static::validateName($this);

        return parent::isValid();
    }

    public static function validateName(Form $form, $_name = null)
    {
        $filter = $form->getInputFilter();
        if ($_name !== null) $filter = $filter->get($_name);

        $invalid = array_keys($filter->getInvalidInput());
        $orCondition = array('last_name', 'first_name', 'nickname');
        $orConditionInvalid = array_intersect($orCondition, $invalid);

        if (count($orCondition) != count($orConditionInvalid)) {
            foreach ($orCondition as $input) {
//                $filter->get($input)->setRequired(false);
                $filter->get($input)->setAllowEmpty(true);
            }

            $form->hasValidated = false;
        }
    }

    public function createElements()
    {
        $ele = new Element\Text('last_name');
        $ele->setOptions(array('is_optional_required' => true));
        $ele->setLabel('Фамилия');
        $this->add($ele);

        $ele = new Element\Text('first_name');
        $ele->setOptions(array('is_optional_required' => true));
        $ele->setLabel('Имя');
        $this->add($ele);

        $ele = new Element\Text('middle_name');
        $ele->setLabel('Отчество');
        $this->add($ele);

        $ele = new Element\Text('nickname');
        $ele->setOptions(array(
            'is_optional_required' => true,
            'description' => 'Имя для отображения на сайте'
        ));

        $ele->setLabel('Ник');
        $this->add($ele);

        {
            $ele = new Element\Radio('sex_id');
            $ele->setLabel('Обращение');
            $this->add($ele);

            $options = array();
            foreach (Model::getSexItems() as $item) {
                $options[$item['id']] = $item['title'];
            }

            $ele->setValueOptions($options);
        }

        /**
         * @todo Подумать о том, чтобы вынести из ядра
         */
        $ele = new Element\Text('city_id');
        $ele->setLabel('Город');
        $ele->setAttribute('class', 'select2');
        $ele->setAttribute('data-url', '/api/cities');
        $this->add($ele);

        $ele = new Element\Text('position');
        $ele->setLabel('Вид деятельности');
        $ele->setOptions(array('description' => 'или&nbsp;должность'));
        $this->add($ele);

        $ele = new Element\Text('birth_day');
        $ele->setLabel('День и месяц рождения');
        $ele->setOptions(array('description' => 'ДД.ММ, например 01.12'));
        $this->add($ele);

        $ele = new Element\Number('birth_year');
        $ele->setLabel('Год рождения');
        $ele->setAttribute('min', date('Y') - 120);
        $ele->setAttribute('max', date('Y'));
        $this->add($ele);

        $ele = new Element\Textarea('about');
        $ele->setLabel('Описание');
        $ele->setAttribute('rows', 5);
        $this->add($ele);

        $ele = new Element\Checkbox('is_published');
        $ele->setLabel('Использовать?');
        $this->add($ele);

        $ele = new Element\Number('rating');
        $ele->setLabel('Рейтинг');
        $this->add($ele);
    }
}
