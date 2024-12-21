<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Models\Vendor;
use Illuminate\Support\Facades\Storage;

class VendorsController extends Controller
{
    public function index($category_id)
    {
        try {
            $vendors = Vendor::where('main_category_id', $category_id)
                ->where('active', 1)
                ->selectionForIndexing()
                ->with(['mainCategory' => function ($q) {
                    $q->select('id', 'name');
                }])->get();

            if ($vendors->isEmpty())
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'vendors not found...!',
                ], 400);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'all vendors returned..',
                'vendors' => $vendors,
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function show($id)
    {
        try {
            $vendor = Vendor::where('active', 1)
                ->selectionForShowing()->find($id);

            if (!isset($vendor))
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'the vendor not found...!',
                ], 400);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'the vendor returned successfully...',
                'vendor' => $vendor,
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }
}
