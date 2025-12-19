<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\TrainingGroup;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrainingGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar un business existente o crear uno de prueba
        $business = Business::where('is_active', true)->first();

        if (!$business) {
            $this->command->info('No se encontró ningún business activo. Crea uno primero.');
            return;
        }

        $coach = $business->owner;

        if (!$coach) {
            $this->command->info('El business no tiene un owner asignado.');
            return;
        }

        // Crear 3 grupos de entrenamiento
        $groups = [
            [
                'name' => 'Grupo Principiantes Lunes/Miércoles',
                'description' => 'Grupo orientado a corredores que están comenzando. Entrenamientos de baja intensidad enfocados en construir base aeróbica.',
                'level' => 'beginner',
                'max_members' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Grupo Intermedio Martes/Jueves',
                'description' => 'Para corredores con experiencia. Incluye series, fartlek y entrenamientos de tempo.',
                'level' => 'intermediate',
                'max_members' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Grupo Avanzado Competición',
                'description' => 'Entrenamiento de alto rendimiento para corredores con objetivos competitivos. Preparación para 10K, media maratón y maratón.',
                'level' => 'advanced',
                'max_members' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($groups as $groupData) {
            $group = TrainingGroup::create([
                'business_id' => $business->id,
                'coach_id' => $coach->id,
                'name' => $groupData['name'],
                'description' => $groupData['description'],
                'level' => $groupData['level'],
                'max_members' => $groupData['max_members'],
                'schedule' => null,
                'is_active' => $groupData['is_active'],
            ]);

            $this->command->info("✓ Grupo creado: {$group->name}");

            // Asignar algunos alumnos al grupo (runners del business)
            $runners = User::where('business_id', $business->id)
                ->where('role', 'runner')
                ->inRandomOrder()
                ->limit(rand(3, 8))
                ->get();

            foreach ($runners as $runner) {
                $group->members()->attach($runner->id, [
                    'joined_at' => now()->subDays(rand(1, 30)),
                    'is_active' => true,
                ]);
            }

            $this->command->info("  └─ {$runners->count()} miembros asignados");
        }

        $this->command->info("\n✅ Seeder completado: 3 grupos de entrenamiento creados");
    }
}
