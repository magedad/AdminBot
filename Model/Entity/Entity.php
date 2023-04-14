<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

use Magento\Authorization\Model\Acl\AclRetriever;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\App\ObjectManager;

#[\AllowDynamicProperties]
abstract class Entity
{
    public const AUTO_REPLY_WORDS = [];
    public const NO_AUTO_REPLY_QUERY = [];

    /**
     * Auto Reply Query Check
     *
     * @param string $query
     * @return mixed
     */
    abstract public function autoReplyQueryCheck(string $query);

    /**
     * Constructor
     *
     * @param AclRetriever|null $aclRetriever
     * @param AuthorizationInterface|null $authorization
     */
    public function __construct(
        AclRetriever $aclRetriever = null,
        AuthorizationInterface $authorization = null
    ) {
        $this->aclRetriever =
            $aclRetriever ?: ObjectManager::getInstance()->get(\Magento\Authorization\Model\Acl\AclRetriever::class);
        $this->authorization =
            $authorization ?: ObjectManager::getInstance()->get(AuthorizationInterface::class);
    }

    /**
     * Clean up query
     *
     * @param array $words
     * @param string $query
     * @return string
     */
    public function cleanUpQuery(array $words, string $query)
    {
        usort($words, function ($a, $b) {
            return strlen($b) - strlen($a);
        });
        foreach ($words as $key => $value) {
            $query = trim(str_replace(strtolower($value), '', strtolower($query)));
        }

        return $query;
    }

    /**
     * Check Query With Keyword
     *
     * @param array $words
     * @param string $query
     * @return bool
     */
    public function checkQueryWithKeyword(array $words, string $query)
    {
        $words = array_map('strtolower', $words);
        foreach ($words as $key => $word) {
            if (strpos($query, $word) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get key words
     *
     * @return array
     */
    public function getKeyWords()
    {
        return static::AUTO_REPLY_WORDS;
    }

    /**
     * Return data
     *
     * @param string $title
     * @param array $options
     * @param string $url
     * @param string $placeholder
     * @param string $extraInfo
     * @param string $type
     * @param string $subtitle
     * @return array
     */
    public function returnData(
        $title = '',
        $options = [],
        $url = '',
        $placeholder = '',
        $extraInfo = '',
        $type = '',
        $subtitle = ''
    ) {
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

    /**
     * Get key words
     *
     * @param \Magento\Framework\Phrase $string
     * @return string
     */
    protected function typeCommand(\Magento\Framework\Phrase $string): string
    {
        return __('Enter:')." ".$string;
    }

    /**
     * Shortcut List
     *
     * @return array
     */
    public function getShortcutList(): array
    {
        return [];
    }
}
