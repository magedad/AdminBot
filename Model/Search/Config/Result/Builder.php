<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */

namespace MageDad\AdminBot\Model\Search\Config\Result;

use Magento\Backend\Model\Search\Config\Structure\ElementBuilderInterface;
use Magento\Backend\Model\UrlInterface;
use Magento\Config\Model\Config\StructureElementInterface;

/**
 * Config SearchResult Builder
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Builder
{
    /**
     * @var array
     */
    private $results = [];

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ElementBuilderInterface[]
     */
    private $structureElementTypes;

    /**
     * @param UrlInterface $urlBuilder
     * @param array $structureElementTypes
     */
    public function __construct(UrlInterface $urlBuilder, array $structureElementTypes)
    {
        $this->urlBuilder = $urlBuilder;
        $this->structureElementTypes = $structureElementTypes;
    }

    /**
     * Get all
     *
     * @return array
     */
    public function getAll()
    {
        return $this->results;
    }

    /**
     * Add
     *
     * @param StructureElementInterface $structureElement
     * @param string $elementPathLabel
     * @return void
     */
    public function add(StructureElementInterface $structureElement, $elementPathLabel)
    {
        $urlParams = [];
        $elementData = $structureElement->getData();

        if (!in_array($elementData['_elementType'], array_keys($this->structureElementTypes))) {
            return;
        }

        if (isset($this->structureElementTypes[$elementData['_elementType']])) {
            $urlParamsBuilder = $this->structureElementTypes[$elementData['_elementType']];
            $urlParams = $urlParamsBuilder->build($structureElement);
        }

        $this->results[] = [
            'id'    => $structureElement->getPath(),
            'type'  => 'Configuration',
            'subtitle'  => 'Configuration',
            'name'  => (string)$structureElement->getLabel(),
            'extraInfo' => $elementPathLabel,
            'url'   => $this->urlBuilder->getUrl('*/system_config/edit', $urlParams),
        ];
    }
}
