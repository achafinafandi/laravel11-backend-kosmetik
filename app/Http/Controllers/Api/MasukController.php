<?php

namespace App\Http\Controllers\Api;

use App\Models\Masuk; // Import model ProdukMasuk
use App\Models\Barang; // Import model Produk
use App\Models\Supplier; // Import model Supplier
use App\Models\Rak; // Import model User
use App\Http\Controllers\Controller;
use App\Http\Resources\MasukResource; // Import resource ProdukMasukResource
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasukController extends Controller
{
    /**
     * Index
     *
     * @return void
     */
    public function index()
    {
        // Get all Masuk with pagination and eager load relations
        $Masuks = Masuk::with(['Barang', 'Lokasi', 'Supplier', 'User'])->latest()->paginate(5);

        // Return collection of ProdukMasuk as a resource
        return new MasukResource(true, 'List Data Produk Masuk', $Masuks);
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
            'id_barang'      => 'required|exists:tb_barang,id',
            'id_lokasi'      => 'required|exists:tb_rak,id',
            'id_user'      => 'required|exists:users,id',
            'id_supplier'      => 'required|exists:tb_supplier,id',
            'jumlah_masuk'         => 'required|integer',
            'harga_beli_per_pcs'         => 'required|integer',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        // Create new Masuk
        $Masuk = Masuk::create($request->all());
    
        // Update stok lokasi setelah produk masuk
        $Rak = Rak::find($Masuk->id_lokasi);
        if ($Rak) {
            // Tambahkan jumlah produk yang masuk ke stok Rak
            $Rak->jumlah_stok += $Masuk->jumlah_masuk;
            // Update stok produk
            $Rak->save();
        }
        
        $Barang = Barang::find($Masuk->id_barang);
        if ($Barang) {
            // Perbarui harga beli barang
            $Barang->harga_beli = $Masuk->harga_beli_per_pcs;
            $Barang->save();
        }

        // Return response
        return new MasukResource(true, 'Data Produk Masuk Berhasil Ditambahkan!', $Masuk);
    }

    public function show($id)
    {
        // Find ProdukMasuk by ID
        $Masuk = Masuk::with(['Barang', 'Lokasi', 'Supplier', 'User'])->find($id);

        // Check if ProdukMasuk exists
        if (!$Masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk Masuk Not Found',
            ], 404);
        }

        // Return single ProdukMasuk as a resource
        return new MasukResource(true, 'Detail Data Produk Masuk!', $Masuk);
    }

        public function update(Request $request, $id)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'id_barang' => 'required|exists:tb_barang,id',
            'id_lokasi' => 'required|exists:tb_rak,id',
            'id_user' => 'required|exists:users,id',
            'id_supplier' => 'required|exists:tb_supplier,id',
            'jumlah_masuk' => 'required|integer',
            'harga_beli_per_pcs' => 'required|integer',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find Masuk by ID
        $Masuk = Masuk::find($id);

        // Check if Masuk exists
        if (!$Masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk Masuk Not Found',
            ], 404);
        }

        // Save current quantity (old quantity) for later adjustment
        $oldQuantity = $Masuk->jumlah_masuk;

        // Update Masuk data with new request
        $Masuk->update($request->all());

        // Update stok lokasi (Rak)
        $Rak = Rak::find($Masuk->id_lokasi);
        if ($Rak) {
            // Calculate quantity difference
            $quantityDifference = $Masuk->jumlah_masuk - $oldQuantity;

            // Validate if stok becomes negative
            if ($Rak->jumlah_stok + $quantityDifference < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah barang melebihi stok yang tersedia di lokasi asal. Harap isi stok barang terlebih dahulu',
                ], 400);
            }

            // Adjust stok Rak
            $Rak->jumlah_stok += $quantityDifference;
            $Rak->save();
        }

        // Update Barang harga beli
        $Barang = Barang::find($Masuk->id_barang);
        if ($Barang) {
            $Barang->harga_beli = $Masuk->harga_beli_per_pcs;
            $Barang->save();
        }

        // Return response
        return new MasukResource(true, 'Data Produk Masuk Berhasil Diubah!', $Masuk);
    }

    
        /**
     * Destroy
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        // Find Masuk by ID
        $Masuk = Masuk::find($id);

        // Check if Masuk exists
        if (!$Masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk Masuk Not Found',
            ], 404);
        }

        // Save necessary data before deleting
        $idLokasi = $Masuk->id_lokasi;
        $jumlahMasuk = $Masuk->jumlah_masuk;

        // Delete Masuk
        $Masuk->delete();

        // Update stok lokasi (Rak)
        $Rak = Rak::find($idLokasi);
        if ($Rak) {
            // Validate if stok becomes negative
            if ($Rak->jumlah_stok - $jumlahMasuk < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah barang melebihi stok yang tersedia di lokasi asal. Harap isi stok barang terlebih dahulu',
                ], 400);
            }

            // Reduce stok Rak
            $Rak->jumlah_stok -= $jumlahMasuk;
            $Rak->save();
        }

        // Return response
        return new MasukResource(true, 'Data Produk Masuk Berhasil Dihapus!', null);
    }
}

