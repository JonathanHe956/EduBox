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

        // Crear permisos (si es necesario)
        // Permission::create(['name' => 'view grades']);
        // Permission::create(['name' => 'manage users']);

        // Asignar permisos a roles (si es necesario)
        // $adminRole->givePermissionTo(['view grades', 'manage users']);
        // $studentRole->givePermissionTo(['view grades']);

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

        // Crear datos de ejemplo para alumnos y docentes
        $carrera = \App\Models\carrera::firstOrCreate([
            'nombre' => 'Ingeniería en TICs',
            'creditos' => 240,
        ]);

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
