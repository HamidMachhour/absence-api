@props(['user'])


<div class="card card-widget widget-user-2 shadow-sm ml-2">
    <!-- Add the bg color to the header using any of the bg-* classes -->
    <div class="widget-user-header bg-gray">
        <a class="widget-user-image" href="{{ route('users.show', ['user' => $user->id]) }}">
            <img class="img-circle elevation-2" alt="{{ $user_name = $user->fullName()}}"
                src="{{ $user->userImage->uri ?? asset('/assets/images/no-image.png') }}">
        </a>
        <!-- /.widget-user-image -->
        <h3 class="widget-user-username">{{ $user_name }}</h3>
        <h5 class="widget-user-desc">
            @isset($user->roles)
            <x-user-roles :user_roles="$user->roles" />
            @endisset
        </h5>
    </div>
    <div class="card-footer p-0">
        <ul class="nav flex-column ml-2">
            @isset($user->email)
            <li class="nav-item">

                <a href="mailto:{{ $user->email }}">
                    <i class="fas fa-envelope"></i>
                    {{ $user->email }}</a>
            </li>
            @endisset
            @isset($user->email_verified_at)
            <li class="nav-item">
                <a href="{{ request()->fullUrlWithQuery(['verified' => 'yes', 'page' => 1]) }}"
                    class="flex text-blue-600 hover:text-gray-700 justify-center">
                    {{__('vocab.account').' '.__('vocab.active')}}
                </a>
            </li>

            @else
            <li class="nav-item">
                <a href="{{ request()->fullUrlWithQuery(['verified' => 'no', 'page' => 1]) }}"
                    class="flex text-red-600 hover:text-gray-700 justify-center">
                    {{__('vocab.account').' '.__('vocab.inactive')}}
                </a>
            </li>
            @endisset
            @isset($user->address)
            <li class="nav-item">
                <x-address-phones :phonesCsv="$user->address->phones" />
            </li>
            @endisset
            @auth
            <li class="nav-item">
                <div class="btn-group">
                    @if (auth()->user()->hasRole('admin'))
                    <a href="{{ route('users.edit', ['user' => $user->id]) }}" class="btn btn-app bg-info">
                        <i class="fas fa-edit"></i>
                        {{__('vocab.edit')}}
                    </a>
                    <form action="{{ route('users.destroy', ['user' => $user->id]) }}" method="POST"
                        class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-app bg-danger"
                            onclick="return confirm('{{__('vocab.delete_permanently').'?'}}')">
                            <i class="fas fa-trash"></i>
                            {{__('vocab.delete')}}
                        </button>
                    </form>

                    @isset($user->email_verified_at)
                    <form action="{{ route('users.refute',$user) }}" class="inline" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-app bg-danger">
                            <i class="fas fa-x"></i>
                            {{__('vocab.disable')}} </button>
                    </form>
                    @else
                    <form action="{{ route('users.activate',$user) }}" class="inline" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-app bg-success">
                            <i class="fas fa-check"></i>
                            {{__('vocab.enable')}}
                        </button>
                    </form>
                    @endisset
                    <form action="{{ route('userRoles.index',$user) }}" class="inline" method="GET">
                        <button type="submit" class="btn btn-app bg-gray ">
                            <i class="fas fa-user"></i>
                            {{__('vocab.roles')}}
                        </button>
                    </form>
                    @endif
                </div>
                <div class="btn-group">

                    {{-- 1. Do not show buttons for self-profile --}}
                    @if(auth()->user()->id !== $user->id)

                    {{-- 2. State: Friends (Show Unfriend, Block, and Message) --}}
                    @if(auth()->user()->isFriendWith($user->id))
                    <div class="btn-group gap-2">

                        {{-- UNFRIEND Action: Typically deletes the Friendship record --}}
                        <form action="{{ route('friends.destroy', ['friend' => $user->id]) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE') {{-- Use the DELETE method for resource destruction --}}
                            <button type="submit" class="btn btn-app bg-danger"
                                onclick="return confirm('{{__('vocab.unfriend').'?'}}')">
                                <i class="fas fa-user-slash"></i>
                                {{__('vocab.unfriend')}}
                            </button>
                        </form>

                        {{-- BLOCK Action: Separate from Unfriend, changes status to BLOCKED --}}
                        <form action="{{ route('friends.block', ['friend' => $user->id]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-app btn-default text-danger"
                                onclick="return confirm('{{__('vocab.block_friend').'?'}}')">
                                <i class="fas fa-user-lock"></i>
                                {{__('vocab.block_friend')}}
                            </button>
                        </form>

                        {{-- SEND MESSAGE Action --}}
                        <a href="{{ route('messages.compose', ['to' => $user->id]) }}" class="btn btn-app bg-primary">
                            <i class="fas fa-envelope"></i>
                            {{__('vocab.send_message')}}
                        </a>
                    </div>

                    {{-- 3. State: Pending Request *TO* Current User (Show Accept/Reject) --}}
                    @elseif(auth()->user()->hasPendingFriendRequestFrom($user->id))
                    <form action="{{ route('friends.accept', ['friend' => $user->id]) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-app bg-success">
                            <i class="fas fa-user-check"></i>
                            {{__('vocab.accept_friend_request')}}
                        </button>
                    </form>
                    
                    <form action="{{ route('friends.reject', ['friend' => $user->id]) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-app bg-danger"
                            onclick="return confirm('{{__('vocab.reject_friend_request').'?'}}')">
                            <i class="fas fa-user-xmark"></i>
                            {{__('vocab.reject_friend_request')}}
                        </button>
                    </form>

                    {{-- 4. State: Pending Request *FROM* Current User (Show Request Sent) --}}
                    @elseif(auth()->user()->hasPendingFriendRequestTo($user->id))
                    <button type="button" class="btn btn-app bg-warning">
                        <i class="fas fa-user-clock"></i>
                        {{__('vocab.request_sent')}}
                    </button>

                    {{-- 5. State: Blocked/Rejected History (Show no action) --}}
                    {{-- This check should be placed before the 'Add Friend' logic --}}
                    @elseif(auth()->user()->hasBlockedFriendship($user->id) || auth()->user()->hasRejectedFriendship($user->id))
                    <button type="button" class="btn btn-app bg-secondary disabled" disabled>
                        <i class="fas fa-ban"></i>
                        {{__('vocab.no_action_available')}}
                    </button>

                    {{-- 6. State: Available to Add (Not Friends, Not Pending, Not Blocked/Rejected) --}}
                    @else
                    <form action="{{ route('friends.store') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="friend" value="{{ $user->id }}">
                        <button type="submit" class="btn btn-app bg-success">
                            <i class="fas fa-user-plus"></i>
                            {{__('vocab.add_friend')}}
                        </button>
                    </form>
                    @endif
                    @endif
                </div>
            </li>

            @endauth
        </ul>
    </div>



</div>