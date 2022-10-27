<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use GDText\Box;
use GDText\Color;



class apiController extends Controller
{



    /* Du måste välja:
    - Bredd (endast heltal)
    - Höjd (endast heltal)
    - En bild 
    
    Väljer du ej position på loggan (top-right/top-left, bottom-left/right)
    blir den satt by default till top-left. 
    
    Vill du ha en svart logga, skriv 'Black' på logoColor, default är vit.
    
    Vill du byta textstorlek ändrar du fontSize, default är 32px.

    Vill du byta textfärg ändrar du textColor till 'Black', default är vit.

    Öka bredden på textrutan på bilden genom att öka boxWidth.
    */



    public function generateImage(Request $request)
    {

        if ($request->header('Authorization') !== 'Bearer apa') {
            return response()->json(['error' => 'invalid token'], 401);
        }


        $width = $request->width;
        $height = $request->height;
        $logoColor = $request->logoColor;
        $position = $request->position;
        $text = $request->text;
        $textColor = $request->textColor;
        $brightness = $request->brightness;
        $fontSize = $request->fontSize;
        $boxWidth = $request->boxWidth;
        $error = [];
       

        $image = $request->photo;

        


        if ($image === null) {
            
            array_push($error, 'Choose image.');
        }

        if (!is_numeric($width) || !is_numeric($height)) {
            array_push($error, 'Choose width and height.');
        }

        if ($boxWidth > $width) {
            array_push($error, 'BoxWidth cannot be higher than width.');
        }


        if(!is_numeric($fontSize) && $fontSize != null)
        {
            array_push($error, 'FontSize has to be numeric.');
        }

        if ($error != null) {
                return response()->json(['Error' => $error], 400);
            }


        $width = (int)$width;
        $height = (int)$height;

     


    
        $image->storeAs('public', $image->getClientOriginalName());
        $img = Image::make(storage_path('app/public') . '/' . $image->getClientOriginalName());
        $img->fit($width, $height);

        if ($logoColor === 'black') {

            $watermark = Image::make(storage_path('app/public/svartlogga.png'));
            $img->insert($watermark, $position, 10, 10);
        } else {

            $watermark = Image::make(storage_path('app/public/vitlogga.png'));
            $img->insert($watermark, $position, 10, 10);
        }


        $textBoxPosition = ($width - $boxWidth) / 2;
        $coreImage = $img->getCore();
        $box = new Box($coreImage);
        $box->setBox($textBoxPosition, null, $boxWidth, $height);
        $box->setTextAlign('center', 'center');
        $box->setFontFace(storage_path('app/public/GT-Cinetype-Light.ttf')); // http://www.dafont.com/franchise.font


        if ($brightness) {
            $img->insert(Image::canvas($img->width(), $img->height(), 'rgba(0,0,0,0.5)'));
        }


        if ($text !== null && $textColor !== 'black') {
            $box->setFontColor(new Color(255, 255, 255));
        } else {
            $box->setFontColor(new Color(0, 0, 0));
        }

        if ($fontSize) {
            $box->setFontSize($fontSize);
        } else {
            $box->setFontSize(32);
        }
      


        $box->draw($text);


        $img->save(storage_path('app/public') . '/' . $image->getClientOriginalName());


        return response()->json([

            'url' => ['img' => asset('storage') . '/' . $image->getClientOriginalName()]
        ]);
    }
}
