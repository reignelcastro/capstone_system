<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Service;
use App\Requirement;
use App\Flowchart;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

class ServicesController extends Controller
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
        $result     = Service::where('is_archived',false)->orderBy('services_id')->get();
        return view('services.index')->with(['result' => $result]);
    }

    public function store(Request $request){
        $rules = array(
            'aics_services' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string']
        );

        $error  = Validator::make($request->all(),$rules);
            
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $services_id        = $request->input('services_id');
        $aics_services      = $request->input('aics_services');
        $description        = $request->input('description');
        $id                 = Auth::user()->id;

        if(!empty($services_id))
        {
            $new    = Service::where('services_id',$services_id)->first();
            $new->aics_services = $aics_services;
            $new->description   = $description;
        }
        else
        {
            $new    = new Service;
            $new->aics_services = $aics_services;
            $new->description   = $description;
            $new->id            = $id;
        }
        

        try{
            $new->save();
            $pk   = $new->services_id;
            return response()->json([
                'success' => true,
                'services_id' => $pk
            ]);
        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }

    public function save(Request $request){
        $rules = array(
            'requirement_description' => ['required', 'string', 'max:255'],
            'requirement_type' => ['required', 'string'],
            'services_id' => ['required', 'integer'],
        );

        $error  = Validator::make($request->all(),$rules);
            
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $requirement_description = $request->input('requirement_description');
        $requirement_type        = $request->input('requirement_type');
        $services_id             = $request->input('services_id');
        $id                      = Auth::user()->id;
        
        $new    = new Requirement;

        $new->requirement_description   = $requirement_description;
        $new->requirement_type          = $requirement_type;
        $new->services_id               = $services_id;
        $new->id                        = $id;

        try{
            $new->save();
            return response()->json([
                'success' => true
            ]);
        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }

    public function flowchart(Request $request){
        $rules = array(
            'instruction' => ['required', 'string', 'max:255'],
            'services_id' => ['required', 'integer'],
        );

        $error  = Validator::make($request->all(),$rules);
            
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $instruction        = $request->input('instruction');
        $services_id        = $request->input('services_id');
        $id                 = Auth::user()->id;
        
        $new    = new Flowchart;

        $new->instruction       = $instruction;
        $new->services_id       = $services_id;
        $new->id                = $id;

        try{
            $new->save();
            return response()->json([
                'success' => true
            ]);
        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }

    public function load(Request $request){
        $services_id    = $request->input('services_id');
        $r              = Service::where('services_id',$services_id)->first();

        $actions        = '<a href="javascript:void(0)" 
                                class="flowchart green mr-5 hidden"
                                data-services_id="'.$services_id.'">
                            <i class="bi bi-diagram-2-fill"><i class="mt-0 fa fa-plus w3-small"></i></i>
                            </a>

                            <a href="javascript:void(0)" 
                                class="edit"
                                data-services_id="'.$services_id.'"
                                data-aics_services="'.strtoupper($r->aics_services).'"
                                data-description="'.$r->description.'">
                            <i class="bi bi-pencil-square green"></i>
                            </a>
                            <a href="javascript:void(0)" 
                                class="delete"
                                data-action="archive"
                                data-services_id="'.$services_id.'">
                             <i class="fa fa-remove green"></i>
                            </a>';

        
        $description    = '<div class="col-md-12 w3-border p-1">'.$r->description.'</div>';

        

        $result     = Requirement::where('services_id',$services_id)->get();
        $html       = "";
        $html       .= '<div class="col-md-12 w3-border p-1">';
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $html .= '<div class="row border-bottom w-100 m-0" id="req_'.$row->requirement_id.'">
                            <div class="col-sm-6"><b>'.strtoupper($row->requirement_description).'</b></div>
                            <div class="col-sm-4"><b>'.strtoupper($row->requirement_type).'</b></div>
                            <div class="col-sm-2">
                                <a href="#" 
                                    class="remove"
                                    data-table="requirements"
                                    data-requirement_id="'.$row->requirement_id.'"
                                    data-services_id="'.$row->services_id.'">
                                <i class="bi bi-trash-fill green"></i>
                                </a>
                            </div>
                        </div>';
            }
        }
        else
        {
            $html .= '<center><i>No requirement was added on this service.</i></center>';
        }
        
        $html       .= '</div>';

        $flowchart     = Flowchart::where('services_id',$services_id)->first();
        if(!empty($flowchart))
        {
            $fc_file       = '<p class="text-right m-0 w-100">
                                <a href="#" 
                                        class="btn btn-success remove p-1 px-2"
                                        data-table="flowchart"
                                        data-flowchart_id="'.$flowchart->flowchart_id.'"
                                        data-services_id="'.$flowchart->services_id.'">
                                    <i class="fa fa-remove m-0 p-0"></i>
                                </a>
                            </p>
                            <img style="width:100%;max-width:100%;" src="storage/flowcharts/'.$flowchart->instruction.'">';
        }
        else
        {
            $fc_file        = "";
        }

        // $fc       = "";
        // $fc       .= '<div class="col-md-12 p-1">';
        // if(count($flowchart) > 0)
        // {
        //     foreach($flowchart as $rows)
        //     {
        //         $fc .= '<div class="row justify-content-center" id="fc_'.$rows->flowchart_id.'">
        //                     <div class="col-sm-6">
        //                         <div class="col-md-12 card p-0">
        //                         <p class="text-right m-0 p-0">
        //                             <a href="#" 
        //                                 class="remove mx-2 p-0"
        //                                 data-table="flowchart"
        //                                 data-flowchart_id="'.$rows->flowchart_id.'"
        //                                 data-services_id="'.$rows->services_id.'">
        //                             <i class="fa fa-remove m-0 p-0 green"></i>
        //                             </a>
        //                         </p>
        //                         <center>
        //                             <b>'.$rows->instruction.'</b>
        //                         </center>
        //                         </div>
        //                         <center>
        //                             <i class="fa fa-arrow-down green"></i>
        //                         </center>
        //                     </div>
        //                 </div>';
        //     }
        //     $fc .= '<div class="row justify-content-center">
        //                 <div class="col-sm-6">
        //                     <div class="col-md-12 card p-2">
        //                     <center>
        //                         <b>DONE</b>
        //                     </center>
        //                     </div>
        //                 </div>
        //             </div>';
        // }
        // else
        // {
        //     $fc .= '<center><i>No flowchart was added on this service.</i></center>';
        // }
        
        // $fc       .= '</div>';


        return response()->json([
            'success' => true,
            'html' => $html,
            'flowchart' => $fc_file,
            'services_id' => $services_id,
            'description' => $description,
            'actions' => $actions
        ]);
    }

    public function delete(Request $request){
        $services_id     = $request->input('services_id');
        $action          = $request->input('action');

        if(empty($services_id))
        {
            return response()->json(['error' => "Submitted data is empty!"]);
        }

        $data  = Service::where('services_id',$services_id)->first();

        if(empty($data))
        {
            return response()->json(['error' => "Invalid request detected!"]);
        }

        if($action === 'archive')
        {
            $data->is_archived  = true;
        }
        elseif($action === 'restore')
        {
            $data->is_archived  = false;
        }
        else
        {
            Requirement::where('services_id',$services_id)->delete();
        }

        try{
            if($action === 'archive')
            {
                $data->save();
            }
            elseif($action === 'restore')
            {
                $data->save();
            }
            else
            {
                $data->delete();
            }
            return response()->json([
                'success' => 'Record has been successfully '.$action.'d!',
            ]);

        }catch(QueryException $e){
            return response()->json(['errors' => $e->errorInfo[2]]);
        }
    }

    public function remove(Request $request){
        $table          = $request->input('table');
        $id             = $request->input('id');

        if(empty($id))
        {
            return response()->json(['error' => "Submitted data is empty!"]);
        }

        if($table === "requirements")
        {
            $data  = Requirement::where('requirement_id',$id)->first();
        }
        else
        {
            $data  = Flowchart::where('flowchart_id',$id)->first();
        }


        if(empty($data))
        {
            return response()->json(['error' => "Invalid request detected!"]);
        }

        try{
            $data->delete();
            return response()->json([
                'success' => 'Record has been removed.',
            ]);

        }catch(QueryException $e){
            return response()->json(['errors' => $e->errorInfo[2]]);
        }
    }

    public function upload(Request $request){
        if ($request->ajax()) {
            
            $rules  = array(
                'flowchart_file' => ['image','required','max:1999']
            );

            $error  = Validator::make($request->all(),$rules);        
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            if ($request->hasFile('flowchart_file')) 
            {
                $filenameWithExtension   = $request->file('flowchart_file')->getClientOriginalName();

                $file                    = pathinfo($filenameWithExtension, PATHINFO_FILENAME);

                $extension               = $request->file('flowchart_file')->getClientOriginalExtension();

                $toStore                 = $file.'_'.time().'.'.$extension;
            }
            else
            {
                $toStore    = 'n/a';
            }

            $services_id    = $request->input('services_id');
            $get            = Flowchart::where('services_id',$services_id)->first();

            if (!empty($get)) 
            {
                $filex   = 'public/flowcharts/'.$get->instruction;
                if (Storage::exists($filex)) 
                {
                    Storage::delete($filex);   
                }

                $data   = Flowchart::where('services_id',$services_id)->first();
            }
            else
            {
                $data   = new Flowchart;
            }

            $data->instruction  = $toStore;
            $data->services_id  = $services_id;
            $data->id           = Auth::user()->id;

            try{
                $data->save();
                $path    = $request->file('flowchart_file')->storeAs('public/flowcharts',$toStore);
                return response()->json([
                    'success' => 'File has been successfully uploaded.',
                    'file' => $toStore
                ]);
            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }
}
