<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index($vendor_id)
    {
        try {

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function show($id)
    {
        try {

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function store(ProductRequest $request)
    {
        try {

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function update(ProductRequest $request)
    {
        try {

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function destroy()
    {
        try {

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'something went wrong...!'
            ], 400);
        }
    }
}
