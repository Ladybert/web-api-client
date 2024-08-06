<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Unit as UnitApi;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all residential estates with pagination
        $unit = UnitApi::with('unitType')->latest()->paginate(5);
        // Return a response using the resource with status code 200 (OK)
        return (new UnitResource(true, 'List Data Residential Estates', $unit, 200))
                ->response()
                ->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:unit,name|string',
            'image.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:500048',
            'unit_type_id' => 'required|exists:unit_types,id',
            'description' => 'required|string',
            'size' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'address' => 'required|string',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Errors',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        // Handle file upload
        $imagePaths = [];

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                // Store image and get the filename
                $imagePath = $image->store('residential_estate', 'public');
                $imagePaths[] = Storage::url($imagePath);
            }
        }

        // Create the residential estate
        $unit = UnitApi::create([
            'name' => $request->name,
            'image' => json_encode($imagePaths),
            'unit_type_id' => $request->unit_type_id,
            'description' => $request->description,
            'size' => $request->size,
            'city' => $request->city,
            'province' => $request->province,
            'address' => $request->address,
        ]);

        return (new UnitResource(true, 'Residential estate data added successfully!', $unit, 201))
                    ->response()
                    ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $unit = UnitApi::with('unitType')->findOrFail($id);

        return (new UnitResource(true, 'Residential estate data retrieved successfully!', $unit, 200))
                    ->response()
                    ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:unit,name',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:500048',
            'unit_type_id' => 'required|exists:unit_types,id',
            'description' => 'nullable|string',
            'size' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Errors',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        $unit = UnitApi::findOrFail($id);

        // Ensure $unit->image is a valid JSON string and decode it
        $oldImages = [];

        if (is_string($unit->image)) {
            $decodedImages = json_decode($unit->image, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedImages)) {
                $oldImages = $decodedImages;
            } else {
                Log::error('JSON decode error:', ['error' => json_last_error_msg()]);
            }
        } elseif (is_array($unit->image)) {
            $oldImages = $unit->image;
        }

        $newImages = [];

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                // Store the file on the 'public' disk
                $imagePath = $image->store('residential_estate', 'public');
                $newImages[] = Storage::url($imagePath);
            }
        
            // Log new images for debugging
            Log::info('New images:', ['newImages' => $newImages]);
        
            // Handle old images deletion
            foreach ($oldImages as $oldImage) {
                $oldImagePath = 'public/' . str_replace('storage/', '', $oldImage);
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
        }
        

        $unit->update([
            'name' => $request->name,
            'image' => json_encode($newImages),
            'unit_type_id' => $request->unit_type_id,
            'description' => $request->description,
            'size' => $request->size,
            'city' => $request->city,
            'province' => $request->province,
            'address' => $request->address,
        ]);

        return (new UnitResource(true, 'Residential estate data updated successfully!', $unit, 200))
                    ->response()
                    ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Log::info('Attempting to delete Residential Estate with ID: ' . $id);
            
        $unit = UnitApi::findOrFail($id);

        $images = [];
        if (is_string($unit->image)) {
            $images = json_decode($unit->image, true) ?: [];
        } elseif (is_array($unit->image)) {
            $images = $unit->image;
        }

        foreach ($images as $image) {
            // Remove the 'storage/' prefix to get the relative path
            $relativePath = str_replace('storage/', '', $image);
            // Construct the path for the public disk
            $imagePath = 'public/' . $relativePath;

            if (Storage::exists($imagePath)) {
                Storage::delete($imagePath);
                Log::info('Deleted image: ' . $imagePath);
            } else {
                Log::warning('Image not found: ' . $imagePath);
            }
        }


        $unit->delete();

        return response()->json(['success' => true, 'message' => 'Residential Estate data deleted successfully!'], 200);
    }
}
