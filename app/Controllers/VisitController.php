<?php

namespace App\Controllers;

use App\Libraries\AuditLogger;
use App\Models\Icd10tmModel;
use App\Models\PatientModel;
use App\Models\VisitModel;

class VisitController extends BaseController
{
    public function index()
    {
        $model = new VisitModel();
        $visits = $model
            ->select('visits.*, patients.hn, patients.first_name, patients.last_name')
            ->join('patients', 'patients.id = visits.patient_id')
            ->orderBy('visits.visit_date', 'DESC')
            ->findAll(100);

        return view('visits/index', ['visits' => $visits]);
    }

    public function new(int $patientId)
    {
        $patient = (new PatientModel())->find($patientId);
        if (! $patient) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('visits/form', [
            'mode' => 'create',
            'visit' => null,
            'patient' => $patient,
        ]);
    }

    public function create(int $patientId)
    {
        $patient = (new PatientModel())->find($patientId);
        if (! $patient) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $model = new VisitModel();
        $data = $this->extractVisitData();
        $data['diseasecode'] = $this->resolveDiseaseCode($data['diagnosis'], (string) $this->request->getPost('diseasecode'));
        $data['patient_id'] = $patientId;
        $data['created_by'] = session()->get('user_id');
        $data['updated_by'] = session()->get('user_id');

        $model->insert($data);
        $id = (int) $model->getInsertID();

        AuditLogger::log('create', 'visit', $id, null, $data);

        return redirect()->to('/visits/timeline/' . $patientId)->with('success', 'บันทึกการตรวจเรียบร้อย');
    }

    public function edit(int $id)
    {
        $model = new VisitModel();
        $visit = $model->find($id);

        if (! $visit) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $patient = (new PatientModel())->find((int) $visit['patient_id']);

        return view('visits/form', [
            'mode' => 'edit',
            'visit' => $visit,
            'patient' => $patient,
        ]);
    }

    public function update(int $id)
    {
        $model = new VisitModel();
        $old = $model->find($id);

        if (! $old) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = $this->extractVisitData();
        $data['diseasecode'] = $this->resolveDiseaseCode($data['diagnosis'], (string) $this->request->getPost('diseasecode'));
        $data['updated_by'] = session()->get('user_id');

        $model->update($id, $data);
        AuditLogger::log('update', 'visit', $id, $old, $data);

        return redirect()->to('/visits/timeline/' . $old['patient_id'])->with('success', 'อัปเดตการตรวจเรียบร้อย');
    }

    public function timeline(int $patientId)
    {
        $patient = (new PatientModel())->find($patientId);
        if (! $patient) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $visits = (new VisitModel())->byPatient($patientId);

        return view('visits/timeline', [
            'patient' => $patient,
            'visits' => $visits,
        ]);
    }

    private function extractVisitData(): array
    {
        $visitDate = (string) ($this->request->getPost('visit_date') ?: date('Y-m-d H:i:s'));
        $visitDate = str_replace('T', ' ', $visitDate);

        return [
            'visit_date' => $visitDate,
            'chief_complaint' => trim((string) $this->request->getPost('chief_complaint')),
            'vital_signs' => trim((string) $this->request->getPost('vital_signs')),
            'diagnosis' => trim((string) $this->request->getPost('diagnosis')),
            'treatment' => trim((string) $this->request->getPost('treatment')),
            'medication' => trim((string) $this->request->getPost('medication')),
            'doctor_note' => trim((string) $this->request->getPost('doctor_note')),
        ];
    }

    private function resolveDiseaseCode(string $diagnosis, string $selectedCode = ''): ?string
    {
        $selectedCode = strtoupper(trim($selectedCode));
        $icdModel = new Icd10tmModel();

        if ($selectedCode !== '') {
            $row = $icdModel->where('diseasecode', $selectedCode)->first();
            if ($row) {
                return $selectedCode;
            }
        }

        return $icdModel->findBestDiseaseCode($diagnosis);
    }
}
