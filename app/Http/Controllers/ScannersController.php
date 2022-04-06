<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Location;
use App\Address;
use App\Passenger;
use App\Log;
use App\LogHistory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

use Twilio\Rest\Client;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ScannersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('logs.options');
    }

    public function scan($option){
        $options = array("am-in", "am-out", "pm-in", "pm-out");

        if (in_array($option, $options))
        {
            return view('logs.index')->with('option',$option);
        }
        else
        {
            return redirect('/admin/scan');
        }
    }

    public function count(Request $request){
        $type   = $request->input('type');
        $id     = Auth::user()->id;
        $location_id    = Auth::user()->location_id;
        $today  = date('Y-m-d');
        
        $result2     = LogHistory::where('log_type',$type)
                    ->where('log_date',$today)
                    ->where('id',$id)
                    ->get();
        try{
            return response()->json([
                'count2' => count($result2),
            ]);
        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }

    public function load(Request $request){
        $today          = date('Y-m-d');
        $id             = Auth::user()->id;
        $location_id    = Auth::user()->location_id;

        $result         = Log::where('id',$id)->where('location_id',$location_id)
                            ->where('log_date',$today)
                            ->orderBy('updated_at','DESC')->get();

        $html   = "";
        foreach($result as $row)
        {
            $rows   = Passenger::where('passenger_id',$row->passenger_id)->first();
            $html .= '<tr id="logs_'.$row->log_id.'">
                        <td>'.strtoupper($rows->student_code).'</td>
                        <td>'.strtoupper($rows->student_id).'</td>
                        <td>'.strtoupper($rows->lastname.' '.$rows->suffix.', '.$rows->firstname.' '.$rows->middlename).'</td>';

                        if(!empty($row->am_in))
                        {
                            $html .='<td>'.date('h:i:sa',strtotime($row->am_in)).'</td>';
                        }
                        else
                        {
                            $html .='<td></td>';
                        }
                        if(!empty($row->am_out))
                        {
                            $html .='<td>'.date('h:i:sa',strtotime($row->am_out)).'</td>';
                        }
                        else
                        {
                            $html .='<td></td>';
                        }

                        if(!empty($row->pm_in))
                        {
                            $html .='<td>'.date('h:i:sa',strtotime($row->pm_in)).'</td>';
                        }
                        else
                        {
                            $html .='<td></td>';
                        }
                        if(!empty($row->pm_out))
                        {
                            $html .='<td>'.date('h:i:sa',strtotime($row->pm_out)).'</td>';
                        }
                        else
                        {
                            $html .='<td></td>';
                        }
                        $html .='<td>'.date('F d, Y h:i:sa',strtotime($row->updated_at)).'</td>
                    </tr>';
        }

        try{
            return response()->json([
                'html' => $html
            ]);
        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }

    private function sendMessage($message, $recipients)
    {
        $account_sid = env("TWILIO_SID");
        $auth_token = env("TWILIO_AUTH_TOKEN");
        $twilio_number = env("TWILIO_NUMBER");

        $client = new Client($account_sid, $auth_token);

        // $client->messages->create($recipients, 
        //         ['from' => $twilio_number, 'body' => $message]);

        try{
            $client->messages->create($recipients, 
                ['from' => $twilio_number, 'body' => $message]);
            
        }catch(Exception $e){
            return $e->getCode() . ' : ' . $e->getMessage()."<br>";
        }
    }

    private function sendMail($body, $email)
    {
        try {
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = env('MAIL_PORT');

            //Recipients
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), 'pasaHERO');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'PasaHERO Alert Message';
            $mail->Body    = $body;
            $mail->Send();
            logger()->error("Email has been sent!");
        } catch (Exception $e) {
            logger()->error("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }



    public function save(Request $request){
        if($request->ajax())
        {
            $rules = array(
                'type' => ['required','string'],
                'passenger_id' => ['required','numeric'],
                'student_code' => ['required','numeric']
            );
    
            $error  = Validator::make($request->all(),$rules);
                
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $type           = $request->input('type');
            
            $passenger_id   = $request->input('passenger_id');
            $student_code   = $request->input('student_code');
            $log_date       = date('Y-m-d');
            $time           = date('H:i:s');
            $location_id    = Auth::user()->location_id;
            $id             = Auth::user()->id;


            $options = array("am-in", "am-out", "pm-in", "pm-out");
            if (!in_array($type, $options))
            {
                return response()->json(['error' => 'Submitted data is invalid.']);
            }

            $checks  = Passenger::where('passenger_id',$passenger_id)
                            ->first();
            if (empty($checks))
            {
                return response()->json(['error' => 'Record not recognized!']);
            }

            $check  = Log::where('passenger_id',$passenger_id)
                            ->where('student_code',$student_code)
                            ->where('log_date',$log_date)
                            ->first();
            
            if(empty($check))
            {
                $data   = new Log;
                $data->passenger_id     = $passenger_id;
                $data->student_code     = $student_code;
                $data->log_date         = $log_date;
                if($type === 'am-in')
                {
                    $data->am_in        = $time;
                }
                elseif($type === 'am-out')
                {
                    $data->am_out       = $time;
                }
                elseif($type === 'pm-in')
                {
                    $data->pm_in       = $time;
                }
                elseif($type === 'pm-out')
                {
                    $data->pm_out       = $time;
                }
                else
                {
                    return response()->json(['error' => 'Submitted data is invalid!']);
                }

                $data->location_id     = $location_id;
                $data->id              = $id;
            }
            else
            {
                $data   = Log::where('passenger_id',$passenger_id)
                            ->where('student_code',$student_code)
                            ->where('log_date',$log_date)
                            ->first();
                            
                if($type === 'am-in' && empty($check->am_in))
                {
                    $data->am_in        = $time;
                }
                elseif($type === 'am-out' && empty($check->am_out))
                {
                    $data->am_out       = $time;
                }
                elseif($type === 'pm-in' && empty($check->pm_in))
                {
                    $data->pm_in       = $time;
                }
                elseif($type === 'pm-out' && empty($check->pm_out))
                {
                    $data->pm_out       = $time;
                }
                else
                {
                    return response()->json(['error' => 'Student has already logged on selected time.']);
                }
            }

            try{
                $rows2       = Passenger::where('passenger_id',$passenger_id)->first();
                $u           = User::where('id',$id)->first();
                $location    = Location::where('location_id',$location_id)->first();

                $recipient   = '+63'.$rows2->guardian_number;
                $message     = strtoupper($rows2->lastname.' '.$rows2->suffix.', '.$rows2->firstname.' '.$rows2->middlename).' has been picked up by the School Bus at '.date('F j, Y h:i:sa').' : '.strtoupper($type).' (Logged by : '.strtoupper($u->lastname.' '.$u->suffix.', '.$u->firstname.' '.$u->middlename).' || Vehicle Plate # : '.strtoupper($u->plate_number).' || sent by : '.strtoupper($location->name).')';
                $this->sendMessage($message,$recipient);

                
                $data->save();

                $last_id    = $data->log_id;
                $row         = Log::where('log_id',$last_id)->first();

                $history    = new LogHistory;
                $history->log_id        = $last_id;
                $history->passenger_id  = $passenger_id;
                $history->log_type      = $type;
                $history->sms           = $message;
                $history->log_date      = $log_date;
                $history->location_id   = $location_id;
                $history->id            = $id;
                $history->save();



                $rows3       = User::where('id',$id)->first();

                $row2           = DB::table('regions')->where('id',$rows2->region)->first();
                $row3           = DB::table('provinces')->where('provCode',$rows2->province)->first();
                $row4           = DB::table('cm')->where('citymunCode',$rows2->city_municipality)->first();
                $row5           = DB::table('brgy')->where('brgyCode',$rows2->barangay)->first();

                $html   ="";
                $html2  ="";

                if($type === 'am-in')
                {
                    $status     = 'TIME IN : AM';
                }
                elseif($type === 'am-out')
                {
                    $status     = 'TIME OUT : AM';
                }
                elseif($type === 'pm-in')
                {
                    $status     = 'TIME IN : PM';
                }
                elseif($type === 'pm-out')
                {
                    $status     = 'TIME OUT : PM';
                }

                $html2 .= '<tr id="logs_'.$row->log_id.'">
                        <td>'.strtoupper($rows2->student_code).'</td>
                        <td>'.strtoupper($rows2->student_id).'</td>
                        <td>'.strtoupper($rows2->lastname.' '.$rows2->suffix.', '.$rows2->firstname.' '.$rows2->middlename).'</td>';

                        if(!empty($row->am_in))
                        {
                            $html2 .='<td>'.date('h:i:sa',strtotime($row->am_in)).'</td>';
                        }
                        else
                        {
                            $html2 .='<td></td>';
                        }
                        if(!empty($row->am_out))
                        {
                            $html2 .='<td>'.date('h:i:sa',strtotime($row->am_out)).'</td>';
                        }
                        else
                        {
                            $html2 .='<td></td>';
                        }

                        if(!empty($row->pm_in))
                        {
                            $html2 .='<td>'.date('h:i:sa',strtotime($row->pm_in)).'</td>';
                        }
                        else
                        {
                            $html2 .='<td></td>';
                        }
                        if(!empty($row->pm_out))
                        {
                            $html2 .='<td>'.date('h:i:sa',strtotime($row->pm_out)).'</td>';
                        }
                        else
                        {
                            $html2 .='<td></td>';
                        }
                        $html2 .='<td>'.date('F d, Y h:i:sa',strtotime($row->updated_at)).'</td>
                    </tr>';

                $html .= '<div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <center>';
                                        if($rows2->profile_picture == 'default_photo.png')
                                        {
                                            $html .= '<img src="'.asset('images/default_photo.png').'" height="200" width="200" alt="USER">';
                                        }
                                        else
                                        {
                                            $html .= '<img src="/storage/user_images/'.$rows2->profile_picture.'" height="200" class="profile_picture" width="200" alt="USER">';
                                        }
                                        $html .= '</center>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-12 alert-info" style="border-radius:10px;">
                                        <center>
                                            <h5>
                                                <b>'.$status.'</b><br />
                                                <b>'.date('h:i:s a',strtotime($time)).'</b>
                                            </h5>
                                        </center>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-md-right">
                                        <label>
                                            CODE
                                        </label>
                                    </div>
                                    <div class="col-md-8">
                                        <b class="uppercase">
                                            '.$rows2->student_code.'
                                        </b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-md-right">
                                        <label>
                                            STUDENT ID
                                        </label>
                                    </div>
                                    <div class="col-md-8">
                                        <b class="uppercase">
                                            '.$rows2->student_id.'
                                        </b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-md-right">
                                        <label>
                                            NAME
                                        </label>
                                    </div>
                                    <div class="col-md-8">
                                        <b class="uppercase">
                                            '.$rows2->lastname.' '.$rows2->suffix.', '.$rows2->firstname.' '.$rows2->middlename.'
                                        </b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-md-right">
                                        <label>
                                            ADDRESS
                                        </label>
                                    </div>
                                    <div class="col-md-8">
                                        <b class="uppercase">
                                            '.$rows2->address_line.' '.$row5->brgyDesc.' '.$row4->citymunDesc.' '.$row3->provDesc.'
                                        </b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-md-right">
                                        <label>
                                            CONTACT NUMBER
                                        </label>
                                    </div>
                                    <div class="col-md-8">
                                        <b class="uppercase">
                                            '.$rows2->contact_number.'
                                        </b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-md-right">
                                        <label>
                                            EMAIL
                                        </label>
                                    </div>
                                    <div class="col-md-8">
                                        <b class="uppercase">
                                            '.$rows2->email.'
                                        </b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-md-right">
                                        <label>
                                            GUARDIAN NAME
                                        </label>
                                    </div>
                                    <div class="col-md-8">
                                        <b class="uppercase">
                                            '.$rows2->guardian_name.'
                                        </b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-md-right">
                                        <label>
                                            GUARDIAN NUMBER
                                        </label>
                                    </div>
                                    <div class="col-md-8">
                                        <b class="uppercase">
                                            (+63)'.$rows2->guardian_number.'
                                        </b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-md-right">
                                        <label>
                                            GUARDIAN ADDRESS
                                        </label>
                                    </div>
                                    <div class="col-md-8">
                                        <b class="uppercase">
                                            '.$rows2->guardian_address.'
                                        </b>
                                    </div>
                                </div>
                            </div>
                        </div>';

                return response()->json([
                    'success' => 'Image has been successfully uploaded.',
                    'html' => $html,
                    'html2' => $html2,
                    'last_id' => $last_id
                ]);
            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }
}
