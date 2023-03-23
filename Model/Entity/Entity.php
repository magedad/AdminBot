<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\App\ObjectManager;

class Entity
{
    public $authorization;
    public $aclRetriever;

    public function __construct(
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever = null,
        AuthorizationInterface $authorization = null
    ) {
        $this->aclRetriever = $aclRetriever ?: ObjectManager::getInstance()->get(\Magento\Authorization\Model\Acl\AclRetriever::class);
        $this->authorization = $authorization ?: ObjectManager::getInstance()->get(AuthorizationInterface::class);
    }

    public const SEARCH_WORDS = [];

    public function cleanUpQuery($words, $query)
    {
        usort($words, function($a, $b){
            return strlen($b) - strlen($a);
        });

        foreach ($words as $key => $value) {
            $query = trim(str_replace(strtolower($value),'', strtolower($query)));
        }

        return $query;
    }

    public function checkQueryWithKeyword($words, $query)
    {
       foreach ($words as $key => $word) {
            if (strpos(strtolower($query), strtolower($word)) !== false) {
                return true;
            }
        }

        return false;
    }

    public function getKeyWords()
    {
        return static::SEARCH_WORDS;
    }

    public function returnData($title = '', $options = [], $url = '', $placeholder = '', $extraInfo = '', $type = '', $subtitle = '')
    {
        foreach ($options as $key => $option) {
            if (!isset($option['title'])) {
                unset($options[$key]);
            }
        }

        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'options' => $options ?? [],
            'url' => $url,
            'placeholder' => $placeholder,
            'extraInfo' => $extraInfo,
            'type' => $type,
        ];
    }
}