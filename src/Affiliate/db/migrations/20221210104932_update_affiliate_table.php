<?php

use Phinx\Migration\AbstractMigration;

class UpdateAffiliateTable extends AbstractMigration
{
    public function change()
    {
        $this->dropTableIfExist("affiliates");
        $this->dropTableIfExist("affiliate_users");
        $this->dropTableIfExist("affiliate_logs");
        $this->table('affiliate_users')
            ->addColumn("ref_id", "integer")
            ->addColumn("target_id", "integer")
            ->addForeignKey("ref_id", "users", ['id'], ['delete' => 'CASCADE'])
            ->addForeignKey("target_id", "users", ['id'], ['delete' => 'CASCADE'])
            ->addTimestamps()
            ->create();
        $this->table('affiliates')
            ->addColumn("token", "string")
            ->addColumn("user_id", "integer")
            ->addColumn("visitors", "integer", ['default' => 0])
            ->addColumn("signups", "integer", ['default' => 0])
            ->addColumn("balance", "float", ['default' => 0]) // total
            ->addColumn("withdrawn", "float", ['default' => 0]) // en attente
            ->addForeignKey("user_id", "users", ['id'], ['delete' => 'CASCADE'])
            ->addTimestamps()
            ->create();

        $this->table('affiliates_withdrawals')
            ->addColumn("user_id", "integer")
            ->addColumn("amount", "integer", ['default' => 0])
            ->addColumn("state", "enum", ['values' => ['PENDING', 'ACCEPTED', 'REFUSED']])
            ->addForeignKey("user_id", "users", ['id'], ['delete' => 'CASCADE'])
            ->addTimestamps()
            ->create();

        $this->table('affiliate_logs')
            ->addColumn("description", "string")
            ->addColumn("amount", "float")
            ->addColumn("user_id", "integer")
            ->addColumn("aff_id", "integer")
            ->addForeignKey("aff_id", "affiliates", ['id'], ['delete' => 'CASCADE'])
            ->addTimestamps()
            ->create();
    }

    private function dropTableIfExist(string $table)
    {
        $table = $this->table($table);
        if ($table->exists()){
            $table->drop()->save();
        }
    }
}
