<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WithdrawTest extends TestCase
{
    use  RefreshDatabase;

    protected string $endpoint = '/api/withdraws';
    protected string $tableName = 'withdraws';

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateWithdraw(): void
    {
        $this->markTestIncomplete('This test case needs review.');

        $this->actingAs(User::factory()->create());

        $payload = Withdraw::factory()->make([])->toArray();

        $this->json('POST', $this->endpoint, $payload)
             ->assertStatus(201)
             ->assertSee($payload['name']);

        $this->assertDatabaseHas($this->tableName, ['id' => 1]);
    }

    public function testViewAllWithdrawsSuccessfully(): void
    {
        $this->markTestIncomplete('This test case needs review.');

        $this->actingAs(User::factory()->create());

        Withdraw::factory(5)->create();

        $this->json('GET', $this->endpoint)
             ->assertStatus(200)
             ->assertJsonCount(5, 'data')
             ->assertSee(Withdraw::first(rand(1, 5))->name);
    }

    public function testViewAllWithdrawsByFooFilter(): void
    {
        $this->markTestIncomplete('This test case needs review.');

        $this->actingAs(User::factory()->create());

        Withdraw::factory(5)->create();

        $this->json('GET', $this->endpoint.'?foo=1')
             ->assertStatus(200)
             ->assertSee('foo')
             ->assertDontSee('foo');
    }

    public function testsCreateWithdrawValidation(): void
    {
        $this->markTestIncomplete('This test case needs review.');

        $this->actingAs(User::factory()->create());

        $data = [
        ];

        $this->json('post', $this->endpoint, $data)
             ->assertStatus(422);
    }

    public function testViewWithdrawData(): void
    {
        $this->markTestIncomplete('This test case needs review.');

        $this->actingAs(User::factory()->create());

        Withdraw::factory()->create();

        $this->json('GET', $this->endpoint.'/1')
             ->assertSee(Withdraw::first()->name)
             ->assertStatus(200);
    }

    public function testUpdateWithdraw(): void
    {
        $this->markTestIncomplete('This test case needs review.');

        $this->actingAs(User::factory()->create());

        Withdraw::factory()->create();

        $payload = [
            'name' => 'Random'
        ];

        $this->json('PUT', $this->endpoint.'/1', $payload)
             ->assertStatus(200)
             ->assertSee($payload['name']);
    }

    public function testDeleteWithdraw(): void
    {
        $this->markTestIncomplete('This test case needs review.');

        $this->actingAs(User::factory()->create());

        Withdraw::factory()->create();

        $this->json('DELETE', $this->endpoint.'/1')
             ->assertStatus(204);

        $this->assertEquals(0, Withdraw::count());
    }
    
}
