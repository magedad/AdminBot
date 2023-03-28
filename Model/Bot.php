<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model;

use Magento\Backend\App\Action\Context;

class Bot
{
    /**
     * @var array
     */
    public $queryFor = [];

    /**
     * Construct
     *
     * @param Context $context
     * @param ReplyFormat $replyFormat
     * @param array $searchModules
     * @param array $autoReplyEntity
     */
    public function __construct(
        Context $context,
        ReplyFormat $replyFormat,
        array $searchModules = [],
        array $autoReplyEntity = []
    ) {
        $this->replyFormat = $replyFormat;
        $this->authorization = $context->getAuthorization();
        $this->searchModules = $searchModules;
        $this->autoReplyEntity = $autoReplyEntity;
        $this->objectManager = $context->getObjectManager();
    }

    /**
     * Get reply for message
     *
     * @param string $msg
     * @return array|string
     */
    public function getReplyForMessage(string $msg)
    {
        $response = [];
        if ($msg != '') {
            $response = $this->getAutoReply($msg);
        }

        if (count($response) == 0) {
            $response = $this->globalSearch($msg);
        }

        if (count($response) > 0) {
            return $response;
        }

        if ($this->queryFor != '') {
            $this->queryFor['title'] = __("Opps, can you please try something else.");
            return $this->queryFor;
        }

        return $this->replyFormat->returnData('Opps, can you please try something else.');
    }

    /**
     * Get auto reply
     *
     * @param string $query
     * @return array
     */
    private function getAutoReply(string $query)
    {
        foreach ($this->autoReplyEntity as $type => $object) {
            if ($object->checkIsMyQuery($query)) {
                $reply = $object->getReply($query);
                if (empty($reply)) {
                    return $this->replyFormat->returnData(
                        __('Opps, can you please try something else. Maybe you search for restricted are.')
                    );
                }
                return $reply;
            }
        }
        return [];
    }

    /**
     * Global search
     *
     * @param string $query
     * @return array
     */
    private function globalSearch(string $query)
    {
        $items = [];
        $originalQuery = $query;
        foreach ($this->autoReplyEntity as $type => $object) {
            $query = $originalQuery;
            if ($object->checkIsMyQueryWithKeyword($query)) {
                $queryClean = $object->cleanQuery($query);
                $items = $this->search($queryClean, $type);
                $type = str_replace($queryClean, '', $query);
                $this->queryFor = $object->getReply(trim($type));
            }
        }

        if (count($items) == 0) {
            $items = $this->search($query);
        }

        return $items;
    }

    /**
     * Search
     *
     * @param string $msg
     * @param string $type
     * @return array
     */
    private function search(string $msg, string $type = null)
    {
        $items = [];
        foreach ($this->searchModules as $key => $searchConfig) {

            if ($type != null && $type != $key) {
                continue;
            }

            if ($searchConfig['acl'] && !$this->authorization->isAllowed($searchConfig['acl'])) {
                continue;
            }

            if (count($items) >= 10) {
                continue;
            }

            $className = $searchConfig['class'];
            if (empty($className)) {
                continue;
            }
            $searchInstance = $this->objectManager->create($className);
            $results = $searchInstance->setStart(
                1
            )->setLimit(
                10
            )->setQuery(
                $msg
            )->load()->getResults();

            $items = array_merge_recursive($items, $results);
        }

        $items = array_slice($items, 0, 10, true);

        $data = [];
        foreach ($items as $key => $item) {
            $data[] = $this->replyFormat->returnData(
                $item['name'],
                [],
                $item['url'],
                '',
                $item['extraInfo'] ?? '',
                $item['type'],
                $item['subtitle'] ?? '',
            );
        }

        if (count($data) > 0) {
            return $this->replyFormat->returnData(
                __('We found below data.'),
                $data
            );
        }

        return [];
    }

    /**
     * Get Predefined words
     *
     * @return mixed
     */
    public function getPredefinedWords()
    {
        $words = [];
        foreach ($this->autoReplyEntity as $type => $object) {
            $words[] = $object->getKeyWords();
        }
        $words = array_reduce($words, 'array_merge', []);
        asort($words);
        return $words;
    }
}
