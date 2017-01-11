<?php
namespace Boxspaced\CmsCoreModule\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\Filter;

class SequenceBlockFieldset extends Fieldset implements InputFilterProviderInterface
{

    /**
     * @param string $name
     * @param array $blockValueOptions
     */
    public function __construct($name, array $blockValueOptions)
    {
        parent::__construct($name);

        $element = new Element\Hidden('orderBy');
        $this->add($element);

        $element = new Element\Select('id');
        $element->setEmptyOption('');
        $element->setValueOptions($blockValueOptions);
        $this->add($element);
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'orderBy' => [
                'allow_empty' => true,
                'filters' => [
                    ['name' => Filter\ToInt::class],
                ],
            ],
            'id' => [
                'allow_empty' => true,
            ],
        ];
    }

}
