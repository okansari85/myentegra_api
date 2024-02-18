<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IProducts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

class ProductController extends Controller
{
    private IProducts $productservice;

    public function __construct(IProducts $_productservice)
    {
        $this->productservice = $_productservice;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $search = $request->query('search');
        $per_page = $request->query('per_page');

        return response()->json($this->productservice->getAllProducts($search,$per_page));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
