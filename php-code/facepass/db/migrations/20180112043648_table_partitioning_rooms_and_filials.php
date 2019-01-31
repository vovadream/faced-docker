<?php


use Phinx\Migration\AbstractMigration;

class TablePartitioningRoomsAndFilials extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('filial_group_rooms');
        $table->addColumn('name', 'text', ['null' => true])
            ->addColumn('worker_id', 'biginteger', ['null' => true])
            ->addColumn('filial_id', 'biginteger', ['null' => true])
            ->addColumn('parent_id', 'biginteger', ['null' => true])
            ->addColumn('department_id', 'biginteger', ['null' => true])
            ->addColumn('delete', 'boolean', ['default' => 'false'])
            ->addColumn('step_in', 'integer', ['null' => true])
            ->addColumn('step_out', 'integer', ['null' => true])
            ->addColumn('date_create', 'date', ['null' => true])
            ->addColumn('date_delete', 'date', ['null' => true])
            ->addColumn('old', 'biginteger', ['null' => true])
            ->save();

        $groupRooms = $this->fetchAll('SELECT * FROM filial_rooms WHERE room IS FALSE');

        foreach ($groupRooms as $groupRoom) {
            $row = [];
            foreach ($groupRoom as $key => $item) {
                if(!is_numeric($key) && $key !== 'room' && $key !== 'number') {
                    $row[$key] = $item;
                }
                if($key === 'delete') {
                    if ($item) {
                        $row['delete'] = 1;
                    } else {
                        $row['delete'] = 0;
                    }
                }
                $row['old'] = $groupRoom['id'];
                unset($row['id']);

            }
            $this->insert('filial_group_rooms', [$row]);
            $lastId = $this->getAdapter()->getConnection()->lastInsertId();

            $this->query('UPDATE topology SET element_id = '.$lastId.', type = \'group_rooms\' WHERE element_id = '.$groupRoom['id'].' AND type = \'floor\';');

            $this->execute('UPDATE filial_rooms SET parent_id = '.$lastId.' WHERE parent_id = '.$groupRoom['id'].';');

            $this->execute('DELETE FROM filial_rooms WHERE id = '.$groupRoom['id'].';');

        }

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $groupRooms = $this->fetchAll('SELECT * FROM filial_group_rooms');

        foreach ($groupRooms as $groupRoom) {
            $row = [];
            foreach ($groupRoom as $key => $item) {
                if(!is_numeric($key)) {
                    $row[$key] = $item;
                }
                if($key === 'delete') {
                    if ($item) {
                        $row['delete'] = 1;
                    } else {
                        $row['delete'] = 0;
                    }
                }
                $row['number'] = -1;
                $row['id'] = $groupRoom['old'];
                $row['room'] = 0;
                unset($row['old']);
            }
            $this->insert('filial_rooms', [$row]);

            $this->query('UPDATE topology SET element_id = '.$row['id'].', type = \'floor\' WHERE element_id = '.$groupRoom['id'].' AND type = \'group_rooms\';');

            $this->execute('UPDATE filial_rooms SET parent_id = '.$row['id'].' WHERE parent_id = '.$groupRoom['id'].';');


        }

        $this->dropTable('filial_group_rooms');
    }
}
