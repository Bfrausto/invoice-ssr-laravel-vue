<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear una compañía principal
        $company = Company::factory()->create([
            'name' => 'Mi Empresa de Prueba',
            'email' => 'contacto@miempresa.com',
            'address' => 'Calle Falsa 123, Querétaro',
        ]);

        // Crear 10 clientes
        $clients = Client::factory()->count(10)->create();

        // Crear 50 facturas, asignando la compañía y un cliente aleatorio a cada una
        Invoice::factory()->count(50)->sequence(fn ($sequence) => [
            'company_id' => $company->id,
            'client_id' => $clients->random()->id,
        ])->create();
    }
}
