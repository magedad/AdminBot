<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model;

use MageDad\AdminBot\Api\Data\AdminBotInterface;
use Magento\Framework\Model\AbstractModel;

class AdminBot extends AbstractModel implements AdminBotInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\MageDad\AdminBot\Model\ResourceModel\AdminBot::class);
    }

    /**
     * @inheritDoc
     */
    public function getAdminBotId()
    {
        return $this->getData(self::ADMIN_BOT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAdminBotId($adminBotId)
    {
        return $this->setData(self::ADMIN_BOT_ID, $adminBotId);
    }

    /**
     * @inheritDoc
     */
    public function getQuestion()
    {
        return $this->getData(self::QUESTION);
    }

    /**
     * @inheritDoc
     */
    public function setQuestion($question)
    {
        return $this->setData(self::QUESTION, $question);
    }

    /**
     * @inheritDoc
     */
    public function getReply()
    {
        return $this->getData(self::REPLY);
    }

    /**
     * @inheritDoc
     */
    public function setReply($reply)
    {
        return $this->setData(self::REPLY, $reply);
    }
}

