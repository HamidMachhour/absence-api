{{-- resources/views/terminal/select-room.blade.php --}}
@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>{{ __('vocab.select_room') }}</h3>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if($rooms->isEmpty())
                        <p>{{ __('vocab.no_room_available') }}</p>
                    @else
                        <p>{{ __('vocab.please_select_room') }}</p>
                        <div class="list-group">
                            @foreach($rooms as $room)
                                <a href="{{ route('terminal.dashboard', ['salle' => $room]) }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-door-open me-2"></i>{{ __('vocab.room') }} : <strong>{{ $room }}</strong>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection