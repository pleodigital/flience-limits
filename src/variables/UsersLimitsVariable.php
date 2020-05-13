<?php

namespace flience\userslimit\variables;

use flience\userslimit\UsersLimit;
use Craft;

class UsersLimitVariable {
    public function settings()
    {
        return craft()->plugins->getPlugin('users-limit')->getSettings();
    }
}
