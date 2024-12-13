<?php

namespace App\Http\Controllers\Api;

use App\Models\Pemindahan;
use App\Models\Rak;
use App\Models\Barang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PemindahanResource;

class PemindahanController extends Controller
{
    /**
     * Index
     *
     * @return void
     */
    public function index()
    {
        // Get all pemindahan records with pagination
        $pemindahan = Pemindahan::with(['barang', 'user', 'lokasiAsal', 'lokasiTujuan'])
            ->latest()
            ->paginate(5);

        // Return collection of pemindahan as a resource
        return new PemindahanResource(true, 'List Data Pemindahan', $pemindahan);
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
            'id_barang'        => 'required|exists:tb_barang,id',
            'id_user'          => 'required|exists:users,id',
            'id_lokasi_asal'   => 'required|exists:tb_rak,id',
            'id_lokasi_tujuan' => 'required|exists:tb_rak,id|different:id_lokasi_asal',
            'jumlah_barang'    => 'required|integer|min:1',
            'alasan_pemindahan' => 'nullable|string',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Get Rak asal
        $RakAsal = Rak::find($request->id_lokasi_asal);

        // Validate stock availability
        if ($RakAsal->jumlah_stok < $request->jumlah_barang) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah barang melebihi stok yang tersedia di lokasi asal. Harap isi stok barang terlebih dahulu',
            ], 400);
        }

        // Deduct stock from Rak asal
        $RakAsal->jumlah_stok -= $request->jumlah_barang;
        $RakAsal->save();

        // Add stock to Rak tujuan
        $RakTujuan = Rak::find($request->id_lokasi_tujuan);
        $RakTujuan->jumlah_stok += $request->jumlah_barang;
        $RakTujuan->save();

        // Create new Pemindahan
        $Pemindahan = Pemindahan::create($request->all());

        // Return response
        return new PemindahanResource(true, 'Data Pemindahan Barang Berhasil Ditambahkan!', $Pemindahan);
    }

    /**
     * Show
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        // Find pemindahan by ID
        $pemindahan = Pemindahan::with(['barang', 'user', 'lokasiAsal', 'lokasiTujuan'])->find($id);

        // Check if pemindahan exists
        if (!$pemindahan) {
            return response()->json([
                'success' => false,
                'message' => 'Pemindahan Not Found',
            ], 404);
        }

        // Return single pemindahan as a resource
        return new PemindahanResource(true, 'Detail Data Pemindahan!', $pemindahan);
    }

        public function update(Request $request, $id)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'id_barang'        => 'required|exists:tb_barang,id',
            'id_user'          => 'required|exists:users,id',
            'id_lokasi_asal'   => 'required|exists:tb_rak,id',
            'id_lokasi_tujuan' => 'required|exists:tb_rak,id|different:id_lokasi_asal',
            'jumlah_barang'    => 'required|integer|min:1',
            'alasan_pemindahan' => 'nullable|string',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find existing Pemindahan
        $Pemindahan = Pemindahan::find($id);

        if (!$Pemindahan) {
            return response()->json([
                'success' => false,
                'message' => 'Data Pemindahan Barang Tidak Ditemukan.',
            ], 404);
        }

        // Get Rak asal and tujuan
        $RakAsal = Rak::find($request->id_lokasi_asal);
        $RakTujuan = Rak::find($request->id_lokasi_tujuan);

        // Calculate stock difference
        $stockDifference = $request->jumlah_barang - $Pemindahan->jumlah_barang;

        // Validate stock availability for the updated request
        if ($RakAsal->jumlah_stok < $stockDifference) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah barang melebihi stok yang tersedia di lokasi asal. Harap isi stok barang terlebih dahulu',
            ], 400);
        }

        // Adjust stocks
        $RakAsal->jumlah_stok -= $stockDifference;
        $RakAsal->save();

        $RakTujuan->jumlah_stok += $stockDifference;
        $RakTujuan->save();

        // Update Pemindahan data
        $Pemindahan->update($request->all());

        // Return response
        return new PemindahanResource(true, 'Data Pemindahan Barang Berhasil Diperbarui!', $Pemindahan);
    }

    /**
     * Destroy
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        // Find pemindahan by ID
        $pemindahan = Pemindahan::find($id);

        // Check if pemindahan exists
        if (!$pemindahan) {
            return response()->json([
                'success' => false,
                'message' => 'Pemindahan Not Found',
            ], 404);
        }

        // Get data for stok update
        $rakAsal = Rak::find($pemindahan->id_lokasi_asal);
        $rakTujuan = Rak::find($pemindahan->id_lokasi_tujuan);

        // Reverse stok update
        if ($rakAsal) {
            $rakAsal->jumlah_stok += $pemindahan->jumlah_barang;
            $rakAsal->save();
        }

        if ($rakTujuan) {
            $rakTujuan->jumlah_stok -= $pemindahan->jumlah_barang;
            $rakTujuan->save();
        }

        // Delete pemindahan
        $pemindahan->delete();

        // Return response
        return new PemindahanResource(true, 'Data Pemindahan Berhasil Dihapus!', null);
    }
}
