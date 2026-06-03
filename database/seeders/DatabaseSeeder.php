<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Election;
use App\Models\Option;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

// PROYECTO + BASE LARAVEL:
// Seeder de Laravel personalizado con datos iniciales del sistema de votaciones.
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // PROYECTO:
        // Roles iniciales de la aplicación.
        $admin = Role::create(['name' => 'admin']);
        $voter = Role::create(['name' => 'voter']);

        // PROYECTO:
        // Usuario administrador inicial.
        $adminUser = User::create([
            'dni' => '00000000A',
            'username' => 'admin',
            'full_name' => 'Administrador',
            'email' => 'admin@centro.edu',
            'password' => 'password',
            'role_id' => $admin->id,
        ]);

        // PROYECTO:
        // Usuarios de ejemplo para probar permisos de voto por categoría.
        $profesor1 = User::create([
            'dni' => '11111111B',
            'username' => 'profesor1',
            'full_name' => 'María García López',
            'email' => 'maria@centro.edu',
            'password' => 'password',
            'role_id' => $voter->id,
        ]);
        $alumno1 = User::create([
            'dni' => '22222222C',
            'username' => 'alumno1',
            'full_name' => 'Carlos Martínez Ruiz',
            'email' => 'carlos@centro.edu',
            'password' => 'password',
            'role_id' => $voter->id,
        ]);
        $padre1 = User::create([
            'dni' => '33333333D',
            'username' => 'padre1',
            'full_name' => 'Ana Sánchez Pérez',
            'email' => 'ana@centro.edu',
            'password' => 'password',
            'role_id' => $voter->id,
        ]);
        $mixto = User::create([
            'dni' => '44444444E',
            'username' => 'profypadre',
            'full_name' => 'Pedro Fernández Gil',
            'email' => 'pedro@centro.edu',
            'password' => 'password',
            'role_id' => $voter->id,
        ]);
        // PROYECTO:
        // Votación inicial de ejemplo.
        $election = Election::create([
            'title' => 'Elecciones Consejo Escolar 2026',
            'description' => 'Elecciones para renovar los representantes del consejo escolar del centro.',
            'type' => 'consejo_escolar',
            'is_anonymous' => false,
            'realtime_results_enabled' => true,
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'status' => 'active',
        ]);

        // PROYECTO:
        // Categorías y opciones de voto.
        $catProf = Category::create([
            'election_id' => $election->id,
            'name' => 'Representante del Profesorado',
            'max_selections' => 1,
        ]);
        Option::create(['category_id' => $catProf->id, 'label' => 'Laura Díaz Moreno']);
        Option::create(['category_id' => $catProf->id, 'label' => 'Javier Romero Blanco']);
        Option::create(['category_id' => $catProf->id, 'label' => 'Voto en blanco']);

        $catAlum = Category::create([
            'election_id' => $election->id,
            'name' => 'Representante del Alumnado',
            'max_selections' => 1,
        ]);
        Option::create(['category_id' => $catAlum->id, 'label' => 'Sofía López Navarro']);
        Option::create(['category_id' => $catAlum->id, 'label' => 'Diego Torres Jiménez']);
        Option::create(['category_id' => $catAlum->id, 'label' => 'Voto en blanco']);

        $catPadres = Category::create([
            'election_id' => $election->id,
            'name' => 'Representante de Padres/Madres',
            'max_selections' => 1,
        ]);
        Option::create(['category_id' => $catPadres->id, 'label' => 'Carmen Ruiz Vega']);
        Option::create(['category_id' => $catPadres->id, 'label' => 'Miguel Ángel Herrera']);
        Option::create(['category_id' => $catPadres->id, 'label' => 'Voto en blanco']);
        // PROYECTO:
        // Asignación directa de categorías a usuarios.
        $profesor1->categories()->attach($catProf);
        $alumno1->categories()->attach($catAlum);
        $padre1->categories()->attach($catPadres);
        $mixto->categories()->attach([$catProf->id, $catPadres->id]);
    }
}
