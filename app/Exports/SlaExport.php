<?php

namespace App\Exports;

use App\Models\DataPerijinan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SlaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $dateFrom;
    protected $dateTo;
    protected $perijinanId;

    public function __construct($dateFrom = null, $dateTo = null, $perijinanId = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->perijinanId = $perijinanId;
    }

    public function collection()
    {
        $query = DataPerijinan::where('status', 'approved')
            ->with(['perijinan', 'user', 'validasiRecords.validationFlow', 'validasiRecords.validator.opd']);

        if ($this->dateFrom) {
            $query->whereDate('approved_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('approved_at', '<=', $this->dateTo);
        }
        if ($this->perijinanId) {
            $query->where('perijinan_id', $this->perijinanId);
        }

        return $query->latest('approved_at')->get();
    }

    public function headings(): array
    {
        return [
            'No. Registrasi',
            'Jenis Perijinan',
            'Pemohon',
            'Tanggal Pengajuan',
            'Tanggal Selesai',
            'Detail SLA (Tahapan | Petugas | Durasi)',
            'Total SLA (Detik)',
            'Total SLA (Format)'
        ];
    }

    /**
     * @param DataPerijinan $application
     */
    public function map($application): array
    {
        $slaDetails = [];
        $totalSeconds = 0;

        foreach ($application->validasiRecords as $v) {
            $duration = $v->duration_seconds ?? 0;
            $totalSeconds += $duration;
            
            $assignedOpd = $v->validationFlow->assignedUser->opd ?? null;
            $actualOpd = $v->validator->opd ?? null;
            $opdName = ($actualOpd ?? $assignedOpd)->nama_opd ?? 'N/A';
            
            $roleLabel = $v->validationFlow->role_label ?? 'Tahapan';
            $userName = $v->validator->name ?? ($v->validationFlow->assignedUser->name ?? '-');
            
            $slaDetails[] = "{$roleLabel} ({$opdName}) | {$userName} | " . formatDuration($duration);
        }

        return [
            $application->no_registrasi,
            $application->perijinan->nama_perijinan,
            $application->user->name,
            $application->created_at->format('d/m/Y H:i'),
            $application->approved_at ? $application->approved_at->format('d/m/Y H:i') : '-',
            implode("\n", $slaDetails),
            $totalSeconds,
            formatDuration($totalSeconds)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'F' => ['alignment' => ['wrapText' => true]],
        ];
    }
}
