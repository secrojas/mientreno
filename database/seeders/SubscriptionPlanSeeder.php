<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Plan gratuito para comenzar. Ideal para coaches independientes que recién empiezan.',
                'monthly_price' => 0,
                'annual_price' => 0,
                'currency' => 'USD',
                'features' => [
                    'student_limit' => 5,
                    'group_limit' => 2,
                    'storage_limit_gb' => 1,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Para coaches en crecimiento con hasta 20 alumnos y múltiples grupos.',
                'monthly_price' => 19.99,
                'annual_price' => 199.99, // ~17% descuento
                'currency' => 'USD',
                'features' => [
                    'student_limit' => 20,
                    'group_limit' => 5,
                    'storage_limit_gb' => 5,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Plan profesional con límites amplios para negocios establecidos.',
                'monthly_price' => 49.99,
                'annual_price' => 499.99, // ~17% descuento
                'currency' => 'USD',
                'features' => [
                    'student_limit' => 100,
                    'group_limit' => 20,
                    'storage_limit_gb' => 20,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Plan sin límites para academias y negocios grandes. Escalabilidad total.',
                'monthly_price' => 99.99,
                'annual_price' => 999.99, // ~17% descuento
                'currency' => 'USD',
                'features' => [
                    'student_limit' => null, // Ilimitado
                    'group_limit' => null,   // Ilimitado
                    'storage_limit_gb' => null, // Ilimitado
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('✓ Planes de suscripción creados exitosamente');
        $this->command->info('  - Free: 5 estudiantes, 2 grupos, 1GB');
        $this->command->info('  - Starter: 20 estudiantes, 5 grupos, 5GB ($19.99/mes)');
        $this->command->info('  - Pro: 100 estudiantes, 20 grupos, 20GB ($49.99/mes)');
        $this->command->info('  - Enterprise: Ilimitado ($99.99/mes)');
    }
}
