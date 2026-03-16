<?php
namespace App\Traits;

trait ManageImage{

    public function storeImage($image, $path){

        if ($image) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path($path); 
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $image->move($destinationPath, $imageName);
            return $imageName;
        }

        return null;     
    }

     public function destroyImage($image,$path){

        $imagePath = public_path($path . '/' . $image);
        if (file_exists($imagePath)) {
            unlink($imagePath);  
        }
     }

}