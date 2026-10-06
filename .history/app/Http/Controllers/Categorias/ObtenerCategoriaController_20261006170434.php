<?php

namespace App\Http\Controllers\Categorias;

use App\Http\Controllers\Controller;
use App\Services\Categorias\ObtenerCategoriaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ObtenerCategoriaController extends Controller
{
    protected $obtenerCategoriaService;

    public function __construct(ObtenerCategoriaService $service)
    {
        $this->obtenerCategoriaService = $service;
    }

    
}