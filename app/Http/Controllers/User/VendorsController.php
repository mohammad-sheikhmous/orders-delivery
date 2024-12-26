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
                    'message' => __('messages.vendors not found...!'),
                ], 400);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => __('messages.all vendors returned..'),
                'vendors' => $vendors,
            ]);

        } catch (\Exception $exception) {
            return returnExceptionJson();
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
                    'message' => __('messages.the vendor not found...!'),
                ], 400);

            $vendor->mainCategory = $vendor->mainCategory()->value('name');

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => __('messages.the vendor returned successfully...'),
                'vendor' => $vendor,
            ]);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }
}
