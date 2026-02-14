<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class CardController extends BaseController
{
    public function read()
    {
        $serviceUrl = rtrim((string) env('cardReader.serviceUrl', 'http://127.0.0.1:8787'), '/');

        try {
            $client = \Config\Services::curlrequest([
                'timeout' => 4,
                'http_errors' => false,
            ]);

            $response = $client->get($serviceUrl . '/read-card');
            $status = $response->getStatusCode();
            $json = json_decode((string) $response->getBody(), true);

            if ($status !== 200 || ! is_array($json)) {
                return $this->response->setStatusCode(502)->setJSON([
                    'ok' => false,
                    'message' => 'ไม่สามารถอ่านข้อมูลจาก Card Reader Service',
                ]);
            }

            return $this->response->setJSON([
                'ok' => true,
                'data' => $json,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[CardReader] ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'ok' => false,
                'message' => 'Card Reader Service ไม่พร้อมใช้งาน',
            ]);
        }
    }
}
