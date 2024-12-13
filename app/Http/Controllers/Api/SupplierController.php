<?php

namespace App\Http\Controllers\Api;

use App\Models\Supplier;
use App\Http\Controllers\Controller;
use App\Http\Resources\SupplierResource; // Import resource
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // Get all suppliers
        $suppliers = Supplier::latest()->paginate(5);

        // Return collection of suppliers as a resource
        return new SupplierResource(true, 'List Data Supplier', $suppliers);
    }

    public function store(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'nama'    => 'required|string|max:100',
            'kontak'  => 'required|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create supplier
        $supplier = Supplier::create([
            'nama'    => $request->nama,
            'kontak'  => $request->kontak,
        ]);

        // Return response
        return new SupplierResource(true, 'Data Supplier Berhasil Ditambahkan!', $supplier);
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
        $supplier = Supplier::find($id);

        // Check if supplier exists
        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier Not Found',
            ], 404);
        }

        // Return single supplier as a resource
        return new SupplierResource(true, 'Detail Data Supplier!', $supplier);
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
            'nama'    => 'required|string|max:100',
            'kontak'  => 'required|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find supplier by ID
        $supplier = Supplier::find($id);

        // Check if supplier exists
        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier Not Found',
            ], 404);
        }

        // Update supplier data
        $supplier->update([
            'nama'    => $request->nama,
            'kontak'  => $request->kontak,
        ]);

        // Return response
        return new SupplierResource(true, 'Data Supplier Berhasil Diubah!', $supplier);
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
        $post = Supplier::find($id);

        //delete post
        $post->delete();

        //return response
        return new SupplierResource(true, 'Data Supplier Berhasil Dihapus!', null);
    }
}
