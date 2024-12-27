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
            return returnExceptionJson();
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
                'vendor' => $vendor->makeVisible('name'),
            ]);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function create(VendorRequest $request)
    {
        try {
            $photoPath = saveImages('vendors', $request->photo);

            $request = $request->toArray();
            $request['photo'] = $photoPath;
            $request['name'] = ['en' => $request['name_en'], 'ar' => $request['name_ar']];

            Vendor::create($request);

            return response()->json([
                'status' => true,
                'status code' => 201,
                'message' => 'New Vendor created successfully...'
            ], 201);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function update(VendorRequest $request, $id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor)
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'Vendor not found...'
                ]);

            if ($request->photo) {
                Storage::disk('images')->delete($vendor->photo);

                $photoPath = saveImages('vendors', $request->photo);
            } else {
                $photoPath = $vendor->photo;
            }

            $request = $request->toArray();
            $request['photo'] = $photoPath;
            $request['name'] = ['en' => $request['name_en'], 'ar' => $request['name_ar']];

            $updated = $vendor->update($request);

            $message = ($updated) ? 'The Vendor updated successfully...'
                : 'No modifications have been made...';

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => $message
            ]);

        } catch (\Exception $exception) {
            return returnExceptionJson();
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

            $photoPath = $vendor->photo;
            Storage::disk('images')->delete($photoPath);
            $vendor->delete();

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'The Vendor deleted successfully...',
            ]);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function changeStatus($id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor)
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'The Vendor not found...!',
                ]);

            $status = $vendor->active == 'active' ? 'inactive' : 'active';

            $vendor->update(['active' => $status]);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => "The Vendor status changed successfully...",
            ]);

        } catch (\Exception $ex) {
            return returnExceptionJson();
        }
    }
}
