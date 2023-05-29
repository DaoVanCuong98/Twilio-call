<?php

namespace App\Http\Controllers;
use App\Http\Requests;
use Illuminate\Http\Request;
use Twilio\Twiml\VoiceResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;


class IVRController extends Controller
{

    public function showWelcome()
    {
        $redis = Redis::connection();
        $hotel = DB::table('hotel')->get('room_id');
        $phone = '0333372332';
        $datauser = ['phone'=>$phone];
        $redis->set('user'.$phone,$phone);
        $response = new VoiceResponse(); 
        
        $gather = $response->gather(
            [
                'numDigits' => 1,
                'action' => route('test',$datauser, false),
            ]
        );
            $gather->say(
            
                'Thanks for calling the E T Phone Home Service.' .
                'Please press 1 for room '.$hotel[0]->room_id.'. Press 2 for room '.$hotel[1]->room_id.'.' .
                'Press 3 for room '.$hotel[2]->room_id.'.
                list of planets to call.',
            );

        return $response;
    }
    public function showMenuResponse(Request $request)
    {       
        $redis = Redis::connection();
        $response = new VoiceResponse();
        $selectedOption = $request->input('Digits');
        $room_id = DB::table('hotel')->Where('id','=',$selectedOption)->first();
        $choosroom = $room_id->room_id;
        $datauser = ['phone'=>$request->phone];
        $currentData = json_decode($redis->get('user'.$datauser['phone']), true);
        
        $user_id=$currentData['user_id'];
        $room = $currentData['room'];
        $redis->set('user'.$datauser['phone'], json_encode([
            'user_id'=>$user_id,
            'room' => $room]));

        $currentHour =  Carbon::now(); 
        $desiredTime = Carbon::today()->setTime(10, 0, 0);
        $array = [];
        if($currentHour->greaterThan($desiredTime)){
            for($i=1;$i<=7;$i++) {
                array_push($array,Carbon::today()->addDays($i)->format('d-m-Y'));      
            }
        }else{
            for($i=0;$i<=6;$i++) {
                array_push($array,Carbon::today()->addDays($i)->format('d-m-Y'));    
            }  
        }

        $gather = $response->gather(
                            [
                                'numDigits' => 1,
                                'action' => route('ngay', $datauser, false),
                            ]
                        );
                        $gather->say(
                            'Thank you for choosing room' .$choosroom. 'Please select the date and choosdate.Press 1 for day' .$array[0]. 'press 2 for day'.$array[1].'press 3 for day'.$array[2].
                            'press 4 for day'.$array[3]. 'press 5 for day'.$array[4]. 'press 6 for day'.$array[5]. 'press 7 for day'.$array[6]. ''
                        );
                        return $response;
        
    
    }
    public function choosdate(Request $request){

        $redis = Redis::connection();
        $response = new VoiceResponse();
        $datauser = ['phone'=>$request->phone];
        $arr = json_decode($redis->get('user'.$datauser['phone']), true);
        $selectedOption = $request->input('Digits');
        
        $currentHour =  Carbon::now(); 
        $desiredTime = Carbon::today()->setTime(10, 0, 0);
        $array = [];
        if($currentHour->greaterThan($desiredTime)){
            for($i=1;$i<=7;$i++){
                array_push($array,Carbon::today()->addDays($i)->format('d-m-Y'));      
            }
        }else{
            for($i=0;$i<=6;$i++) {
                array_push($array,Carbon::today()->addDays($i)->format('d-m-Y'));    
            }  
        }
        
        $date = $array[$selectedOption-1];
        $array1 = array('choosedate' => $date);  

        $data = array_merge($arr,$array1);
        $redis->set('user'.$datauser['phone'], json_encode($data));
        $gather = $response->gather(
            [
                'numDigits' => 1,
                'action' => route('menu-hour',$datauser, false),
            ]
        );

        $gather->say(
            'Thank you for choosing date' .$date. 'Press 1 is AM.Press 2 is PM'
        );

        return $response;
    
     
    }
    public function chooshour(Request $request){
        $selectedOption = $request->input('Digits');
        $response = new VoiceResponse();
        $datauser = ['phone'=>$request->phone];
        if($selectedOption==1){
           
            $gather = $response->gather(
                [
                    'numDigits' => 1,
                    'action' => route('hourAM', $datauser, false),
                ]
            );
            $arrayAM = [];
            for($i=1;$i<=4;$i++) {
                array_push($arrayAM,Carbon::today()->setTime(7+$i, 0, 0)->format('hA'));    
            } 
            $gather->say(
                'Thank you for choosing AM time. Here is a list of hours for you to choose. Key 1 is '.$arrayAM[0].'.Key 2 is '.$arrayAM[1].'.Key 3 is '.$arrayAM[2].'.Key 4 is '.$arrayAM[3].'.'
            );
        }else if($selectedOption==2){
            $gather = $response->gather(
                [
                    'numDigits' => 1,
                    'action' => route('hourPM', $datauser, false),
                ]
            );
            $arrayPM = [];
            for($i=1;$i<=4;$i++) {
            array_push($arrayPM,Carbon::today()->setTime(13+$i, 0, 0)->format('hA'));    
        }  
            $gather->say(
                'Thank you for choosing PM time. Here is a list of hours for you to choose. Key 1 is '.$arrayPM[0].'.Key 2 is '.$arrayPM[1].'.Key 3 is '.$arrayPM[2].'.Key 4 is '.$arrayPM[3].'.'
            );
        } 
        return $response; 
 
    }
    public function chooshourAM(Request $request){
        $redis = Redis::connection();
        $response = new VoiceResponse(); 
        $selectedOption = $request->input('Digits');
        $datauser = ['phone'=>$request->phone];
        $arrayAM = [];
        for($i=1;$i<=4;$i++) {
            array_push($arrayAM,Carbon::today()->setTime(7+$i, 0, 0)->format('hA'));    
        }
        $hourAM = $arrayAM[$selectedOption-1];
        $arr = json_decode($redis->get('user'.$datauser['phone']), true);
        $am = array('chooshour' => $hourAM);  
        $data1 = array_merge($arr,$am);
        $redis->set('user'.$datauser['phone'], json_encode($data1));
        $gather = $response->gather(
            [
                'numDigits' => 1,
                'action' => route('confirm',$datauser, false),
            ]
        );
            $gather->say(
            
                'Please press the 1 key to confirm the information.' 
                
            );
            return $response;
    }
    public function chooshourPM(Request $request){
        $redis = Redis::connection();
        $selectedOption = $request->input('Digits');
        $datauser = ['phone'=>$request->phone];
        $response = new VoiceResponse();
        $arrayPM = [];
        for($i=1;$i<=4;$i++) {
            array_push($arrayPM,Carbon::today()->setTime(13+$i, 0, 0)->format('hA'));     
        }
        $hourPM = $arrayPM[$selectedOption-1];
        $arr = json_decode($redis->get('user').$datauser['phone'], true);
        $pm = array('chooshour' => $hourPM);  
        $data1 = array_merge($arr,$pm);
        $redis->set('user'.$datauser['phone'], json_encode($data1));
        $gather = $response->gather(
            [
                'numDigits' => 1,
                'action' => route('confirm', $datauser, false),
            ]
        );
            $gather->say(
            
                'Please press the 1 key to confirm the information.' 
                
            );
            
            return $response;

    }
    public function confirmcustomer(Request $request){
        $selectedOption = $request->input('Digits');
        $redis = Redis::connection();
        $datauser = ['phone'=>$request->phone];
        $data = json_decode($redis->get('user'.$datauser['phone']));//object
        if($selectedOption==1){   
        $room = $data->room;
        $choosdate = $data->choosedate;
        $chooshour =  $data->chooshour;
        $user_id =  $data->user_id;
         DB::table("user_booking")->insert(["room"=>$room,"choosdate"=>$choosdate,"chooshour"=>$chooshour,"user_id"=>$user_id]);
        }

    }
    
    }