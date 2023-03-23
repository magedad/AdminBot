<?php

declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Marketing;

use MageDad\AdminBot\Model\Entity\Entity;

class MainMarketing extends Entity
{
    public const MARKETING_QUERY = 'Marketing';
    public const SEARCH_WORDS = [
        self::MARKETING_QUERY
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
        if (strtolower($query) == strtolower(self::MARKETING_QUERY) || strtolower($query) == 'sale') {
            return $this->sales($query);
        }

        return [];
    }

    private function sales($query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->returnData(__('Catalog Price Rule')),
                $this->returnData(__('Cart Price Rules')),
                $this->returnData(__('Email Templates')),
                $this->returnData(__('URL Rewrites')),
                // $this->returnData(__('Newsletter Templates')),
                // $this->returnData(__('Newsletter Queue')),
                // $this->returnData(__('Search Terms')),
                // $this->returnData(__('Search Synonyms')),
                // $this->returnData(__('Site Map')),
                // $this->returnData(__('Reviews')),
                // $this->returnData(__('Pending Reviews')),
            ]
        );
    }
}