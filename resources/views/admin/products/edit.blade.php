@extends('layouts.admin')

@section('content')

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{route('admin.products.index')}}">المنتجات </a>
                                </li>
                                <li class="breadcrumb-item active">تعديل المنتج
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic form layout section start -->
                <section id="basic-form-layouts">
                    <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-form"> تعديل المنتج </h4>
                                    <a class="heading-elements-toggle"><i
                                            class="la la-ellipsis-v font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                            <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                            <li><a data-action="close"><i class="ft-x"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                @include('admin.includes.alerts.success')
                                @include('admin.includes.alerts.errors')
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form class="form" action="{{route('admin.products.update',$product->id)}}"
                                              method="POST"
                                              enctype="multipart/form-data">
                                            <input type="hidden" value="" id="latitude" name="latitude">
                                            <input type="hidden" value="" id="longitude" name="longitude">
                                            <input type="hidden" value="{{$product->id}}" id="id" name="id">
                                            @csrf
                                            @method('PUT')

                                            <div class="form-group">
                                                <div class="text-center">
                                                    <img
                                                        src="{{asset('../images/'.$product-> photo)}}"
                                                        class="rounded-circle  height-250" alt="صورة المنتج  ">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label> لوجو المنتج </label>
                                                <label id="photo" class="file center-block">
                                                    <input type="file" id="photo" value="" name="photo">
                                                    <span class="file-custom"></span>
                                                </label>
                                                @error('photo')
                                                <span class="text-danger">{{$message}}</span>
                                                @enderror
                                            </div>

                                            <div class="form-body">

                                                <h4 class="form-section"><i class="ft-home"></i> بيانات المنتج </h4>


                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name_ar"> الاسم بالعربي </label>
                                                            <input type="text" value="{{$product->name}}" id="name_ar"
                                                                   class="form-control"
                                                                   placeholder="  "
                                                                   name="name_ar">
                                                            @error("name_ar")
                                                            <span class="text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name_en"> الاسم بالانجليزي </label>
                                                            <input type="text" value="{{$product->name}}" id="name_en"
                                                                   class="form-control"
                                                                   placeholder="  "
                                                                   name="name_en">
                                                            @error("name_en")
                                                            <span class="text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> أختر قسم المنتج </label>
                                                            <select name="product_category_id"
                                                                    class="select2 form-control">
                                                                <optgroup label="من فضلك أختر قسم المنتج ">
                                                                    @if($productCategories && $productCategories -> count() > 0)
                                                                        @foreach($productCategories as $productCategory)
                                                                            <option
                                                                                value="{{$productCategory -> id }}">{{$productCategory -> name}}
                                                                                @if($product->productCategory->id == $productCategory -> id)
                                                                                    selected
                                                                                @endif
                                                                            </option>
                                                                        @endforeach
                                                                    @endif
                                                                </optgroup>
                                                            </select>
                                                            @error('product_category_id')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 ">
                                                        <div class="form-group">
                                                            <label for="price"> السعر </label>
                                                            <input type="text" id="price"
                                                                   class="form-control" value="{{$product->price}}"
                                                                   placeholder="  " name="price">

                                                            @error("price")
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-md-12 ">
                                                        <div class="form-group">
                                                            <label for="description_ar"> الوصف بالعربي </label>
                                                            <input type="text" id="description_ar"
                                                                   class="form-control" value="{{$product->description}}"
                                                                   placeholder="  " name="description_ar">

                                                            @error("description_ar")
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                </div>


                                                <div class="row">
                                                    <div class="col-md-12 ">
                                                        <div class="form-group">
                                                            <label for="description_en"> الوصف بالانجليزي </label>
                                                            <input type="text" id="description_en"
                                                                   class="form-control" value="{{$product->description}}"
                                                                   placeholder="  " name="description_en">

                                                            @error("description_en")
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 ">
                                                        <div class="form-group">
                                                            <label for="amount"> الكمية </label>
                                                            <input type="text" id="amount"
                                                                   class="form-control" value="{{$product->amount}}"
                                                                   placeholder="  " name="amount">

                                                            @error("amount")
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-1">
                                                            <input type="checkbox" value="1"
                                                                   name="active"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if($product -> active == 'active')checked @endif/>
                                                            <label for="switcheryColor4"
                                                                   class="card-title ml-1">الحالة </label>

                                                            @error("active")
                                                            <span class="text-danger"> </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-actions">
                                                <button type="button" class="btn btn-warning mr-1"
                                                        onclick="history.back();">
                                                    <i class="ft-x"></i> تراجع
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="la la-check-square-o"></i> حفظ
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- // Basic form layout section end -->
            </div>
        </div>
    </div>

@endsection
