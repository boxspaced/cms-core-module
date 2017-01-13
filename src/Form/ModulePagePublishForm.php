<?php
namespace Boxspaced\CmsCoreModule\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Boxspaced\CmsBlockModule\Service\BlockService;
use Boxspaced\CmsCoreModule\Service;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Zend\Filter;

class ModulePagePublishForm extends Form
{

    /**
     * @var int
     */
    protected $modulePageId;

    /**
     * @var Service\ModulePageService
     */
    protected $modulePageService;

    /**
     * @var BlockService
     */
    protected $blockService;

    /**
     * @param int $modulePageId
     * @param Service\ModulePageService $modulePageService
     * @param BlockService $blockService
     */
    public function __construct(
        $modulePageId,
        Service\ModulePageService $modulePageService,
        BlockService $blockService
    )
    {
        parent::__construct('module-page-publish');
        $this->modulePageId = $modulePageId;
        $this->modulePageService = $modulePageService;
        $this->blockService = $blockService;

        $this->setAttribute('method', 'post');
        $this->setAttribute('accept-charset', 'UTF-8');

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * @return void
     */
    protected function addElements()
    {
        $element = new Element\Csrf('token');
        $element->setCsrfValidatorOptions([
            'timeout' => 900,
        ]);
        $this->add($element);

        $element = new Element\Hidden('id');
        $this->add($element);

        $element = new Element\Hidden('from');
        $this->add($element);

        $element = new Element\Hidden('partial');
        $this->add($element);

        $freeBlocks = new Fieldset('freeBlocks');
        $this->add($freeBlocks);

        $blockSequences = new Fieldset('blockSequences');
        $this->add($blockSequences);

        foreach ($this->modulePageService->getModulePageBlocks($this->modulePageId) as $block) {

            if (!$block->sequence) {

                $freeBlock = new FreeBlockFieldset(
                    $block->name,
                    $block->adminLabel,
                    $this->getBlockValueOptions()
                );
                $freeBlocks->add($freeBlock);
                continue;
            }

            $blockSequence = new BlockSequenceFieldset(
                $block->name,
                $block->adminLabel,
                $this->getBlockValueOptions()
            );
            $blockSequences->add($blockSequence);
        }

        $element = new Element\Submit('preview');
        $element->setValue('Preview');
        $this->add($element);

        $element = new Element\Submit('publish');
        $element->setValue('Publish');
        $this->add($element);
    }

    /**
     * @return ModulePagePublishForm
     */
    protected function addInputFilter()
    {
        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'filters' => [
                ['name' => Filter\ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'from',
            'allow_empty' => true,
            'validators' => [
                [
                    'name' => Validator\InArray::class,
                    'options' => [
                        'haystack' => ['menu', 'standalone'],
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'partial',
            'allow_empty' => true,
            'filters' => [
                ['name' => Filter\Boolean::class],
            ],
        ]);

        return $this->setInputFilter($inputFilter);
    }

    /**
     * @return array
     */
    public function getBlockValueOptions()
    {
        $valueOptions = [];

        foreach ($this->blockService->getAvailableBlockOptions() as $typeOption) {

            $options = [];

            foreach ($typeOption->blockOptions as $blockOption) {
                $options[$blockOption->value] = $blockOption->label;
            }

            $valueOptions[$typeOption->name] = [
                'label' => $typeOption->name,
                'options' => $options,
            ];
        }

        return $valueOptions;
    }

    /**
     * @param Service\ModulePagePublishingOptions $options
     * @return ModulePagePublishForm
     */
    public function populateFromPublishingOptions(Service\ModulePagePublishingOptions $options)
    {
        $values = (array) $options;

        $freeBlocks = $values['freeBlocks'];
        $blockSequences = $values['blockSequences'];

        $values['freeBlocks'] = [];
        $values['blockSequences'] = [];

        foreach ($freeBlocks as $freeBlock) {
            $values['freeBlocks'][$freeBlock->name]['id'] = $freeBlock->id;
        }

        foreach ($blockSequences as $blockSequence) {

            $values['blockSequences'][$blockSequence->name] = [];

            foreach ($blockSequence->blocks as $key => $block) {

                $values['blockSequences'][$blockSequence->name]['blocks'][$key + 1]['id'] = $block->id;
                $values['blockSequences'][$blockSequence->name]['blocks'][$key + 1]['orderBy'] = $block->orderBy;
            }
        }

        return parent::populateValues($values);
    }

}
