<?php


use Phinx\Migration\AbstractMigration;

class CreateUserImagesTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('user_images');
        $table->addColumn('path', 'string')
            ->addColumn('path_mini', 'string')
            ->addColumn('user_id', 'integer')
            ->addColumn('type', 'string')
            ->save();
    }

    public function down()
    {
        $this->dropTable('user_images');
    }
}
