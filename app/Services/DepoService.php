<?php

namespace App\Services;

use App\Interfaces\IDepo;
use App\Models\Depos;
use Illuminate\Support\Facades\DB;

class DepoService implements IDepo
{
    public function getMainCategories($search=null,$per_page=null){

        $nestedCategories = [];
        $categories = Depos::where('parent_id', 0)->orderBy('order', 'ASC')->get();

        foreach ($categories as $category) {
            $nestedCategory = $category->toArray();
            $nestedCategory['children'] = $this->getAllChildrens($category, $category->id);
            $nestedCategories[] = $nestedCategory;
        }

        return $nestedCategories;

    }

    protected function getAllChildrens($category,$id){

        $nestedCategories = [];
        $subCategories = Depos::where('parent_id', $id)->orderBy('order', 'ASC')->get();

        foreach ($subCategories as $subCategory) {
            $nestedCategory = $subCategory->toArray();
            // Eğer bu alt kategorinin alt kategorileri varsa, recursive olarak çağırarak tüm alt kategorileri alın
            $nestedCategory['children'] = $this->getAllChildrens($subCategory, $subCategory->id);
            $nestedCategories[] = $nestedCategory;
        }

        return $nestedCategories;

    }

    public function getSubCategories($parent_id,$search=null,$per_page=null){
        $categories = Depos::with('parent')->where('parent_id', $parent_id)->orderBy('order', 'ASC')->get();
        return $categories;
    }

    public function getCategorywithChildren($id,$search=null,$per_page=null){
        $category = Depos::with('children')->find($id);
        return $category;

    }

    public function getCategorywithParent($id,$search=null,$per_page=null){
        $category = Depos::with('parent')->find($id);
        return $category;

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
        $category = Depos::with('parent','children')->find($id);

        if (!$category) {
            return [];
        }
        $nestedCategories = $this->buildNestedArray($category, $category->parent_id);
        return $category;


    }

    public function changeCategoryOrder($categories){

        $arr = json_decode($categories, true);
        $arr2=[];
        $i=0;
        foreach ($arr as $b) {
            $arr2[] =  ['id'=>$b['id'],'order'=>$i];
            $i++;
        }
        $userInstance = new Depos;
        $index = 'id';

       batch()->update($userInstance, $arr2, $index);

       return $arr2;

    }

    public function addCategory($category_name,$parent_id){
        $category = new Depos();
        $category->name = $category_name;
        $category->parent_id = $parent_id;
        $category_order= Depos::where('parent_id', (int)$parent_id)->max('order');
        $category_order++;
        $category->order=$category_order;
        $category->save();

        return Depos::with('parent')->find($category->id);

    }

    public function deleteCategory($id)
    {
        Depos::destroy($id);
        return $id;
    }

    public function updateCategory($id,$category_name){
        $category = Depos::with('parent')->find($id);
        $category->name = $category_name;
        $category->update();
        return $category;
    }
}
