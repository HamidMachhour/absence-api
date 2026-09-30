<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Friendship;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;

class FriendshipController extends Controller
{

    public function index(Request $request, $page_size = 30)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 401);
        }

        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            $userId = Auth::id();
            // Fetch friendships where the user is either the sender or receiver
            $friendships = Friendship::where(function ($query) use ($userId) {
                $query->where('userId', $userId)
                    ->whereNotIn('status', [Friendship::STATUS_BLOCKED, Friendship::STATUS_REJECTED]);
            })->orWhere(function ($query) use ($userId) {
                $query->where('friend', $userId)
                    ->whereNotIn('status', [Friendship::STATUS_BLOCKED, Friendship::STATUS_REJECTED]);
            })
                ->with(['user', 'friend'])
                ->filter($request)
                ->orderBy('created_at', 'desc')
                ->paginate($page_size);
            return $friendships;
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.no_data') . $e->getMessage()], 404);
        }
    }

    // store friendship
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 401);
        }
        $request->validate([
            'friend' => 'required|exists:_User,id|not_in:' . Auth::id(),
        ], [
            'friend.not_in' => __('vocab.cannot_add_yourself'),
        ]);
        try {
            $userId = Auth::id();
            $friendId = $request->input('friend');

            // Check if a friendship already exists in either direction
            $existingFriendship = Friendship::where(function ($query) use ($userId, $friendId) {
                $query->where('userId', $userId)
                    ->where('friend', $friendId);
            })->orWhere(function ($query) use ($userId, $friendId) {
                $query->where('userId', $friendId)
                    ->where('friend', $userId);
            })->first();

            if ($existingFriendship) {
                if ($existingFriendship->status === Friendship::STATUS_PENDING) {
                    return response()->json(['message' => __('vocab.friend_request_already_sent')], 400);
                } elseif ($existingFriendship->status === Friendship::STATUS_ACCEPTED) {
                    return response()->json(['message' => __('vocab.already_friends')], 400);
                } elseif ($existingFriendship->status === Friendship::STATUS_BLOCKED) {
                    return response()->json(['message' => __('vocab.cannot_send_request_blocked')], 400);
                } elseif ($existingFriendship->status === Friendship::STATUS_REJECTED) {
                    // If previously rejected, allow sending a new request
                    $existingFriendship->status = Friendship::STATUS_PENDING;
                    $existingFriendship->userId = $userId; // Update the requester
                    $existingFriendship->friend = $friendId; // Update the recipient
                    $existingFriendship->save();
                    return response()->json(['message' => __('vocab.friend_request_sent')], 200);
                }
            }

            // Create a new friendship request
            $friendship = new Friendship();
            $friendship->userId = $userId;
            $friendship->friend = $friendId;
            $friendship->status = Friendship::STATUS_PENDING;
            $friendship->save();

            return response()->json(['message' => __('vocab.friend_request_sent')], 200);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.error_sending_request') . $e->getMessage()], 500);
        }
    }

    // accept friendship
    public function acceptFriend(Request $request, $friend)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 401);
        }
        try {
            $userId = Auth::id();
            $friendship = Friendship::where('userId', $friend)
                ->where('friend', $userId)
                ->where('status', Friendship::STATUS_PENDING)
                ->first();

            if (!$friendship) {
                return response()->json(['message' => __('vocab.friend_request_not_found')], 404);
            }

            $friendship->status = Friendship::STATUS_ACCEPTED;
            $friendship->save();

            return response()->json(['message' => __('vocab.friend_request_accepted')], 200);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.error_accepting_request') . $e->getMessage()], 500);
        }
    }
    // block friendship
    public function blockFriend(Request $request, $friend)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 401);
        }
        try {
            $userId = Auth::id();
            $friendship = Friendship::where(function ($query) use ($userId, $friend) {
                $query->where('userId', $userId)
                    ->where('friend', $friend);
            })->orWhere(function ($query) use ($userId, $friend) {
                $query->where('userId', $friend)
                    ->where('friend', $userId);
            })->first();

            if (!$friendship) {
                return response()->json(['message' => __('vocab.friendship_not_found')], 404);
            }

            $friendship->status = Friendship::STATUS_BLOCKED;
            $friendship->save();

            return response()->json(['message' => __('vocab.friend_blocked_successfully')], 200);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.error_blocking_friend') . $e->getMessage()], 500);
        }
    }
    // reject friendship
    public function rejectFriend(Request $request, $friend)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 401);
        }
        try {
            $userId = Auth::id();
            $friendship = Friendship::where('userId', $friend)
                ->where('friend', $userId)
                ->where('status', Friendship::STATUS_PENDING)
                ->first();
            if (!$friendship) {
                return response()->json(['message' => __('vocab.friend_request_not_found')], 404);
            }
            $friendship->status = Friendship::STATUS_REJECTED;
            $friendship->save();
            return response()->json(['message' => __('vocab.friend_request_rejected')], 200);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.error_rejecting_request') . $e->getMessage()], 500);
        }
    }


    //destroy friendship
    public function destroy(Request $request, $friend)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 401);
        }

        try {
            $currentUserId = Auth::id();

            // 1. Build the Symmetric Query
            $deleteQuery = Friendship::where('status', Friendship::STATUS_ACCEPTED)
                ->where(function ($query) use ($currentUserId, $friend) {
                    // Scenario A: Current user is the sender AND target is the recipient
                    $query->where('userId', $currentUserId)
                        ->where('friend', $friend);
                })
                ->orWhere(function ($query) use ($currentUserId, $friend) {
                    // Scenario B: Current user is the recipient AND target is the sender
                    $query->where('userId', $friend)
                        ->where('friend', $currentUserId);
                });

            // 2. Execute Deletion and Get Row Count
            // $deletedRows will be 0 or 1
            $deletedRows = $deleteQuery->delete();

            // 3. Handle Result
            if ($deletedRows === 0) {
                // If no rows were deleted, the friendship was not found or was not accepted.
                return response()->json([
                    'message' => __('vocab.friendship_not_found')
                ], 404);
            }

            // 4. Success Response
            return response()->json(['message' => __('vocab.friendship_deleted_successfully')], 200);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.error_deleting_friendship') . $e->getMessage()], 500);
        }
    }
}
