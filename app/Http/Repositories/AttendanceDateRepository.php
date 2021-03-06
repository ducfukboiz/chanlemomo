<?php
/**
 *File name : AttendanceSessionRepository.php / Date: 10/26/2021 - 9:39 PM

 */

namespace App\Http\Repositories;

use App\Models\AccountMomo;
use App\Models\AttendanceDateSetting;
use App\Models\AttendanceSession;
use App\Models\AttendanceSetting;
use App\Models\LichSuChoiAttendanceDate;
use App\Models\LichSuChoiMomo;
use App\Models\Setting;
use App\Models\UserAttendanceSession;
use App\Models\DoanhThu;
use App\Traits\PhoneNumber;
use Carbon\Carbon;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class AttendanceDateRepository extends Repository
{

    public function __construct()
    {
    }


    public function getMocchoi()
    {
        return AttendanceDateSetting::orderBy('mocchoi')->get()->toArray();
    }

    public function checkTurOnAttendanceDate()
    {
        $attendanceRepo = new AttendanceSessionRepository();
        $setting        = $attendanceRepo->getSettingWebsite();
        if (isset($setting['baotri']) && $setting['baotri'] == 1) {
            return false;
        }
        if (!isset($setting['on_diemdanh_ngay'])) {
            return true;
        }
        return $setting['on_diemdanh_ngay'] == TURN_ON_SETTING;
    }


    public function handleAttendanceDate($data)
    {
        $attendanceDateRepo = new AttendanceDateRepository();
        $phone              = (new PhoneNumber)->convert($data['phone']);
        $phoneOld           = (new PhoneNumber)->convert($data['phone'], true);
        $phonesAccount      = AccountMomo::where('sdt', $phone)->orWhere('sdt', $phoneOld)->get();
        $date               = Carbon::today()->toDateString();
        $lichSuMomosOfPhone = LichSuChoiMomo::where('sdt', $phone)
            ->where('created_at', '>=', $date)
            ->orWhere(function($query) use ($phoneOld, $date) {
                $query->where('sdt', $phoneOld)
                    ->where('created_at', '>=', $date);
            })
            ->get();
        if (count($lichSuMomosOfPhone) == 0 && count($phonesAccount) == 0) {
            return $this->responseResult("Oh !! S??? ??i???n tho???i n??y ch??a ch??i game n??o, h??y ki???m tra l???i");
        }
        $mocchois     = $attendanceDateRepo->getMocchoi();
        $mocchoiFirst = collect($mocchois)->first();
        if (count($mocchois) == 0) {
            return $this->responseResult("H??? th???ng ??ang b???o tr?? vui l??ng th??? l???i sau!");
        }
        $sumTien = $lichSuMomosOfPhone->sum('tiencuoc');
        $lichsuChoi = $this->getLichSuChoiDiemDanhNgay($date, $phone, $phoneOld);
        if (count($lichsuChoi) == 0) {
            if ($sumTien < $mocchoiFirst['mocchoi']) {
                return $this->responseResult("Oh !! . Nay b???n ???? ch??i h???t: ".number_format($sumTien)." VN??. B???n ch??a ????? m???c ti???n ????? nh???n th?????ng trong ng??y h??m nay. C??? g???ng ch??i th??m nh??!!!");
            }
            $mocSumTien = collect($mocchois)->where('mocchoi', "<=", $sumTien)->last();

            if (is_null($mocSumTien)) {
                return $this->responseResult("H??? th???ng ??ang b???o tr?? vui l??ng th??? l???i sau!");
            }
            $tiennhan = $mocSumTien['tiennhan'];
            $this->insertPhoneToTableLichSu($phoneOld, $mocSumTien['mocchoi'], $tiennhan);
        } else {
            $mocDaChoiMax = array_key_last($lichsuChoi);
            $mocSumTien = collect($mocchois)->where('mocchoi', "<=", $sumTien)->last();
//            $mocTiepTheo  = collect($mocchois)->where('mocchoi', ">", $mocDaChoiMax)->first();
            if (is_null($mocSumTien) || $this->mocchoiIsMax(collect($mocchois)->last(), $mocDaChoiMax)) {
                return $this->responseResult("B???n ???? nh???n th?????ng h???t trong ng??y h??m nay. Vui l??ng quay l???i tr?? ch??i v??o ng??y mai!!!");
            }
            if ($mocDaChoiMax)
//            $mocSumTien['mocchoi'] == $mocDaChoiMax
                $mocDatTiepTheo = $mocSumTien['mocchoi'];
            if ($mocDaChoiMax == $mocDatTiepTheo){
                return $this->responseResult("Oh !! . Nay b???n ???? ch??i h???t: ".number_format($sumTien)." VN??. B???n ch??a ????? m???c ti???n ti???p theo ????? nh???n th?????ng th??m trong h??m nay. C??? g???ng Pang th??m nh??!!!");
            }
            if ($sumTien >= $mocDatTiepTheo) {
                $tiennhan = $mocSumTien['tiennhan'];
                $this->insertPhoneToTableLichSu($phoneOld, $mocDatTiepTheo, $tiennhan);
            } else {
                return $this->responseResult("Oh !! . Nay b???n ???? ch??i h???t: ".number_format($sumTien)." VN??. B???n ch??a ????? m???c ti???n ti???p theo ????? nh???n th?????ng th??m trong h??m nay. C??? g???ng Pang th??m nh??!!!");
            }
        }
        return $this->responseResult("Oh!! Ch??c m???ng b???n ???? nh???n ???????c ".number_format($tiennhan)." VN?? ?????P ??T TH??I!!");
    }

    private function getPhoneAccountMomo()
    {
        $cache = Cache::get('cache_get_sdt_account_momo');
        if (!is_null($cache)) {
            return $cache;
        }
        $account = AccountMomo::orderBy('status')->first();
        $phone   = $account->sdt;
        Cache::put('cache_get_sdt_account_momo', $phone, Carbon::now()->addMinutes(10));
        return $phone;
    }


    private function insertPhoneToTableLichSu($phone, $mocchoi, $tienNhan)
    {
        $phoneGet = $this->getPhoneAccountMomo();
        $billCode = 'Nghi???m v??? ng??y '.bin2hex(random_bytes(3)).time();
        $this->insertToLichSuMoMo($phone, $tienNhan, $phoneGet, $billCode);
        $this->insertToLichSuDiemDanhNgay($phone, $mocchoi, $tienNhan, $phoneGet, $billCode);
    }

    /**
     * @param $phone
     * @param $tienNhan
     *
     * @throws \Exception
     */
    private function insertToLichSuMoMo($phone, $tienNhan, $phoneGet, $billCode)
    {
        // update doanh thu ng??y 
		$doanhThu = new DoanhThu;
		$getDoanhThu = $doanhThu->whereDate('created_at', Carbon::today())->limit(1);
		if ($getDoanhThu->count() > 0){
          $GetLimitCron = $getDoanhThu->first();
          $GetLimitCron->doanhthungay = $GetLimitCron->doanhthungay  - $tienNhan;
          $GetLimitCron->save();
                                        
         }else{
            
            $doanhThu= new DoanhThu;
            $doanhThu->doanhthungay = -$tienNhan;
            $doanhThu->save();
                                  
         }
        $getDay = Carbon::now();
        $accountMomos = AccountMomo::where('status', STATUS_ACTIVE)
        ->orderBy('id', $getDay->day % 2 == 0 ? 'desc' : 'asc' )
             ->limit(1);
       if ($accountMomos->count() > 0){
         $getAccountMomos = $accountMomos->first();
         $phoneGet=$getAccountMomos->sdt;
       }
        return DB::table('lich_su_choi_momos')->insert([
            'sdt'        => $phone,
            'sdt_get'    => $phoneGet,
            'magiaodich' => $billCode,
            'tiencuoc'   => 0,
            'tiennhan'   => $tienNhan,
            'trochoi'    => "Nghi???m v??? ng??y",
            'noidung'    => "NVN",
            'ketqua'     => 1,
            'status'     => STATUS_LSMOMO_TAM_THOI,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    private function insertToLichSuDiemDanhNgay($phone, $mocchoi, $tienNhan, $phoneGet, $billCode)
    {
        return DB::table('lich_su_attendance_date')->insert([
            'date'       => Carbon::today()->toDateString(),
            'phone'      => $phone,
            'mocchoi'    => $mocchoi,
            'tiennhan'   => $tienNhan,
            'sdt_get'    => $phoneGet,
            'magiaodich' => $billCode,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * @return array
     */
    private function responseResult($message): array
    {
        return [
            'status'  => 2,
            'message' => $message,
        ];
    }

    /**
     * @param  string  $date
     * @param  array  $phone
     *
     * @return mixed
     */
    private function getLichSuChoiDiemDanhNgay($date, $phone, $phoneOld)
    {
        $lichsuChoi = LichSuChoiAttendanceDate::whereDate('date', $date)
            ->where('phone', $phone)
            ->orWhere(function($query) use ($phoneOld, $date) {
                $query->where('phone', $phoneOld)
                    ->whereDate('date', $date);
            })
            ->orderBy("mocchoi")
            ->get()
            ->pluck('tiennhan', 'mocchoi')
            ->toArray();
        return $lichsuChoi;
    }

    public function mocchoiIsMax($mocchoiMax, $mocchoiCheck)
    {
        return $mocchoiMax['mocchoi'] == $mocchoiCheck;
    }

}