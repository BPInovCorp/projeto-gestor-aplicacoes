<?php

namespace Database\Seeders;

use App\Models\CalendarEventAction;
use App\Models\CalendarEventType;
use App\Models\CompanySetting;
use App\Models\ContactRole;
use App\Models\Country;
use App\Models\VatRate;
use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Portugal', 'code' => 'PT'],
            ['name' => 'Espanha', 'code' => 'ES'],
            ['name' => 'França', 'code' => 'FR'],
        ];

        foreach ($countries as $country) {
            Country::firstOrCreate(['code' => $country['code']], ['name' => $country['name'], 'is_active' => true]);
        }

        $contactRoles = ['Diretor', 'Gerente', 'Comercial', 'Financeiro'];

        foreach ($contactRoles as $role) {
            ContactRole::firstOrCreate(['name' => $role], ['is_active' => true]);
        }

        $calendarEventTypes = [
            ['name' => 'Reunião', 'color' => '#3B82F6'],
            ['name' => 'Chamada', 'color' => '#10B981'],
        ];

        foreach ($calendarEventTypes as $type) {
            CalendarEventType::firstOrCreate(['name' => $type['name']], ['color' => $type['color'], 'is_active' => true]);
        }

        $calendarEventActions = ['Preparar', 'Executar'];

        foreach ($calendarEventActions as $action) {
            CalendarEventAction::firstOrCreate(['name' => $action], ['is_active' => true]);
        }

        $vatRates = [
            ['name' => 'Taxa Normal', 'value' => 23.00],
            ['name' => 'Taxa Intermédia', 'value' => 13.00],
            ['name' => 'Taxa Reduzida', 'value' => 6.00],
            ['name' => 'Isento', 'value' => 0.00],
        ];

        foreach ($vatRates as $vat) {
            VatRate::updateOrCreate(['name' => $vat['name']], ['value' => $vat['value'], 'is_active' => true]);
        }

        if (CompanySetting::count() === 0) {
            CompanySetting::create([
                'name' => 'Empresa Fictícia, Lda.',
                'vat_number' => '500123456',
                'address' => 'Avenida Principal, 123',
                'postal_code' => '1000-001',
                'city' => 'Lisboa',
            ]);
        }
    }
}