<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Models\Vendor;
use Illuminate\Support\Facades\Storage;

class VendorsController extends Controller
{
    public function index($category_id)
    {
        try {
            if (auth()->tokenCan('admin'))
                $vendors = Vendor::selection()->get();
            else
                $vendors = Vendor::where('category_id', $category_id)->where('active', 1)
                    ->select('id', 'name', 'logo')
                    ->with(['category' => function ($q) {
                        $q->select('id', 'name');
                    }])->get();

            if (!$vendors)
                return response()->json([
                    'message' => 'vendors not found...!',
                ], 204);

            return response()->json([
                'message' => 'all vendors returned..',
                'vendors' => $vendors,
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function show($id)
    {
        try {
            $vendor = Vendor::selection()->find($id);
            if (!$vendor)
                return response()->json([
                    'message' => 'the vendor not found...!',
                ]);

            return response()->json([
                'message' => 'the vendor returned successfully...',
                'vendor' => $vendor,
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function store(VendorRequest $request)
    {
        try {
            $logo = $request->logo;
            $logoPath = saveImages('vendors', $logo);
            $request->replace(['logo' => $logoPath]);

            Vendor::create($request->all());

            return response()->json([
                'message' => 'New Vendor created successfully...'
            ], 201);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function update(VendorRequest $request)
    {
        try {
            $vendor_id = $request->id;
            $vendor = Vendor::find($vendor_id);
            if (!$vendor)
                return response()->json([
                    'message' => 'Vendor not found...'
                ]);

            if ($request->logo) {
                $logoPath = saveImages('vendors', $request->logo);
            } else {
                $logoPath = $vendor->logo;
            }
            $request->replace(['logo' => $logoPath]);

            $vendor->update($request->all());

            return response()->json([
                'message' => 'The Vendor updated successfully...'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function destroy()
    {
        try {
            $vendor = Vendor::find(request()->id);
            if (!$vendor)
                return response()->json([
                    'message' => 'Vendor not found...',
                ]);

            $products = $vendor->products();
            if (isset($products) && $products->count() > 0)
                return response()->json([
                    'message' => 'The Vendor cannot be deleted...',
                ], 400);

            $logoPath = $vendor->logo;
            Storage::disk('images')->delete($logoPath);
            $vendor->delete();

            return response()->json([
                'message' => 'The Vendor deleted successfully...',
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'something went wrong...!'
            ], 400);
        }
    }
}
