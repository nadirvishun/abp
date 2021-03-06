<?php

use yii\db\Migration;

/**
 * Handles the creation of table `access_token`.
 */
class m170328_083427_create_user_token_table extends Migration
{
    const TBL_NAME = '{{%user_token}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            //获取mysql版本
            $version = $this->db->getServerVersion();
            //utf8mb4在小于5.5.3的mysql版本中不支持
            if (version_compare($version, '5.5.3', '<')) {
                throw new \yii\base\Exception('Character utf8mb4 is not supported in mysql < 5.5.3');
            }
            //如果mysql数据库版本小于5.7.7，则需要将varchar默认值修改为191，否则报错：Specified key was too long error
            if (version_compare($version, '5.7.7', '<')) {
                $queryBuilder = $this->db->getQueryBuilder();
                $queryBuilder->typeMap[\yii\db\mysql\Schema::TYPE_STRING] = 'varchar(191)';
            }
            //如果是用utf8字符集，则不需要上面的两个判定
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="移动端登录token表"';
        }

        $this->createTable(self::TBL_NAME, [
            'access_token' => $this->string(40)->notNull()->comment('接口token'),
            'user_id' => $this->integer()->notNull()->defaultValue(0)->comment('用户ID'),
            'access_expires' => $this->bigInteger()->notNull()->defaultValue(0)->comment('access_token过期时间'),
            'client_type' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('客户端类型，0未知，1为安卓，2为ios，3为wap'),
            'refresh_token'=>$this->string(40)->notNull()->defaultValue('')->comment('刷新token'),
            'refresh_expires'=>$this->bigInteger()->notNull()->defaultValue(0)->comment('refresh_token过期时间'),
            'created_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('更新时间')
        ], $tableOptions);
        //添加主键及索引
        $this->addPrimaryKey('access_token', self::TBL_NAME, 'access_token');
        $this->createIndex('user_id', self::TBL_NAME, 'user_id');
        $this->createIndex('refresh_token', self::TBL_NAME, 'refresh_token');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
