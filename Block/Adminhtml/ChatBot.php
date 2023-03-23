<?php

declare(strict_types=1);

namespace MageDad\AdminBot\Block\Adminhtml;

class ChatBot extends \Magento\Backend\Block\Template
{
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->authorization = $context->getAuthorization();
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function isAllowed()
    {
        return $this->authorization->isAllowed('MageDad_AdminBot::chatbot_manage');
    }
}

