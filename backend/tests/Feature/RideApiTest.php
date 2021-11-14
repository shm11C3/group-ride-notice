<?php

namespace Tests\Feature;

use App\Models\Follow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Ride;
use App\Models\User;

class RideApiTest extends TestCase
{

    /**
     * データ準備
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::where('id', 1)->first();
        $this->post = Ride::where('id', 1)->first();
    }

    public function testGetRides()
    {
        $testingApi_urls = [
            'all' => '/api/get/rides/0/0/0/0?page=1',
            'filter_date' => '/api/get/rides/4/0/0/0?page=1',
            'filter_prefecture' => '/api/get/rides/0/14/0/0?page=1',
            'filter_intensity' => '/api/get/rides/0/0/5/0?page=1',
            'all_follows' => '/api/get/rides/0/0/0/1?page=1',
            'filter_date_follows' => '/api/get/rides/4/0/0/1?page=1',
            'filter_prefecture_follows' => '/api/get/rides/0/14/0/1?page=1',
            'filter_intensity_follows' => '/api/get/rides/0/0/5/1?page=1',
        ];

        foreach($testingApi_urls as $testingApi_url){
            $response = $this->getJson($testingApi_url); // すべてのライドを取得

            //検証 (非ログイン時)
            dump($testingApi_url);
            $response->assertStatus(200)
                ->assertJsonFragment(['publish_status' => 0]) // 公開設定のライド
                ->assertJsonMissing(['publish_status' => 1])  // 限定公開設定のライド
                ->assertJsonMissing(['publish_status' => 2]); // 非公開設定のライド
        }

        // ログイン時
        foreach($testingApi_urls as $testingApi_url){
            $response = $this->actingAs($this->user)->getJson($testingApi_url); // すべてのライドを取得

            //検証 (ログイン時)
            dump($testingApi_url);
            $response->assertStatus(200)
                ->assertJsonFragment(['publish_status' => 0]); // 公開設定のライド
        }

        $response = $this->getJson($testingApi_urls['all_follows']);
        $response->assertJsonMissing(['publish_status' => 2])
            ->assertJsonMissing(['host_user_uuid' => $this->user->uuid]); // 非公開設定のライド

        $response = $this->getJson($testingApi_urls['all']);

        $response->assertJsonFragment([
                // 自アカウントの限定公開設定のライドは取得できる
                'host_user_uuid' => $this->user->uuid,
                'publish_status' => 1
            ])->assertJsonFragment([
                // 自アカウントの限定公開設定のライドは取得できる
                'host_user_uuid' => $this->user->uuid,
                'publish_status' => 2
            ]);
    }
}
