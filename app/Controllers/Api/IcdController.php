<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\Icd10tmModel;

class IcdController extends BaseController
{
    public function suggest()
    {
        $q = trim((string) $this->request->getGet('q'));
        $limit = (int) ($this->request->getGet('limit') ?? 8);

        if ($q === '') {
            return $this->response->setJSON(['items' => []]);
        }

        $items = (new Icd10tmModel())->suggestByDiagnosis($q, $limit);

        return $this->response->setJSON(['items' => $items]);
    }
}

