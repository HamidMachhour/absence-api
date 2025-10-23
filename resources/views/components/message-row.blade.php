@props(['message'])

@php
// Determine the current user's role in this message
$isSent = (auth()->id() === $message->s_user_id);
$isRead = ($message->status === 'read') ;
// Assign Bootstrap card classes based on direction
$cardBgClass = $isSent ? 'bg-info' : 'bg-success'; // Customize colors here
@endphp

<div class="card card-outline m-1" :title="{{ $message->status }}">

    <div class="card-header {{ $cardBgClass }} ">
        <div class="card-title h3">

            <a href="{{ request()->fullUrlWithQuery(['status' => $message->status, 'page' => 1]) }}"
                class="flex text-white hover:text-gray-700 justify-center">
                {{ Str::limit($message->subject, 15) }}
            </a>
        </div>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="card-body p-1">

        {{-- You may need to adjust text colors inside card-body if you use solid bg-* colors --}}

        <div class="d-flex flex-column">
            <div>
                <a href="{{ request()->fullUrlWithQuery(['sourceUser' => $message->s_user_id, 'page' => 1]) }}"
                    class="flex text-gray-700 hover:text-blue-600 justify-center">
                    {{ $message->sourceUser->fullName()  }}
                </a>
            </div>
            <div>
                <a href="{{ request()->fullUrlWithQuery(['destinationUser' => $message->d_user_id, 'page' => 1]) }}"
                    class="flex text-blue-600 hover:text-gray-700 justify-center">
                    {{ $message->destinationUser->fullName()  }}
                </a>
            </div>
            <div class="text-center">
                {{ Str::limit($message->message, 30) }}
            </div>
            <a
                href="{{ request()->fullUrlWithQuery(['date_message' => $message->created_at->format('Y-m-d'), 'page' => 1]) }}">
                <div class="flex text-gray-700 hover:text-blue-600 justify-center">
                    <div>{{ $message->created_at->format('Y-m-d H:i')  }}</div>
                </div>
            </a>

            <div class="btn-group gap-2 ">

                <form action="{{ route('messages.destroy',['message'=>$message->id]) }}" method="POST"

                    class="inline">

                    @csrf

                    @method('DELETE')

                    <button type="submit" class="btn bg-danger" title="{{__('vocab.delete')}}" onclick="return confirm('{{__('vocab.delete_permanently').'?'}}')">

                        <i class="fa-solid fa-trash"></i>

                    </button>

                </form>

                <a href="{{ route('messages.show',['message'=>$message]) }}" title="{{__('vocab.view_message')}}">

                    <div class="btn bg-success">

                        <i class="fa-solid fa-eye"></i>

                    </div>

                </a>



                @if ($isSent)
                {{-- Single Checkmark: Delivered but UNREAD (Default status is unread) --}}
                @if (!$isRead)
                <div class="btn bg-secondary" title="{{__('vocab.delivered_unread')}}">
                    <i class="fa-solid fa-check text-white"></i>
                </div>
                {{-- Double Checkmark: Delivered and READ --}}
                @else
                <div class="btn bg-primary" title="{{__('vocab.read')}}">
                    <i class="fa-solid fa-check-double text-white"></i>
                </div>
                @endif

                {{-- 2. LOGIC FOR THE RECIPIENT (SHOW ACTION BUTTON) --}}
                @else
                {{-- Only show 'Mark as Read' button if the message is UNREAD --}}
                @if (!$isRead)
                <form action="{{ route('messages.markAsRead',['message'=>$message->id]) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn bg-info" title="{{__('vocab.mark_as_read')}}">
                        <i class="fa-solid fa-envelope-open"></i>
                    </button>
                </form>
                @endif
                @endif

            </div>
        </div>

    </div>
</div>