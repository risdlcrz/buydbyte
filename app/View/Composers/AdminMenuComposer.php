<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Route;

class AdminMenuComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $adminRoutes = collect(Route::getRoutes())
            ->filter(function ($route) {
                return str_starts_with($route->getName() ?? '', 'admin.') 
                    && !str_contains($route->getName(), 'dashboard')
                    && str_contains($route->getName(), '.index');
            })
            ->map(function ($route) {
                $routeName = $route->getName();
                $routeParts = explode('.', $routeName);
                $module = $routeParts[1] ?? '';
                
                return [
                    'name' => ucfirst($module),
                    'route' => $routeName,
                    'icon' => $this->getIconForModule($module),
                    'active' => request()->routeIs("admin.{$module}.*")
                ];
            })
            ->unique('name')
            ->values();

        $view->with('adminMenuItems', $adminRoutes);
    }

    /**
     * Get appropriate icon for module.
     */
    private function getIconForModule(string $module): string
    {
        return match($module) {
            'users' => 'bi-people',
            'products' => 'bi-box-seam',
            'categories' => 'bi-tags',
            'orders' => 'bi-receipt',
            'customers' => 'bi-person-badge',
            'inventory' => 'bi-graph-up',
            'reports' => 'bi-bar-chart',
            'settings' => 'bi-gear',
            default => 'bi-circle'
        };
    }
}