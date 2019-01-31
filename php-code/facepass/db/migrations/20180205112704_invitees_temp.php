<?php


use Phinx\Migration\AbstractMigration;

class InviteesTemp extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change()
    {
        $inv = $this->table('invitees_guests');
        $inv->addColumn('first_name', 'string')
            ->addColumn('patronymic', 'string')
            ->addColumn('surname', 'string')
            ->addColumn('phone', 'string')
            ->addColumn('hearing_id', 'integer')
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('status', 'boolean', ['default' => false])
            ->addColumn('created', 'datetime');
        $inv->create();
    }
}
