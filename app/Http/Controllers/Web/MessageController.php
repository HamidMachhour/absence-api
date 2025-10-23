<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\MessageController as V1MessageController;
use App\Http\Controllers\Controller;
use App\Models\Message;

class MessageController extends Controller
{
    public function inbox(Request $request, V1MessageController $controller)
    {
        $messages = $controller->inbox($request);

        if ($messages instanceof JsonResponse) {
            $status =  $messages->getStatusCode();
            $data =  $messages->getData(); // Get the underlying data object from the JSON response

            // Test on response status for errors
            if ($status === 401 || $status === 404) {
                $message = $data->message ?? __('vocab.not_authorized');
                return redirect()->route('error', ['message' => $message]);
            }
        }

        $params = $this->prepareFilterParams($request, $messages);
        // pass messages to the view    
        return view('messages.inbox', [
            'messages' => $messages,
            'totalCount' => $messages->total(),
            'params' => $params
        ]);
    }

    // sent message
    public function sent(Request $request, V1MessageController $controller)
    {
        
        $response = $controller->sent($request);
        // get response message
        $message = $response->getData()->message;

        if ($response->getStatusCode() == 201) {
            // redirect back to the contact form
            return redirect()->route('messages.inbox')->with('success', $message ?? __('vocab.message_sent_successfully'));
        } else {
            return redirect()->back()->with('error', $message ??  __('vocab.error_sending_message'));
        }
    }


    // reply to message
    public function reply(Request $request)
    {
        $reply_to = $request->query('reply_to');

        $replyMessage = null;
        if ($reply_to) {
            $replyMessage = Message::with(['sourceUser', 'destinationUser'])->find($reply_to);
            if (!$replyMessage) {
                return redirect()->route('error', ['message' => __('vocab.message_not_found')]);
            }
            //if is not the sender or recipient, redirect to error
            if (auth()->check() && auth()->id() !== $replyMessage->s_user_id && auth()->id() !== $replyMessage->d_user_id) {
                return redirect()->route('error', ['message' => __('vocab.not_authorized')]);
            }
        }
        //get user friends list 
        $users = auth()->user()->friends();

        return view('messages.reply', ['replyMessage' => $replyMessage, 'users' => $users]);
    }

    // create new message
    public function compose(Request $request, $to = null)
    {
        $destination_user = null;
        if ($to) {
            $destination_user = User::find($to);
            if (!$destination_user) {
                return redirect()->route('error', ['message' => __('vocab.user_not_found')]);
            }
        }
        //get user friends list 
        $users = auth()->user()->friends();

        return view('messages.compose', ['destination_user' => $destination_user, 'users' => $users]);
    }

    // show contact us form
    public function showContactForm()
    {
        return view('messages.contact');
    }
    //contact us form submission
    public function contactUs(Request $request,  V1MessageController $controller)
    {
        $response = $controller->contactUs($request);
        // get response message
        $message = $response->getData()->message;

        if ($response->getStatusCode() == 201) {
            // redirect back to the contact form
            return redirect()->route('messages.inbox')->with('success', $message ?? __('vocab.message_sent_successfully'));
        } else {
            return redirect()->back()->with('error', $message ??  __('vocab.error_sending_message'));
        }
    }

    public function prepareFilterParams(Request $request, $response)
    {

        $params = [];
        if (!$response || $response->isEmpty())
            return $params;

        if ($request->filled('status')) {
            $params['status'] = __('vocab.status') . ':' . $response->first()->status;
        }
        if ($request->filled('sourceUser')) {
            $params['sourceUser'] = __('vocab.from') . ':' . $response->first()->sourceUser->fullName();
        }
        if ($request->filled('destinationUser')) {
            $params['destinationUser'] = __('vocab.to') . ':' . $response->first()->destinationUser->fullName();
        }
        if ($request->filled('date_message')) {
            $params['date_message'] = '>> ' . $request->date_message;
        }
        return $params;
    }
    // mark as read
    public function markAsRead(Request $request, $message, V1MessageController $controller)
    {
        $response = $controller->markAsRead($request, $message);
        // get response message
        $message = $response->getData()->message;
        if ($response->getStatusCode() == 200) {
            // redirect back to the inbox
            return redirect()->back()->with('success', $message ?? __('vocab.message_marked_as_read'));
        } else {
            return redirect()->back()->with('error', $message ??  __('vocab.error_marking_message_as_read'));
        }
    }
    // show message
    public function show(Request $request, $tchatMessage)
    {

        $message = Message::with(['sourceUser', 'destinationUser'])->find($tchatMessage);
        if (!$message) {
            return redirect()->route('error', ['message' => __('vocab.message_not_found')]);
        }
        //if is not the sender or recipient, redirect to error
        if (auth()->check() && auth()->id() !== $message->s_user_id && auth()->id() !== $message->d_user_id) {
            return redirect()->route('error', ['message' => __('vocab.not_authorized')]);
        }
        // if the authenticated user is the recipient and the message is unread, mark it as read
        if (auth()->check() && auth()->id() === $message->d_user_id && $message->status === Message::STATUS_UNREAD) {
            $message->status = Message::STATUS_READ;
            $message->save();
        }
        return view('messages.show', ['message' => $message]);
    }
    // destroy message
    public function destroy(Request $request, $message, V1MessageController $controller)
    {
        $response = $controller->destroy($request, $message);
        // get response message
        $message = $response->getData()->message;
        if ($response->getStatusCode() == 200) {
            // redirect back to the inbox
            return redirect()->back()->with('success', $message ?? __('vocab.message_deleted_successfully'));
        } else {
            return redirect()->back()->with('error', $message ??  __('vocab.error_deleting_message'));
        }
    }
}
