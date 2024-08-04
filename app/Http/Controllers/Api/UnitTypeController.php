<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitTypeResource as ResourcesUnitType;
use App\Http\Resources\UnitTypeResource;
use App\Models\UnitType as ApiUnitType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UnitTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all unit types with pagination
        $unitType = ApiUnitType::latest()->paginate(5);
        
        // Return a response using the resource with status code 200 (OK)
        return response()->json([
            'success' => true,
            'message' => 'List Unit Type Data',
            'data'    => [
                'current_page' => $unitType->currentPage(),
                'data'         => UnitTypeResource::collection($unitType->items()),
                'first_page_url' => $unitType->url(1),
                'from'         => $unitType->firstItem(),
                'last_page'    => $unitType->lastPage(),
                'last_page_url' => $unitType->url($unitType->lastPage()),
                'next_page_url' => $unitType->nextPageUrl(),
                'path'         => $unitType->path(),
                'per_page'     => $unitType->perPage(),
                'prev_page_url' => $unitType->previousPageUrl(),
                'to'           => $unitType->lastItem(),
                'total'        => $unitType->total(),
            ],
            'status'  => 200,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        // Mendefinisikan aturan validasi
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:unit_types,name',
        ]);

        // Memeriksa apakah validasi gagal
        if ($validator->fails()) {
            // Mengembalikan respons dengan status kode 422 (Unprocessable Entity)
            return response()->json([
                'success' => false,
                'message' => 'Validation Errors',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        // Membuat unit type
        $unitType = ApiUnitType::create([
            'name' => $request->name,
        ]);

        // Mengembalikan respons dengan status kode 201 (Created)
        return response()->json([
            'success' => true,
            'message' => 'Unit type data added successfully!',
            'data'    => new ResourcesUnitType($unitType),
            'status'  => 201,
        ], 201);
    }

    /**
     * Menampilkan unit type tertentu.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        // Mencari unit type berdasarkan ID
        $unitType = ApiUnitType::findOrFail($id);

        // Mengembalikan respons dengan status kode 200 (OK)
        return response()->json([
            'success' => true,
            'message' => 'List Data Unit Types ID : '. $id,
            'data'    => new ResourcesUnitType($unitType),
            'status'  => 200,
        ], 200);
    }

    /**
     * Memperbarui unit type yang sudah ada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        // Mendefinisikan aturan validasi
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:unit_types,name,' . $id,
        ]);

        // Memeriksa apakah validasi gagal
        if ($validator->fails()) {
            // Mengembalikan respons dengan status kode 422 (Unprocessable Entity)
            return response()->json([
                'success' => false,
                'message' => 'Validation Errors',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        // Mencari unit type berdasarkan ID
        $unitType = ApiUnitType::findOrFail($id);

        // Memperbarui unit type
        $unitType->update([
            'name' => $request->name,
        ]);

        // Mengembalikan respons dengan status kode 200 (OK)
        return response()->json([
            'success' => true,
            'message' => 'Unit type data changed successfully!',
            'data'    => new ResourcesUnitType($unitType),
            'status'  => 200,
        ], 200);
    }

    /**
     * Menghapus unit type yang sudah ada.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        // Mencari unit type berdasarkan ID
        $unitType = ApiUnitType::findOrFail($id);
        $unitType->delete();

        // Mengembalikan respons dengan status kode 200 (OK)
        return response()->json([
            'success' => true,
            'message' => 'Unit type data deleted successfully!',
            'status'  => 200,
        ], 200);
    }
}

