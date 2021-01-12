<?php

namespace App\Exports;

use App\Model\Member;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class MemberExport implements FromQuery, WithMapping, WithHeadings, WithEvents, ShouldAutoSize
{
    use Exportable;
    public function __construct($data)
    {
        // dd($data);
        //declare var based on parameter
        $this->q = $data['q'];
        $this->status = $data['status'];
        $this->region_id = $data['region_id'];
        $this->row = 0;
        $this->no = 0;
    }
    public function query()
    {
        //filter data
        $filter = [
            'q' => $this->q,
            'status' => $this->status,
            'region_id' => $this->region_id,
        ];
        //start query
        $query = Member::query()->with(['village', 'district', 'regency', 'province', 'region', 'user']);
        // search by keyword
        if (!empty($filter['q'])) {
            $query->where(function ($q) use ($filter) {
                $q->where("code", "like", "%{filter['q']}%")
                    ->orWhere("name", "like", "%{$filter['q']}%")
                    ->orWhere("nik", "like", "%{$filter['q']}%")
                    ->orWhere("email", "like", "%{$filter['q']}%")
                    ->orWhere("phone", "like", "%{$filter['q']}%")
                    ->orWhere("profession", "like", "%{$filter['q']}%")
                    ->orWhere("place_of_birth", "like", "%{$filter['q']}%")
                    ->orWhere("address", "like", "%{$filter['q']}%");
            });
        }
        // serach by status
        if (isset($filter['status']) && $filter['status'] != 'all') {
            $query->where('status', $filter['status']);
        }
        // search by tipe anggota
        if (isset($filter['region_id']) && $filter['region_id'] != 'all') {
            $query->where('region_id', $filter['region_id']);
        }
        // count total row
        $this->row = $query->count();
        return $query;
    }
    public function map($anggota): array
    {
        return [
            ++$this->no,
            $anggota->code,
            $anggota->name,
            $anggota->status == 0 ? 'Non Anggota' : ($anggota->status == 1 ? 'Anggota Aktif' : 'Anggota Keluar'),
            $anggota->gender == 1 ? 'Laki-Laki' : 'Perempuan',
            $anggota->place_of_birth,
            $anggota->date_of_birth,
            $anggota->religion,
            $anggota->education,
            $anggota->address,
            $anggota->village->name . ', ' . $anggota->district->name . ', ' . $anggota->regency->name . ', ' . $anggota->province->name,
            $anggota->region->name,
            $anggota->craftman,
            $anggota->soybean_ration . ' Kg',
            $anggota->raw_material,
            $anggota->adjuvant,
            $anggota->extra_material,
            $anggota->production_result,
            'Rp' . number_format($anggota->income),
            $anggota->marketing,
            $anggota->capital,
            $anggota->experience,
            $anggota->domicile,
            $anggota->place_of_business,
            $anggota->production_tool,
            $anggota->criteria,
            $anggota->ho_letter == 1 ? 'Ada' : 'Tidak Ada',
            $anggota->license == 1 ? 'Ada' : 'Tidak Ada',
            $anggota->imb_letter == 1 ? 'Ada' : 'Tidak Ada',
            $anggota->pbb_letter == 1 ? 'Ada' : 'Tidak Ada',
            $anggota->extinguisher == 1 ? 'Ada' : 'Tidak Ada',
            $anggota->join_date,
            $anggota->out_date,
            $anggota->dependent,
            $anggota->total_dependent,
            $anggota->total_children,
            $anggota->phone
        ];
    }
    public function headings(): array
    {
        return [
            '#', //A
            'Kode Anggota', //B
            'Nama Anggota', //C
            'Keanggotaan', //D
            'Jenis Kelamin', //E
            'Tempat Lahir', //F
            'Tanggal Lahir', //G
            'Agama', //H
            'Pendidikan', //I
            'Alamat', //J
            'Desa/Kelurahan', //K
            'Wilayah', //L
            'Pengrajin', //M,
            'Jatah Kedelai', //N
            'Bahan Baku', //O
            'Bahan Pembantu', //P
            'Bahan Tambahan', //Q
            'Hasil Produksi', //R
            'Pendapatan', //S
            'Pemasaran', //T
            'Permodalan', //U
            'Pengalaman', //V
            'Domisili', //W
            'Tempat Usaha', //X
            'Alat Produksi', //Y
            'Kriteria', //Z
            'Surat HO', //AA
            'Surat Izin', //AB
            'Surat IMB', //AC
            'Surat PBB', //AD
            'Alat Pemadam', //AE
            'Tanggal Bergabung', //AF
            'Tanggal Keluar', //AG
            'Tanggungan', //AH
            'Jumlah Tanggungan', //AI
            'Jumlah Anak', //AJ
            'Telepon' //AK
        ];
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        return [
            AfterSheet::class => function (AfterSheet $event) use ($style) {
                //give borders
                $i = $this->row + 1;
                $event->sheet->getStyle('A2')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A2'), 'A1:AK' . $i);
                //give style for heading
                $event->sheet->getStyle('A1:AK1')->applyFromArray($style['head']);
            }
        ];
    }
}