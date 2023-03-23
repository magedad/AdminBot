<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AdminBot extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('magedad_adminbot_admin_bot', 'admin_bot_id');
    }
}

