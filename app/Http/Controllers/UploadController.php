<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Intervention\Image\Facades\Image;


class UploadController extends Controller
{
    public function uploadForm()
    {
        return view ('upload');
    }






    public function uploadFile(Request $request)
    {


        $image = $request->file;
        $request->file->storeAs('public', $image->getClientOriginalName());
        $img = '../storage/' .$image->getClientOriginalName();
        $img = Image::make(storage_path('app/public').'/'.$image->getClientOriginalName());


        if($request->get){


        $img->fit($width, $height);
             


       } else{
           $img->fit(1080, 1080);
            

        }


       if($request->get('color') === "black"){

          $watermark = Image::make(storage_path('app/public/svartlogga.png'));
           $img->insert($watermark, 'bottom-right', 10, 10);
            

       }else {  

          $watermark = Image::make(storage_path('app/public/vitlogga.png'));
         $img->insert($watermark, 'bottom-right', 10, 10);     

        }



        $img->save(storage_path('app/public') . '/' . $image->getClientOriginalName());

        return view ('view-finished',['img' => asset('storage') . '/' . $image->getClientOriginalName()]);


        }


}


