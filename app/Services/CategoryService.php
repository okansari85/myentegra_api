<?php

namespace App\Services;

use App\Interfaces\ICategory;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;

class CategoryService implements ICategory
{
    public function getMainCategories($search=null,$per_page=null){

        $categories = ProductCategory::where('parent_id', 0)->orderBy('order', 'ASC')->get();
        return response()->json($categories);

    }

    public function getSubCategories($parent_id,$search=null,$per_page=null){
        $categories = ProductCategory::where('parent_id', $parent_id)->orderBy('order', 'ASC')->get();
        return response()->json($categories);
    }

    public function getCategorywithChildren($id,$search=null,$per_page=null){
        $category = ProductCategory::with('children')->find($id);
        return response()->json($category);

    }

    protected function buildNestedArray($category, $parentId)
    {

        $nestedCategories = [];
        $is_main_category = $category->parent_id === 0 ? true :false;


            if (!$is_main_category){
                $nestedCategory = $category->toArray();
                $nestedCategory['parent'] = $this->buildNestedArray($category->parent, $category->parent_id);
                $nestedCategories[] = $nestedCategory;
            }

        return $nestedCategories;
    }

    public function getRelatedCategories($id,$search=null,$per_page=null){

        $nestedCategories = [];
        $category = ProductCategory::with('parent')->find($id);

        if (!$category) {
            return [];
        }
        $nestedCategories = $this->buildNestedArray($category, $category->parent_id);
        return response()->json($category);


    }

    public function changeCategoryOrder($categories){


        $arr = json_decode($categories, true);

        $arr2=[];
        $i=0;
        foreach ($arr as $b) {
            $arr2[] =  ['id'=>$b['id'],'order'=>$i];
            $i++;
        }
        $userInstance = new ProductCategory;
        $index = 'id';

       batch()->update($userInstance, $arr2, $index);

       return $arr2;

    }

    public function addCategory($category_name,$parent_id){
        $category = new ProductCategory();
        $category->name = $category_name;
        $category_order= ProductCategory::where('parent_id', (int)$parent_id)->max('order');
        $category_order++;
        $category->order=$category_order;
        $category->save();
        return $category;

    }

    public function deleteCategory($id)
    {
        ProductCategory::destroy($id);
        return response()->json(['id'=>$id],200);
    }

    public function updateCategory($id,$category_name){
        $category = ProductCategory::find($id);
        $category->name = $category_name;
        $category->update();
        return $category;
    }
}
