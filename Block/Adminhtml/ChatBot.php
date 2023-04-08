<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */

declare(strict_types=1);

namespace MageDad\AdminBot\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ChatBot extends Template
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    private $authorization;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->authorization = $context->getAuthorization();
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * Is allowed check
     *
     * @return mixed
     */
    public function isAllowed()
    {
        return $this->authorization->isAllowed('MageDad_AdminBot::chatbot_manage')
            && $this->scopeConfig->getValue('admin_chatbot/general/active');
    }

    /**
     * Get predefined words
     *
     * @return array
     */
    public function getPredefinedWords()
    {
        $options = [];

        if ($this->authorization->isAllowed('Magento_Sales::sales')) {
            $options[] = __('Sales');
        }

        if ($this->authorization->isAllowed('Magento_Catalog::catalog')) {
            $options[] = __('Catalog');
        }

        if ($this->authorization->isAllowed('Magento_Customer::customer')) {
            $options[] = __('Customers');
        }

        if ($this->authorization->isAllowed('Magento_Cms::page')) {
            $options[] = __('CMS Pages');
        }

        if ($this->authorization->isAllowed('Magento_Cms::block')) {
            $options[] = __('CMS Block');
        }

        if ($this->authorization->isAllowed('Magento_Config::config')) {
            $options[] = __('Config');
        }

        if ($this->authorization->isAllowed('Magento_Backend::marketing')) {
            $options[] = __('Marketing');
        }

        $options[] = __('Menu');

        return $options;
    }
}
