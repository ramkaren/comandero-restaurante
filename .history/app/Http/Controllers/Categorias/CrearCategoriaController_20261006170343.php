<?php

namespace App\Http\Controllers\Categorias;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoriaRequest;
use App\Services\Categorias\CrearCategoriaService;
use Illuminate\Http\JsonResponse;

class CrearCategoriaController extends Controller
{
    protected $crearCategoriaService;

    public function __construct(CrearCategoriaService $service)
    {
        $this->crearCategoriaService = $service;
    }

    
}