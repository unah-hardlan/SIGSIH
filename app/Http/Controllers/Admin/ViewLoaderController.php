<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Services\PermissionService;
use App\Support\AdminModuleRegistry;

class ViewLoaderController extends Controller
{
    public function load(Request $request)
    {
        $view = $request->get('view');

        if (!$view || !preg_match('/^[a-zA-Z0-9_-]+$/', $view)) {
            return response('Invalid view', 400);
        }

        $viewDefinition = AdminModuleRegistry::view($view);

        if (!$viewDefinition) {
            return $this->denyAccessResponse($view, __('La vista solicitada no está disponible.'));
        }

        // Enforce permisos for specific admin views (consultar)
        $user = Auth::user();
        if ($user) {
            // Admin bypass
            try {
                if (mb_strtolower($user->rol?->rol ?? '') !== 'administrador') {
                    $candidates = AdminModuleRegistry::permissionCandidates($view);
                    if (!empty($candidates)) {
                        $perm = app(PermissionService::class);
                        if (!$perm->can($user, $candidates, 'consultar')) {
                            return $this->denyAccessResponse($view, null, AdminModuleRegistry::labelForView($view));
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Si falla relación u otro error, negar por seguridad
                return $this->denyAccessResponse($view);
            }
        }

        // Primero verificar si existe una vista parcial específica
        $partialBlade = $viewDefinition['blade'] ?? "admin.partials.{$view}";
        if (($viewDefinition['type'] ?? 'partial') === 'partial' && View::exists($partialBlade)) {
            return $this->renderPartial($partialBlade);
        }

        // Si no existe vista parcial, intentar cargar la vista completa y extraer contenido
        $fullView = $viewDefinition['blade'] ?? "admin.{$view}";
        if (!View::exists($fullView)) {
            return response('View not found', 404);
        }

        try {
            return $this->renderFullView($fullView);
        } catch (\Exception $e) {
            return response('Error loading view: ' . $e->getMessage(), 500);
        }
    }

    private function renderPartial(string $blade, array $data = []): string
    {
        $headerHtml = view('partials.admin-header')->render();
        $contentHtml = view($blade, $data)->render();

        return $headerHtml . '<div class="p-6 rounded-lg shadow bg-white dark:bg-gray-900">' . $contentHtml . '</div>';
    }

    private function renderFullView(string $blade): string
    {
        $fullHtml = view($blade)->render();

        if (preg_match('/<div class="bg-white p-6 rounded-lg shadow">(.*?)<\/div>\s*<\/main>/s', $fullHtml, $matches)) {
            return $matches[1];
        }

        if (preg_match('/<div[^>]*class="[^"]*bg-white[^"]*"[^>]*>(.*?)<\/div>/s', $fullHtml, $matches)) {
            return $matches[1];
        }

        return $fullHtml;
    }

    private function denyAccessResponse(string $view, ?string $customMessage = null, ?string $label = null)
    {
        $message = $customMessage ?? __('No cuentas con los permisos necesarios para acceder a esta sección.');
        $targetLabel = $label ?? $this->resolveViewLabel($view);

        $content = $this->renderPartial('admin.partials.access-denied', [
            'code' => 403,
            'title' => __('Acceso restringido'),
            'message' => $message,
            'targetLabel' => $targetLabel,
            'actionUrl' => route('admin.dashboard'),
            'actionText' => __('Ir al panel principal'),
            'helpText' => __('Comunícate con un administrador si consideras que deberías tener acceso.'),
        ]);

        return response($content, 403);
    }

    private function resolveViewLabel(string $view): string
    {
        return AdminModuleRegistry::labelForView($view);
    }
}
