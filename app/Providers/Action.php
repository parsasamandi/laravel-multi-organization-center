<?php

namespace App\Providers;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Media;
use File;

class Action {

    /**
     * Edit
     * 
     * @return json
     */
    public function edit($model, $id) {
        try {
            $values = $model::find($id);

            return $values ? response()->json($values) 
                : $this->failedResponse();

        } catch (Throwable $e) {
            return response()->json($e);
        }
    }

    /**
     * Delete
     * 
     * @return json
     */
    public function delete($model, $id) {
        // Why did not try catch work?
        $values = $model::find($id);

        return $values ? $values->delete() 
                : $this->failedResponse();

        return $this->successfulResponse();
    }

    /**
     * Delete with image
     * 
     * @return json
     */
    public function deleteWithFile($model, $id, $file) {

        // Model file
        $modelFile = $model::find($id);

        if($modelFile) {
            // Media
            $fileDelete = public_path("receipts/" . $file);

            if($fileDelete) {
                File::delete($fileDelete); 
            }

            return $modelFile->delete();

        } else {
            return $this->failedResponse();
        }
 
    }

    // Image
    public function image($request, $media_id, $class) {

        $imageUploader = Media::where('media_id', $media_id)->where('media_type', $class)->first();
        // Update
        if(!$imageUploader) {
            // Insert
            $imageUploader = new Media();
        }
        $imageUploader->media_id = $media_id;
        $imageUploader->media_type = $class;
        // 0 = Image
        $imageUploader->type = Media::IMAGE;

        // File
        $image = $request->file('image');
        $file = $image->getClientOriginalName();

        if(isset($file)) {
            // Delete the old picture
            File::delete(public_path("images/$imageUploader->media_url")); 

            $image->move(public_path('images'), $file);
            $imageUploader->media_url = $file;
        }
        $imageUploader->save();
    }

<<<<<<< HEAD
    // English to Persian conversion
    function englishToPersianNumbers($number) {
        $persianNumbers = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $englishNumbers = range(0, 9);
        return str_replace($englishNumbers, $persianNumbers, $number);
    }
=======
    public function englishToPersianNumbers($number) {
        // Define the mapping of English digits to Persian digits
        $englishDigits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $persianDigits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    
        // Convert each English digit in the number to its Persian equivalent
        $persianNumber = str_replace($englishDigits, $persianDigits, $number);
    
        return $persianNumber;
    }

    public function persianToEnglishNumbers($number) {
        // Define the mapping of English digits to Persian digits
        $persianDigits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $englishDigits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    
        // Convert each English digit in the number to its Persian equivalent
        $englishNumber = str_replace($persianDigits, $englishDigits, $number);
    
        return $englishNumber;
    }

    
>>>>>>> c2417751

    // Response with error
    public function failedResponse() {
        return response()->json(['error' => 'No data was found'], Response::HTTP_NOT_FOUND);
    }

    // Response with success
    public function successfulResponse() {
        return response()->json(['success' => 'Deleted successfully'], Response::HTTP_OK);
    }
}

?>