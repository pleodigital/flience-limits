<?php

namespace flience\userslimit\migrations;

use Craft;
use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $usersLimit = "userslimit";
        if (!$this->db->tableExists($usersLimit)) {
            $this->createTable($usersLimit, [
                'id' => $this->primaryKey(11),
                "downloaded" => $this->integer(),
                "dateCreated" => $this->dateTime()->notNull()
            ]);
            $this->alterColumnt($usersLimit, "id", $this->smallInteger(8)." NOT NULL AUTO_INCREMENT");
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $usersLimit = "userslimit";
        if ($this->db->tableExists($usersLimit)) {
            $this->dropTable($usersLimit);
        }
    }
}

?>

