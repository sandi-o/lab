<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use App\User;
use App\Lab;
use App\LabLog;
use Illuminate\Foundation\Testing\WithFaker;

class LabTest extends TestCase
{
    use WithFaker;
    /**
     * Show all lab records with their current values
     *
     * @return void
     */
    public function testApiLabRecords()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');
        
        $this->getJson('api/labs/get_all_records')
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    [
                        'id',
                        'code',
                        'content',
                        'created_at',
                        'updated_at'
                    ]
                ]
            );
    }

        /**
     * Create a Lab Record
     *
     * @return void
     */
    public function testApiCreateLabRecord()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');
        
        $labData = ["data" => '{"mainkey1": {"key1":"val1", "key2":"val2"}}'];

        $response = $this->postJson('api/labs',$labData)
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    'status',
                    'message',
                    'data'
                ]
            );
    }

    /**
     * update a Lab Record
     * @return void
     */
    public function testApiUpdateLabRecord()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');
        
        $labRecord = factory(Lab::class)->create();

        $labData = ["data" => '{"'.$labRecord->code.'": {"key1":"val1", "key2":"val2"}}'];

        $response = $this->postJson('api/labs',$labData)
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    'status',
                    'message',
                    'data'
                ]
            );
    }

    /**
     * Show a lab record without timestamp in request
     */
    public function testApiRetrieveLabRecordWithoutTimestamp() 
    {
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');
        
        $labRecord = factory(Lab::class)->create();
        
        $this->getJson('api/labs/'.$labRecord->code)
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    'status',
                    'message',
                    'data'
                ]
            );
    }

    /**
     * Show a lab record with timestamp in the request
     */
    public function testApiRetrieveLabRecordWithTimestamp()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');
        
        $labRecord = factory(Lab::class)->create();
        
        $this->getJson('api/labs/'.$labRecord->code,['unix_timestamp'=> strtotime($labRecord->created_at)])
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    'status',
                    'message',
                    'data'
                ]
            );
    }
}
