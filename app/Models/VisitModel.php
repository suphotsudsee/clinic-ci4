<?php

namespace App\Models;

use CodeIgniter\Model;

class VisitModel extends Model
{
    protected $table = 'visits';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'patient_id',
        'visit_date',
        'chief_complaint',
        'vital_signs',
        'diagnosis',
        'diseasecode',
        'treatment',
        'medication',
        'doctor_note',
        'created_by',
        'updated_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function byPatient(int $patientId): array
    {
        return $this->where('patient_id', $patientId)
            ->orderBy('visit_date', 'DESC')
            ->findAll();
    }
}
