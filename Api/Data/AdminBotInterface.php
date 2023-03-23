<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Api\Data;

interface AdminBotInterface
{

    const QUESTION = 'question';
    const ADMIN_BOT_ID = 'admin_bot_id';
    const REPLY = 'reply';

    /**
     * Get admin_bot_id
     * @return string|null
     */
    public function getAdminBotId();

    /**
     * Set admin_bot_id
     * @param string $adminBotId
     * @return \MageDad\AdminBot\AdminBot\Api\Data\AdminBotInterface
     */
    public function setAdminBotId($adminBotId);

    /**
     * Get question
     * @return string|null
     */
    public function getQuestion();

    /**
     * Set question
     * @param string $question
     * @return \MageDad\AdminBot\AdminBot\Api\Data\AdminBotInterface
     */
    public function setQuestion($question);

    /**
     * Get reply
     * @return string|null
     */
    public function getReply();

    /**
     * Set reply
     * @param string $reply
     * @return \MageDad\AdminBot\AdminBot\Api\Data\AdminBotInterface
     */
    public function setReply($reply);
}

