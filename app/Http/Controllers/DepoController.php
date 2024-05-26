<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IDepo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;


class DepoController extends Controller
{

    private IDepo $depoService;

    public function __construct(IDepo $_depoService)
    {
        $this->depoService = $_depoService;
    }


    public function index(Request $request)
    {
        //
        $search = $request->query('search');
        $per_page = $request->query('per_page');

        return response()->json($this->depoService->getMainCategories($search,$per_page));
    }

    public function show(string $id, Request $request)
    {
        //
        $search = $request->query('search');
        $per_page = $request->query('per_page');

        return response()->json($this->depoService->getSubCategories($id,$search,$per_page));
    }

    public function store(Request $request)
    {
        return $this->depoService->changeCategoryOrder($request->categories);
    }


    public function addCategory(Request $request){

        return response()->json($this->depoService->addCategory($request->category_name,$request->parent_id),200);

    }


    public function destroy($category_id)
    {
        return response()->json($this->depoService->deleteCategory($category_id),200);
    }

    public function update($id,Request $request){
         $category_name =$request['name'];
         return response()->json($this->depoService->updateCategory($id,$category_name),200);

    }

}
