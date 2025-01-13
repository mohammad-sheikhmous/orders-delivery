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
                return returnErrorJson(__('messages.vendors not found...!'), 400);

            return returnDataJson('vendors', $vendors, __('messages.all vendors returned..'));

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
                return returnErrorJson(__('messages.the vendor not found...!'), 400);

            $vendor->mainCategory = $vendor->mainCategory()->value('name');

            return returnDataJson('vendor', $vendor, __('messages.the vendor returned successfully...'));

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }
}
