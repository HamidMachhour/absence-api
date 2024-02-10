@extends('layouts.master')

@section('title')
    اضافة صورة
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('classeImages.index', ['classe' => $classe->id]) }}">صور القسم {{ $classe->name }}</a>
@endsection

@section('page-title')
    اضافة صورة
@endsection

@section('page')
    اضافة صورة
@endsection
 
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">

                            <h3 class="card-title "> اضافة صورة جديدة </h3>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @csrf
                        <div class='card-body'>

                            <form method="POST" action="{{ route('classeImages.store') }}">
                                @csrf
                                <div class="form-group text-right">
                                    <label for="uri">رابط الصورة</label>
                                    <input type="text" class="form-control text-right" name="uri"
                                        placeholder="http://..." value="{{ old('uri') }}" />

                                    @error('uri')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <input type="hidden" name="classeId" value="{{ $classe->id }}">

                                <div class="card-footer text-right">
                                    <a href="javascript:history.back()" class=class="btn btn-outline-gray">
                                        رجوع </a>

                                    <button type="submit" class="btn btn-outline-primary">
                                        حفظ
                                    </button>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>

        <x-flash-message />
    @endsection

    @section('scripts')
    @endsection
