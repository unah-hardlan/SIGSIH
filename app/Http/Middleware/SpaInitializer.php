<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpaInitializer
{
    
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        
        if ($request->is('admin/*') && !$request->is('load-view') && !$request->ajax()) {
            
            $response->headers->set('X-SPA-Page', 'true');
            
            
            $path = $request->path();
            $viewName = $this->extractViewNameFromPath($path);
            $response->headers->set('X-SPA-View', $viewName);
        }
        
        return $response;
    }
    
    
    private function extractViewNameFromPath($path)
    {
        $match = preg_match('/admin\/(.+)$/', $path, $matches);
        return $match ? $matches[1] : 'dashboard';
    }
}
