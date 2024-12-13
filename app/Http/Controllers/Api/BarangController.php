<?php

namespace App\Http\Controllers\Api;

use App\Models\Barang; // Import model Barang
use App\Http\Controllers\Controller;
use App\Http\Resources\BarangResource; // Import resource untuk response standar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    /**
     * Index
     *
     * @return void
     */
    public function index()
    {
    // Get all barang where stok_minimum >= 10 with pagination
    $barangs = Barang::with(['kategori', 'supplier'])
        ->where('stok_minimum', '>=', 10)
        ->latest()
        ->paginate(5);

    // Return collection of barang as a resource
    return new BarangResource(true, 'List Data Barang', $barangs);
    }


    /**
     * Store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {

        $stokMinimum = $request->input('stok_minimum', 0); // Default to 0 if not provided
        if ($stokMinimum < 10) {
            return response()->json([
                'success' => false,
                'message' => 'Stok minimum tidak boleh kurang dari 10.'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'id_kategori'    => 'required|exists:tb_kategori,id',
            'id_supplier'    => 'required|exists:tb_supplier,id',
            'nama_barang'    => 'required|string|max:255',
            'harga_beli'          => 'min:0|integer',
            'harga_jual'          => 'min:0|integer',
            'stok_minimum'      => 'min:10|integer',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create new barang
        $barang = Barang::create($request->all());

        // Return response
        return new BarangResource(true, 'Data Barang Berhasil Ditambahkan!', $barang);
    }

    /**
     * Show
     *
     * @param  int $id
     * @return void
     */
    public function show($id)
    {
        // Find barang by ID
        $barang = Barang::with(['kategori', 'supplier'])->find($id);

        // Check if barang exists
        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang Not Found',
            ], 404);
        }

        // Return single barang as a resource
        return new BarangResource(true, 'Detail Data Barang!', $barang);
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
            'id_kategori'    => 'required|exists:tb_kategori,id',
            'id_supplier'    => 'required|exists:tb_supplier,id',
            'nama_barang'    => 'required|string|max:255',
            'harga_beli'          => 'min:0|integer',
            'harga_jual'          => 'min:0|integer',
            'stok_minimum'      => 'min:0|integer',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find barang by ID
        $barang = Barang::find($id);

        // Check if barang exists
        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang Not Found',
            ], 404);
        }

        // Update barang data
        $barang->update($request->all());

        // Return response
        return new BarangResource(true, 'Data Barang Berhasil Diubah!', $barang);
    }

    /**
     * Destroy
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        // Find barang by ID
        $barang = Barang::find($id);

        // Check if barang exists
        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang Not Found',
            ], 404);
        }

        // Delete barang
        $barang->delete();

        // Return response
        return new BarangResource(true, 'Data Barang Berhasil Dihapus!', null);
    }
}
