<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Interfaces\IImages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Validator;



class ImageController extends Controller
{
    //
    private IImages $imageservice;

    public function __construct(IImages $_imageService) {
        $this->imageservice = $_imageService;
    }

    public function changeImageOrder(Request $request){
        return $this->imageservice->changeImageOrder($request->images);
    }

    public function uploadImages(Request $request){

        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        if($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 413);
        }

        $file = $request->file('file');
        $imageServiceResponse = $this->imageservice->uploadImage($file);

        if ($imageServiceResponse['status']==true){
            return [
            "status" => true,
            "message" => "Image uploaded successfully",
            "data" => ["imageId" => $imageServiceResponse['data']['image_id']]
            ];
        }
    }

    public function deleteImages($id){
        return $this->imageservice->deleteImage($id);
    }




}
