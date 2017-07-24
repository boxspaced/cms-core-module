<?php
namespace Boxspaced\CmsCoreModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Filter\StaticFilter;
use Zend\Form\Element;
use Zend\Filter\Word\CamelCaseToDash;

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

        $this->view->formElementErrors()
            ->setMessageCloseString('</strong></span>')
            ->setMessageOpenFormat('<span%s><strong>')
            ->setMessageSeparatorString('</strong><strong>');

        $this->view->formRadio()
            ->setSeparator('</div><div class="radio">');

        $this->view->formMultiCheckbox()
            ->setSeparator('</div><div class="checkbox">');

        // @todo set ID in form
        $element->setAttribute('id', $this->nameToId($element->getName()));

        if ('hidden' === $element->getAttribute('type')) {
            return $this->decorateNone($element);
        }

        if ('checkbox' === $element->getAttribute('type')) {
            return $this->decorateCheckbox($element);
        }

        return $this->decorate($element);
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
        $html = $this->generateOpeningWrapperTag($element);
        $html .= $this->generateLabel($element);
        $html .= $this->generateOpeningElementWrapperTag($element);

        if (!$this->isMulti($element) && 'file' !== $element->getAttribute('type')) {
            $element->setAttribute('class', 'form-control');
        }

        $html .= $this->view->formElement($element);

        // @todo remove and add button with JS
        if (1 === preg_match('/([a-z]+)\-browser/i', $element->getAttribute('class'), $matches)) {
            $html .= sprintf(
                '<input type="button" class="%s-browse-button" value="%s" />',
                $matches[1],
                $this->view->translate('Browse server...')

            );
        }

        $html .= $this->view->formElementErrors($element, [
            'class' => 'help-block',
        ]);

        $html .= $this->generateHelpBlock($element);
        $html .= $this->generateClosingElementWrapperTag($element);
        $html .= $this->generateClosingWrapperTag($element);

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
     * @param Element $element
     * @return bool
     */
    protected function isMulti(Element $element)
    {
        return in_array($element->getAttribute('type'), [
            'radio',
            'multi_checkbox',
        ]);
    }

    /**
     * @param Element $element
     * @return string
     */
    protected function generateOpeningWrapperTag(Element $element)
    {
        if ('checkbox' === $element->getAttribute('type')) {
            $class = 'checkbox';
        } else {
            $class = 'form-group';
        }

        if (count($element->getMessages()) > 0) {
            $class .= ' has-error';
        }

        return sprintf(
            '<div id="%s-element" class="%s">',
            $element->getAttribute('id'),
            $class
        );
    }

    /**
     * @param Element $element
     * @return string
     */
    protected function generateClosingWrapperTag(Element $element)
    {
        return '</div>';
    }

    /**
     * @param Element $element
     * @return string
     */
    protected function generateOpeningElementWrapperTag(Element $element)
    {
        if (!$this->isMulti($element)) {
            return '<div class="col-md-10">';
        }

        if ('radio' === $element->getAttribute('type')) {
            return '<div class="radio">';
        }

        return '<div class="checkbox">';
    }

    /**
     * @param Element $element
     * @return string
     */
    protected function generateClosingElementWrapperTag(Element $element)
    {
        return '</div>';
    }

    /**
     * @param Element $element
     * @return string
     */
    protected function generateHelpBlock(Element $element)
    {
        $options = $element->getOptions();

        if (empty($options['description'])) {
            return '';
        }

        return sprintf(
            '<span class="help-block">%s</span>',
            $this->view->translate($options['description'])
        );
    }

    /**
     * @param Element $element
     * @param bool $withLabelTag
     * @return string
     */
    protected function generateLabel(Element $element, $withLabelTag = true)
    {
        $content = $this->view->translate($element->getLabel());

        if ($element->hasAttribute('required')) {
            $content .= '<img src="/images/required_star.png" class="required-star" alt="Required">';
        }

        if (!$withLabelTag) {
            return $content;
        }

        $element->setLabelAttributes([
            'class' => 'col-md-2 control-label',
        ]);

        if ($this->isMulti($element)) {
            $open = $this->view->formLabel()->openTag();
        } else {
            $open = $this->view->formLabel()->openTag($element);
        }

        $close = $this->view->formLabel()->closeTag();

        return $open . $content . $close;
    }

    /**
     * @param Element $element
     * @return string
     */
    protected function decorateCheckbox(Element $element)
    {
        $html = $this->generateOpeningWrapperTag($element);
        $html .= $this->view->formElementErrors($element, [
            'class' => 'help-block',
        ]);
        $html .= $this->view->formLabel()->openTag($element);
        $html .= $this->view->formCheckbox($element);
        $html .= $this->generateLabel($element, false);
        $html .= $this->view->formLabel()->closeTag();
        $html .= $this->generateHelpBlock($element);
        $html .= $this->generateClosingWrapperTag($element);
        return $html;
    }

    /**
     * @param Element $element
     * @return string
     */
    protected function decorateNone(Element $element)
    {
        $html = $this->view->formElementErrors($element, [
            'class' => 'errors',
        ]);
        $html .= $this->view->formElement($element);
        return $html;
    }

}
