<?php

namespace App\Controllers;

use App\Models\PatientModel;
use App\Models\VisitModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $patientModel = new PatientModel();
        $visitModel = new VisitModel();

        $today = date('Y-m-d');

        $data = [
            'totalPatients' => $patientModel->countAllResults(),
            'todayVisits' => $visitModel->where('DATE(visit_date)', $today)->countAllResults(),
            'recentVisits' => $visitModel
                ->select('visits.*, patients.hn, patients.first_name, patients.last_name, patients.photo')
                ->join('patients', 'patients.id = visits.patient_id')
                ->orderBy('visits.visit_date', 'DESC')
                ->findAll(10),
        ];

        return view('dashboard/index', $data);
    }
}
