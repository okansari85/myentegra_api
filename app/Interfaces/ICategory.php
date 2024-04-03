<?php

namespace App\Interfaces;

interface ICategory
{
    public function getMainCategories($search=null,$per_page=null);
    public function getCategorywithChildren($id,$search=null,$per_page=null);
    public function getRelatedCategories($id,$search=null,$per_page=null);
    public function addCategory($category_name,$parent_id);
    public function getSubCategories($parent_id,$search=null,$per_page=null);
    public function deleteCategory($id);
    public function updateCategory($id,$category_name);
    public function changeCategoryOrder($categories);

}
