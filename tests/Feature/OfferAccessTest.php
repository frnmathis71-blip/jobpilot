<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_user_can_access_offer_create_page(): void
    {
        $company = Company::create();
        $user = User::factory()->create([
            'role' => 'company',
            'company_id' => $company->id,
        ]);

        $this->actingAs($user)
            ->get('/offer/create')
            ->assertOk();
    }

    public function test_offer_create_resolves_to_the_create_route(): void
    {
        $route = app('router')->getRoutes()->match(
            \Illuminate\Http\Request::create('/offer/create', 'GET')
        );

        $this->assertSame('offer.create', $route->getName());
    }

    public function test_candidat_user_cannot_access_offer_create_page(): void
    {
        $user = User::factory()->create(['role' => 'candidat']);

        $this->actingAs($user)
            ->get('/offer/create')
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/offer/create')->assertRedirect(route('login'));
    }
}
