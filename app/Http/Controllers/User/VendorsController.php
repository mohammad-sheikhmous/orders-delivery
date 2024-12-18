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
            $vendors = Vendor::where('main_category_id', $category_id)->where('active', 0)
                ->select('id', 'name', 'logo','main_category_id')
                ->with(['mainCategory' => function ($q) {
                    $q->select('id', 'name');
                }])->get();

            if (!isset($vendors))
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

    public function show($category_id, $id)
    {
        try {
            $vendor = Vendor::where('main_category_id', $category_id)->selection()->find($id);
            if (!isset($vendors))
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
