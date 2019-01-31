<?php


use Phinx\Migration\AbstractMigration;

class AddTopologyTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('topology');
        $table->addColumn('parent_id', 'integer')
            ->addColumn('element_id', 'integer')
            ->addColumn('type', 'string')
            ->save();
        $floors = $this->fetchAll('SELECT filial_rooms.* FROM filial_rooms WHERE room IS FALSE AND delete IS FALSE ORDER BY id ASC');
        foreach ($floors as $floor) {
            $topology = $this->fetchRow('SELECT * FROM topology WHERE element_id = '.$floor['parent_id']);
            $singleRow = ['element_id' => $floor['id'], 'parent_id' => $topology['id'] ? $topology['id']: 0, 'type' => 'floor'];
            $this->insert('topology', [$singleRow]);
        }

        $deps = $this->fetchAll('SELECT filial_departament.*
                                        FROM filial_departament 
                                        WHERE filial_departament.group IS FALSE AND delete IS FALSE ORDER BY id ASC');
        foreach ($deps as $dep) {
            $data = [];
            $row = $this->fetchRow('SELECT filial_departament.*, topology.id AS t_id
                                    FROM filial_departament 
                                    JOIN filial_departament_floor ON filial_departament_floor.departament_id = filial_departament.id 
                                    JOIN topology ON topology.type = \'floor\' AND filial_departament_floor.floor_id = topology.element_id
                                    WHERE filial_departament.id='.$dep['id'].' AND delete IS FALSE ORDER BY id ASC');
            if(!$row) {
                continue;
            }
            $data[] = ['element_id' => $row['id'], 'parent_id' => (!empty($row['t_id']) ? $row['t_id'] : 0), 'type' => 'departament'];
            $this->insert('topology', $data);
        }

        $deps = $this->fetchAll('SELECT filial_departament.*
                                    FROM filial_departament 
                                    WHERE filial_departament.group IS TRUE AND delete IS FALSE ORDER BY id ASC');
        foreach ($deps as $dep) {
            $data = [];
            $row = $this->fetchRow('SELECT filial_departament.*, topology.id AS t_id FROM filial_departament JOIN topology ON (topology.type = \'departament\' AND filial_departament.parent_id = topology.element_id) WHERE filial_departament.id = ' . $dep['id'] . ' AND delete IS FALSE ORDER BY id ASC');
            if(!$row) {
                continue;
            }
            $data[] = ['element_id' => $row['id'], 'parent_id' => ($row['t_id'] ? $row['t_id'] : 0), 'type' => 'departament'];
            $this->insert('topology', $data);
        }

        $rooms = $this->fetchAll('SELECT filial_rooms.*, topology.id AS t_id FROM filial_rooms JOIN topology ON (topology.type = \'departament\' AND topology.element_id = filial_rooms.department_id) WHERE room IS TRUE AND delete IS FALSE ORDER BY id ASC');
        $data = [];
        foreach ($rooms as $room) {
            $data[] = ['element_id' => $room['id'], 'parent_id' => $room['t_id'], 'type' => 'room'];
        }
        $this->insert('topology', $data);
        $workers = $this->fetchAll('SELECT workers.*, users.surname, users.first_name, users.patronymic, topology.id AS t_id
                            FROM workers
                            LEFT JOIN users ON users.id = workers.user_id
                            JOIN topology ON topology.type = \'room\' AND topology.element_id = workers.room_id');

        foreach ($workers as $worker) {
            $data = [];
            $data[] = ['element_id' => $worker['id'], 'parent_id' => $worker['t_id'], 'type' => 'worker'];
            $this->insert('topology', $data);

            $hearings = $this->fetchAll("SELECT filial_rooms_hearing.*, topology.id AS t_id FROM filial_rooms_hearing JOIN topology ON topology.type='worker' AND topology.element_id = filial_rooms_hearing.worker_id WHERE room_id = ".$worker['room_id']." AND worker_id = ".$worker['id']);
            foreach ($hearings as $hearing) {
                $data = [];
                $data[] = ['element_id' => $hearing['id'], 'parent_id' => $hearing['t_id'], 'type' => 'hearing'];
                $this->insert('topology', $data);
            }
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('topology');
    }
}
