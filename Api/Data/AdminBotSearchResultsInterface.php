<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Api\Data;

interface AdminBotSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get admin_bot list.
     * @return \MageDad\AdminBot\Api\Data\AdminBotInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \MageDad\AdminBot\Api\Data\AdminBotInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

