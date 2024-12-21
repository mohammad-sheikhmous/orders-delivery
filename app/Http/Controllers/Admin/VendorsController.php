<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Models\Vendor;
use Illuminate\Support\Facades\Storage;

class VendorsController extends Controller
{
    public function index()
    {
        try {
//            $vendors = Vendor::selection()->get();

            $vendors = Vendor::selectionForIndexing()
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
            $vendor = Vendor::selectionForShowing()->find($id);

            if (!$vendor)
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

    public function create(VendorRequest $request)
    {
        try {
            $logoPath = saveImages('vendors', $request->logo);

            $request->merge(['logo' => $logoPath]);

            Vendor::create($request->all());

            return response()->json([
                'status' => true,
                'status code' => 201,
                'message' => 'New Vendor created successfully...'
            ], 201);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function update(VendorRequest $request,$id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor)
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'Vendor not found...'
                ]);

            if ($request->logo) {
                Storage::disk('images')->delete($vendor->logo);

                $logoPath = saveImages('vendors', $request->logo);
            } else {
                $logoPath = $vendor->logo;
            }
            $request->merge(['logo' => $logoPath]);

            $updated = $vendor->update($request->all());

            $message = ($updated) ? 'The Vendor updated successfully...'
                : 'No modifications have been made...';

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => $message
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor)
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'The Vendor not found...',
                ], 400);

            $products = $vendor->productCategories();
            if (isset($products) && $products->count() > 0)
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'The Vendor cannot be deleted...',
                ], 400);

            $logoPath = $vendor->logo;
            Storage::disk('images')->delete($logoPath);
            $vendor->delete();

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'The Vendor deleted successfully...',
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
