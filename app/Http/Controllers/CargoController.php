<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\ICargo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;


class CargoController extends Controller
{

    private ICargo $cargoservice;

    public function __construct(ICargo $_cargoservice)
    {
        $this->cargoservice = $_cargoservice;
    }

    public function getCargoPriceFromN11(){
        return $this->cargoservice->getCargoPricesFromN11();
    }

    public function importHbCargoPricesFromFile(Request $request){

        return $this->cargoservice->importHbCargoPricesFromFile(request()->file('file'));
    }

    public function getN11CargoPrices(){

        return $this->cargoservice->getN11CargoPrices();
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //


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
