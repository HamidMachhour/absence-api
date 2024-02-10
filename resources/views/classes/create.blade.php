@extends('layouts.master')

@section('title')
    اضافة قسم
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('classes.manage') }}">اقسامي</a>
@endsection

@section('page-title')
    اضافة قسم
@endsection

@section('page')
    اضافة قسم
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"> انشاء قسم جديد</h3>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @csrf
                        <div class='card-body'>

                            <form method="POST" action="{{ route('classes.store') }}">
                                @csrf
                                <div class="form-group text-right">
                                    <label for="name">الاسم</label>
                                    <input type="text" class="form-control text-right" name="name"
                                        placeholder="مثال: الثانية ع ح ا 1" value="{{ old('name') }}" />

                                    @error('name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group text-right">
                                    <label for="tags">
                                        Tags (Comma Separated)
                                    </label>
                                    <input type="text" class="form-control text-right" name="tags"
                                        placeholder="مثال :svt, 1bac, " value="{{ old('tags') }}" />

                                    @error('tags')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group text-right">
                                    <label for="description">
                                        معلومات تفصيلية حول القسم
                                    </label>
                                    <textarea class="form-control text-right" name="description" rows="4" placeholder="تفاصيل ....">{{ old('description') }}</textarea>

                                    @error('description')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

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

        <x-flash-message />
    @endsection

    @section('scripts')
    @endsection
