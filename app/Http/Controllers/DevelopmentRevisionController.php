<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\Finder\Finder;

class DevelopmentRevisionController extends Controller
{
    public function __invoke(): JsonResponse
    {
        abort_unless(app()->environment('local'), 404);

        $code = [];
        $styles = [];
        $files = Finder::create()->files()->in([
            resource_path('views'), public_path('css'), public_path('js'),
            base_path('routes'), app_path(),
        ])->name('/\.(php|css|js)$/')->sortByName();

        foreach ($files as $file) {
            $fingerprint = $file->getRealPath().':'.hash_file('sha256', $file->getRealPath());
            if ($file->getExtension() === 'css') {
                $styles[] = $fingerprint;
            } else {
                $code[] = $fingerprint;
            }
        }

        return response()->json([
            'code' => hash('sha256', implode('|', $code)),
            'styles' => hash('sha256', implode('|', $styles)),
        ])->header('Cache-Control', 'no-store');
    }
}
