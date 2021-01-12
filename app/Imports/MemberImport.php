<?php

namespace App\Imports;

use App\Classes\MasterClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MemberImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $master = new MasterClass();
        $data = [];
        $i = 0;
        foreach ($collection as $key => $value) {
            $region = $master->regionGet(['code', $value['wilayah']]);
            if ($value['kode'] || !$region) {
                $data[$i]['created_by'] = auth()->user()->id;
                $data[$i]['created_at'] = date('Y-m-d H:i:s');
                $data[$i]['code'] = $value['kode'];
                $data[$i]['name'] = $value['nama'];
                $keanggotaan = strtolower($value['keanggotaan']);
                switch ($keanggotaan) {
                    case 'aktif':
                        $data[$i]['status'] = 1;
                        break;
                    case 'keluar':
                        $data[$i]['status'] = 2;
                        break;
                    default:
                        $data[$i]['status'] = 0;
                        break;
                }
                $data[$i]['place_of_birth'] = $value['tempat_lahir'];
                $data[$i]['date_of_birth'] = $value['tanggal_lahir'] != null ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['tanggal_lahir'])->format('Y-m-d') : null;
                $gender = strtolower($value['jenis_kelamin']);
                $data[$i]['gender'] = $gender == 'l' ? 1 : 0;
                $data[$i]['religion'] = $value['agama'];
                $data[$i]['education'] = $value['pendidikan'];
                $data[$i]['address'] = $value['alamat'];

                $data[$i]['region_id'] = $region->id ?? 0;
                $i++;
            }
        }
        DB::table('member_uploads')->insert($data);
    }
}