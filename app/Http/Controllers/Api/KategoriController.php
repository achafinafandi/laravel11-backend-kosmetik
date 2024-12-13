<?php

namespace App\Http\Controllers\Api;

use App\Models\Kategori;
use App\Http\Controllers\Controller;
use App\Http\Resources\KategoriResource; // Import resource
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // Get all suppliers
        $kategoris = Kategori::latest()->paginate(5);

        // Return collection of suppliers as a resource
        return new KategoriResource(true, 'List Data Kategori', $kategoris);
    }

    public function store(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'nama_kategori'    => 'required|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create supplier
        $kategori = Kategori::create([
            'nama_kategori'    => $request->nama_kategori,
        ]);

        // Return response
        return new KategoriResource(true, 'Data Kategori Berhasil Ditambahkan!', $kategori);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        // Find supplier by ID
        $kategori = Kategori::find($id);

        // Check if supplier exists
        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Not Found',
            ], 404);
        }

        // Return single supplier as a resource
        return new KategoriResource(true, 'Detail Data Kategori!', $kategori);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'nama_kategori'    => 'required|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find supplier by ID
        $kategori = Kategori::find($id);

        // Check if supplier exists
        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Not Found',
            ], 404);
        }

        // Update supplier data
        $kategori->update([
            'nama_kategori'    => $request->nama_kategori,
        ]);

        // Return response
        return new KategoriResource(true, 'Data Kategori Berhasil Diubah!', $kategori);
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {

        //find post by ID
        $post = Kategori::find($id);

        //delete post
        $post->delete();

        //return response
        return new KategoriResource(true, 'Data Kategori Berhasil Dihapus!', null);
    }
}
