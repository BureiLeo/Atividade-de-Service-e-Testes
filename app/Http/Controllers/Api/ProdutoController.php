<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProdutoRequest;
use App\Http\Resources\ProdutoResource;
use App\Models\Produto;
use App\Services\ProdutoService;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProdutoResource::collection(Produto::latest()->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProdutoRequest $request, ProdutoService $service)
    {
        $produto = $service->create($request->validated());

        // Código 201 : Recurso Criado
        return new ProdutoResource($produto);
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        return new ProdutoResource($produto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProdutoRequest $request, Produto $produto, ProdutoService $service)
    {
        $produto = $service->update($produto, $request->validated());

        return new ProdutoResource($produto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        $produto->delete();

        return response()->noContent(); // 204
    }
}
