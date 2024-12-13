<?php

namespace App\Http\Controllers\Api;

use App\Models\Lokasi;
use App\Http\Controllers\Controller;
use App\Http\Resources\LokasiResource; // Import resource
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LokasiController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // Get all lokasis
        $lokasis = Lokasi::latest()->paginate(5);

        // Return collection of lokasis as a resource
        return new LokasiResource(true, 'List Data Lokasi', $lokasis);
    }

    public function store(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'nama_lokasi'    => 'required|string|max:100',
            'deskripsi_lokasi'  => 'nullable|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create lokasi
        $lokasi = lokasi::create([
            'nama_lokasi'    => $request->nama_lokasi,
            'deskripsi_lokasi'  => $request->deskripsi_lokasi,
        ]);

        // Return response
        return new LokasiResource(true, 'Data Lokasi Berhasil Ditambahkan!', $lokasi);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        // Find lokasi by ID
        $lokasi = Lokasi::find($id);

        // Check if lokasi exists
        if (!$lokasi) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi Not Found',
            ], 404);
        }

        // Return single lokasi as a resource
        return new LokasiResource(true, 'Detail Data Lokasi!', $lokasi);
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
            'nama_lokasi'    => 'required|string|max:100',
            'deskripsi_lokasi'  => 'required|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find lokasi by ID
        $lokasi = Lokasi::find($id);

        // Check if lokasi exists
        if (!$lokasi) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi Not Found',
            ], 404);
        }

        // Update lokasi data
        $lokasi->update([
            'nama_lokasi'    => $request->nama_lokasi,
            'deskripsi_lokasi'  => $request->deskripsi_lokasi,
        ]);

        // Return response
        return new LokasiResource(true, 'Data Lokasi Berhasil Diubah!', $lokasi);
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
        $post = Lokasi::find($id);

        //delete post
        $post->delete();

        //return response
        return new LokasiResource(true, 'Data Lokasi Berhasil Dihapus!', null);
    }
}
