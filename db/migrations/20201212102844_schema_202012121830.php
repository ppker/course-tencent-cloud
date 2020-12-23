<?php

use Phinx\Db\Adapter\MysqlAdapter;

class Schema202012121830 extends Phinx\Migration\AbstractMigration
{
    public function change()
    {
        $this->table('kg_consult')
            ->addColumn('replier_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'comment' => '回复者编号',
                'after' => 'owner_id',
            ])
            ->save();
        $this->table('kg_connect')
            ->addColumn('union_id', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => 'union_id',
                'after' => 'user_id',
            ])
            ->addIndex(['union_id', 'provider'], [
                'name' => 'union_provider',
                'unique' => false,
            ])
            ->addIndex(['user_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->save();
        $this->table('kg_wechat_subscribe', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => 'enable',
                'comment' => '主键编号',
            ])
            ->addColumn('user_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'comment' => '用户编号',
                'after' => 'id',
            ])
            ->addColumn('open_id', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '开放ID',
                'after' => 'user_id',
            ])
            ->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'comment' => '删除标识',
                'after' => 'open_id',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'comment' => '创建时间',
                'after' => 'deleted',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->addIndex(['open_id'], [
                'name' => 'open_id',
                'unique' => false,
            ])
            ->addIndex(['user_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->create();
    }
}
