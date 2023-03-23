<?php

declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

class MainCatalog extends Entity
{
    public const CATALOG_QUERY = 'Catalog';
    public const SEARCH_WORDS = [
        self::CATALOG_QUERY
    ];

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageDad\AdminBot\Model\ReplyFormat $replyFormat
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->replyFormat = $replyFormat;
        parent::__construct();
    }

    public function checkIsMyQuery($query)
    {
        $salesAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $salesAllQuery);
    }

    public function checkIsMyQueryWithKeyword($query)
    {
        return $this->checkQueryWithKeyword(self::SEARCH_WORDS, $query);
    }

    public function cleanQuery($query)
    {
        return $this->cleanUpQuery(self::SEARCH_WORDS, $query);
    }

    public function getReply($query)
    {
        if (strtolower($query) == strtolower(self::CATALOG_QUERY) || strtolower($query) == 'sale') {
            return $this->mainOption($query);
        }

        return [];
    }

    private function mainOption($query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->getProductOption(),
                $this->getCategoryOption()
            ]
        );
    }

    private function getProductOption()
    {
        if ($this->authorization->isAllowed('Magento_Catalog::products')) {
            return $this->returnData(__('Products'));
        }
    }

    private function getCategoryOption()
    {
        if ($this->authorization->isAllowed('Magento_Catalog::categories')) {
            return $this->returnData(__('Categories'));
        }
    }
}