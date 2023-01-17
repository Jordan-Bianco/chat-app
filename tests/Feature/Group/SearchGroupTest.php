<?php

namespace Tests\Feature\Group;

use App\Events\SearchGroup;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SearchGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_search_for_a_group()
    {
        $user = $this->user();
        $this->actingAs($user);

        Group::factory()->create(['name' => 'test 1']);
        Group::factory()->create(['name' => 'group 2']);

        Event::fake();

        $this->get('/groups?search=test');

        Event::assertDispatched(SearchGroup::class);
    }

    public function test_return_a_page_with_all_search_results()
    {
        $user = $this->user();
        $this->actingAs($user);

        Group::factory()->create(['name' => 'test 1']);
        Group::factory()->create(['name' => 'group 2']);

        $response = $this->get('/groups/search/test');

        $response->assertInertia(function ($page) {
            $page
                ->component('Search')
                ->has('results.data', 1)
                ->has('search')
                ->has('results.data.0', function ($page) {
                    $page
                        ->where('name', 'test 1')
                        ->etc();
                });
        });
    }
}
