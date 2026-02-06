<?php

namespace App\Providers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class Action {

    /**
     * Edit
     * 
     * @return json
     */
    public function edit($model, $id) {
        try {
            $values = $model::findOrFail($id); // Use findOrFail to throw exception if not found
            return response()->json($values);

        } catch (ModelNotFoundException $e) {
            // Handle case when the record is not found
            return $this->failedResponse();
        } catch (Throwable $e) {
            // Handle other possible exceptions
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete
     * 
     * @return json
     */
    public function delete($model, $id) {
        try {
            $values = $model::findOrFail($id); // Use findOrFail for better error handling
            $values->delete();
            
            // Return success response after deletion
            return response()->json(['message' => 'گزارش و فایل رسید با موفقیت حذف شد.'], Response::HTTP_OK);


        } catch (ModelNotFoundException $e) {
            // Handle case when the record is not found
            return $this->failedResponse();
        } catch (Throwable $e) {
            // Handle other possible exceptions
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Response with error
     * 
     * @return json
     */
    public function failedResponse() {
        return response()->json(['message' => 'اطلاعاتی یافت نشد.'], Response::HTTP_NOT_FOUND);
    }
}
