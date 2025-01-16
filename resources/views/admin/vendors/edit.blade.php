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
                                <li class="breadcrumb-item"><a href="{{route('admin.vendors.index')}}">المتاجر </a>
                                </li>
                                <li class="breadcrumb-item active">تعديل متجر
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
                                    <h4 class="card-title" id="basic-layout-form"> تعديل متجر </h4>
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
                                        <form class="form" action="{{route('admin.vendors.update',$vendor -> id)}}"
                                              method="POST"
                                              enctype="multipart/form-data">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="id" value="{{$vendor -> id}}">

                                            {{--                                            <input type="hidden"  value="{{$vendor -> latitude}}" id="latitude" name="latitude">--}}
                                            {{--                                            <input type="hidden" value="{{$vendor -> longitude}}" id="longitude"  name="longitude">--}}

                                            <div class="form-group">
                                                <div class="text-center">
                                                    <img
                                                        src="{{asset('../images/'.$vendor-> photo)}}"
                                                        class="rounded-circle  height-250" alt="صورة التاجر  ">
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <label> لوجو التجار </label>
                                                <label id="projectinput7" class="file center-block">
                                                    <input type="file" id="file" name="photo">
                                                    <span class="file-custom"></span>
                                                </label>
                                                @error('photo')
                                                <span class="text-danger">{{$message}}</span>
                                                @enderror
                                            </div>

                                            <div class="form-body">

                                                <h4 class="form-section"><i class="ft-home"></i> بيانات المتجر </h4>


                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> الاسم بالعربي </label>
                                                            <input type="text" value="{{$vendor -> name}}" id="name"
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
                                                            <label for="projectinput1"> الاسم بالانكليزي </label>
                                                            <input type="text" value="{{$vendor -> name}}" id="name"
                                                                   class="form-control"
                                                                   placeholder="  "
                                                                   name="name_en">
                                                            @error("name_en")
                                                            <span class="text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> أختر القسم </label>
                                                            <select name="main_category_id"
                                                                    class="select2 form-control">
                                                                <optgroup label="من فضلك أختر القسم ">
                                                                    @if($mainCategories && $mainCategories -> count() > 0)
                                                                        @foreach($mainCategories as $mainCategory)
                                                                            <option
                                                                                value="{{$mainCategory -> id }}"
                                                                                @if($vendor -> main_category_id == $mainCategory -> id  )  selected @endif
                                                                            >{{$mainCategory -> name}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </optgroup>
                                                            </select>
                                                            @error('main_category_id')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-md-6 ">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> رقم الهاتف </label>
                                                            <input type="text" id="mobile"
                                                                   class="form-control"
                                                                   placeholder="  " name="mobile"
                                                                   value="{{$vendor -> mobile}}">

                                                            @error("mobile")
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 ">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> ألبريد الالكتروني </label>
                                                            <input type="text" id="email"
                                                                   class="form-control"
                                                                   placeholder="  " name="email"
                                                                   value="{{$vendor -> email}}">

                                                            @error("email")
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>


                                                </div>


                                                <div class="row">
                                                    <div class="class col-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1">كلمة المرور </label>
                                                            <input type="password" id="password"
                                                                   class="form-control"
                                                                   placeholder="" name="password">

                                                            @error("password")
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>

                                                    </div>

                                                    <div class="class col-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1">تأكيد كلمة المرور</label>
                                                            <input type="password" id="password_confirmation"
                                                                   class="form-control"
                                                                   placeholder="  " name="password_confirmation">

                                                            @error("password_confirmation")
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>

                                                    </div>
                                                </div>


                                                <di class="row">
                                                    <div class="col-md-6 ">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> العنوان </label>
                                                            <input type="text" id="pac-input"
                                                                   class="form-control"
                                                                   placeholder="  " name="address"
                                                                   value="{{$vendor -> address}}"
                                                            >

                                                            @error("address")
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                </di>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-1">
                                                            <input type="checkbox" value="1"
                                                                   name="active"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if($vendor -> active == 'active')checked @endif/>
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
