<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    /**
     * Retrieves the inbox messages for the authenticated user.
     * This includes messages sent to and from the user, excluding those that are archived.
     */
    public function inbox(Request $request, $page_size = 30)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 401);
        }

        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            $userId = Auth::id();
            // Fetch messages where the user is either the sender or receiver
            $messages = Message::where(function ($query) use ($userId) {
                $query->where('s_user_id', $userId)
                    ->whereNotIn('status', [Message::STATUS_SOURCE_ARCHIVED, Message::STATUS_BOTH_ARCHIVED]);
            })->orWhere(function ($query) use ($userId) {
                $query->where('d_user_id', $userId)
                    ->whereNotIn('status', [Message::STATUS_DESTINATION_ARCHIVED, Message::STATUS_BOTH_ARCHIVED]);
            })
                ->with(['sourceUser', 'destinationUser'])
                ->filter($request)
                ->orderBy('created_at', 'desc')
                ->paginate($page_size);
            return $messages;
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.empty') . $e->getMessage()], 404);
        }
    }


    public function sent(Request $request)
    {
        // Authorization Check
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 401);
        }

        try {
            // 1. Validation and Existence Check
            $validated = $request->validate([
                'recipient' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::exists('_User', 'id') // Ensures the recipient user exists
                ],
                'message' => ['required', 'string', 'max:2000'],
                'subject' => ['nullable', 'string', 'max:255'],
            ]);

            $senderId = Auth::id();
            $recipientId = $validated['recipient'];

            // Safety Check: Prevent messaging self
            if ($senderId === $recipientId) {
                return response()->json(['message' => __('vocab.error_sending_message')], 400);
            }

            // 2. Message Creation (This is the critical block that might fail)
            Message::create([
                's_user_id' => $senderId,
                'd_user_id' => $recipientId,
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'status' => Message::STATUS_UNREAD,
            ]);

            // 3. Success Response
            return response()->json([
                'message' => __('vocab.message_sent_successfully'),
            ], 201);
        } catch (Exception $e) {
            // 4. Error Response
            // Return a generic error message to the client while logging the details server-side.
            return response()->json([
                'message' => __('vocab.error_sending_message'),
                // In a production environment, avoid exposing $e->getMessage() directly
                // 'error_details' => $e->getMessage() 
            ], 500);
        }
    }


    /**
     * Handles a 'contact us' message submission.
     * This typically sends a message from a user (s_user_id) to an admin or specific destination user (d_user_id).
     */
    public function contactUs(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 401);
        }
        // 1. Validation
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'subject' => ['nullable', 'string', 'max:255'],
        ]);

        // The authenticated user is the sender (s_user_id)

        $senderId = Auth::id();

        //the recipient is the admin user
        $admin = User::withRole('admin')->first();
        // If no admin user is found, return an error response
        if (!$admin) {
            return response()->json(['message' => __('vocab.no_admins')], 500);
        }
        $recipientId = $admin->id;

        // 2. Message Creation
        Message::create([
            's_user_id' => $senderId,
            'd_user_id' => $recipientId, // This should be set to the admin or support user ID
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => Message::STATUS_UNREAD, // New messages are always unread
        ]);

        // 3. Response
        return response()->json([
            'message' =>  __('vocab.message_sent_successfully'),
        ], 201);
    }
    // mark as read
    public function markAsRead(Request $request, $message)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 401);
        }
        $userId = Auth::id();
        // Find the message by ID
        $msg = Message::where('id', $message)
            ->where('d_user_id', $userId) // Ensure the authenticated user is the recipient
            ->first();
        if (!$msg) {
            return response()->json(['message' => __('vocab.message_not_found')], 404);
        }
        // Update the message status to 'read' if it's currently 'unread'
        if ($msg->status === Message::STATUS_UNREAD) {
            $msg->status = Message::STATUS_READ;
            $msg->save();
            return response()->json(['message' => __('vocab.message_marked_as_read')], 200);
        } else {
            return response()->json(['message' => __('vocab.message_marked_as_read')], 200);
        }
    }
    //destroy message
    public function destroy(Request $request, $message)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 401);
        }
        $userId = Auth::id();
        // Find the message by ID
        $msg = Message::where('id', $message)
            ->where(function ($query) use ($userId) {
                $query->where('s_user_id', $userId)
                    ->orWhere('d_user_id', $userId);
            }) // Ensure the authenticated user is either the sender or recipient
            ->first();
        if (!$msg) {
            return response()->json(['message' => __('vocab.message_not_found')], 404);
        }
        // Determine the new status based on the current status and the user's role
        if ($msg->s_user_id === $userId) {
            // User is the sender
            if ($msg->status === Message::STATUS_DESTINATION_ARCHIVED) {
                $msg->status = Message::STATUS_BOTH_ARCHIVED;
            } elseif ($msg->status === Message::STATUS_UNREAD || $msg->status === Message::STATUS_READ) {
                $msg->status = Message::STATUS_SOURCE_ARCHIVED;
            }
        } elseif ($msg->d_user_id === $userId) {
            // User is the recipient
            if ($msg->status === Message::STATUS_SOURCE_ARCHIVED) {
                $msg->status = Message::STATUS_BOTH_ARCHIVED;
            } elseif ($msg->status === Message::STATUS_UNREAD || $msg->status === Message::STATUS_READ) {
                $msg->status = Message::STATUS_DESTINATION_ARCHIVED;
            }
        }
        $msg->save();
        return response()->json(['message' => __('vocab.message_deleted_successfully')], 200);
    }
}
