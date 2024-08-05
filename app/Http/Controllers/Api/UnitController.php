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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:500048',
            'unit_type_id' => 'required|exists:unit_types,id',
            'description' => 'required|string',
            'size' => 'required|string',
            'location' => 'required|string',
            'address' => 'required|string',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Return a response with status code 422 (Unprocessable Entity)
            return response()->json([
                'success' => false,
                'message' => 'Validation Errors',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        // Handle file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->storeAs('public/residential_estate', $image->hashName());
            $imagePath = 'storage/residential_estate/' . $image->hashName();
        } else {
            $imagePath = null;
        }

        // Create the residential estate
        $unit = UnitApi::create([
            'name' => $request->housing_name,
            'image' => $imagePath,
            'unit_type_id' => $request->unit_type_id,
            'description' => $request->description,
            'size' => $request->size,
            'location' => $request->location,
            'address' => $request->address,
        ]);

        // Return response with status code 201 (Created)
        return (new UnitResource(true, 'Residential estate data added successfully!', $unit, 201))
                    ->response()
                    ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the residential estate by ID and include unitType relationship
        $unit = UnitApi::with('unitType')->findOrFail($id);

        // Return a response using the resource with status code 200 (OK)
        return (new UnitResource(true, 'Residential estate data retrieved successfully!', $unit, 200))
                    ->response()
                    ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Debugging
        log::info('Request Data:', $request->all());
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:unit,name,'.$id,
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:500048',
            'unit_type_id' => 'required|exists:unit_types,id',
            'description' => 'nullable|string',
            'size' => 'nullable|string',
            'location' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Return a response with status code 422 (Unprocessable Entity)
            return response()->json([
                'success' => false,
                'message' => 'Validation Errors',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        // Find the residential estate by ID
        $unit = UnitApi::with('unitType')->findOrFail($id);

        // Initialize $imagePath with current image path
        $imagePath = $unit->image;

        // Check if there is a new image file
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->storeAs('public/unit', $image->hashName());
            $imagePath = 'storage/unit/' . $image->hashName();
            
            // Delete old image file if it exists
            if ($unit->image && Storage::exists($unit->image)) {
                Storage::delete($unit->image);
            }
        }

        // Update residential estate
        $unit->update([
            'name' => $request->housing_name,
            'image' => $imagePath,
            'unit_type_id' => $request->unit_type_id,
            'description' => $request->description,
            'size' => $request->size,
            'location' => $request->location,
            'address' => $request->address,
        ]);

        // Return response with status code 200 (OK)
        return (new UnitResource(true, 'Residential estate data updated successfully!', $unit, 200))
                    ->response()
                    ->setStatusCode(200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Log the incoming ID for debugging
        Log::info('Attempting to delete Residential Estate with ID: ' . $id);

        // Use find() for debugging
        $unit = UnitApi::findOrFail($id);

        if (!$unit) {
            return response()->json(['success' => false, 'message' => 'Residential Estate not found'], 404);
        }

        // Proceed with deletion
        if ($unit->image && Storage::exists($unit->image)) {
            Storage::delete($unit->image);
        }

        $unit->delete();

        return response()->json(['success' => true, 'message' => 'Residential Estate data deleted successfully!'], 200);
                    
    }
}
