<?php
namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Filter\StaticFilter;
use Zend\Form\Element;
use Zend\Filter\Word\CamelCaseToDash;
use Zend\Form\View\Helper\FormLabel;
use Zend\Form\Element\MultiCheckbox;

class AdminFormElement extends AbstractHelper
{

    /**
     * @param Element $element
     * @return string
     */
    public function __invoke(Element $element = null)
    {
        if (null === $element) {
            return $this;
        }

        // @todo set ID in form
        $element->setAttribute('id', $this->nameToId($element->getName()));

        if ('hidden' !== $element->getAttribute('type')) {
            return $this->decorate($element);
        }

        return $this->decorateDefault($element);
    }

    /**
     * @param string $name
     * @return string
     */
    public function nameToId($name)
    {
        $name = str_replace(array('[', ']'), '-', $name);
        $name = str_replace('--', '-', $name);
        $name = rtrim($name, '-');
        $name = strtolower(StaticFilter::execute($name, CamelCaseToDash::class));
        return $name;
    }

    /**
     * @param Element $element
     * @return string
     */
    protected function decorate(Element $element)
    {
        $html = sprintf(
            '<div id="%s-element" class="form-element">',
            $element->getAttribute('id')
        );

        $labelClass = 'lined-up';

        if ($element->hasAttribute('required')) {
            $labelClass .= ' required';
        }

        $element->setLabelAttributes([
            'class' => $labelClass,
        ]);

        if ($element->hasAttribute('required')) {
            $html .= $this->view->formLabel($element, '<img src="/images/required_star.png" class="required-star" alt="Required">', FormLabel::PREPEND);
        } else {
            $html .= $this->view->formLabel($element);
        }

        if (count($element->getMessages()) > 0) {

            $currentClass = $element->getAttribute('class');
            $element->setAttribute('class', ($currentClass ? $currentClass . ' errored' : 'errored'));

            foreach ($element->getMessages() as $message) {
                $html .= sprintf('<div class="validation-error line-up">%s</div>', $message);
            }
        }

        if ($element instanceof MultiCheckbox) {

            $this->overrideValueOptionLabelAttributes($element, [
                'class' => '',
            ]);

            $elementHtml = $this->view->formMultiCheckbox()
                ->setSeparator('<br>')
                ->render($element);

        } else {
            $elementHtml = $this->view->formElement($element);
        }

        // @todo remove and add button with JS
        if (1 === preg_match('/([a-z]+)\-browser/i', $element->getAttribute('class'), $matches)) {
            $elementHtml .= sprintf(
                '<input type="button" class="%s-browse-button" value="%s" />',
                $matches[1],
                $this->view->translate('Browse server...')

            );
        }

        $html .= sprintf(
            '<div class="form-inputs line-up">%s</div>',
            $elementHtml
        );

        $options = $element->getOptions();

        if (isset($options['description'])) {
            $html .= sprintf(
                '<div class="form-tip line-up">%s</div>',
                $this->view->translate($options['description'])
            );
        }

        $html .= '<div class="clear"></div></div>';

        if (false !== strpos($element->getAttribute('class'), 'wysiwyg')) {

            $html .= sprintf('
                <script type="text/javascript">
                    var editor = CKEDITOR.replace(\'%s\', {
                        width: 700,
                        height: 300
                    });
                    CKFinder.setupCKEditor(editor, \'/ckfinder/\');
                </script>',
                $element->getAttribute('id')
            );
        }

        return $html;
    }

    /**
     * @todo remove this hack when layout revised as only needed to remove the 'lined-up' class from value option labels
     * @param Element $element
     * @return Element
     */
    protected function overrideValueOptionLabelAttributes(Element $element, array $labelAttributes)
    {
        if (!method_exists($element, 'getValueOptions')) {
            return $element;
        }

        $valueOptions = [];

        foreach ($element->getValueOptions() as $key => $valueOption) {

            if (is_scalar($valueOption)) {
                $valueOption = [
                    'label' => $valueOption,
                    'value' => $key
                ];
            }

            $valueOption['label_attributes'] = (isset($valueOption['label_attributes']))
                    ? array_merge($valueOption['label_attributes'], $labelAttributes)
                    : $labelAttributes;

            $valueOptions[] = $valueOption;
        }

        return $element->setValueOptions($valueOptions);
    }

    /**
     * @param Element $element
     * @return string
     */
    protected function decorateDefault(Element $element)
    {
        $html = '';

        foreach ($element->getMessages() as $message) {
            $html .= sprintf('<div class="validation-error line-up">%s</div>', $message);
        }

        $html .= $this->view->formElement($element);

        return $html;
    }

}
