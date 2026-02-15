<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhotoToPatients extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('photo', 'patients')) {
            $this->forge->addColumn('patients', [
                'photo' => [
                    'type' => 'MEDIUMTEXT',
                    'null' => true,
                    'after' => 'address',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('photo', 'patients')) {
            $this->forge->dropColumn('patients', 'photo');
        }
    }
}
