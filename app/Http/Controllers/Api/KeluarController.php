<?php

namespace App\Http\Controllers\Api;

use App\Models\Keluar; // Import model tb_keluar
use App\Models\Barang; // Import model tb_barang
use App\Models\Rak; // Import model tb_rak
use App\Models\User; // Import model users
use App\Http\Controllers\Controller;
use App\Http\Resources\KeluarResource; // Import resource KeluarResource
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KeluarController extends Controller
{
    public function store(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'id_barang'            => 'required|exists:tb_barang,id',
            'id_user'              => 'required|exists:users,id',
            'id_lokasi_asal'       => 'required|exists:tb_rak,id',
            'jumlah_keluar'        => 'required|integer',
            'harga_jual_per_pcs'   => 'nullable|decimal:0,2',
            'tujuan_barang_keluar' => 'nullable|string|max:255',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        // Get harga jual from Barang
        $Barang = Barang::find($request->id_barang);
        if (!$Barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }
    
        // If harga_jual_per_pcs is not provided, use harga_jual from Barang
        $hargaJualPerPcs = $request->harga_jual_per_pcs ?: $Barang->harga_jual;
    
        // Calculate total
        $total = $request->jumlah_keluar * $hargaJualPerPcs;
    
        // Create new Keluar entry with the calculated total
        $Keluar = Keluar::create(array_merge($request->all(), [
            'total' => $total,
            'harga_jual_per_pcs' => $hargaJualPerPcs
        ]));
    
        // Update rak stock after barang keluar
        $Rak = Rak::find($Keluar->id_lokasi_asal);
        if ($Rak) {
            // Reduce stock based on jumlah_keluar
            if ($Rak->jumlah_stok >= $Keluar->jumlah_keluar) {
                $Rak->jumlah_stok -= $Keluar->jumlah_keluar;
                $Rak->save();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok Rak tidak mencukupi untuk pengeluaran ini.',
                ], 400);
            }
        }
    
        // Return response
        return new KeluarResource(true, 'Data Barang Keluar Berhasil Ditambahkan!', $Keluar);
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
            'id_barang'            => 'required|exists:tb_barang,id',
            'id_user'              => 'required|exists:users,id',
            'id_lokasi_asal'       => 'required|exists:tb_rak,id',
            'jumlah_keluar'        => 'required|integer',
            'harga_jual_per_pcs'   => 'nullable|decimal:0,2',
            'tujuan_barang_keluar' => 'nullable|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find Keluar by ID
        $Keluar = Keluar::find($id);

        // Check if Keluar exists
        if (!$Keluar) {
            return response()->json([
                'success' => false,
                'message' => 'Barang Keluar Not Found',
            ], 404);
        }

        // Get harga jual from Barang
        $Barang = Barang::find($request->id_barang);
        if (!$Barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        // If harga_jual_per_pcs is not provided in the request, use harga_jual from Barang
        $hargaJualPerPcs = $request->harga_jual_per_pcs ?: $Barang->harga_jual;

        // Save the old quantity for later adjustment
        $oldQuantity = $Keluar->jumlah_keluar;

        // Calculate new total
        $newTotal = $request->jumlah_keluar * $hargaJualPerPcs;

        // Update Keluar data with new request, including recalculated total
        $Keluar->update(array_merge($request->all(), ['total' => $newTotal, 'harga_jual_per_pcs' => $hargaJualPerPcs]));

        // Update rak stock (Lokasi Asal)
        $Rak = Rak::find($Keluar->id_lokasi_asal);
        if ($Rak) {
            // Calculate quantity difference
            $quantityDifference = $Keluar->jumlah_keluar - $oldQuantity;

            // Validate if stock becomes negative
            if ($Rak->jumlah_stok + $quantityDifference < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok Rak tidak mencukupi untuk perubahan ini.',
                ], 400);
            }

            // Adjust stock
            $Rak->jumlah_stok -= $quantityDifference;
            $Rak->save();
        }

        // Return response
        return new KeluarResource(true, 'Data Barang Keluar Berhasil Diubah!', $Keluar);
    }
}
