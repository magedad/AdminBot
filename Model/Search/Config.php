<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search;

use MageDad\AdminBot\Model\Search\Config\Result\Builder;
use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\Element\AbstractComposite;
use Magento\Config\Model\Config\Structure\Element\Iterator as ElementIterator;
use Magento\Framework\DataObject;

class Config extends DataObject
{
    /**
     * @param Structure $configStructure
     * @param Builder $resultBuilder
     */
    public function __construct(Structure $configStructure, Builder $resultBuilder)
    {
        $this->configStructure = $configStructure;
        $this->resultBuilder = $resultBuilder;
    }

    /**
     * Set query
     *
     * @param string $query
     * @return $this
     */
    public function setQuery(string $query)
    {
        $this->setData('query', $query);
        return $this;
    }

    /**
     * Get query
     *
     * @return string|null
     */
    public function getQuery()
    {
        return $this->getData('query');
    }

    /**
     * Has query
     *
     * @return bool
     */
    public function hasQuery()
    {
        return $this->hasData('query');
    }

    /**
     * Set result
     *
     * @param array $results
     * @return $this
     */
    public function setResults(array $results)
    {
        $this->setData('results', $results);
        return $this;
    }

    /**
     * Get result
     *
     * @return array|null
     */
    public function getResults()
    {
        return $this->getData('results');
    }

    /**
     * Load search results
     *
     * @return $this
     */
    public function load()
    {
        $this->findInStructure($this->configStructure->getTabs(), $this->getQuery());
        $this->setResults($this->resultBuilder->getAll());
        return $this;
    }

    /**
     * Find in structure
     *
     * @param ElementIterator $structureElementIterator
     * @param string $searchTerm
     * @param string $pathLabel
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    private function findInStructure(ElementIterator $structureElementIterator, $searchTerm, $pathLabel = '')
    {
        if (empty($searchTerm)) {
            return;
        }
        foreach ($structureElementIterator as $structureElement) {
            if (mb_stripos((string)$structureElement->getLabel(), $searchTerm) !== false) {
                $this->resultBuilder->add($structureElement, $pathLabel);
            }
            $elementPathLabel = $pathLabel . ' / ' . $structureElement->getLabel();
            if ($structureElement instanceof AbstractComposite && $structureElement->hasChildren()) {
                $this->findInStructure($structureElement->getChildren(), $searchTerm, $elementPathLabel);
            }
        }
    }
}
