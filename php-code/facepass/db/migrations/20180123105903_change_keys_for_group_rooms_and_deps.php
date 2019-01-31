<?php


use Phinx\Migration\AbstractMigration;

class ChangeKeysForGroupRoomsAndDeps extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('filial_departament_floor');
        $table->dropForeignKey('floor_id', 'filial_departament_floor_floor_id_fkey');
        $table->addForeignKey('floor_id', 'filial_group_rooms', ['id'],
            ['delete' => 'cascade', 'update' => 'cascade', 'constraint'=>'filial_departament_floor_floor_id_fkey'])
        ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('filial_departament_floor');
        $table->dropForeignKey('floor_id', 'filial_departament_floor_floor_id_fkey');
        $table->addForeignKey('floor_id', 'filial_rooms', ['id'],
            ['delete' => 'cascade', 'update' => 'cascade', 'constraint'=>'filial_departament_floor_floor_id_fkey'])
            ->save();
    }
}
