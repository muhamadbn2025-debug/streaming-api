<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MovieCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MovieCategoryController extends Controller
{
    // GET /api/categories
    public function index()
    {
        $categories = MovieCategory::withCount('movies')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data kategori berhasil diambil',
            'data' => $categories
        ]);
    }

    // POST /api/categories
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:movie_category,name',
                'description' => 'required|string',
            ]);

            $category = MovieCategory::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $category
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // GET /api/categories/{id}
    public function show($id)
    {
        $category = MovieCategory::withCount('movies')->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kategori berhasil diambil',
            'data' => $category
        ]);
    }

    // PUT /api/categories/{id}
    public function update(Request $request, $id)
    {
        $category = MovieCategory::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }
        try {

            $validated = $request->validate([
                'name' => 'sometimes|string|max:100|unique:movie_category,name,' . $id,
                'description' => 'nullable|string',
            ]);

            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diupdate',
                'data' => $category
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // DELETE /api/categories/{id}
    public function destroy($id)
    {
        $category = MovieCategory::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        // Cek apakah kategori masih dipakai oleh movie
        if ($category->movies()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak bisa dihapus karena masih dipakai oleh film',
            ], 409);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus',
        ]);
    }
}


