<?php

namespace flience\userslimit\models;
use craft\base\Model;
use flience\userslimit\UsersLimit;

class Settings extends Model
{
    public $limit = 1;

    public function rules()
    {
        return [
            ['limit', 'number'],
        ];
    }
}
