<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Setting;
use App\Models\AccountMomo;
use App\Models\LichSuChoiMomo;
use App\Models\TaiXiu;
use App\Models\ChanLe;
use App\Models\ChanLe2;
use App\Models\Gap3;
use App\Models\Tong3So;
use App\Models\X1Phan3;
use App\Models\ConfigMessageMomo;
use App\Models\WEB2M;
use App\Models\Cache;
use App\Models\LichSuTraThuongTuan;
use App\Models\SettingPhanThuongTop;
use App\Models\NoHuu;
use App\Models\LichSuChoiNoHu;
use App\Models\LimitCron;
use App\Models\MaGiaoDich;
use App\Models\TopTuan;
use App\Models\AccountLevelMoney;
use Illuminate\Support\Facades\Log;
use App\Traits\PhoneNumber;
use App\Models\DoanhThu;
use App\Models\LichSuBank;





class BotXuLiController extends Controller
{
    
    
    public function __construct(){
               
        if(!isset($_GET['cron'])) {
            exit('Exit!');
        }
    }
    
    public function sendHetTien($method,$parameters){
		$bot_token='5190886198:AAFcHwqC1Sf5Vg4igG9fRz1pPJ_rSqHBoaA';
		$url ="https://api.telegram.org/bot$bot_token/$method";
		if(!$curl = curl_init()){
			exit();
		}
		curl_setopt($curl,CURLOPT_POST,true);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$parameters);
		curl_setopt($curl,CURLOPT_URL,$url);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		$output = curl_exec($curl);
		return $output;
	}
	
    public function send($method,$parameters){
		$bot_token='1986646734:AAEOYxaY55RjB7J-Uh_BKlOLZFIH9h2QOEc';
		$url ="https://api.telegram.org/bot$bot_token/$method";
		if(!$curl = curl_init()){
			exit();
		}
		curl_setopt($curl,CURLOPT_POST,true);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$parameters);
		curl_setopt($curl,CURLOPT_URL,$url);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		$output = curl_exec($curl);
		return $output;
	}
	
	public function sendSimMomo($method,$parameters){
		$bot_token='2052533102:AAEvfPzY18-cl2rsfGP1KsqHM7BTpYnE4gg';
		$url ="https://api.telegram.org/bot$bot_token/$method";
		if(!$curl = curl_init()){
			exit();
		}
		curl_setopt($curl,CURLOPT_POST,true);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$parameters);
		curl_setopt($curl,CURLOPT_URL,$url);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		$output = curl_exec($curl);
		return $output;
	}
	//X??? l?? l???i status l???i qu?? 3 ph??t ch??a tr??? ti???n
    public function getDoanhThuNgay(request $request){
        
        $TongDoanhThuGameNgay = 0;
		//T???ng ng??y
		$LichSuChoiMomo = new LichSuChoiMomo;
        $GetLichSuChoiMomo = $LichSuChoiMomo
        ->whereDate('created_at', Carbon::today())->get();
        $count = 0;
        $nhan = 0;
        $tra = 0;
        foreach ($GetLichSuChoiMomo as $row) {
            // $nhan = $nhan + $row->tiencuoc;
            // $tra =  $tra  - $row->tiennhan;
            
            $TongDoanhThuGameNgay = $TongDoanhThuGameNgay + $row->tiencuoc;
            $TongDoanhThuGameNgay = $TongDoanhThuGameNgay - $row->tiennhan;
            
            
        }
        //$TongDoanhThuGameNgay = $nhan - $tra;
        //T???ng th??ng
         $TongDoanhThuGameThang = 0;
          $month             = now()->month;
         // $GetLichSuChoiMomo = $LichSuChoiMomo->whereMonth('created_at', '=', $month)->get();

        //  foreach ($GetLichSuChoiMomo as $row) {
        //      $TongDoanhThuGameThang = $TongDoanhThuGameThang + $row->tiencuoc;
        //      $TongDoanhThuGameThang = $TongDoanhThuGameThang - $row->tiennhan;
        //  }
        
		$parameters = array(
	                                      "chat_id" => '1090916551',
                	                        "text" => 'hello chao',
                	                        "parse_mode" => 'Markdown'
                                    	);
                	                    
         $parameters["text"]='*AUTO CH???N L??? : Doanh thu ng??y:* '. Carbon::now()  .' ???? :  '. number_format($TongDoanhThuGameNgay) .' Th??ng : '.number_format($TongDoanhThuGameThang);
         $this->sendSimMomo("sendMessage",$parameters);
        echo 'Update doanh thu ng??y th??nh c??ng';
    }
	
	//X??? l?? r??t ti???n v??? t??i kho???n
    public function getTranferMoney(request $request){
		$parameters = array(
	                                      "chat_id" => '1090916551',
                	                        "text" => 'hello chao1',
                	                        "parse_mode" => 'Markdown'
                                    	);
                                    	
         $AccountMomo = new AccountMomo;                            	
        $ListAccountMomo = $AccountMomo->get();
		$WEB2M = new WEB2M;
        foreach($ListAccountMomo as $row){
			
			$soDu = $WEB2M->getMoney_momo($row->token,$row->webapi);

			$parameters["text"] = '*AUTO CH???N L???* : ' . $row->sdt . ' S??? d?? hi???n t???i =' . number_format($soDu)  ;
			if($soDu > 2000000){
			    
				for ($i = 0; $i < 3; $i++){
				    $content='R??T '. bin2hex(random_bytes(3));
				    $soTienRut = $soDu - 2000000;
					if($soTienRut <= 5000000){
						$res = $WEB2M->Bank(
                            $row->token,
                            '0394343329',
                            $row->password,
                            $soTienRut,
                            $content,
							$row->webapi
                        );
					}else{
						$res = $WEB2M->Bank(
                            $row->token,
                            '0394343329',
                            $row->password,
                            4999999,
                            $content,
							$row->webapi
							);
						
					}
					if ( isset($res['status']) && $res['status'] == 200) {
					    $parameters["text"] =$parameters["text"] .' R??T TH??NH C??NG: s??? cu???i =' .number_format($res['balance']); 
							if($soTienRut <= 5000000){
							   break;
							}
							
						
					}else{
					    $parameters["text"]= $parameters["text"] . $res['msg']; 
					} 
					sleep(3);
				}         
			}else{
			    $parameters["text"] = $parameters["text"] . ' *KH??NG R??T*';
			}
			 $this->sendSimMomo("sendMessage",$parameters);
			 $parameters["text"]='';
		} 
		$parameters["text"]= 'H???T';
		$this->sendSimMomo("sendMessage",$parameters);
			 
        echo 'r??t ti???n  th??nh c??ng';
    }
	
	 //X??? l?? l???i status l???i qu?? 3 ph??t ch??a tr??? ti???n
    public function updateStatusError(request $request){
        $newDateTime = Carbon::now()->subSeconds(5);
        //print_r($newDateTime);
        $LichSuChoiMomo = new LichSuChoiMomo;
        $GetLichSuChoiMomo = $LichSuChoiMomo->whereDate('created_at', Carbon::today())-> where('created_at','<=',$newDateTime)->where([
                        'status' => 2,'ketqua' => 1,
                    ])->get();
        foreach($GetLichSuChoiMomo as $row){
            $newDateTime = Carbon::now()->subMinutes(5);
            // 'dem ';
			$MaGiaoDich = new MaGiaoDich;
		    $dem = $MaGiaoDich->whereDate('created_at', Carbon::today())-> where('created_at','>=',$newDateTime)->where([
            'magiaodich' => $row->magiaodich,
		    ])->count();
		   // 'dem '. $dem;
			if($dem > 0){
			  $row->status = 3;
		    }else{
		       $row->status = 4;
		    }
           
            $row->save();
        }            
        echo 'Update 2 to 4 th??nh c??ng';
    }
    //Get giao d???ch v?? l??u l???i
    public function SaveGiaoDich(request $request){
        //sleep(10);
        // 'cu';
        //return;
        $type_cron = 'savegiaodich';

        // $LimitCron = new LimitCron;
        // $GetLimitCron = $LimitCron->where([
        //     'type' => $type_cron,
        // ])->orderBy('id', 'desc')->limit(1);

        // if ($GetLimitCron->count() > 0){
        //     $GetLimitCron = $GetLimitCron->first();

        //     $time = time();
        //     if ( ($time - $GetLimitCron->time) <= 10 ) {
        //         //return 'Cron kh??ng th??? x??? l?? ngay l??c n???y';
        //     }
    
        //     $LimitCron->type = $type_cron;
        //     $LimitCron->time = time();
        //     $LimitCron->save();
        // } else {
        //     $LimitCron->type = $type_cron;
        //     $LimitCron->time = time();
        //     $LimitCron->save();
        // }

        //Setting
        $Setting = new Setting;
        $GetSetting = $Setting->first();

        //Check b???o tr??
        if ($GetSetting->baotri == 1) {
            return;
        }

        //WEB2M
        $WEB2M = new WEB2M;
        //'123';
        // + L??u l???i l???ch s??? giao d???ch
        $AccountMomo = new AccountMomo;
        //$ListAccountMomo = $AccountMomo->where([
        //   'status' => 1,
       // ])->get();
        $ListAccountMomo = $AccountMomo->get();
        // '1234';
        foreach($ListAccountMomo as $row){
            
            
            $accountLevelMoney = new AccountLevelMoney;
			$countSdtAccountLevelMoney = $accountLevelMoney->where([
			'sdt' => $row->sdt,
			'status' => 1,
			])->count();
			if($countSdtAccountLevelMoney == 0){
			    continue;
			}
            //L???y l???ch s??? giao d???ch
            //dump($row);
           $ListGiaoDich = $WEB2M->GetGiaoDich($row->token,$row->webapi);
          
            //if($row->webapi ==1 ){
            if(true){
                
				 echo $row->webapi.'w2'; 
				      if (!isset($ListGiaoDich['status'])) {
               // dump($ListGiaoDich['momoMsg']);
                //if(isset($ListGiaoDich['status']) && $ListGiaoDich['status'] == 200){
                if ( isset($ListGiaoDich['momoMsg']['tranList']) ) {
                    $ListGD = $ListGiaoDich['momoMsg']['tranList'];
                 //if ( isset($ListGiaoDich['thueapimomo.vn']) ) {
                 //   $ListGD = $ListGiaoDich['thueapimomo.vn'];
                   echo '<br />L???y d??? li???u l???ch s??? => ' . $row->sdt;
            
                } else {
                    $ListGD = [];
                }
                //dump($ListGD);
                //L???y t???ng giao d???ch
                $countGetNhanTien=0;
               // dump($ListGD);
               // $ListGD = collect($ListGD)->reverse()->toArray();
                //dump($ListGD);
                $ListGD = collect($ListGD)->sortByDesc('finishTime')->toArray();
                //dump($ListGD);
                
                foreach($ListGD as $res){
                    echo ' ' .   $res['tranId'].',';
                    if($res['io'] != 1){
					   continue;
				    }
				    $countGetNhanTien ++;
				    if($countGetNhanTien >=30){
				       // break;
				    }
                    // //Check gi???i h???n ng??y
                    // $AccountMomo = new AccountMomo;
                    // $GetAccountMomo = $AccountMomo->where([
                    //     'sdt' => $row->sdt,
                    // ])->first();

                    // $gh1 = $GetAccountMomo->gioihan;

                    // $LichSuChoiMomo = new LichSuChoiMomo;
                    // $GetLichSuChoiMomo = $LichSuChoiMomo->whereDate('created_at', Carbon::today())->where('status', '!=', 5)->where([
                    //     'sdt_get' => $row->sdt,
                    // ])->get();

                    // $listLimit = 0;
                    // foreach($GetLichSuChoiMomo as $crush){
                    //     $listLimit = $listLimit + $crush->tiennhan;
                    // }

                    // $listLimit = $listLimit + (int) $res['amount'];
                    // $gh2 = $listLimit;
                      // echo 'a';
                    //if ($gh2 < $gh1) {
                        if (true) {
                        // echo 'c';
                        if ( !isset($res['comment']) ) {
                             $res['comment'] = ' ';
                        }
                        if ( isset($res['comment']) ) {
                            
                            //????a n???i dung v??? ch??? th?????ng
                            $res['comment2'] = $res['comment'];
                            $res['comment'] = strtolower($res['comment']);
                            $res['comment'] = str_replace(' ', '', $res['comment']);

                            if ( $res['comment'] == 'h1' ) {
                        
                                //Setting
                                $Setting = new Setting;
                                $GetSetting = $Setting->first();
                        
                                //Check off game
                                if ($GetSetting->on_nohu == 1) {
                                    //Setting N??? H??
                                    $NoHuu = new NoHuu;
                                    $Setting_NoHu = $NoHuu->first();

                                    //Ki???m tra giao d???ch t???n t???i ch??a
                                    $LichSuChoiNoHu = new LichSuChoiNoHu;
                                    $Check = $LichSuChoiNoHu->where([
                                        'magiaodich' => (string) $res['tranId'],
                                    ])->count();

                                    if ($Check == 0) {
                                        if( (int) $res['amount'] == $Setting_NoHu->tiencuoc ){
                                            $nhanduoc = 0;
                                            $ketqua = 99;

                                            $socuoi = substr( (string) $res['tranId'], -4 );

                                            if ($socuoi[0] == $socuoi[1] && $socuoi[1] == $socuoi[2] && $socuoi[2] == $socuoi[3]) {
                                                $ketqua = 99;
                                                $LichSuChoiNoHu = new LichSuChoiNoHu;
                                                $GetLichSuChoiNoHu = $LichSuChoiNoHu->get();
                    
                                                foreach ($GetLichSuChoiNoHu as $sm) {
                                                    $nhanduoc = $nhanduoc + $sm->tiencuoc;
                                                    $nhanduoc = $nhanduoc - $sm->tiennhan;
                                                }
                    
                                                $nhanduoc = $nhanduoc + ($res['amount'] / 100) * $Setting_NoHu->ptvaohu;    
                                            }

                                            $LichSuChoiNoHu = new LichSuChoiNoHu;
                                            $LichSuChoiNoHu->sdt = $res['partnerId']; //S??T ng?????i ch??i
                                            $LichSuChoiNoHu->magiaodich = (string) $res['tranId']; //M?? giao d???ch
                                            $LichSuChoiNoHu->tiencuoc = $res['amount']; //Ti???n c?????c
                                            $LichSuChoiNoHu->tienvaohu = ($res['amount'] / 100) * $Setting_NoHu->ptvaohu;
                                            $LichSuChoiNoHu->tiennhan = $nhanduoc; //Nh???n ???????c
                                            $LichSuChoiNoHu->noidung = $res['comment2']; //N???i dung chuy???n
                                            $LichSuChoiNoHu->ketqua = $ketqua;
                                            $LichSuChoiNoHu->status = 3; //M???c ?????nh ch??? x??? l??
                                            $Setting_NoHu->tienmacdinh = $Setting_NoHu->tienmacdinh + $LichSuChoiNoHu->tienvaohu;
											$Setting_NoHu->save();
                                            $LichSuChoiNoHu->save();
                                        }
                                    }
                                }

                            } else {
                            
                                //Ki???m tra giao d???ch t???n t???i ch??a
                                $newDateTime = Carbon::now()->subHours(72);
                                $LichSuChoiMomo = new LichSuChoiMomo;
                                $Check = $LichSuChoiMomo->where('created_at','>=',$newDateTime)->where([
                                    'magiaodich' => (string) $res['tranId'],
                                ])->count();

                                if ($Check == 0) {

                                    $NameGame = '';
                                    $tiennhan = 0;
                                    $ketqua = '';
                                    $flagMinMax = false;
                                    $accountLevelMoney = new AccountLevelMoney;
									$sdtLevelMoney = $accountLevelMoney->where([
									'sdt' => $row->sdt,
									'status' => 1,
									])->first();
									$minSdt= $sdtLevelMoney->min;
									$maxSdt= $sdtLevelMoney->max;
			                        //dump($sdtLevelMoney);
			                        //echo $minSdt;
			                        //echo $maxSdt;
                                    //Logic ch???n l???
                                    if ( $res['comment'] == 'c' || $res['comment'] == 'l' ) {
                                        if ($GetSetting->on_chanle == 1) {
                                            $ChanLe = new ChanLe;
                                            $Setting_ChanLe = $ChanLe->first();

                                            //if ( (int) $res['amount'] >= $Setting_ChanLe->min && (int) $res['amount'] <= $Setting_ChanLe->max ) {
                                            if ( (int) $res['amount'] >=  $minSdt && (int) $res['amount'] <= $maxSdt ) {
                                                $NameGame = 'Ch???n l???';

                                                //Logic
                                                $x = substr( (string) $res['tranId'] , -1);

                                                if ($x == 0 || $x == 9) {
                                                    $ra = 3;
                                                } else {
                                                    if ($x % 2 == 0) {
                                                        $ra = 1;
                                                    } else {
                                                        $ra = 2;
                                                    }
                                                }

                                                if ($res['comment'] == 'c') {
                                                    $dat = 1;
                                                } else {
                                                    $dat = 2;
                                                }

                                                if ($dat == $ra) {
                                                    $ketqua = 1;
                                                    $tiennhan = (int) $res['amount'] * $Setting_ChanLe->tile;
                                                } else {
                                                    $ketqua = 99;
                                                }
                                            }else{
												$flagMinMax = true;
											}
                                        }
                                    }

                                    //Logic t??i x???u
                                    if ( $res['comment'] == 't' || $res['comment'] == 'x' ) {
                                        if ($GetSetting->on_taixiu == 1) {
                                            $TaiXiu = new TaiXiu;
                                            $Setting_TaiXiu = $TaiXiu->first();

                                            //if ( (int) $res['amount'] >= $Setting_TaiXiu->min && (int) $res['amount'] <= $Setting_TaiXiu->max ) {
                                             if ( (int) $res['amount'] >= $minSdt && (int) $res['amount'] <= $maxSdt ) { 
                                                $NameGame = 'T??i x???u';

                                                //Logic
                                                $x = substr( (string) $res['tranId'] , -1);

                                                if ($x == 5 || $x == 6 || $x == 7 || $x == 8) {
                                                    $ra = 1;
                                                } else {

                                                    if ($x == 0 || $x == 9) {
                                                        $ra = 3;
                                                    } else {
                                                        $ra = 2;
                                                    }
                                                }

                                                if ($res['comment'] == 't') {
                                                    $dat = 1;
                                                } else {
                                                    $dat = 2;
                                                }

                                                if ($dat == $ra) {
                                                    $ketqua = 1;
                                                    $tiennhan = (int) $res['amount'] * $Setting_TaiXiu->tile;
                                                } else {
                                                    $ketqua = 99;
                                                }
                                            }else{
												$flagMinMax = true;
											}
                                        }
                                    }

                                    //Logic ch???n l??? 2
                                    if ( $res['comment'] == 'c2' || $res['comment'] == 'l2' ) {
                                        if ($GetSetting->on_chanle2 == 1) {
                                            $ChanLe2 = new ChanLe2;
                                            $Setting_ChanLe2 = $ChanLe2->first();

                                            //if ( (int) $res['amount'] >= $Setting_ChanLe2->min && (int) $res['amount'] <= $Setting_ChanLe2->max ) {
                                              if ( (int) $res['amount'] >=  $minSdt && (int) $res['amount'] <=  $maxSdt ) {
                                                $NameGame = 'Ch???n l??? T??i X???u 2';

                                                //Logic
                                                $x = substr( (string) $res['tranId'] , -1);

                                                if ($x % 2 == 0) {
                                                    $ra = 1;
                                                } else {
                                                    $ra = 2;
                                                }

                                                if ($res['comment'] == 'c2') {
                                                    $dat = 1;
                                                } else {
                                                    $dat = 2;
                                                }

                                                if ($dat == $ra) {
                                                    $ketqua = 1;
                                                    $tiennhan = (int) $res['amount'] * $Setting_ChanLe2->tile;
                                                } else {
                                                    $ketqua = 99;
                                                }
                                            }else{
												$flagMinMax = true;
											}
                                        }
                                    }
                                    
                                    if ( $res['comment'] == 'x2' || $res['comment'] == 't2' ) {
                                        if ($GetSetting->on_chanle2 == 1) {
                                            $ChanLe2 = new ChanLe2;
                                            $Setting_ChanLe2 = $ChanLe2->first();

                                            //if ( (int) $res['amount'] >= $Setting_ChanLe2->min && (int) $res['amount'] <= $Setting_ChanLe2->max ) {
                                              if ( (int) $res['amount'] >= $minSdt && (int) $res['amount'] <= $maxSdt ) {
                                                $NameGame = 'Ch???n l??? T??i X???u 2';

                                                //Logic
                                                $x = substr( (string) $res['tranId'] , -1);

                                                if ($x < 5) {
                                                    $ra = 1;
                                                } else {
                                                    $ra = 2;
                                                }

                                                if ($res['comment'] == 'x2') {
                                                    $dat = 1;
                                                } else {
                                                    $dat = 2;
                                                }

                                                if ($dat == $ra) {
                                                    $ketqua = 1;
                                                    $tiennhan = (int) $res['amount'] * $Setting_ChanLe2->tile;
                                                } else {
                                                    $ketqua = 99;
                                                }
                                            }else{
												$flagMinMax = true;
											}
                                        }
                                    }

                                    //Logic g???p 3
                                    if ( $res['comment'] == 'g3' ) {
                                        if ($GetSetting->on_gap3 == 1) {
                                            $Gap3 = new Gap3;
                                            $Setting_Gap3 = $Gap3->first();

                                            //if ( (int) $res['amount'] >= $Setting_Gap3->min && (int) $res['amount'] <= $Setting_Gap3->max ) {
                                              if ( (int) $res['amount'] >= $minSdt && (int) $res['amount'] <= $maxSdt ) {
                                                $NameGame = 'G???p 3';

                                                //Logic
                                                $x = substr( (string) $res['tranId'] , -2);
                                                $y = substr( (string) $res['tranId'] , -3);

                                                //Lo???i 1
                                                if ($x == '02' || $x == '13' || $x == '17' || $x == '19' || $x == '21' || $x == '29' || $x == '35' || $x == '37' || $x == '47' || $x == '49' || $x == '51' || $x == '54' || $x == '57' || $x == '63' || $x == '64' || $x == '74' || $x == '83' || $x == '91' || $x == '95' || $x == '96') {
                                                    $ketqua = 1;
                                                    $tiennhan = (int) $res['amount'] * $Setting_Gap3->tile1;
                                                } elseif ($x == '69' || $x == '96' || $x == '66' || $x == '99') {
                                                    $ketqua = 1;
                                                    $tiennhan = (int) $res['amount'] * $Setting_Gap3->tile2;
                                                } elseif ($y == '123' || $y == '234' || $y == '456' || $y == '678' || $y == '789') {
                                                    $ketqua = 1;
                                                    $tiennhan = (int) $res['amount'] * $Setting_Gap3->tile3;
                                                } else {
                                                    $ketqua = 99;
                                                }
                                            }else{
												$flagMinMax = true;
											}
                                        }
                                    }
                                    // L??
                                    if ( $res['comment'] == 'f' ) {
                                        if ($GetSetting->on_gap3 == 1) {
                                            $Gap3 = new Gap3;
                                            $Setting_Gap3 = $Gap3->first();

                                            //if ( (int) $res['amount'] >= $Setting_Gap3->min && (int) $res['amount'] <= $Setting_Gap3->max ) {
                                            if ( (int) $res['amount'] >= $minSdt && (int) $res['amount'] <= $maxSdt ) {
                                                $NameGame = 'L??';

                                                //Logic
                                                $x = substr( (string) $res['tranId'] , -2);
                                                //$y = substr( (string) $res['tranId'] , -3);

                                                //Lo???i 1
                                                if ($x == '00' || $x == '04' || $x == '10' || $x == '15' || $x == '18' || $x == '22' || $x == '24' || $x == '27' || $x == '33' || $x == '35' || $x == '38' || $x == '40' || $x == '42' || $x == '47' || $x == '54' || $x == '56' || $x == '61' || $x == '65' || $x == '69' || $x == '72' || $x == '77' || $x == '81' || $x == '84' || $x == '94' || $x == '99') {
                                                    $ketqua = 1;
                                                    $tiennhan = (int) $res['amount'] * 3.5;
                                                } else {
                                                    $ketqua = 99;
                                                }
                                            }else{
												$flagMinMax = true;
											}
                                        }
                                    }

                                    //Logic t???ng 3 s???
                                    if ( $res['comment'] == 's' ) {
                                        if ($GetSetting->on_tong3so == 1) {
                                            $Tong3So = new Tong3So;
                                            $Setting_Tong3So = $Tong3So->first();

                                           // if ( (int) $res['amount'] >= $Setting_Tong3So->min && (int) $res['amount'] <= $Setting_Tong3So->max ) {
                                             if ( (int) $res['amount'] >= $minSdt && (int) $res['amount'] <= $maxSdt ) {
                                                $NameGame = 'T???ng 3 s???';
                                                $x = substr( (string) $res['tranId'] , -3);
                                                $y = str_split($x);

                                                $tong = 0 ;
                                                foreach($y as $ris){
                                                    $tong = $tong + (int) $ris;
                                                }

                                                if ($tong == '7' || $tong == '17' || $tong == '27') {
                                                    $ketqua = 1;
                                                    $tiennhan = (int) $res['amount'] * $Setting_Tong3So->tile1;
                                                } elseif ($tong == '8' || $tong == '18') {
                                                    $ketqua = 1;
                                                    $tiennhan = (int) $res['amount'] * $Setting_Tong3So->tile2;
                                                } elseif ($tong == '9' || $tong == '19'){
                                                    $ketqua = 1;
                                                    $tiennhan = (int) $res['amount'] * $Setting_Tong3So->tile3;
                                                } else {
                                                    $ketqua = 99;
                                                }
                                            }else{
												$flagMinMax = true;
											}
                                        }
                                    }

                                    //Logic 1 ph???n 3
                                    if ( $res['comment'] == 'n1' || $res['comment'] == 'n2' || $res['comment'] == 'n3' ) {
                                        if ($GetSetting->on_1phan3 == 1) {
                                            $X1Phan3 = new X1Phan3;
                                            $Setting_1Phan3 = $X1Phan3->first();

                                           // if ( (int) $res['amount'] >= $Setting_1Phan3->min && (int) $res['amount'] <= $Setting_1Phan3->max ) {
                                             if ( (int) $res['amount'] >= $minSdt && (int) $res['amount'] <= $maxSdt ) {
                                                $NameGame = '1 ph???n 3';

                                                $x = substr( (string) $res['tranId'] , -1);
                                                if ($res['comment'] == 'n1') {
                                                    if ($x == '1' || $x == '2' || $x == '3') {
                                                        $ketqua = 1;
                                                        $tiennhan = (int) $res['amount'] * $Setting_1Phan3->tile;
                                                    } else {
                                                        $ketqua = 99;
                                                    }
                                                } elseif ($res['comment'] == 'n2') {
                                                    if ($x == '4' || $x == '5' || $x == '6') {
                                                        $ketqua = 1;
                                                        $tiennhan = (int) $res['amount'] * $Setting_1Phan3->tile;
                                                    } else {
                                                        $ketqua = 99;
                                                    }
                                                } elseif ($res['comment'] == 'n3') {
                                                    if ($x == '7' || $x == '8' || $x == '9') {
                                                        $ketqua = 1;
                                                        $tiennhan = (int) $res['amount'] * $Setting_1Phan3->tile;
                                                    } else {
                                                        $ketqua = 99;
                                                    }
                                                }
                                            }else{
												$flagMinMax = true;
											}
                                        }
                                    }
                                    echo'e';
                                    $LichSuChoiMomo;
                                    if ($NameGame != '' && $row->status == 1) {
                                        //Ki???m tra s??? ??i???n tho???i t???n t???i ch??a t?? dong ho??n ti???n max 20k
									//$LichSuChoiMomo1 = new LichSuChoiMomo;
								//	$Check = $LichSuChoiMomo1->where([
								//		'sdt' => $res['partnerId'],
								//	])->count();
                                    $ketquagane= $ketqua == '1' ? '???????????????????? *TH???NG* ' . $row->sdt  : ' ~~THUA~~  ' . $row->sdt;
                                    //if ($Check == -1) {
									if (false) {
										if($ketqua == 99 && (int) $res['amount'] <= 20000){
												$tiennhan = $res['amount'];
												$ketqua = 1;
												$ketquagane= $ketqua == '1' ? '???????????????????? *HO??N TI???N* ' . $row->sdt : ' ~~THUA~~ ' ;
										}
									}
									
                                        $LichSuChoiMomo = new LichSuChoiMomo;
                                        $LichSuChoiMomo->sdt = $res['partnerId']; //S??T ng?????i ch??i
                                        $LichSuChoiMomo->sdt_get = $row->sdt; //S??T admin
                                        $LichSuChoiMomo->magiaodich = (string) $res['tranId']; //M?? giao d???ch
                                        $LichSuChoiMomo->tiencuoc = $res['amount']; //Ti???n c?????c
                                        $LichSuChoiMomo->tiennhan = $tiennhan; //Nh???n ???????c
                                        $LichSuChoiMomo->trochoi = $NameGame; //T??n tr?? ch??i
                                        $LichSuChoiMomo->noidung = $res['comment2']; //N???i dung chuy???n
                                        $LichSuChoiMomo->ketqua = $ketqua; //K???t qu??? Th???ng hay Thua
                                        $LichSuChoiMomo->status = 4; //M???c ?????nh ch??? x??? l??
                                        $LichSuChoiMomo->save();
                                        // save top tu???n
                                        $topTuan= new TopTuan;
                                        $getTopTuan = $topTuan->whereBetween('created_at',
                                        [Carbon::today()->startOfWeek(), Carbon::today()->endOfWeek()])
                                         ->where('sdt', $res['partnerId'])
                                            ->limit(1);
                                        if ($getTopTuan->count() > 0){
                                         $GetLimitCron = $getTopTuan->first();
                                         $GetLimitCron->tongtientuan = $GetLimitCron->tongtientuan + $res['amount'];
                                         $GetLimitCron->save();
                                        
                                        }else{
                                            $topTuan= new TopTuan;
                                            $topTuan->sdt = $res['partnerId'];
                                            $topTuan->tongtientuan =$res['amount'];
                                            $topTuan->save();
                                        }
                                        // end save top tuan
                                        $parameters = array(
	                                      "chat_id" => '1090916551',
                	                        "text" => 'hello chao',
                	                        "parse_mode" => 'Markdown'
                                    	);
                	                    $sdtConvert = new PhoneNumber();
                        	            $parameters["text"]='*SDT* ' . $LichSuChoiMomo->sdt . ' -> '. $sdtConvert->convert($LichSuChoiMomo->sdt) .' Game '.  $LichSuChoiMomo->trochoi .'  M?? Giao D???ch : ' . $LichSuChoiMomo->magiaodich .' N???i dung: ' . $LichSuChoiMomo->noidung .' C?????c: '. number_format($LichSuChoiMomo->tiencuoc)  .'  Ti???n nh???n ' . number_format($LichSuChoiMomo->tiennhan) . '  K???t Qu???:  ' . $ketquagane;
                        	             
                        	             $this->send("sendMessage",$parameters);
                        	            $parameters["chat_id"]='1970029182';
                        	             $this->send("sendMessage",$parameters);
                        	             $parameters["chat_id"]='5004810472';
                        	             $this->send("sendMessage",$parameters);
                        	             
                                    } else {
                                        if ( $res['comment'] == 'nap' || $res['comment'] == 'Nap' ) {
                                            //return;
                                            continue;
                                        }
										$LichSuChoiMomo = new LichSuChoiMomo;
										
										// 80% ti???n nh???n n???u sai n???i dung ho???c gi???i h???n min max c?????c ti???n
										
										$tiennhan = 0;
										$ketquagane = '~~THUA~~';
										if((int) $res['amount'] > 1000){
										    if($row->status == 2){
												if($ketqua==1){
													$tiennhan= (int) $res['amount'] * 1;
													$ketquagane= $ketqua == '1' ? '???????????????????? *HO??N TI???N S??? B???O TR??* '. $row->sdt : ' ~~THUA~~ ' ;
												}else{
												$ketqua=1;    
    											$tiennhan= (int) $res['amount'] * 0.82;
    											$ketquagane= $ketqua == '1' ? '???????????????????? *HO??N TI???N S??? B???O TR??* '. $row->sdt : ' ~~THUA~~ ' ;
												}
    										}else{
    										    $ketqua=1;
    										    if($flagMinMax){
    										    	$tiennhan= (int) $res['amount'] * 0.80;
    										    	$ketquagane= $ketqua == '1' ? '???????????????????? *HO??N TI???N MAX*  '. $row->sdt : ' ~~THUA~~ ' ;
    										    }else{
    											    $tiennhan= (int) $res['amount'] * 0.81;
    											    $ketquagane= $ketqua == '1' ? '???????????????????? *HO??N TI???N N???i DUNG* '. $row->sdt : ' ~~THUA~~ ' ;
    										    }
    										}
    										
    										
										}
										if($tiennhan == 0){
										    $ketqua = 99;
										}
										$LichSuChoiMomo->sdt = $res['partnerId']; //S??T ng?????i ch??i
                                        $LichSuChoiMomo->sdt_get = $row->sdt; //S??T admin
                                        $LichSuChoiMomo->magiaodich = (string) $res['tranId']; //M?? giao d???ch
                                        $LichSuChoiMomo->tiencuoc = $res['amount']; //Ti???n c?????c
                                        $LichSuChoiMomo->tiennhan = $tiennhan; //Nh???n ???????c
                                        $LichSuChoiMomo->trochoi = 'Ch???n l???'; //T??n tr?? ch??i
                                        $LichSuChoiMomo->noidung = $res['comment2']; //N???i dung chuy???n
                                        $LichSuChoiMomo->ketqua = $ketqua; //K???t qu??? Th???ng hay Thua
                                        $LichSuChoiMomo->status = 4; //M???c ?????nh ch??? x??? l??
                                        $LichSuChoiMomo->save();
										$parameters = array(
	                                      "chat_id" => '1090916551',
                	                        "text" => 'hello chao',
                	                        "parse_mode" => 'Markdown'
                                    	);
                                    	$sdtConvert = new PhoneNumber();
                        	            $parameters["text"]='*SDT* ' . $LichSuChoiMomo->sdt .' -> '. $sdtConvert->convert($LichSuChoiMomo->sdt) .' Game '.  $LichSuChoiMomo->trochoi .'  M?? Giao D???ch : ' . $LichSuChoiMomo->magiaodich .' N???i dung: ' . $LichSuChoiMomo->noidung .' C?????c: '. number_format($LichSuChoiMomo->tiencuoc)  .'  Ti???n nh???n ' . number_format($LichSuChoiMomo->tiennhan) . '  K???t Qu???:  ' . $ketquagane;
                        	            $this->send("sendMessage",$parameters);
                        	            $parameters["chat_id"]='1970029182';
                        	             $this->send("sendMessage",$parameters);
									}
									
									// update doanh thu ng??y 
									$doanhThu = new DoanhThu;
									$getDoanhThu = $doanhThu->whereDate('created_at', Carbon::today())->limit(1);
									if ($getDoanhThu->count() > 0){
                                         $GetLimitCron = $getDoanhThu->first();
                                         $GetLimitCron->doanhthungay = $GetLimitCron->doanhthungay + $LichSuChoiMomo->tiencuoc - $LichSuChoiMomo->tiennhan ;
                                         $GetLimitCron->save();
                                        
                                    }else{
                                            $doanhThu= new DoanhThu;
                                            $doanhThu->doanhthungay = $LichSuChoiMomo->tiencuoc - $LichSuChoiMomo->tiennhan;
                                            $doanhThu->save();
                                    }
                                   
                                }
                            }
                       
                        }
                        
                    }
                  echo'b';
                }
               echo'K';
            }
           
            }else
			{ 
			    
            }
            
        }
        echo "<br />".'+ L???y giao d???ch th??nh c??ng';        
    }
    //test kich hoat sdt 
    public function GetKichHoatSdt(request $request){
        $type_cron = 'kichhoatsdt ';

        $LimitCron = new LimitCron;
        $GetLimitCron = $LimitCron->where([
            'type' => $type_cron,
        ])->orderBy('id', 'desc')->limit(1);

        if ($GetLimitCron->count() > 0){
            $GetLimitCron = $GetLimitCron->first();

            $time = time();
            if ( ($time - $GetLimitCron->time) <= 10 ) {
                //return 'Cron kh??ng th??? x??? l?? ngay l??c n???y';
            }
    
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        } else {
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        }
	    // + L??u l???i l???ch s??? giao d???ch
        $AccountMomo = new AccountMomo;
        $ListAccountMomo = $AccountMomo->where([
            'status' => 2,
        ])->get();

        foreach($ListAccountMomo as $row){
			$row->status = 1 ;
			$row->save();
		}
        echo "<br />".'+ test kich hoat sdt';
    }	
    //X??? l?? giao d???ch
    public function TraThuongGiaoDich(request $request){
        return;
        $type_cron = 'trathuonggiaodich';

        $LimitCron = new LimitCron;
        $GetLimitCron = $LimitCron->where([
            'type' => $type_cron,
        ])->orderBy('id', 'desc')->limit(1);

        if ($GetLimitCron->count() > 0){
            $GetLimitCron = $GetLimitCron->first();

            $time = time();
            if ( ($time - $GetLimitCron->time) <= 10 ) {
                //return 'Cron kh??ng th??? x??? l?? ngay l??c n???y';
            }
    
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        } else {
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        }


        $WEB2M = new WEB2M;
        $ConfigMessageMomo = new ConfigMessageMomo;

        //B???o tr??
        $Setting = new Setting;
        $GetSetting = $Setting->first();

        $GetSetting->baotri;

        //Check b???o tr??
        if ($GetSetting->baotri == 1) {
            echo 'M??y ch??? b???o tr??!';
            return;
        }

        $LichSuChoiMomo = new LichSuChoiMomo;
        $ListLichSuChoiMomo = $LichSuChoiMomo->where([
            'status' => 1,
        ])->limit(15)->get();

        foreach($ListLichSuChoiMomo as $row){
            //Ki???m tra l???i
            $Check = $LichSuChoiMomo->where([
                'id' => $row->id,
            ])->first();
            $newDateTime = Carbon::now()->subMinutes(20);
            //echo 'dem ';
			$MaGiaoDich = new MaGiaoDich;
		    $dem = $MaGiaoDich->whereDate('created_at', Carbon::today())-> where('created_at','>=',$newDateTime)->where([
            'magiaodich' => $Check->magiaodich,
		    ])->count();
		   echo 'dem '. $dem;
			if($dem > 0){
			  // update v??? tr???ng th??i ???? thanh to??n;
			  $Check->status = 3;
              $Check->save();
			 continue;
		    }
            //N???u v???n ??ang ??? tr???ng th??i ch???
            if ($Check->status == 1) {

                //Chuy???n th??nh tr???ng th??i ??ang x??? l??
                $Check->status = 2;
                $Check->save();

                $AccountMomo = new AccountMomo;
                $Account = $AccountMomo->where([
                    'sdt' => $Check->sdt_get,
                ])->first();

                $GetMessageTraThuong = $ConfigMessageMomo->where([
                    'type' => 'tra-thuong',
                ])->first();

                if ($Check->tiennhan > 0) {
                    // begin check c???nh b??o s???p b???o tr??
				// 	$LichSuChoiMomo = new LichSuChoiMomo;
    //                 $GetLichSuChoiMomo = $LichSuChoiMomo->whereDate('created_at', Carbon::today())->where([
    //                     'sdt_get' => $Check->sdt_get,'status' => 3,'ketqua' => 1,
    //                 ])->get();

                    
	//				$countLimit = 0;
    //                 foreach($GetLichSuChoiMomo as $crush){
    //                     $listLimit = $listLimit + $crush->tiennhan;
	// 		               $countLimit = $countLimit + 1;
    //                 }
					//L???y s??? l???n bank
                    $LichSuBank    = new LichSuBank;
                    $countLimit     = 0;
                    $listLimit      = 0;
                    $getLichSuBank = $LichSuBank->whereDate('created_at', Carbon::today())->where([
                        'sdtbank' => $Check->sdt_get,
                    ])->get();
        
                    foreach ($getLichSuBank as $r) {
                        $j = json_decode($r->response, true);
        
                        if (isset($j['status']) && $j['status'] == 200) {
                            $countLimit++;
                            $listLimit=$listLimit + $r->sotien;
                        }
                    }
					
					if($countLimit >= 185 || $listLimit > 27000000){
						//$GetMessageTraThuong->message ='C???NH B??O:SDT S???P B???O TR?? H??Y L??N WEB L???Y S??? M???I' ;
						//$GetMessageTraThuong->message ='Canh Bao:SDT '. $Check->sdt_get .' Sap Bao Tri Vi Sap Dat Han Muc Hay Len WEB Lay So Moi' ;
						$GetMessageTraThuong->message ='c???nh b??o:SDT ' . $Check->sdt_get .' s???p b???o tr?? v?? ?????t h???n m???c!h??y l??n WEB l???y s??? m???i' ;
						if($countLimit >= 190 || $listLimit > 28000000){
							$AccountMomo = new AccountMomo;
							$GetAccountMomo = $AccountMomo->where([
							'sdt' => $Check->sdt_get,
							])->first();
							$GetAccountMomo->status = 2;
							$GetAccountMomo->save();
							$parameters = array(
	                                      "chat_id" => '1090916551',
                	                        "text" => 'hello chao',
                	                        "parse_mode" => 'Markdown'
                                    	);
                            $parameters["text"]='AUTO CH???N L???:  '.  $Check->sdt_get . ' ?????t v??o tr???ng th??i *B???O TR??* S??? Giao D???ch = ' . $countLimit . ' .S??? Ti???n Giao D???ch = ' . number_format($listLimit) ;        	
                            $this->sendSimMomo("sendMessage",$parameters);
                            $parameters["chat_id"]='1970029182';
                        	$this->sendSimMomo("sendMessage",$parameters);
						}
					}
				    // ho??n ti???n
					if($Check->tiennhan < $Check->tiencuoc){
					    $amount= (int)($Check->tiencuoc * 0.81);
					    $amountnhan =(int)$Check->tiennhan;
					    if((int)$amount == (int)$amountnhan ){
					        	$GetMessageTraThuong->message ='ho??n ti???n do: ghi sai n???i dung ch??i ho???c n???i dung th???a d???u c??ch';
					    } 
					    if((int)$amount < (int)$amountnhan ){
					        	//$GetMessageTraThuong->message ='hoan ti???n do: sdt ' . $Check->sdt_get .' dang choi bao tri! Hay Len WEB Lay So Moi';
					        	$GetMessageTraThuong->message ='ho??n ti???n do: sdt ??ang ch??i b???o tr??! h??y l??n WEB l???y s??? m???i';
					    }
					    if((int)$amount > (int)$amountnhan ){
					        	$GetMessageTraThuong->message ='ho??n ti???n do: ch??i qu?? gi???i h???n Min ho???c gi???i h???n Max';
					    }
					
					}
                    // end
                        $Check->status = 4;
                        $Check->save();
                        continue;
                        //$res = $WEB2M->Bank(
                          //  $Account->token,
                          //  $Check->sdt,
                          //  $Account->password,
                          ///  $Check->tiennhan,
                           // $GetMessageTraThuong->message.' '.$Check->magiaodich,
						   //$Account->webapi
                      // );
    
                        
                        if ( isset($res['status']) && $res['status'] == 200) {
                            $Check->status = 3;
                            $Check->save();
                            $MaGiaoDich = new MaGiaoDich;
                            $MaGiaoDich->magiaodich = $Check->magiaodich;
                            $MaGiaoDich->save();
                        } else {
                            $parameters = array(
	                                      "chat_id" => '1090916551',
                	                        "text" => 'hello chao',
                	                        "parse_mode" => 'Markdown'
                                    	);
                            if (str_contains($res['msg'], '5.000.000') || str_contains($res['msg'], '30.000.000') || str_contains($res['msg'], '100.000.000' )  || str_contains($res['msg'], 'giao') || str_contains($res['msg'], '150')) {
                                
                                if (str_contains($res['msg'], '5.000.000')){
                                  $parameters["text"]= $Check->sdt_get . '    ?????t max 5M/1 ng??y '. ' AUTO CH???N L??? '  . $Check->magiaodich  . ' SDT : ' . $Check->sdt .' ';
                                }
                                if (str_contains($res['msg'], '30.000.000')){
                                 $parameters["text"]= $Check->sdt_get . '     ?????t max 30M/1 ng??y'. ' AUTO CH???N L???' . $Check->magiaodich  . ' SDT : ' . $Check->sdt .' ';
                                }
                                if (str_contains($res['msg'], '100.000.000')){
                                  $parameters["text"]= $Check->sdt_get . '    ?????t max 100M/1 ng??y'. ' AUTO CH???N L???' . $Check->magiaodich  . ' SDT : ' . $Check->sdt .' ';
                                }
                                if (str_contains($res['msg'], '150')){
                                  $parameters["text"]= $Check->sdt_get . '    ?????t max 150 giao d???ch'. ' AUTO CH???N L???' . $Check->magiaodich  . ' SDT : ' . $Check->sdt .' ';
                                }
                        	      $this->sendSimMomo("sendMessage",$parameters);
								  $ListAccountMomo = $AccountMomo->where([
									'status' => 1,
									])->first();
									$Check->sdt_get = $ListAccountMomo->sdt;	
									$Account->status = 2;
									$Account->save();
							}
							if(str_contains($res['msg'], '????? ti???n')){
							 $parameters["text"]=' AUTO CH???N L???: ' . $Check->sdt_get . '   *kh??ng ????? ti???n!* M?? : ' . $Check->magiaodich  . ' SDT : ' . $Check->sdt . ' N???i dung: ' . $Check->noidung . ' C?????c: '. number_format($Check->tiencuoc)  .' Ti???n nh???n ' . number_format($Check->tiennhan);
							    $this->sendSimMomo("sendMessage",$parameters);
							    $this->sendHetTien("sendMessage",$parameters);
							    $parameters["chat_id"]='1970029182';
                        	$this->sendSimMomo("sendMessage",$parameters);
                        	
							    
							}
						
                            $Check->status = 4;
                            $Check->save();
                        }
                        
                        var_dump($res);
                        
                        sleep(3);
                        
                        if($Check->status == 3){
                            $parameters = array(
	                                      "chat_id" => '1090916551',
                	                        "text" => 'hello chao',
                	                        "parse_mode" => 'Markdown'
                                    	);
                	                    
                        	             $parameters["text"]='*Tr??? Th?????ng SDT* ' .    $Check->sdt  .' Game '.  $Check->trochoi .'  M?? Giao D???ch : ' . $Check->magiaodich . ' N???i Dung : ' .  $Check->noidung  .' C?????c: '. number_format($Check->tiencuoc)  .'  Ti???n nh???n ' . number_format($Check->tiennhan) . '  *TH??NH C??NG* '. $Check->sdt_get ;
                        	            $this->send("sendMessage",$parameters);
                        	             $parameters["chat_id"]='1970029182';
                        	             $this->send("sendMessage",$parameters);
                        }
                    
                    
                } else {
                    $Check->status = 3;
                    $Check->save();
                }
            }

        }

        echo "<br />".'+ X??? l?? giao d???ch ho??n t???t';
    }

    //X??? l?? l???i giao d???ch l???i
    public function TraThuongGiaoDichError(request $request){
        //return;
        $type_cron = 'trathuonggiaodicherror';

        $LimitCron = new LimitCron;
        $GetLimitCron = $LimitCron->where([
            'type' => $type_cron,
        ])->orderBy('id', 'desc')->limit(1);

        if ($GetLimitCron->count() > 0){
            $GetLimitCron = $GetLimitCron->first();

            $time = time();
            if ( ($time - $GetLimitCron->time) <= 10 ) {
                //return 'Cron kh??ng th??? x??? l?? ngay l??c n???y';
            }
    
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        } else {
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        }

        $WEB2M = new WEB2M;
        $ConfigMessageMomo = new ConfigMessageMomo;

        //B???o tr??
        $Setting = new Setting;
        $GetSetting = $Setting->first();

        $GetSetting->baotri;

        //Check b???o tr??
        if ($GetSetting->baotri == 1) {
            echo 'M??y ch??? b???o tr??!';
            return;
        }

        $LichSuChoiMomo = new LichSuChoiMomo;
        $ListLichSuChoiMomo = $LichSuChoiMomo->where([
            'status' => 4,
        ])->limit(15)->get();

        foreach($ListLichSuChoiMomo as $row){
            //Ki???m tra l???i
            $Check = $LichSuChoiMomo->where([
                'id' => $row->id,
            ])->first();
            $newDateTime = Carbon::now()->subMinutes(20);
            //echo 'dem ';
			$MaGiaoDich = new MaGiaoDich;
			//$dem = $MaGiaoDich->whereDate('created_at', Carbon::today())-> where('created_at','>=',$newDateTime)->where([
		    $dem = $MaGiaoDich->where([
            'magiaodich' => $Check->magiaodich,
		    ])->count();
		   //echo 'dem 12312'. $dem;
			if($dem > 0){
			   // update v??? tr???ng th??i ???? thanh to??n;
			  $Check->status = 3;
              $Check->save();
			  continue;
		    }
		      // 'demabc'. $dem;
            //N???u v???n ??ang ??? tr???ng th??i ch???
            if ($Check->status == 4) {

                //Chuy???n th??nh tr???ng th??i ??ang x??? l??
                $Check->status = 2;
                $Check->save();

                $AccountMomo = new AccountMomo;
                $Account = $AccountMomo->where([
                    'sdt' => $Check->sdt_get,
                ])->first();

                $GetMessageTraThuong = $ConfigMessageMomo->where([
                    'type' => 'tra-thuong',
                ])->first();

                if ($Check->tiennhan > 0) {
                        // begin check c???nh b??o s???p b???o tr??
				// 	$LichSuChoiMomo = new LichSuChoiMomo;
    //                 $GetLichSuChoiMomo = $LichSuChoiMomo->whereDate('created_at', Carbon::today())->where([
    //                     'sdt_get' => $Check->sdt_get,'status' => 3,'ketqua' => 1,
    //                 ])->get();

    //                 $listLimit = 0;
				// 	$countLimit = 0;
    //                 foreach($GetLichSuChoiMomo as $crush){
    //                     $listLimit = $listLimit + $crush->tiennhan;
				// 		$countLimit = $countLimit + 1;
    //                 }
						//L???y s??? l???n bank
					
                    $LichSuBank    = new LichSuBank;
                    $countLimit     = 0;
                    $listLimit      = 0;
                    $getLichSuBank = $LichSuBank->whereDate('created_at', Carbon::today())->where([
                        'sdtbank' => $Check->sdt_get,
                    ])->get();
        
                    foreach ($getLichSuBank as $r) {
                        $j = json_decode($r->response, true);
        
                        if (isset($j['status']) && $j['status'] == 200) {
                            $countLimit++;
                            $listLimit=$listLimit + $r->sotien;
                        }
                    }
					if($countLimit >= 185 || $listLimit > 27000000){
						//$GetMessageTraThuong->message ='C???NH B??O:SDT S???P B???O TR?? H??Y L??N WEB L???Y S??? M???I';
						$GetMessageTraThuong->message ='c???nh b??o:SDT ' . $Check->sdt_get .' s???p b???o tr?? v?? ?????t h???n m???c!h??y l??n WEB l???y s??? m???i' ;
						if($countLimit >= 190 || $listLimit > 28000000){
							$AccountMomo = new AccountMomo;
							$GetAccountMomo = $AccountMomo->where([
							'sdt' => $Check->sdt_get,
							])->first();
							$GetAccountMomo->status = 2;
							$GetAccountMomo->save();
							$parameters = array(
	                                      "chat_id" => '1090916551',
                	                        "text" => 'hello chao',
                	                        "parse_mode" => 'Markdown'
                                    	);
                            $parameters["text"]=' AUTO CH???N L??? '. $Check->sdt_get . ' ?????t v??o tr???ng th??i *B???O TR??* S??? Giao D???ch = ' . $countLimit . ' .S??? Ti???n Giao D???ch = ' . number_format($listLimit) ;        	
                            $this->sendSimMomo("sendMessage",$parameters);
                            $parameters["chat_id"]='1970029182';
                        	$this->sendSimMomo("sendMessage",$parameters);
						}
					}
					// ho??n ti???n
					if($Check->tiennhan <= $Check->tiencuoc){
					    $amount= (int)($Check->tiencuoc * 0.81);
					    $amountnhan =(int)$Check->tiennhan;
					    if((int)$amount == (int)$amountnhan ){
					        	$GetMessageTraThuong->message ='ho??n ti???n do: ghi sai n???i dung ch??i ho???c n???i dung th???a d???u c??ch';
					        	
					    } 
					    //if((int)$amount <= (int)$amountnhan ){
					    if((int)($Check->tiencuoc * 0.82) == (int)$amountnhan || (int)($Check->tiencuoc * 1) == (int)$amountnhan){
					        	//$GetMessageTraThuong->message ='hoan ti???n do: sdt ' . $Check->sdt_get .' dang choi bao tri! Hay Len WEB Lay So Moi';
					        	$GetMessageTraThuong->message ='ho??n ti???n do: sdt ??ang ch??i b???o tr??! h??y l??n WEB l???y s??? m???i';
					        
					    }
					    if((int)$amount > (int)$amountnhan ){
					        	$GetMessageTraThuong->message ='ho??n ti???n do: ch??i qu?? gi???i h???n Min ho???c gi???i h???n Max';
					        
					    }
					
					}
                    // end
                    // update code l???i 51 bank ti??n l???i v???n m???t ti???n
                        $LichSuBankCode51    = new LichSuBank;	
				    	$getLichSuBankCode51 = $LichSuBankCode51->whereDate('created_at', Carbon::today())->where([
                        'noidung' => $GetMessageTraThuong->message.' '.$Check->magiaodich,
                         ])->get();
                         $coundCode51 = 0;
                         foreach ($getLichSuBankCode51 as $r) {
                         $j = json_decode($r->response, true);

                         if (isset($j['status']) && $j['status'] == 51) {
                            $coundCode51++;
                         }
                        }
                        if($coundCode51 >= 2){
                            continue;
                        }
                        // end update code l???i 51 bank ti??n l???i v???n m???t ti???n
                        $res = $WEB2M->Bank(
                            $Account->token,
                            $Check->sdt,
                            $Account->password,
                            $Check->tiennhan,
                            $GetMessageTraThuong->message.' '.$Check->magiaodich,
							$Account->webapi
                        );
                        
                        
                        
                        if ( isset($res['status']) && ($res['status'] == 200 || (($res['status'] == 51 && $Check->tiencuoc > 20000)  || ($res['status'] == 51 && $Check->tiencuoc == 0)) )) {
                             $Check->status = 3;
                             $Check->save();
                             $parameters = array(
	                            "chat_id" => '1090916551',
                	            "text" => 'hello chao',
                	            "parse_mode" => 'Markdown'
                            	);
                            	$sdtConvert = new PhoneNumber();
                            	//*Tr??? Th?????ng SDT*
                            	//$parameters["text"]='*X??? l?? giao d???ch l???i*: SDT ' . $Check->sdt .' Game '. $Check->trochoi .' M?? giao d???ch: ' .$Check->magiaodich  . ' N???i dung: ' . $Check->noidung  .' C?????c: '. number_format($Check->tiencuoc)  .' Ti???n nh???n ' . number_format($Check->tiennhan) .'  *S??T=>* '. $Check->sdt_get;
                	            $parameters["text"]='*Tr??? Th?????ng SDT*: SDT ' . $Check->sdt .' -> '.$sdtConvert->convert($Check->sdt) .' Game '. $Check->trochoi .' M?? giao d???ch: ' .$Check->magiaodich  . ' N???i dung: ' . $Check->noidung  .' C?????c: '. number_format($Check->tiencuoc)  .' Ti???n nh???n ' . number_format($Check->tiennhan) .'  *S??T=>* '. $Check->sdt_get;
                	            if($res['status'] == 51){
                	                $Check->status = 99;
                	                $Check->save();
                	                $parameters["text"] = $parameters["text"] . ' CH??  ?? m?? 51 . C???n check';
                	                $this->sendSimMomo("sendMessage",$parameters);
							        $this->sendHetTien("sendMessage",$parameters);
                	            }
                	            
                	            $this->send("sendMessage",$parameters);
                	            $parameters["chat_id"]='1970029182';
                        	   $this->send("sendMessage",$parameters);
                        	   $parameters["chat_id"]='5004810472';
                        	   $this->send("sendMessage",$parameters);
                            $MaGiaoDich = new MaGiaoDich;
                            $MaGiaoDich->magiaodich = $Check->magiaodich;
                            $MaGiaoDich->save();
                        } else {
                            
                            
                 //              if($res['msg'] == null || $res['msg'] == 'null' || $res['msg'] == 'NULL'){
				// 					continue;
				// 			}
                            $parameters = array(
        	                                      "chat_id" => '1090916551',
                        	                        "text" => 'hello chao',
                        	                        "parse_mode" => 'Markdown'
                                            	);
                                if (str_contains($res['msg'], '5') || str_contains($res['msg'], '30') || str_contains($res['msg'], '100' )  || str_contains($res['msg'], '5') || str_contains($res['msg'], '150') ) {
                                        if (str_contains($res['msg'], 'm???i')){
                                          $parameters["text"]= $Check->sdt_get . '    ?????t max 5M/1 ng??y AUTO CH???N L???' . $Check->magiaodich  . ' SDT : ' . $Check->sdt .' ' ;
                                        }
                                        if (str_contains($res['msg'], '30')){
                                         $parameters["text"]= $Check->sdt_get . '     ?????t max 30M/1 ng??y AUTO CH???N L??? '. $Check->magiaodich  . ' SDT : ' . $Check->sdt .' ' ;
                                        }
                                        if (str_contains($res['msg'], '100')){
                                          $parameters["text"]= $Check->sdt_get . '    ?????t max 100M/1 ng??y AUTO CH???N L??? ' . $Check->magiaodich  . ' SDT : ' . $Check->sdt .' ';
                                        }
                                        if (str_contains($res['msg'], '150')){ 
                                          $parameters["text"]= $Check->sdt_get . '    ?????t max 150 giao d???ch AUTO CH???N L??? '. $Check->magiaodich  . ' SDT : ' . $Check->sdt .' ' ;
                                        }
                                        if (str_contains($res['msg'], '150')){ 
                                          $parameters["text"]= $Check->sdt_get . '    ?????t max 150 giao d???ch AUTO CH???N L??? '. $Check->magiaodich  . ' SDT : ' . $Check->sdt .' ' ;
                                        }
                                        $parameters["text"] = $parameters["text" ] .  $res['msg'];
                                	      $this->sendSimMomo("sendMessage",$parameters);
        								  $ListAccountMomo = $AccountMomo->where([
        									'status' => 1,
        									])->first();
        								  if (!is_null($ListAccountMomo)){
        									$Check->sdt_get = $ListAccountMomo->sdt;
                                          }
        									$Account->status = 2;
        									$Account->save();
        						  }
        					if (str_contains($res['msg'], 'N???i dung n??y ???? ???????c chuy???n ti???n r???i')){ 
                                         $Check->status = 3;
                                         $Check->save();
                                         continue;
        					}	  
							if(str_contains($res['msg'], '????? ti???n')){
							    $parameters["text"]='AUTO CH???N L??? :'. $Check->sdt_get . '   *kh??ng ????? ti???n!* M?? : ' . $Check->magiaodich  . ' SDT : ' . $Check->sdt . ' N???i dung: ' . $Check->noidung . ' C?????c: '. number_format($Check->tiencuoc)  .' Ti???n nh???n ' . number_format($Check->tiennhan) . $res['msg'];;
							    $this->sendSimMomo("sendMessage",$parameters);
							    $this->sendHetTien("sendMessage",$parameters);
							    $parameters["chat_id"]='1970029182';
                        	$this->sendSimMomo("sendMessage",$parameters);
                        	$parameters["chat_id"]='-1001649646662';
                        	$parameters["text"]='AUTO CH???N L??? :'. $Check->sdt_get . '   *kh??ng ????? ti???n!* M?? : ' . $Check->magiaodich  . ' SDT : ......' . substr( (string) $Check->sdt, -6 ) . ' N???i dung: ' . $Check->noidung . ' C?????c: '. number_format($Check->tiencuoc)  .' Ti???n nh???n ' . number_format($Check->tiennhan) .' L?? do '. $res['msg'];
                        	$this->sendSimMomo("sendMessage",$parameters);
							}
							
						    $Check->status = 4;
                            $Check->save();
                           
                        }
                        
                        if($res['status'] == 51){
                             $parameters = array(
	                            "chat_id" => '1090916551',
                	            "text" => 'hello chao',
                	            "parse_mode" => 'Markdown'
                            	);
                            	$parameters["text"]='*CHECK Tr??? Th?????ng SDT*: SDT ' . $Check->sdt .' Game '. $Check->trochoi .' M?? giao d???ch: ' .$Check->magiaodich  . ' N???i dung: ' . $Check->noidung  .' C?????c: '. number_format($Check->tiencuoc)  .' Ti???n nh???n ' . number_format($Check->tiennhan) .'  *S??T=>* '. $Check->sdt_get;
                	            if($res['status'] == 51){
                	                $parameters["text"] = $parameters["text"] . ' CH??  ?? m?? 51 . C???n check';
                	                $this->sendSimMomo("sendMessage",$parameters);
							        $this->sendHetTien("sendMessage",$parameters);
                	            }
                        }
                        
                        var_dump($res);
                        
                        sleep(1);
                    
                    
                } else {
                    $Check->status = 3;
                    $Check->save();
                }
            }

        }

        echo "<br />".'+ X??? l?? giao d???ch ho??n t???t';
    }

    //Tr??? th?????ng top tu???n
    public function GetTraThuongTuan(request $request){
        return;
        $type_cron = 'gettrathuongtuan';

        $LimitCron = new LimitCron;
        $GetLimitCron = $LimitCron->where([
            'type' => $type_cron,
        ])->orderBy('id', 'desc')->limit(1);

        if ($GetLimitCron->count() > 0){
            $GetLimitCron = $GetLimitCron->first();

            $time = time();
            if ( ($time - $GetLimitCron->time) <= 10 ) {
                //return 'Cron kh??ng th??? x??? l?? ngay l??c n???y';
            }
    
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        } else {
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        }

        //Setting
        $Setting = new Setting;
        $GetSetting = $Setting->first();

        //Check b???o tr??
        if ($GetSetting->baotri == 1) {
            return;
        }

        //Check game c?? b???t kh??ng
        if ($GetSetting->on_trathuongtuan != 1) {
            return;
        }

        $SettingPhanThuongTop = new SettingPhanThuongTop;
        $LichSuChoiMomo = new LichSuChoiMomo;
        $Cache = new Cache;
        $GetCache = $Cache->first();

        //
        $TimeUpdate = $GetCache->time_bank_top_tuan;
        $TimeNow = date("d/m/Y");

        //C???p nh???t khi b??? r???ng
        if ($GetCache->time_bank_top_tuan == '') {
            $GetCache->time_bank_top_tuan = date("d/m/Y");
            $GetCache->save();
        }

        $weekday = date("l");
        $weekday = strtolower($weekday);
        
        if ($weekday == 'monday') {
            if ($TimeNow != $TimeUpdate) {

                //C???p nh???t l???i th???i gian update
                $GetCache->time_bank_top_tuan = date("d/m/Y");
                $GetCache->save();

                //Thu???t to??n t??m TOP tu???n
                $TopTuan = [];
                $dem = 0;

                $ListSDT = [];
                $st = 0;

                $now = Carbon::now();
                $weekStartDate = $now->startOfWeek()->format('Y-m-d H:i');
                $weekEndDate = $now->endOfWeek()->format('Y-m-d H:i');
                $date = \Carbon\Carbon::today()->subDays(7);

                $ListLichSuChoiMomo = $LichSuChoiMomo->where('created_at','>=',$date)->get();

                foreach ($ListLichSuChoiMomo as $row) {
                    $sdt = $row->sdt;

                    $check = True;
                    foreach ($ListSDT as $res) {
                        if ($res == $sdt) {
                            $check = False;
                        }
                    }

                    if ($check) {
                        $ListSDT[$st] = $sdt;
                        $st ++;
                    }
                    
                }

                $ListUser = [];
                $dem = 0;

                foreach($ListSDT as $row){
                    $Result = $LichSuChoiMomo->where([
                        'sdt' => $row,
                        'status' => 3,
                    ])->where('created_at','>=',$date)->get();

                    $ListUser[$dem]['sdt'] = $row;
                    $ListUser[$dem]['tiencuoc'] = 0;

                    foreach ($Result as $res) {
                        $ListUser[$dem]['tiencuoc'] = $ListUser[$dem]['tiencuoc'] + $res->tiencuoc;
                    }

                    $dem ++;
                }

                $UserTop = [];
                $st = 0;

                if ($dem > 1) {
                    // ?????m t???ng s??? ph???n t??? c???a m???ng
                    $sophantu = count($ListUser);
                    // L???p ????? s???p x???p
                    for ($i = 0; $i < $sophantu - 1; $i++)
                    {
                        // T??m v??? tr?? ph???n t??? l???n nh???t
                        $max = $i;
                        for ($j = $i + 1; $j < $sophantu; $j++){
                            if ($ListUser[$j]['tiencuoc'] > $ListUser[$max]['tiencuoc']){
                                $max = $j;
                            }
                        }
                        // Sau khi c?? v??? tr?? l???n nh???t th?? ho??n v???
                        // v???i v??? tr?? th??? $i
                        $temp = $ListUser[$i];
                        $ListUser[$i] = $ListUser[$max];
                        $ListUser[$max] = $temp;
                    }

                    $UserTop = $ListUser;
                } else {
                    $UserTop = $ListUser;
                }

                $UserTopTuan = [];
                $dem = 0;

                foreach ($UserTop as $row) {
                    if ( $dem < 5 ) {
                        $UserTopTuan[$dem] = $row;
                        $UserTopTuan[$dem]['sdt2'] = substr($row['sdt'], 0, 6).'******';
                        $dem ++;
                    }
                }


                $dem = 1;
                foreach ($UserTopTuan as $row) {
                    $SettingPhanThuongTop = new SettingPhanThuongTop;
                    $GetSettingPhanThuongTop = $SettingPhanThuongTop->where([
                        'top' => $dem,
                    ])->first();

                    $LichSuTraThuongTuan = new LichSuTraThuongTuan;
                    $LichSuTraThuongTuan->sdt = $row['sdt'];
                    $LichSuTraThuongTuan->sotien = $GetSettingPhanThuongTop->phanthuong;
                    $LichSuTraThuongTuan->status = 1;
                    $LichSuTraThuongTuan->save();    
                    
                    $dem ++;
                }

            }
        }

        echo "<br />".'+ L??u d??? li???u tr??? th?????ng ho??n t???t';
    }

    public function XuLiTraThuongTuan(request $request){
        return;
        $type_cron = 'xulitrathuongtuan';

        $LimitCron = new LimitCron;
        $GetLimitCron = $LimitCron->where([
            'type' => $type_cron,
        ])->orderBy('id', 'desc')->limit(1);

        if ($GetLimitCron->count() > 0){
            $GetLimitCron = $GetLimitCron->first();

            $time = time();
            if ( ($time - $GetLimitCron->time) <= 10 ) {
                //return 'Cron kh??ng th??? x??? l?? ngay l??c n???y';
            }
    
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        } else {
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        }

        $LichSuTraThuongTuan = new LichSuTraThuongTuan;
        $GetLichSuTraThuongTuan = $LichSuTraThuongTuan->where([
            'status' => 1,
        ])->limit(15)->get();

        $WEB2M = new WEB2M;
        foreach ($GetLichSuTraThuongTuan as $row) {
            $Check = $LichSuTraThuongTuan->where([
                'id' => $row->id,
            ])->first();

            if ($Check->status == 1) {

                //??ang x??? l??
                $Check->status = 2;
                $Check->save();

                //L???y t??i kho???n Momo
                $AccountMomo = new AccountMomo;
                $Account = $AccountMomo->where([
                    'status' => 1
                ])->first();

                //L???y message
                $ConfigMessageMomo = new ConfigMessageMomo;
                $GetMessageTraThuong = $ConfigMessageMomo->where([
                    'type' => 'thuong-top-tuan',
                ])->first();

                        $res = $WEB2M->Bank(
                            $Account->token,
                            $Check->sdt,
                            $Account->password,
                            $Check->sotien,
                            $GetMessageTraThuong->message.' '.$Check->magiaodich,
							$Account->webapi
                        );
    
                        
                        if ( isset($res['status']) && $res['status'] == 200) {
                            $Check->status = 3;
                            $Check->save();
                        } else {
                            $Check->status = 4;
                            $Check->save();
                        }
                        
                        var_dump($res);
                        
                        sleep(3);                

            }
        }
        echo 'X??? l?? giao d???ch ho??n t???t';
    }

    public function XuLiTraThuongTuanLoi(request $request){
        return;
        $type_cron = 'xulitrathuongtuanloi';

        $LimitCron = new LimitCron;
        $GetLimitCron = $LimitCron->where([
            'type' => $type_cron,
        ])->orderBy('id', 'desc')->limit(1);

        if ($GetLimitCron->count() > 0){
            $GetLimitCron = $GetLimitCron->first();

            $time = time();
            if ( ($time - $GetLimitCron->time) <= 10 ) {
                //return 'Cron kh??ng th??? x??? l?? ngay l??c n???y';
            }
    
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        } else {
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        }

        $LichSuTraThuongTuan = new LichSuTraThuongTuan;
        $GetLichSuTraThuongTuan = $LichSuTraThuongTuan->where([
            'status' => 4,
        ])->limit(15)->get();

        $WEB2M = new WEB2M;
        foreach ($GetLichSuTraThuongTuan as $row) {
            $Check = $LichSuTraThuongTuan->where([
                'id' => $row->id,
            ])->first();

            if ($Check->status == 4) {

                //??ang x??? l??
                $Check->status = 2;
                $Check->save();

                //L???y t??i kho???n Momo
                $AccountMomo = new AccountMomo;
                $Account = $AccountMomo->where([
                    'status' => 1
                ])->first();

                //L???y message
                $ConfigMessageMomo = new ConfigMessageMomo;
                $GetMessageTraThuong = $ConfigMessageMomo->where([
                    'type' => 'thuong-top-tuan',
                ])->first();
                        $res = $WEB2M->Bank(
                            $Account->token,
                            $Check->sdt,
                            $Account->password,
                            $Check->sotien,
                            $GetMessageTraThuong->message.' '.$Check->magiaodich,
							$Account->webapi
                        );
    
                        
                        if ( isset($res['status']) && $res['status'] == 200) {
                            $Check->status = 3;
                            $Check->save();
                        } else {
                            $Check->status = 4;
                            $Check->save();
                        }
                        
                        var_dump($res);
                        
                        sleep(3);
                
            }
        }
        echo "<br />".'+ X??? l?? giao d???ch ho??n t???t';
    }

    public function XuLiNoHuu(request $request){
        $type_cron = 'xulinohu';

        $LimitCron = new LimitCron;
        $GetLimitCron = $LimitCron->where([
            'type' => $type_cron,
        ])->orderBy('id', 'desc')->limit(1);

        if ($GetLimitCron->count() > 0){
            $GetLimitCron = $GetLimitCron->first();

            $time = time();
            if ( ($time - $GetLimitCron->time) <= 10 ) {
                //return 'Cron kh??ng th??? x??? l?? ngay l??c n???y';
            }
    
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        } else {
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        }

        //Setting n??? h??
        $NoHuu = new NoHuu;
        $Setting_NoHu = $NoHuu->first();

        $LichSuChoiNoHu = new LichSuChoiNoHu;
        $GetLichSuChoiNoHu = $LichSuChoiNoHu->where([
            'status' => 1,
        ])->limit(15)->get();

        $WEB2M = new WEB2M;
        foreach ($GetLichSuChoiNoHu as $row) {
            $Check = $LichSuChoiNoHu->where([
                'id' => $row->id,
            ])->first();

            if ($Check->status == 1) {

                //??ang x??? l??
                $Check->status = 2;
                $Check->save();

                //L???y t??i kho???n Momo
                $AccountMomo = new AccountMomo;
                $Account = $AccountMomo->where([
                    'status' => 1
                ])->first();

                //L???y message
                $ConfigMessageMomo = new ConfigMessageMomo;
                $GetConfigMessageMomo = $ConfigMessageMomo->where([
                    'type' => 'no-huu',
                ])->first();

                if ($Check->ketqua == 100){

                        $res = $WEB2M->Bank(
                            $Account->token,
                            $Check->sdt,
                            $Account->password,
                            $Check->tiennhan,
                            $GetMessageTraThuong->message.' '.$Check->magiaodich,
							$Account->webapi
                        );
    
                        
                        if ( isset($res['status']) && $res['status'] == 200) {
                            $Check->status = 3;
                            $Check->save();
                        } else {
                            $Check->status = 4;
                            $Check->save();
                        }
                        
                        var_dump($res);
                        
                        sleep(3);
                
                    
                } else {
                    $res->status = 3;
                    $res->save();
                }              
            }
        }
        echo "<br />".'+ X??? l?? giao d???ch ho??n t???t';
    }

    public function XuLiNoHuuLoi(request $request){
        $type_cron = 'xulinohuloi';

        $LimitCron = new LimitCron;
        $GetLimitCron = $LimitCron->where([
            'type' => $type_cron,
        ])->orderBy('id', 'desc')->limit(1);

        if ($GetLimitCron->count() > 0){
            $GetLimitCron = $GetLimitCron->first();

            $time = time();
            if ( ($time - $GetLimitCron->time) <= 10 ) {
                //return 'Cron kh??ng th??? x??? l?? ngay l??c n???y';
            }
    
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        } else {
            $LimitCron->type = $type_cron;
            $LimitCron->time = time();
            $LimitCron->save();
        }

        //Setting n??? h??
        $NoHuu = new NoHuu;
        $Setting_NoHu = $NoHuu->first();

        $LichSuChoiNoHu = new LichSuChoiNoHu;
        $GetLichSuChoiNoHu = $LichSuChoiNoHu->where([
            'status' => 4,
        ])->limit(15)->get();

        $WEB2M = new WEB2M;
        foreach ($GetLichSuChoiNoHu as $row) {
            $Check = $LichSuChoiNoHu->where([
                'id' => $row->id,
            ])->first();

            if ($Check->status == 4) {
                
                //??ang x??? l??
                $Check->status = 2;
                $Check->save();

                //L???y t??i kho???n Momo
                $AccountMomo = new AccountMomo;
                $Account = $AccountMomo->where([
                    'status' => 1
                ])->first();

                //L???y message
                $ConfigMessageMomo = new ConfigMessageMomo;
                $GetConfigMessageMomo = $ConfigMessageMomo->where([
                    'type' => 'no-huu',
                ])->first();

                if ($Check->ketqua == 1){

                        $res = $WEB2M->Bank(
                            $Account->token,
                            $Check->sdt,
                            $Account->password,
                            $Check->tiennhan,
                            $GetMessageTraThuong->message.' '.$Check->magiaodich,
							$Account->webapi
                        );
    
                        
                        if ( isset($res['status']) && $res['status'] == 200) {
                            $Check->status = 3;
                            $Check->save();
                        } else {
                            $Check->status = 4;
                            $Check->save();
                        }
                        
                        var_dump($res);
                        
                        sleep(3);
                
                } else {
                    $Check->status = 3;
                    $Check->save();
                }              
            }
        }
        echo "<br />".'+ X??? l?? giao d???ch ho??n t???t';
    }

}