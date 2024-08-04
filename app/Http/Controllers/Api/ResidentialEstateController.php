<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResidentialEstateResource as ResourcesResidentialEstate;
use App\Models\ResidentialEstate as ApiResidentialEstate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResidentialEstateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all residential estates with pagination
        $residentialEstates = ApiResidentialEstate::with('unitType')->latest()->paginate(5);
        
        // Return a response using the resource with status code 200 (OK)
        return (new ResourcesResidentialEstate(true, 'List Data Residential Estates', $residentialEstates, 200))
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
            'housing_name' => 'required|unique:residential_estates,housing_name|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:500048',
            'unit_type_id' => 'required|exists:unit_types,id',
            'description' => 'required|string',
            'size' => 'required|string',
            'location' => 'required|string',
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
        $residentialEstate = ApiResidentialEstate::create([
            'housing_name' => $request->housing_name,
            'image' => $imagePath,
            'unit_type_id' => $request->unit_type_id,
            'description' => $request->description,
            'size' => $request->size,
            'location' => $request->location,
        ]);

        // Return response with status code 201 (Created)
        return (new ResourcesResidentialEstate(true, 'Residential estate data added successfully!', $residentialEstate, 201))
                    ->response()
                    ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the residential estate by ID and include unitType relationship
        $residentialEstate = ApiResidentialEstate::with('unitType')->findOrFail($id);

        // Return a response using the resource with status code 200 (OK)
        return (new ResourcesResidentialEstate(true, 'Residential estate data retrieved successfully!', $residentialEstate, 200))
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
            'housing_name' => 'required|string|max:255|unique:residential_estates,housing_name,'.$id,
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:500048',
            'unit_type_id' => 'required|exists:unit_types,id',
            'description' => 'nullable|string',
            'size' => 'nullable|string',
            'location' => 'nullable|string',
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
        $residentialEstate = ApiResidentialEstate::with('unitType')->findOrFail($id);

        // Initialize $imagePath with current image path
        $imagePath = $residentialEstate->image;

        // Check if there is a new image file
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->storeAs('public/residential_estate', $image->hashName());
            $imagePath = 'storage/residential_estate/' . $image->hashName();
            
            // Delete old image file if it exists
            if ($residentialEstate->image && Storage::exists($residentialEstate->image)) {
                Storage::delete($residentialEstate->image);
            }
        }

        // Update residential estate
        $residentialEstate->update([
            'housing_name' => $request->housing_name,
            'image' => $imagePath,
            'unit_type_id' => $request->unit_type_id,
            'description' => $request->description,
            'size' => $request->size,
            'location' => $request->location,
        ]);

        // Return response with status code 200 (OK)
        return (new ResourcesResidentialEstate(true, 'Residential estate data updated successfully!', $residentialEstate, 200))
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
        $residentialEstate = ApiResidentialEstate::findOrFail($id);

        if (!$residentialEstate) {
            return response()->json(['success' => false, 'message' => 'Residential Estate not found'], 404);
        }

        // Proceed with deletion
        if ($residentialEstate->image && Storage::exists($residentialEstate->image)) {
            Storage::delete($residentialEstate->image);
        }

        $residentialEstate->delete();

        return response()->json(['success' => true, 'message' => 'Residential Estate data deleted successfully!'], 200);
                    
    }
}
