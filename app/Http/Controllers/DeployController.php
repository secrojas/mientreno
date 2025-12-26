<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DeployController extends Controller
{
    /**
     * Ejecuta el script de deploy cuando recibe un webhook de GitHub
     */
    public function deploy(Request $request)
    {
        // Validar token de seguridad
        $token = $request->header('X-Deploy-Token') ?? $request->input('token');

        if ($token !== config('app.deploy_token')) {
            Log::warning('Deploy: Intento de acceso no autorizado', [
                'ip' => $request->ip(),
                'token_received' => $token,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Validar que sea un push a main (si viene desde GitHub)
        $payload = $request->input('payload');
        if ($payload) {
            $data = json_decode($payload, true);
            $ref = $data['ref'] ?? '';

            if ($ref !== 'refs/heads/main') {
                return response()->json([
                    'success' => false,
                    'message' => 'Deploy only triggered on main branch',
                    'ref' => $ref,
                ], 200);
            }
        }

        Log::info('Deploy: Iniciando deploy automático', [
            'triggered_by' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            // Ruta al script de deploy
            $scriptPath = '/home/srojasw1/deploy_mientreno.sh';

            // Verificar que el script existe
            if (!file_exists($scriptPath)) {
                throw new \Exception("Script de deploy no encontrado en: {$scriptPath}");
            }

            // Ejecutar el script de deploy
            $process = new Process(['bash', $scriptPath]);
            $process->setTimeout(600); // 10 minutos máximo
            $process->run();

            // Verificar si fue exitoso
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $output = $process->getOutput();
            $errorOutput = $process->getErrorOutput();

            Log::info('Deploy: Completado exitosamente', [
                'output' => $output,
                'errors' => $errorOutput,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Deploy completed successfully',
                'output' => $output,
                'timestamp' => now()->toIso8601String(),
            ], 200);

        } catch (ProcessFailedException $e) {
            Log::error('Deploy: Falló la ejecución del script', [
                'error' => $e->getMessage(),
                'output' => $e->getProcess()->getOutput(),
                'error_output' => $e->getProcess()->getErrorOutput(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Deploy failed',
                'error' => $e->getMessage(),
                'output' => $e->getProcess()->getOutput(),
                'error_output' => $e->getProcess()->getErrorOutput(),
            ], 500);

        } catch (\Exception $e) {
            Log::error('Deploy: Error general', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Deploy failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Endpoint de prueba para verificar que el webhook está accesible
     */
    public function ping(Request $request)
    {
        $token = $request->header('X-Deploy-Token') ?? $request->input('token');

        if ($token !== config('app.deploy_token')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Deploy webhook is working',
            'server_time' => now()->toIso8601String(),
            'script_exists' => file_exists('/home/srojasw1/deploy_mientreno.sh'),
        ], 200);
    }
}
