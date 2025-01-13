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
                return returnErrorJson('vendors not found...!', 400);

            return returnDataJson('vendors', $vendors, 'all vendors returned..');

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function show($id)
    {
        try {
            $vendor = Vendor::selectionForShowing()->find($id);

            if (!$vendor)
                return returnErrorJson('the vendor not found...!', 400);

            return returnDataJson('vendor', $vendor->makeVisible('name'), 'the vendor returned successfully...');

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function create(VendorRequest $request)
    {
        try {
            $photoPath = saveImages('vendors', $request->photo);


            $request->merge([
                'name' => ['en' => $request['name_en'], 'ar' => $request['name_ar']],
                'photo' => $photoPath,
            ]);

            Vendor::create($request->except('name_ar','name_en','password_confirmation'));

            return returnSuccessJson('New Vendor created successfully...', 201);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function update(VendorRequest $request, $id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor)
                return returnErrorJson('Vendor not found...', 400);

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

            return returnSuccessJson($message);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function destroy($id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor)
                return returnErrorJson('The Vendor not found...', 400);

            $products = $vendor->productCategories();
            if (isset($products) && $products->count() > 0)
                return returnErrorJson('The Vendor cannot be deleted...', 400);

            $photoPath = $vendor->photo;
            Storage::disk('images')->delete($photoPath);
            $vendor->delete();

            return returnSuccessJson('The Vendor deleted successfully...');

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function changeStatus($id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor)
                return returnErrorJson('The Vendor not found...!', 400);

            $status = $vendor->active == 'active' ? 'inactive' : 'active';

            $vendor->update(['active' => $status]);

            return returnSuccessJson('The Vendor status changed successfully...');

        } catch (\Exception $ex) {
            return returnExceptionJson();
        }
    }
}
