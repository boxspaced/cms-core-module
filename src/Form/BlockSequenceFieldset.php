<?php
namespace Core\Form;

use Zend\Form\Fieldset;
use Zend\Form\Element;

class BlockSequenceFieldset extends Fieldset
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

        $element = new Element\Collection('blocks');
        $element->setCount(0);
        $element->setTargetElement(new SequenceBlockFieldset('block', $blockValueOptions));
        $this->add($element);
    }

}
