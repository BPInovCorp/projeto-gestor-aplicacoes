<?php

namespace Tests\Feature;

use App\Models\CalendarEventAction;
use App\Models\CalendarEventType;
use App\Models\CompanySetting;
use App\Models\ContactRole;
use App\Models\Country;
use App\Models\VatRate;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferenceDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ReferenceDataSeeder::class);
    }

    public function test_reference_data_seeder_creates_records_successfully()
    {
        $this->assertDatabaseHas('countries', ['code' => 'PT']);
        $this->assertDatabaseHas('contact_roles', ['name' => 'Diretor']);
        $this->assertDatabaseHas('calendar_event_types', ['name' => 'Reunião', 'color' => '#3B82F6']);
        $this->assertDatabaseHas('calendar_event_actions', ['name' => 'Executar']);
        $this->assertDatabaseHas('vat_rates', ['name' => 'Taxa Normal', 'value' => 23.00]);
        $this->assertDatabaseHas('company_settings', ['vat_number' => '500123456']);
    }

    public function test_reference_data_seeder_can_be_executed_multiple_times_without_duplication()
    {
        $countryCountBefore = Country::count();
        $vatRateCountBefore = VatRate::count();
        $companyCountBefore = CompanySetting::count();

        $this->seed(ReferenceDataSeeder::class);

        $this->assertEquals($countryCountBefore, Country::count());
        $this->assertEquals($vatRateCountBefore, VatRate::count());
        $this->assertEquals($companyCountBefore, CompanySetting::count());
    }
}