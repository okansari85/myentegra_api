<?php

namespace App\Services;

use App\Interfaces\IImages;

use App\Models\ProductImages;
use Illuminate\Support\Facades\Storage;


class ImageService implements IImages
{

    public function changeImageOrder($images){

        $arr = $images;

        $arr2=[];
        $i=0;
        foreach ($arr as $b) {
            $arr2[] =  ['id'=>$b['file']['id'],'order'=>$i];
            $i++;
        }
        $imageInstance = new ProductImages;
        $index = 'id';

       batch()->update($imageInstance, $arr2, $index);

       return $arr2;

    }


    public function uploadImage($file){

        try{

            $path = $file->store('files', ['disk' => 'my_files']);//$file->store('files');
            $name = $file->getClientOriginalName();

            $filename = pathinfo($path, PATHINFO_FILENAME);
            $extension = pathinfo($path, PATHINFO_EXTENSION);

            $image_id = ProductImages::create([
               "name" =>$filename,
               "type"=>$extension,
               "url" => env("APP_URL")."/storage/files/".$filename.".".$extension
            ]);

            return  [
                "status" => true,
                "message" => "Image uploaded successfully",
                "data" =>[
                    "image_id" => $image_id->id,
                ]
            ];

        }
        catch(\Exception $e) {
            //return error message if image is not uploaded
            return [
                "status" => false,
                "message" => "Error while uploading the file. Please try again later.",
                "error" => $e->getMessage()
            ];
        }
    }

    public function deleteImage($file_id){

        try{
            $image = ProductImages::findOrFail($file_id);
            Storage::delete('public/storage/files/' . $image['name'].'.'.$image['type']);
            $image->delete();
            return [
                "status" => true,
                "message" => "Image deleted Successfully!"
            ];
        }catch (\Exception $e) {
            return [
              "status" => false,
              "message" => "Error Occurred! Image not found.",
            ];
        }
    }

}
