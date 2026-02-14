<?php

namespace App\Controllers;

use App\Models\VisitModel;

class ReportController extends BaseController
{
    public function daily()
    {
        $start = $this->request->getGet('start') ?: date('Y-m-d');
        $end = $this->request->getGet('end') ?: date('Y-m-d');

        $rows = $this->queryVisits($start, $end);

        return view('reports/daily', [
            'rows' => $rows,
            'start' => $start,
            'end' => $end,
        ]);
    }

    public function exportExcel()
    {
        $start = $this->request->getGet('start') ?: date('Y-m-d');
        $end = $this->request->getGet('end') ?: date('Y-m-d');
        $rows = $this->queryVisits($start, $end);

        if (! class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            return redirect()->back()->with('error', 'เธขเธฑเธเนเธกเนเธ•เธดเธ”เธ•เธฑเนเธ PhpSpreadsheet');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['เธงเธฑเธเธ—เธตเน', 'HN', 'เน€เธฅเธเธเธฑเธ•เธฃ', 'เธเธทเนเธญ-เธชเธเธธเธฅ', 'เธญเธฒเธเธฒเธฃเธชเธณเธเธฑเธ', 'เธงเธดเธเธดเธเธเธฑเธข'], null, 'A1');

        $rowNum = 2;
        foreach ($rows as $row) {
            $sheet->fromArray([
                $row['visit_date'],
                $row['hn'],
                $row['cid'],
                trim($row['first_name'] . ' ' . $row['last_name']),
                $row['chief_complaint'],
                $row['diagnosis'],
            ], null, 'A' . $rowNum++);
        }

        $filename = 'clinic_report_' . $start . '_to_' . $end . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

        public function exportPdf()
    {
        $start = $this->request->getGet('start') ?: date('Y-m-d');
        $end = $this->request->getGet('end') ?: date('Y-m-d');
        $rows = $this->queryVisits($start, $end);
        $html = view('reports/pdf', ['rows' => $rows, 'start' => $start, 'end' => $end]);
        $filename = 'clinic_report_' . $start . '_to_' . $end . '.pdf';

        if (class_exists('\\Mpdf\\Mpdf')) {
            $tempDir = WRITEPATH . 'cache';
            if (! is_dir($tempDir)) {
                @mkdir($tempDir, 0775, true);
            }

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'tempDir' => $tempDir,
                'default_font' => 'garuda',
            ]);
            $mpdf->WriteHTML($html);

            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($mpdf->Output($filename, 'S'));
        }

        if (! class_exists('Dompdf\\Dompdf')) {
            return redirect()->back()->with('error', 'PDF engine not installed');
        }

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'NotoSansThai');
        $options->set('chroot', ROOTPATH);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename, ['Attachment' => 1]);
        exit;
    }
    private function queryVisits(string $start, string $end): array
    {
        return (new VisitModel())
            ->select('visits.*, patients.hn, patients.cid, patients.first_name, patients.last_name')
            ->join('patients', 'patients.id = visits.patient_id')
            ->where('DATE(visits.visit_date) >=', $start)
            ->where('DATE(visits.visit_date) <=', $end)
            ->orderBy('visits.visit_date', 'ASC')
            ->findAll();
    }
}
