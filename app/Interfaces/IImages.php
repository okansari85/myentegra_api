<?php

namespace App\Interfaces;

interface IImages
{
    public function uploadImage($file);
    public function deleteImage($file_id);
    public function changeImageOrder($images);
}
