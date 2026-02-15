<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'cid',
        'hn',
        'title_name',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'phone',
        'address',
        'photo',
        'allergy_note',
        'created_by',
        'updated_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function search(string $keyword)
    {
        return $this->groupStart()
            ->like('hn', $keyword)
            ->orLike('cid', $keyword)
            ->orLike('first_name', $keyword)
            ->orLike('last_name', $keyword)
            ->groupEnd()
            ->orderBy('id', 'DESC')
            ->findAll(50);
    }
}
