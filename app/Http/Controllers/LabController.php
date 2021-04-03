<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\ApiResponse;
use App\Lab;
use App\LabLog;
use Carbon\Carbon;

class LabController extends Controller
{
    use ApiResponse;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * show all record in the resource
     */
    public function showAll() {
        return Lab::all();
    }

    /**
     * Update or Create a record in the given resource
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response json
     */
    public function upstore(Request $request)
    {
        $valid = $this->validateIncomingData($request);
        
        if($valid) {
            $results = array();
            foreach(json_decode($request->data,true) as $key => $values) {
                if(!$this->validKeyDataType($key)) {
                    return $this->error('Invalid data format.',422);
                }
     
                $lab = Lab::where('code',$key)->first();

                if(!$lab) {
                    $labResult = Lab::create(['code'=> $key, 'content'=> json_encode($values)]);
                    
                    if($labResult) {
                        $results['data'][] = $labResult;
                        $results['message'][] = 'Record successfully created!';
                    }
                } else {
                    $labResult = $lab->update(['content'=> json_encode($values)]);
                    
                    if($labResult) {
                        $results['data'][] = $lab;
                        $results['message'][] = 'Record successfully updated!';
                    }
                }
            }  
       
            return $this->success($results['data'],$results['message'], 200);
        } else {
            return $this->error('Invalid data format.',422);
        }
        
    }

    /**
     * find a given resource base on code
     * @param Int $code
     * @param UNIX_timestamp $unix_timestamp
     * @return \Illuminate\Http\Response json
     */
    public function show(Request $request, $code) {        
        $lab = LabLog::where('code',$code);
        
        if(isset($request->unix_timestamp)) {
            $validTimestamp = $this->validateTimestampFormat($request->unix_timestamp);
            
            if($validTimestamp) {
                $lab->where('created_at',$validTimestamp);
            } else {
                return $this->error('Invalid timestamp format.',422);
            }            
        } else {
            $lab->latest();
        }
                
        $result = $lab->first();
        
        if($result) {
            return $this->success($result->content,'Record Found.',200);
        } else {
            return $this->error('Record Not Found.',404);
        }
    }

    /**
     * Incoming data validation rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Boolean
     */
    protected function validateIncomingData($request)
    {
        json_decode($request->data,true);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * key validation rules.
     *
     * @param  String  $key
     * @return Boolean
     */
    protected function validKeyDataType($key) {
        return is_string($key);
    }

    /**
     *  timestamp validation rule
     * 
     * @param UnixTimestamp $unix_timestamp 
     * @return Boolean/String
     */
    protected function validateTimestampFormat($unix_timestamp) {
        if(((string) (int) $unix_timestamp === $unix_timestamp) && ($unix_timestamp <= PHP_INT_MAX) && ($unix_timestamp >= ~PHP_INT_MAX)) {
            return Carbon::createFromTimestamp($unix_timestamp)->toDateTimeString();
        } else {
            return false;
        }
    }

}
