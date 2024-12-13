<?php

namespace App\Http\Controllers\Api;

use App\Models\Rak; // Import model Rak
use App\Http\Controllers\Controller;
use App\Http\Resources\RakResource; // Import resource untuk response standar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RakController extends Controller
{
    /**
     * Index
     *
     * @return void
     */
    public function index()
    {
        // Get all rak with pagination
        $raks = Rak::with(['barang', 'lokasi'])->latest()->paginate(5);

        // Return collection of rak as a resource
        return new RakResource(true, 'List Data Rak', $raks);
    }

    /**
     * Store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'id_barang'    => 'required|exists:tb_barang,id',
            'id_lokasi'    => 'required|exists:tb_lokasi,id',
            'nama_rak'    => 'required|string|max:255',
            'jumlah_stok'      => 'min:0|integer',
            'deskripsi_rak'  => 'required|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create new rak
        $rak = Rak::create($request->all());

        // Return response
        return new RakResource(true, 'Data Rak Berhasil Ditambahkan!', $rak);
    }

    /**
     * Show
     *
     * @param  int $id
     * @return void
     */
    public function show($id)
    {
        // Find rak by ID
        $rak = Rak::with(['barang', 'lokasi'])->find($id);

        // Check if rak exists
        if (!$rak) {
            return response()->json([
                'success' => false,
                'message' => 'Rak Not Found',
            ], 404);
        }

        // Return single rak as a resource
        return new RakResource(true, 'Detail Data Rak!', $rak);
    }

    /**
     * Update
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'id_barang'    => 'required|exists:tb_barang,id',
            'id_lokasi'    => 'required|exists:tb_lokasi,id',
            'nama_rak'    => 'required|string|max:255',
            'jumlah_stok'      => 'min:0|integer',
            'deskripsi_rak'  => 'required|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find rak by ID
        $rak = Rak::find($id);

        // Check if rak exists
        if (!$rak) {
            return response()->json([
                'success' => false,
                'message' => 'Rak Not Found',
            ], 404);
        }

        // Update rak data
        $rak->update($request->all());

        // Return response
        return new RakResource(true, 'Data Rak Berhasil Diubah!', $rak);
    }

    /**
     * Destroy
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        // Find rak by ID
        $rak = Rak::find($id);

        // Check if rak exists
        if (!$rak) {
            return response()->json([
                'success' => false,
                'message' => 'Rak Not Found',
            ], 404);
        }

        // Delete rak
        $rak->delete();

        // Return response
        return new RakResource(true, 'Data Rak Berhasil Dihapus!', null);
    }
}
