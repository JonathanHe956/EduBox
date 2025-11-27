<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear roles (usar firstOrCreate para evitar duplicados)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $studentRole = Role::firstOrCreate(['name' => 'estudiante']);
        $docenteRole = Role::firstOrCreate(['name' => 'docente']);

        // Crear usuarios de ejemplo y asignar roles (usar firstOrCreate para evitar duplicados)
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Administrador',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $student = User::firstOrCreate([
            'email' => 'estudiante@example.com',
        ], [
            'name' => 'Estudiante',
            'password' => bcrypt('password'),
        ]);
        $student->assignRole('estudiante');

        $docente = User::firstOrCreate([
            'email' => 'docente@example.com',
        ], [
            'name' => 'Docente',
            'password' => bcrypt('password'),
        ]);
        $docente->assignRole('docente');

        // Asignar rol 'estudiante' a todos los alumnos existentes
        $alumnos = \App\Models\alumno::all();
        foreach ($alumnos as $alumno) {
            $user = User::where('email', $alumno->email)->first();
            if (!$user) {
                // Crear usuario si no existe
                $user = User::create([
                    'name' => $alumno->nombre . ' ' . ($alumno->apaterno ?? ''),
                    'email' => $alumno->email,
                    'password' => bcrypt('password'),
                ]);
            }
            // Asignar rol si no lo tiene
            if (!$user->hasRole('estudiante')) {
                $user->assignRole('estudiante');
            }
        }

        // Asignar rol 'docente' a todos los docentes existentes
        $docentes = \App\Models\docente::all();
        foreach ($docentes as $docente) {
            $user = User::where('email', $docente->email)->first();
            if (!$user) {
                // Crear usuario si no existe
                $user = User::create([
                    'name' => $docente->nombre . ' ' . ($docente->apaterno ?? ''),
                    'email' => $docente->email,
                    'password' => bcrypt('password'),
                ]);
            }
            // Asignar rol si no lo tiene
            if (!$user->hasRole('docente')) {
                $user->assignRole('docente');
            }
        }
    }
}
