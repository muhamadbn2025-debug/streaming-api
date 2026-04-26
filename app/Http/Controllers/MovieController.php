<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MovieController extends Controller
{
    // GET /movies
    public function index(Request $request)
    {
        $query = Movie::with('category');

        // Filter by search (judul)
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by category_id
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Sorting
        if ($request->sort_by && $request->order) {
            $query->orderBy($request->sort_by, $request->order);
        }

        $movies = $query->get();

        if ($movies->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Film tidak ditemukan',
                'data' => []
            ], 404);
        }
        // Rating Classification
        $movies->transform(function ($movie) {
            if ($movie->rating >= 8.5) {
                $movie->rating_class = 'Top Rated';
            } elseif ($movie->rating >= 7.0) {
                $movie->rating_class = 'Popular';
            } else {
                $movie->rating_class = 'Regular';
            }
            return $movie;
        });

        return response()->json([
            'success' => true,
            'message' => 'Data film berhasil diambil',
            'data' => $movies
        ]);

    }

    // POST /movies
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'rating' => 'required|numeric|min:0|max:10',
                'release_year' => 'required|integer|min:1900|max:2030',
                'category_id' => 'required|exists:movie_category,id',
                'thumbnail' => 'required|string|max:255',
            ]);

            $movie = Movie::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Film berhasil ditambahkan',
                'data' => $movie->load('category')
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // GET /movies/{id}
    public function show($id)
    {
        $movie = Movie::with('category')->find($id);

        if (!$movie) {
            return response()->json([
                'success' => false,
                'message' => 'Film tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail film berhasil diambil',
            'data' => $movie
        ]);
    }

    // PUT /movies/{id}
    public function update(Request $request, $id)
    {
        $movie = Movie::find($id);

        if (!$movie) {
            return response()->json([
                'success' => false,
                'message' => 'Film tidak ditemukan',
            ], 404);
        }
        try {

            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'rating' => 'sometimes|numeric|min:0|max:10',
                'release_year' => 'sometimes|integer|min:1900|max:2030',
                'category_id' => 'sometimes|exists:movie_category,id',
                'thumbnail' => 'sometimes|string|max:255',
            ]);

            $movie->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Film berhasil diupdate',
                'data' => $movie->load('category')
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // DELETE /movies/{id}
    public function destroy($id)
    {
        $movie = Movie::find($id);

        if (!$movie) {
            return response()->json([
                'success' => false,
                'message' => 'Film tidak ditemukan',
            ], 404);
        }

        $movie->delete();

        return response()->json([
            'success' => true,
            'message' => 'Film berhasil dihapus',
        ]);
    }
}
