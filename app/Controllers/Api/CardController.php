<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class CardController extends BaseController
{
    public function read()
    {
        $serviceUrl = rtrim((string) env('cardReader.serviceUrl', 'http://127.0.0.1:8888'), '/');
        $endpoints = ['/read', '/read-card'];

        try {
            $client = \Config\Services::curlrequest([
                'timeout' => 4,
                'http_errors' => false,
            ]);

            $json = null;
            foreach ($endpoints as $endpoint) {
                $response = $client->get($serviceUrl . $endpoint);
                $status = $response->getStatusCode();
                $payload = json_decode((string) $response->getBody(), true);

                if ($status === 200 && is_array($payload)) {
                    $json = $payload;
                    break;
                }
            }

            if (! is_array($json)) {
                return $this->response->setStatusCode(502)->setJSON([
                    'ok' => false,
                    'message' => 'ไม่สามารถอ่านข้อมูลจาก Card Reader Service',
                ]);
            }

            if (($json['success'] ?? null) === false) {
                return $this->response->setStatusCode(502)->setJSON([
                    'ok' => false,
                    'message' => (string) ($json['message'] ?? 'Card Reader Service returned unsuccessful response'),
                ]);
            }

            $normalized = $this->normalizeCardData($json);
            if (($normalized['cid'] ?? '') === '' && ($normalized['first_name'] ?? '') === '' && ($normalized['last_name'] ?? '') === '') {
                return $this->response->setStatusCode(422)->setJSON([
                    'ok' => false,
                    'message' => 'อ่านบัตรแล้ว แต่ไม่พบข้อมูลที่ใช้งานได้',
                ]);
            }

            return $this->response->setJSON([
                'ok' => true,
                'data' => $normalized,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[CardReader] ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'ok' => false,
                'message' => 'Card Reader Service ไม่พร้อมใช้งาน',
            ]);
        }
    }

    private function normalizeCardData(array $payload): array
    {
        $card = $payload;
        if (($payload['success'] ?? false) === true && isset($payload['data']) && is_array($payload['data'])) {
            $card = $payload['data'];
        }

        $nameParts = $this->parseThaiName((string) ($card['name_th'] ?? ''));

        return [
            'cid' => trim((string) ($card['cid'] ?? '')),
            'title_name' => (string) ($card['title_name'] ?? $nameParts['title_name']),
            'first_name' => (string) ($card['first_name'] ?? $nameParts['first_name']),
            'last_name' => (string) ($card['last_name'] ?? $nameParts['last_name']),
            'gender' => $this->normalizeGender((string) ($card['gender'] ?? '')),
            'dob' => $this->normalizeDob((string) ($card['dob'] ?? '')),
            'address' => trim((string) ($card['address'] ?? '')),
            'photo' => (string) ($card['photo'] ?? ''),
        ];
    }

    private function parseThaiName(string $name): array
    {
        $clean = trim(preg_replace('/\s+/u', ' ', $name) ?? '');
        if ($clean === '') {
            return ['title_name' => '', 'first_name' => '', 'last_name' => ''];
        }

        $parts = preg_split('/\s+/u', $clean) ?: [];
        $titles = ['นาย', 'นาง', 'นางสาว', 'ด.ช.', 'ด.ญ.', 'เด็กชาย', 'เด็กหญิง', 'คุณ'];
        $title = '';

        if (! empty($parts) && in_array($parts[0], $titles, true)) {
            $title = array_shift($parts) ?? '';
        }

        $first = $parts[0] ?? '';
        $last = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

        return [
            'title_name' => $title,
            'first_name' => $first,
            'last_name' => $last,
        ];
    }

    private function normalizeGender(string $gender): string
    {
        $value = strtoupper(trim($gender));
        if ($value === 'M' || $value === 'MALE' || $value === 'ชาย') {
            return 'ชาย';
        }
        if ($value === 'F' || $value === 'FEMALE' || $value === 'หญิง') {
            return 'หญิง';
        }

        return trim($gender);
    }

    private function normalizeDob(string $dob): string
    {
        $value = trim($dob);
        if ($value === '') {
            return '';
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            return $value;
        }

        if (preg_match('/^(\d{4})(\d{2})(\d{2})$/', $value, $matches) !== 1) {
            return '';
        }

        $year = (int) $matches[1];
        $month = (int) $matches[2];
        $day = (int) $matches[3];
        if ($year > 2400) {
            $year -= 543;
        }

        if (! checkdate($month, $day, $year)) {
            return '';
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
