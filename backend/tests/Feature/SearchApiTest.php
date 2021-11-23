<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Ride;
use App\Models\User;

class SearchApiTest extends TestCase
{
    /**
     * データ準備
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::where('id', 1)->first();
        $this->post = Ride::where('id', 1)->first();

        $this->other_user = User::where('id', 2)->first();

        $this->searchWords = [
            'test',
            'テストユーザ',
            'テスト',
            '1',
        ];
    }

    public function test_search()
    {

        $url_all = '/api/search/'.$this->searchWords[0].'/all?page=2';
        $url_rides = '/api/search/'.$this->searchWords[0].'/rides?page=1';
        $url_users = '/api/search/'.$this->searchWords[0].'/users?page=2';

        $response = $this->getJson($url_rides);

        // 非ログイン時
        $response->assertStatus(200)
                ->assertJsonFragment(['publish_status' => 0]) // 公開設定のライド
                ->assertJsonMissing(['publish_status' => 1])  // 限定公開設定のライド
                ->assertJsonMissing(['publish_status' => 2]); // 非公開設定のライド


        // ログイン時 (フォロワーあり)
        $response = $this->actingAs($this->user)->getJson($url_rides);

        $response->assertStatus(200)
                ->assertJsonFragment(['publish_status' => 0]) // 公開設定のライド
                ->assertJsonFragment(['publish_status' => 1])  // 限定公開設定のライド
                ->assertJsonMissing(['publish_status' => 2]); // 非公開設定のライド

        // ログイン時 (フォロワーなし)
        $response = $this->actingAs($this->other_user)->getJson($url_rides);

        $response->assertStatus(200)
                ->assertJsonFragment(['publish_status' => 0]) // 公開設定のライド
                ->assertJsonMissing(['publish_status' => 1])  // 限定公開設定のライド
                ->assertJsonMissing(['publish_status' => 2]); // 非公開設定のライド
    }
}
