<?php
namespace Core\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;

class FreeBlockFieldset extends Fieldset implements InputFilterProviderInterface
{

    /**
     * @param string $name
     * @param string $label
     * @param array $blockValueOptions
     */
    public function __construct($name, $label, array $blockValueOptions)
    {
        parent::__construct($name);

        $this->setLabel($label);

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
            'id' => [
                'allow_empty' => true,
            ],
        ];
    }

}
