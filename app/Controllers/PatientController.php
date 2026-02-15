<?php

namespace App\Controllers;

use App\Libraries\AuditLogger;
use App\Models\PatientModel;
use App\Models\VisitModel;

class PatientController extends BaseController
{
    public function index()
    {
        $model = new PatientModel();
        $keyword = trim((string) $this->request->getGet('q'));

        $patients = $keyword !== '' ? $model->search($keyword) : $model->orderBy('id', 'DESC')->findAll(100);

        return view('patients/index', [
            'patients' => $patients,
            'q' => $keyword,
        ]);
    }

    public function search()
    {
        return $this->index();
    }

    public function new()
    {
        return view('patients/form', ['mode' => 'create', 'patient' => null]);
    }

    public function create()
    {
        $model = new PatientModel();
        $data = $this->extractPatientData();
        $data['created_by'] = session()->get('user_id');
        $data['updated_by'] = session()->get('user_id');

        $model->insert($data);
        $id = (int) $model->getInsertID();

        AuditLogger::log('create', 'patient', $id, null, $data);

        return redirect()->to('/patients')->with('success', 'บันทึกผู้ป่วยเรียบร้อย');
    }

    public function edit(int $id)
    {
        $model = new PatientModel();
        $patient = $model->find($id);

        if (! $patient) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('patients/form', ['mode' => 'edit', 'patient' => $patient]);
    }

    public function update(int $id)
    {
        $model = new PatientModel();
        $old = $model->find($id);

        if (! $old) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = $this->extractPatientData();
        $data['updated_by'] = session()->get('user_id');

        $model->update($id, $data);
        AuditLogger::log('update', 'patient', $id, $old, $data);

        return redirect()->to('/patients')->with('success', 'แก้ไขข้อมูลผู้ป่วยเรียบร้อย');
    }

    public function delete(int $id)
    {
        $model = new PatientModel();
        $old = $model->find($id);

        if ($old) {
            $model->delete($id);
            AuditLogger::log('delete', 'patient', $id, $old, null);
        }

        return redirect()->to('/patients')->with('success', 'ลบข้อมูลผู้ป่วยเรียบร้อย');
    }

    public function show(int $id)
    {
        $patientModel = new PatientModel();
        $visitModel = new VisitModel();

        $patient = $patientModel->find($id);
        if (! $patient) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $visits = $visitModel->byPatient($id);

        return view('patients/show', [
            'patient' => $patient,
            'visits' => $visits,
        ]);
    }

    public function cardForm()
    {
        return view('patients/card');
    }

    public function importFromCard()
    {
        $model = new PatientModel();

        $data = [
            'cid' => trim((string) $this->request->getPost('cid')),
            'title_name' => trim((string) $this->request->getPost('title_name')),
            'first_name' => trim((string) $this->request->getPost('first_name')),
            'last_name' => trim((string) $this->request->getPost('last_name')),
            'gender' => trim((string) $this->request->getPost('gender')),
            'dob' => $this->request->getPost('dob') ?: null,
            'address' => trim((string) $this->request->getPost('address')),
            'photo' => trim((string) $this->request->getPost('photo')),
            'phone' => trim((string) $this->request->getPost('phone')),
            'allergy_note' => trim((string) $this->request->getPost('allergy_note')),
        ];

        $exists = $model->where('cid', $data['cid'])->first();
        if ($exists) {
            if (($data['photo'] ?? '') !== '') {
                $model->update((int) $exists['id'], [
                    'photo' => $data['photo'],
                    'updated_by' => session()->get('user_id'),
                ]);
            }

            return redirect()->to('/patients/edit/' . $exists['id'])
                ->with('error', 'พบข้อมูลเลขบัตรนี้แล้ว ระบบพาไปหน้าแก้ไข');
        }

        $data['hn'] = 'HN' . date('ymd') . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        $data['created_by'] = session()->get('user_id');
        $data['updated_by'] = session()->get('user_id');

        $model->insert($data);
        $id = (int) $model->getInsertID();

        AuditLogger::log('import_card', 'patient', $id, null, $data);

        return redirect()->to('/patients/show/' . $id)->with('success', 'นำเข้าข้อมูลจากบัตรสำเร็จ');
    }

    private function extractPatientData(): array
    {
        return [
            'cid' => trim((string) $this->request->getPost('cid')),
            'hn' => trim((string) $this->request->getPost('hn')),
            'title_name' => trim((string) $this->request->getPost('title_name')),
            'first_name' => trim((string) $this->request->getPost('first_name')),
            'last_name' => trim((string) $this->request->getPost('last_name')),
            'gender' => trim((string) $this->request->getPost('gender')),
            'dob' => $this->request->getPost('dob') ?: null,
            'phone' => trim((string) $this->request->getPost('phone')),
            'address' => trim((string) $this->request->getPost('address')),
            'photo' => trim((string) $this->request->getPost('photo')),
            'allergy_note' => trim((string) $this->request->getPost('allergy_note')),
        ];
    }
}
