<?php

namespace Tests\Feature;

use App\Models\Candidat;
use App\Models\Company;
use App\Models\JobPosting;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostingTest extends TestCase
{
    use RefreshDatabase;

    private function makeOffer(): Offer
    {
        $company = Company::create();
        User::factory()->create(['role' => 'company', 'company_id' => $company->id]);

        return Offer::create([
            'company_id' => $company->id,
            'Name' => 'Développeur PHP',
            'Salary' => 2500,
            'Description' => 'Poste en CDI',
        ]);
    }

    private function makeCandidat(): User
    {
        return User::factory()->create([
            'role' => 'candidat',
            'candidat_id' => Candidat::create()->id,
        ]);
    }

    public function test_candidat_sees_the_offer_summary(): void
    {
        $offer = $this->makeOffer();

        $this->actingAs($this->makeCandidat())
            ->get(route('jobposting.create', $offer))
            ->assertOk()
            ->assertSee('Développeur PHP')
            ->assertSee('Poste en CDI');
    }

    public function test_candidat_can_apply_to_an_offer(): void
    {
        $offer = $this->makeOffer();
        $candidat = $this->makeCandidat();

        $this->actingAs($candidat)
            ->post(route('jobposting.store'), ['offer_id' => $offer->id])
            ->assertRedirect(route('offer.index'));

        $this->assertDatabaseHas('job_postings', [
            'offer_id' => $offer->id,
            'user_id' => $candidat->id,
        ]);
    }

    public function test_applying_twice_does_not_create_a_duplicate(): void
    {
        $offer = $this->makeOffer();
        $candidat = $this->makeCandidat();

        $this->actingAs($candidat)->post(route('jobposting.store'), ['offer_id' => $offer->id]);
        $this->actingAs($candidat)->post(route('jobposting.store'), ['offer_id' => $offer->id]);

        $this->assertDatabaseCount('job_postings', 1);
    }

    public function test_company_cannot_apply(): void
    {
        $offer = $this->makeOffer();
        $company = User::where('role', 'company')->first();

        $this->actingAs($company)
            ->post(route('jobposting.store'), ['offer_id' => $offer->id])
            ->assertForbidden();
    }

    public function test_company_sees_applicants_for_its_offers_only(): void
    {
        $offer = $this->makeOffer();
        $company = User::where('role', 'company')->first();

        $otherOffer = $this->makeOffer();
        $otherOffer->update(['Name' => 'Offre concurrente']);

        $alice = $this->makeCandidat();
        $alice->update(['name' => 'Alice Martin']);
        $bob = $this->makeCandidat();
        $bob->update(['name' => 'Bob Durand']);

        JobPosting::create(['offer_id' => $offer->id, 'user_id' => $alice->id]);
        JobPosting::create(['offer_id' => $otherOffer->id, 'user_id' => $bob->id]);

        $this->actingAs($company)
            ->get(route('jobposting.index'))
            ->assertOk()
            ->assertSee('Alice Martin')
            ->assertDontSee('Bob Durand')
            ->assertDontSee('Offre concurrente');
    }

    public function test_candidat_cannot_see_the_applications_list(): void
    {
        $this->actingAs($this->makeCandidat())
            ->get(route('jobposting.index'))
            ->assertForbidden();
    }
}
