<?php

namespace App\Console\Commands;

use App\Http\Controllers\Auth\v1\RegisterController;
use App\Models\Business;
use Illuminate\Console\Command;

class GenerateInvitationToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invitation:generate {business_slug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera un token de invitación para un business';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $slug = $this->argument('business_slug');

        $business = Business::where('slug', $slug)->first();

        if (!$business) {
            $this->error("Business '{$slug}' no encontrado.");
            return 1;
        }

        $token = RegisterController::generateInvitationToken($business->id);
        $url = url("/register?invitation={$token}");

        $this->info("✅ Token de invitación generado para: {$business->name}");
        $this->newLine();
        $this->line("Link de invitación:");
        $this->line($url);
        $this->newLine();
        $this->comment("Comparte este link con nuevos miembros para que se registren automáticamente en tu grupo.");

        return 0;
    }
}
