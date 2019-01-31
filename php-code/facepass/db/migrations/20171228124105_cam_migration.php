<?php


use Phinx\Migration\AbstractMigration;

class CamMigration extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('DELETE FROM filial_terminal');
        $this->execute('DELETE FROM filial_turnstiles');

        $table = $this->table('filial_terminal');
        $table->dropForeignKey('camera_id');

        $table = $this->table('filial_terminal');
        $table->addForeignKey('camera_id', 'filial_camera', ['id'],
            ['constraint'=>'camera_id_fkey'])
            ->save();

        $table = $this->table('filial_turnstiles');
        $table->dropForeignKey('camera_in_id');

        $table = $this->table('filial_turnstiles');
        $table->addForeignKey('camera_in_id', 'filial_camera', ['id'],
            ['constraint'=>'camera_in_id_fkey'])
            ->save();

        $table = $this->table('filial_turnstiles');
        $table->dropForeignKey('camera_out_id');

        $table = $this->table('filial_turnstiles');
        $table->addForeignKey('camera_out_id', 'filial_camera', ['id'],
            ['constraint'=>'camera_out_id_fkey'])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('filial_terminal');
        $table->addForeignKey('camera_id', 'filial_equipment', ['id'],
            ['constraint'=>'filial_terminal_camera_id_fkey'])
            ->save();

        $table = $this->table('filial_turnstiles');
        $table->addForeignKey('camera_in_id', 'filial_equipment', ['id'],
            ['constraint'=>'filial_turnstiles_camera_in_id_fkey'])
            ->save();

        $table = $this->table('filial_turnstiles');
        $table->addForeignKey('camera_out_id', 'filial_equipment', ['id'],
            ['constraint'=>'filial_turnstiles_camera_out_id_fkey'])
            ->save();
    }
}
