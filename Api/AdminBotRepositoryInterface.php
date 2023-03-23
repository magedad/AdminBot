<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface AdminBotRepositoryInterface
{

    /**
     * Save admin_bot
     * @param \MageDad\AdminBot\Api\Data\AdminBotInterface $adminBot
     * @return \MageDad\AdminBot\Api\Data\AdminBotInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \MageDad\AdminBot\Api\Data\AdminBotInterface $adminBot
    );

    /**
     * Retrieve admin_bot
     * @param string $adminBotId
     * @return \MageDad\AdminBot\Api\Data\AdminBotInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($adminBotId);

    /**
     * Retrieve admin_bot matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MageDad\AdminBot\Api\Data\AdminBotSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete admin_bot
     * @param \MageDad\AdminBot\Api\Data\AdminBotInterface $adminBot
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \MageDad\AdminBot\Api\Data\AdminBotInterface $adminBot
    );

    /**
     * Delete admin_bot by ID
     * @param string $adminBotId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($adminBotId);
}

