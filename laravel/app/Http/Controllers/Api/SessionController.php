<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class SessionController
 * Handles session-related operations for the API.
 */

class SessionController extends Controller
{
    /**
     * Save delivery location and zip code to session.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveDeliveryZipSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'required|string|max:255',
            'zip' => 'required|string|min:4|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors(),
            ], 422);
        }

        session([
            'delivery_location' => $request->location,
            'delivery_zip' => $request->zip,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session data saved successfully',
        ]);
    }
    /**
     * Save guest cart data to session.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function guestCartSession(Request $request){
        //validate cart array
        $validator = Validator::make($request->all(), [
            'cart' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
            ], 422);
        }

        session(['guest_cart' => $request->cart]);

        return response()->json([
            'success' => true,
            'message' => 'Guest cart session saved successfully',
        ]);
    }
}
