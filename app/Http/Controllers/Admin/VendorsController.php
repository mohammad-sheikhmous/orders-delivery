<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Models\MainCategory;
use App\Models\Vendor;
use Illuminate\Support\Facades\Storage;

class VendorsController extends Controller
{
    public function index()
    {
        try {
//            $vendors = Vendor::selection()->get();

            $vendors = Vendor::selectionForShowing()
                ->with(['mainCategory' => function ($q) {
                    $q->select('id', 'name');
                }])->get();
//dd($vendors);
            if ($vendors->isEmpty())
                return to_route('admin.dashboard')->with(['error' => 'vendors not found...!']);
            //         return returnErrorJson('vendors not found...!', 400);

            return view('admin.vendors.index', ['vendors' => $vendors]);
//            return returnDataJson('vendors', $vendors, 'all vendors returned..');

        } catch (\Exception $exception) {
            return to_route('admin.dashboard')->with(['error' => 'هناك خطأ ما قد حدث اثناء اضافة متجر جديد ...!']);
//            return returnExceptionJson();
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

    public function create()
    {
        $mainCategories = MainCategory::selection()->get();
        return view('admin.vendors.create', ['mainCategories' => $mainCategories]);
    }

    public function store(VendorRequest $request)
    {
        try {
            $photoPath = saveImages('vendors', $request->photo);

//            $request = $request->except('', 'password_confirmation');
            $request = $request->toArray();

            $request['name'] = ['en' => $request['name_en'], 'ar' => $request['name_ar']];
            $request['photo'] = $photoPath;

//            dd($request->except('name_ar', 'name_en', 'password_confirmation'));
            Vendor::create($request);

            return to_route('admin.vendors.index')->with(['success' => 'New Vendor created successfully...']);

//            return returnSuccessJson('New Vendor created successfully...', 201);

        } catch (\Exception $exception) {
            return to_route('admin.vendors.index')->with(['error' => __('messages.something went wrong...!')]);

//            return returnExceptionJson();
        }
    }

    public function edit($id)
    {
        try {
            $vendor = Vendor::find($id);
            if (!$vendor)
                return to_route('admin.vendors.index')->with(['error' => 'المتجر غير موجود ليتم التعديل عليه ']);

//            dd($vendor);
            $mainCategories = MainCategory::where('active', 1)->selection()->get();

            return view('admin.vendors.edit', ['vendor' => $vendor, 'mainCategories' => $mainCategories]);

        } catch (\Exception $exception) {
            return to_route('admin.vendors.index')->with(['error' => __('messages.something went wrong...!')]);
        }
    }

    public function update(VendorRequest $request, $id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor)
                return to_route('admin.vendors.index')->with(['error' => 'Vendor not found...']);
//                return returnErrorJson('Vendor not found...', 400);

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

            return to_route('admin.vendors.index')->with(['success' => $message]);
//            return returnSuccessJson($message);

        } catch (\Exception $exception) {
            return to_route('admin.vendors.index')->with(['error' => __('messages.something went wrong...!')]);

//            return returnExceptionJson();
        }
    }

    public function destroy($id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor)
                return to_route('admin.vendors.index')->with(['error' => 'Vendor not found...']);

//                return returnErrorJson('The Vendor not found...', 400);

            $products = $vendor->productCategories();
            if (isset($products) && $products->count() > 0)
                return to_route('admin.vendors.index')->with(['error' => 'The Vendor cannot be deleted...']);

//                return returnErrorJson('The Vendor cannot be deleted...', 400);

            $photoPath = $vendor->photo;
            Storage::disk('images')->delete($photoPath);
            $vendor->delete();

            return to_route('admin.vendors.index')->with(['error' => 'The Vendor deleted successfully...']);
//            return returnSuccessJson('The Vendor deleted successfully...');

        } catch (\Exception $exception) {
            return to_route('admin.vendors.index')->with(['error' => __('messages.something went wrong...!')]);

//            return returnExceptionJson();
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
            return to_route('admin.vendors.index')->with(['error' => __('messages.something went wrong...!')]);

            return returnExceptionJson();
        }
    }
}
