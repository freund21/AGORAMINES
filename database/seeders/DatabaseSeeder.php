<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Election;
use App\Models\Option;
use App\Models\Participation;
use App\Models\Role;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

// PROYECTO + BASE LARAVEL:
// Seeder con datos iniciales del sistema de votaciones.
// Crea roles, un admin, una votación de ejemplo con 3 categorías, varios
// votantes asignados a cada categoría y votos de ejemplo para que las
// gráficas de resultados se vean con datos reales.
// A propósito se deja a 'profesor1' SIN votar para poder votar en la demo.
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // PROYECTO:
        // Roles iniciales.
        $admin = Role::create(['name' => 'admin']);
        $voter = Role::create(['name' => 'voter']);

        // PROYECTO:
        // Usuario administrador inicial.
        User::create([
            'dni' => '00000000A',
            'username' => 'admin',
            'full_name' => 'Administrador',
            'email' => 'admin@centro.edu',
            'password' => 'password',
            'role_id' => $admin->id,
        ]);

        // PROYECTO:
        // Votación de ejemplo (abierta).
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
        // Categorías con sus opciones.
        [$catProf, $optProf] = $this->crearCategoria($election, 'Representante del Profesorado',
            ['Laura Díaz Moreno', 'Javier Romero Blanco', 'Voto en blanco']);
        [$catAlum, $optAlum] = $this->crearCategoria($election, 'Representante del Alumnado',
            ['Sofía López Navarro', 'Diego Torres Jiménez', 'Voto en blanco']);
        [$catPadres, $optPadres] = $this->crearCategoria($election, 'Representante de Padres/Madres',
            ['Carmen Ruiz Vega', 'Miguel Ángel Herrera', 'Voto en blanco']);

        // PROYECTO:
        // Votantes de ejemplo (todos con contraseña "password").
        $profes = $this->crearVotantes($voter, 'profesor', 'Profesor/a', 6, 100);
        $alumnos = $this->crearVotantes($voter, 'alumno', 'Alumno/a', 8, 200);
        $padres = $this->crearVotantes($voter, 'padre', 'Padre/Madre', 6, 300);
        $mixto = User::create([
            'dni' => '99999999Z',
            'username' => 'profypadre',
            'full_name' => 'Pedro Fernández Gil (profe y padre)',
            'email' => 'profypadre@centro.edu',
            'password' => 'password',
            'role_id' => $voter->id,
        ]);

        // PROYECTO:
        // Asignación de votantes a cada categoría (quién puede votar dónde).
        $catProf->users()->sync($profes->pluck('id')->push($mixto->id)->all());
        $catAlum->users()->sync($alumnos->pluck('id')->all());
        $catPadres->users()->sync($padres->pluck('id')->push($mixto->id)->all());

        // PROYECTO:
        // Votos de ejemplo. profesor1 y alumno1 se dejan sin votar para la demo.
        $this->votar($election, $catProf, $optProf, $profes->reject(fn ($u) => $u->username === 'profesor1'));
        $this->votar($election, $catAlum, $optAlum, $alumnos->reject(fn ($u) => $u->username === 'alumno1'));
        $this->votar($election, $catPadres, $optPadres, $padres->push($mixto));

        // PROYECTO:
        // Segunda votación YA CERRADA y anónima, para demostrar el caso de
        // "resultados de una votación finalizada" (no se puede votar, sí ver resultados).
        $cerrada = Election::create([
            'title' => 'Elección Delegado/a de Centro 2025',
            'description' => 'Votación ya finalizada para elegir al delegado/a de centro.',
            'type' => 'delegado',
            'is_anonymous' => true,
            'realtime_results_enabled' => false,
            'start_date' => now()->subDays(40),
            'end_date' => now()->subDays(33),
            'status' => 'closed',
        ]);

        [$catDel, $optDel] = $this->crearCategoria($cerrada, 'Delegado/a de Centro',
            ['Lucía Moreno Sanz', 'Hugo Castro León', 'Marta Vidal Ortiz', 'Voto en blanco']);

        // Votan los alumnos + el usuario mixto.
        $censoDelegado = $alumnos->concat([$mixto]);
        $catDel->users()->sync($censoDelegado->pluck('id')->all());
        $this->votar($cerrada, $catDel, $optDel, $censoDelegado);
    }

    // PROYECTO:
    // Crea una categoría y sus opciones. Devuelve [categoria, opciones].
    private function crearCategoria(Election $election, string $nombre, array $opciones): array
    {
        $categoria = Category::create([
            'election_id' => $election->id,
            'name' => $nombre,
            'max_selections' => 1,
        ]);

        $ops = collect($opciones)->map(fn ($label) => Option::create([
            'category_id' => $categoria->id,
            'label' => $label,
        ]));

        return [$categoria, $ops];
    }

    // PROYECTO:
    // Crea un grupo de votantes (profesorN, alumnoN, padreN...).
    private function crearVotantes(Role $rol, string $prefijo, string $etiqueta, int $cantidad, int $offset)
    {
        return collect(range(1, $cantidad))->map(function ($i) use ($rol, $prefijo, $etiqueta, $offset) {
            return User::create([
                'dni' => str_pad((string) ($offset + $i), 8, '0', STR_PAD_LEFT) . 'X',
                'username' => $prefijo . $i,
                'full_name' => $etiqueta . ' ' . $i,
                'email' => $prefijo . $i . '@centro.edu',
                'password' => 'password',
                'role_id' => $rol->id,
            ]);
        });
    }

    // PROYECTO:
    // Registra votos de ejemplo de un grupo de usuarios en una categoría.
    // Reparte los votos con un ganador claro, un segundo y algún voto en blanco,
    // y deja al último de la lista sin votar (participación realista < 100%).
    private function votar(Election $election, Category $categoria, $opciones, $usuarios): void
    {
        $votantes = $usuarios->values();
        $cuantosVotan = max(1, $votantes->count() - 1);

        $votantes->take($cuantosVotan)->each(function ($usuario, $i) use ($election, $categoria, $opciones) {
            // Distribución determinista (sin azar para que el seeder sea reproducible).
            $indice = match (true) {
                $i % 5 === 4 => $opciones->count() - 1, // ~20% voto en blanco
                $i % 2 === 0 => 0,                       // opción ganadora
                default => 1,                            // segunda opción
            };
            $opcion = $opciones[$indice];

            Vote::create([
                'election_id' => $election->id,
                'category_id' => $categoria->id,
                'option_id' => $opcion->id,
                'user_id' => $election->is_anonymous ? null : $usuario->id,
                'encrypted_vote' => encrypt($opcion->label),
                'receipt_code' => strtoupper(Str::random(12)),
            ]);

            Participation::create([
                'election_id' => $election->id,
                'category_id' => $categoria->id,
                'user_id' => $usuario->id,
            ]);
        });
    }
}
