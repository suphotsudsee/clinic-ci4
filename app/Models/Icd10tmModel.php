<?php

namespace App\Models;

use CodeIgniter\Model;

class Icd10tmModel extends Model
{
    /**
     * Common synonym mapping used by diagnosis text in Thai/English.
     *
     * @var array<string, string>
     */
    private array $synonyms = [
        'อุจจาระร่วง' => 'ท้องร่วง',
        'ถ่ายเหลว' => 'ท้องร่วง',
        'ท้องเสีย' => 'ท้องร่วง',
        'diarrhea' => 'diarrhoea',
    ];

    protected $table = 'icd10tm';
    protected $primaryKey = 'diseasecode';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = [
        'diseasecode',
        'mapdisease',
        'diseasename',
        'diseasenamethai',
        'code504',
        'code506',
        'codechronic',
        'validscore',
    ];

    public function findBestDiseaseCode(string $diagnosis): ?string
    {
        $diagnosis = trim(preg_replace('/\s+/u', ' ', $diagnosis) ?? '');
        if ($diagnosis === '') {
            return null;
        }

        // Allow users to type ICD code directly in diagnosis text.
        if (preg_match('/\b([A-TV-Z][0-9]{2}(?:\.[0-9A-Z]{1,2})?)\b/i', $diagnosis, $m) === 1) {
            $code = strtoupper($m[1]);
            $exists = $this->where('diseasecode', $code)->first();
            if ($exists) {
                return $code;
            }
        }

        foreach ($this->expandSearchTerms($diagnosis) as $term) {
            $row = $this->queryBestRow($term);
            if ($row && ! empty($row['diseasecode'])) {
                return $row['diseasecode'];
            }
        }

        return null;
    }

    public function suggestByDiagnosis(string $diagnosis, int $limit = 8): array
    {
        $diagnosis = trim(preg_replace('/\s+/u', ' ', $diagnosis) ?? '');
        if ($diagnosis === '') {
            return [];
        }

        $limit = max(1, min(20, $limit));
        $merged = [];

        foreach ($this->expandSearchTerms($diagnosis) as $term) {
            $rows = $this->querySuggestRows($term, $limit);
            foreach ($rows as $row) {
                $code = strtoupper((string) ($row['diseasecode'] ?? ''));
                if ($code === '') {
                    continue;
                }

                if (! isset($merged[$code]) || (int) ($row['score'] ?? 0) > (int) ($merged[$code]['score'] ?? 0)) {
                    $merged[$code] = $row;
                }
            }
        }

        $items = array_values($merged);
        usort($items, static function (array $a, array $b): int {
            $scoreCmp = ((int) ($b['score'] ?? 0)) <=> ((int) ($a['score'] ?? 0));
            if ($scoreCmp !== 0) {
                return $scoreCmp;
            }

            $aLen = (int) ($a['name_len'] ?? 0);
            $bLen = (int) ($b['name_len'] ?? 0);

            return $aLen <=> $bLen;
        });

        return array_slice($items, 0, $limit);
    }

    /**
     * @return list<string>
     */
    private function expandSearchTerms(string $diagnosis): array
    {
        $terms = [$diagnosis];

        foreach ($this->synonyms as $from => $to) {
            if (mb_stripos($diagnosis, $from) !== false) {
                $terms[] = str_ireplace($from, $to, $diagnosis);
            }
        }

        return array_values(array_unique(array_filter(array_map(
            static fn (string $t): string => trim(preg_replace('/\s+/u', ' ', $t) ?? ''),
            $terms
        ))));
    }

    private function queryBestRow(string $diagnosis): ?array
    {
        $sql = <<<'SQL'
SELECT
    diseasecode,
    CASE
        WHEN UPPER(?) = UPPER(diseasecode) OR UPPER(?) = UPPER(mapdisease) THEN 220
        WHEN LOWER(?) = LOWER(diseasenamethai) OR LOWER(?) = LOWER(diseasename) THEN 200
        WHEN ? LIKE CONCAT('%', diseasenamethai, '%') THEN 160
        WHEN ? LIKE CONCAT('%', diseasename, '%') THEN 140
        WHEN diseasenamethai LIKE CONCAT('%', ?, '%') THEN 120
        WHEN diseasename LIKE CONCAT('%', ?, '%') THEN 100
        ELSE 0
    END AS score,
    GREATEST(CHAR_LENGTH(COALESCE(diseasenamethai, '')), CHAR_LENGTH(COALESCE(diseasename, ''))) AS name_len
FROM icd10tm
HAVING score > 0
ORDER BY
    score DESC,
    CASE WHEN score IN (120, 100) THEN name_len ELSE -name_len END ASC
LIMIT 1
SQL;

        $row = $this->db->query($sql, [
            $diagnosis,
            $diagnosis,
            $diagnosis,
            $diagnosis,
            $diagnosis,
            $diagnosis,
            $diagnosis,
            $diagnosis,
        ])->getRowArray();

        return $row ?: null;
    }

    private function querySuggestRows(string $diagnosis, int $limit = 8): array
    {
        $sql = <<<'SQL'
SELECT
    diseasecode,
    mapdisease,
    diseasename,
    diseasenamethai,
    CASE
        WHEN UPPER(?) = UPPER(diseasecode) OR UPPER(?) = UPPER(mapdisease) THEN 220
        WHEN LOWER(?) = LOWER(diseasenamethai) OR LOWER(?) = LOWER(diseasename) THEN 200
        WHEN ? LIKE CONCAT('%', diseasenamethai, '%') THEN 160
        WHEN ? LIKE CONCAT('%', diseasename, '%') THEN 140
        WHEN diseasenamethai LIKE CONCAT('%', ?, '%') THEN 120
        WHEN diseasename LIKE CONCAT('%', ?, '%') THEN 100
        WHEN UPPER(diseasecode) LIKE CONCAT(UPPER(?), '%') OR UPPER(mapdisease) LIKE CONCAT(UPPER(?), '%') THEN 90
        ELSE 0
    END AS score,
    GREATEST(CHAR_LENGTH(COALESCE(diseasenamethai, '')), CHAR_LENGTH(COALESCE(diseasename, ''))) AS name_len
FROM icd10tm
HAVING score > 0
ORDER BY
    score DESC,
    CASE WHEN score IN (120, 100) THEN name_len ELSE -name_len END ASC
LIMIT __LIMIT__
SQL;

        $sql = str_replace('__LIMIT__', (string) $limit, $sql);

        return $this->db->query($sql, [
            $diagnosis,
            $diagnosis,
            $diagnosis,
            $diagnosis,
            $diagnosis,
            $diagnosis,
            $diagnosis,
            $diagnosis,
            $diagnosis,
            $diagnosis,
        ])->getResultArray();
    }
}
