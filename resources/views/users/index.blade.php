@extends('layouts.master')

@section('title')
{{__('vocab.platform')}} {{__('vocab.absence')}}
@endsection

@section('css')
@endsection

@section('main-page')
<a href="{{ route('dashboard', ['user' => auth()->user()]) }}">
    {{__('vocab.dashboard')}}
</a>
@endsection

@section('page-title')
{{__('vocab.users')}}
@endsection

@section('page')
{{__('vocab.users')}}
@endsection

@section('content')
{{-- Filters Form --}}
<form method="GET" class="pb-4 btn-group gap-2 flex justify-center">
    <div class="form-group btn-lg">
        <select name="sort" class="form-control" onchange="this.form.submit()">
            <option value=""> {{__('vocab.sort_by')}}
            </option>
            <option value="name_asc" {{ request()->get('sort') == 'name_asc' ? 'selected' : '' }}>{{__('vocab.name_asc')}}</option>
            <option value="name_desc" {{ request()->get('sort') == 'name_desc' ? 'selected' : '' }}>{{__('vocab.name_desc')}}</option>
            <option value="newest" {{ request()->get('sort') == 'newest' ? 'selected' : '' }}>{{__('vocab.newest')}}</option>
            <option value="oldest" {{ request()->get('sort') == 'oldest' ? 'selected' : '' }}>{{__('vocab.oldest')}}</option>
        </select>
    </div>
    <div class="form-group border bg-gray rounded">
        <label class="btn btn-lg text-white" for="btn-check-1">
            <input onchange="this.form.submit()" type="checkbox" id="btn-check-1" name="role[]" class="btn-check" value="student"
                {{ in_array('student', request()->get('role', [])) ? 'checked' : '' }}>
            {{__('vocab.student')}}
        </label>

        <label class="btn btn-lg text-white" for="btn-check-2">
            <input onchange="this.form.submit()" type="checkbox" id="btn-check-2" name="role[]" class="btn-check" value="teacher"
                {{ in_array('teacher', request()->get('role', [])) ? 'checked' : '' }}>
            {{__('vocab.teacher')}}
        </label>

        <label class="btn btn-lg text-white" for="btn-check-3">
            <input onchange="this.form.submit()" type="checkbox" id="btn-check-3" name="role[]" class="btn-check" value="admin"
                {{ in_array('admin', request()->get('role', [])) ? 'checked' : '' }}>
            {{__('vocab.admin')}}
        </label>
        <label class="btn btn-lg text-white" for="btn-check-4">
            <input onchange="this.form.submit()" type="checkbox" id="btn-check-4" name="role[]" class="btn-check" value="manager"
                {{ in_array('manager', request()->get('role', [])) ? 'checked' : '' }}>
            {{__('vocab.manager')}}
        </label>
        <!--
        <button type="submit" class="btn btn-outline-primary"> {{__('vocab.apply_filters')}}
        </button>
        -->
    </div>
    <div class="form-group border bg-gray  rounded">

        <label class="btn btn-lg text-white " for="friend-status-accepted">
            <input onchange="this.form.submit()" type="radio" id="friend-status-accepted" name="friend_status" class="btn-radio" value="accepted"
                {{ request()->get('friend_status') === 'accepted' ? 'checked' : '' }}>
            {{__('vocab.friends')}}
        </label>

        <label class="btn btn-lg text-white" for="friend-status-pending">
            <input onchange="this.form.submit()" type="radio" id="friend-status-pending" name="friend_status" class="btn-radio" value="pending"
                {{ request()->get('friend_status') === 'pending' ? 'checked' : '' }}>
            {{__('vocab.pending_requests')}}
        </label>

        <label class="btn btn-lg text-white" for="friend-status-not-friends">
            <input onchange="this.form.submit()" type="radio" id="friend-status-not-friends" name="friend_status" class="btn-radio" value="not_friends"
                {{ request()->get('friend_status') === 'not_friends' ? 'checked' : '' }}>
            {{__('vocab.not_friends')}}
        </label>

        <label class="btn btn-lg text-white" for="friend-status-blocked">
            <input onchange="this.form.submit()" type="radio" id="friend-status-blocked" name="friend_status" class="btn-radio" value="blocked"
                {{ request()->get('friend_status') === 'blocked' ? 'checked' : '' }}>
            {{__('vocab.blocked')}}
        </label>

        <label class="btn btn-lg text-white" for="friend-status-rejected">
            <input onchange="this.form.submit()" type="radio" id="friend-status-rejected" name="friend_status" class="btn-radio" value="rejected"
                {{ request()->get('friend_status') === 'rejected' ? 'checked' : '' }}>
            {{__('vocab.rejected')}}
        </label>

    </div>

    <div class=' mr-2'>
        <a href="{{ route('users.importForm') }}" class="btn btn-app bg-info">
            <i class="fas fa-arrow-down"></i>
            {{__('vocab.import')}}
        </a>
        <a href="{{ route('users.export',$_REQUEST) }}" class="btn btn-app bg-success">
            <i class="fas fa-arrow-up"></i>
            {{__('vocab.export')}}
        </a>
    </div>
</form>
{{-- Filters Card (optional) --}}
<x-filters-card :params="$params" />

@unless (count($users) == 0)
<div class="lg:grid lg:grid-cols-4 gap-2 mt-2">
    @foreach ($users as $user)
    <x-user-card :user="$user" />
    @endforeach
</div>
<div class="mt-6 p-4">
    {{ $users->appends(request()->query->all())->links() }}
</div>
@else
<p>{{__('vocab.empty')}}</p>
@endunless
@endsection

@section('scripts')
@endsection