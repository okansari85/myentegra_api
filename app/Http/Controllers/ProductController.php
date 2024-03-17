<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IProducts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Validator;

use App\Models\Products;

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

    //servise geçirilecek
    public function addProductCoverImage(Request $request){

        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        if($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 413);
        }

        $file = $request->file('file');

        $path = $file->store('files', ['disk' => 'my_files']);//$file->store('files');
        $name = $file->getClientOriginalName();

        $filename = pathinfo($path, PATHINFO_FILENAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);



        $product = Products::find($request->product_id);


        $product->images()->create([
            'url' => $filename.".".$extension,
            'cover' => true,
        ]);


        return response()->json($product,200);

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
