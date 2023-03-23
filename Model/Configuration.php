<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model;

class Configuration
{
    public function getReplyForMessage($msg)
    {
        if (strtolower($msg) == "product") {
            return ['msg' => 'Do you want?' , 'suggetion' => ["Add new product", "Edit Product", "Delete Product"]];
        }

        return ['msg' => 'Sorry, I didn\'t understand can you please try again.'];
    }
}