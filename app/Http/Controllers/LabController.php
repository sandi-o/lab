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
     * @OA\Get(
     *      path="/api/labs/get_all_records",
     *      operationId="getLabRecords",
     *      tags={"Labs"},
     *      summary="Get All lab Records",
     *      description="Returns Json Array of all records data and their values currently stored in the DB",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      security={
     *         {"passport": {}}
     *     }
     * )
     */
    public function showAll() {
        return Lab::all();
    }

    /**
     * @OA\Post(
     *     path="/api/labs",
     *     tags={"Labs"},
     *     summary="Create or Update a lab record",
     *     operationId="updstore",
     *     @OA\Parameter(
     *          name="data",
     *          description="contains JSON format key value {mykey : value1}",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="Json"
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
    *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     security={
     *         {"passport": {}}
     *     }   
     * )
     */
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
            foreach(json_decode($request->data) as $key => $values) {
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
     * @OA\Get(
     *      path="/api/labs/{code}",
     *      operationId="getLabByCode",
     *      tags={"Labs"},
     *      summary="Get lab Record information",
     *      description="Returns lab data base on the code given AND when given a timestamp, return whatever the value of the key at thetime was",
     *      @OA\Parameter(
     *          name="code",
     *          description="code",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="String"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="unix_timestamp",
     *          description="unix timestamp",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="String, Int"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found"
     *      ),
     *      security={
     *         {"passport": {}}
     *     }  
     * )
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
