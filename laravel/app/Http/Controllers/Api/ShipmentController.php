<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

/**
 * ShipmentController handles the shipment-related operations.
 * This includes creating, updating, and retrieving shipments for authenticated users.
 * @package App\Http\Controllers\Api
 */

class ShipmentController extends Controller
{
    public function index(): JsonResponse
    {
        try { 
            $user = Auth::user()->uuid;
            // Check if the user is authenticated
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            $shipment=Shipment::where('user_id', $user)->get(); 
            if ($shipment->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No shipments found for this user.'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Shipments retrieved successfully.',
                'Shipment' => $shipment
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }

    }
    /**
     * Store a new shipment.
     * This method validates the request data, checks if the user is authenticated,
     * and creates a new shipment if the user does not already have a shipment with the same address.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Check if the user is authenticated
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            $data = $request->validate([
                'contact_name' => 'required|string|max:255',
                'contact_phone' => 'required|integer',
                'address' => 'required|string|max:255',
                'sub_city' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'zip_code' => 'required|string|min:4|max:10',
                'country' => 'required|string|max:100',
                // Add any other fields you need to validate
            ]);
    
            $data['user_id'] = Auth::user()->uuid;
     
            //check if the user already has a shipment with the same address
            $existingShipment = Shipment::where('user_id', $data['user_id'])
                ->where('address', $data['address'])
                ->where('sub_city', $data['sub_city'])
                ->where('city', $data['city'])
                ->where('zip_code', $data['zip_code'])
                ->where('country', $data['country'])
                ->first();
    
            if ($existingShipment) {
                return response()->json([
                    'success' => false,
                    'message' => 'already exists in your shipment list.'
                ], 409);
            }
            $shipment = Shipment::create($data);
    
            return response()->json([
                'success' => true,
                'message' => 'Shipment created successfully',
                'shipment' => $shipment
            ]);
        }catch(\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Update an existing shipment.
     * This method validates the request data, checks if the user is authenticated,
     * and updates the shipment with the given ID.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $shipment = Shipment::findOrFail($id);
            // Check if the user is authenticated
            if (!Auth::check() || Auth::user()->uuid !== $shipment->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Validate the request data
            $data = $request->validate([
                'contact_name' => 'required|string|max:255',
                'contact_phone' => 'required|integer',
                'address' => 'required|string|max:255',
                'sub_city' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'zip_code' => 'required|string|min:4|max:10',
                'country' => 'required|string|max:100',
            ]);

            // Update the shipment
            $shipment->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Shipment updated successfully',
                'shipment' => $shipment
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
