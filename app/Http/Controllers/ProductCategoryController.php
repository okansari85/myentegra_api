<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\ICategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;


class ProductCategoryController extends Controller
{
    //
    private ICategory $categoryservice;

    public function __construct(ICategory $_categoryservice)
    {
        $this->categoryservice = $_categoryservice;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $per_page = $request->query('per_page');

        return response()->json($this->categoryservice->getMainCategories($search,$per_page));
    }

    public function show($id,Request $request)
    {
        $search = $request->query('search');
        $per_page = $request->query('per_page');

        return response()->json($this->categoryservice->getRelatedCategories($id,$search,$per_page));
    }

    public function store(Request $request)
    {
        return $this->categoryservice->changeCategoryOrder($request->categories);
    }

    public function addCategory(Request $request){

        return response()->json($this->categoryservice->addCategory($request->category_name,$request->parent_id),200);

    }

    public function destroy($category_id)
    {
        return response()->json($this->categoryservice->deleteCategory($category_id),200);
    }

    public function update($id,Request $request){

         //
         $category_name = $request->query('name');
         return response()->json($this->categoryservice->updateCategory($id,$category_name),200);

    }


}
