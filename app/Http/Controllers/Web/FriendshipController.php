<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\FriendshipController as V1FriendshipController;
use App\Http\Controllers\Controller;
use App\Models\Message;

class FriendshipController extends Controller
{
    public function index(Request $request, V1FriendshipController $controller)
    {
        $friendships = $controller->index($request);

        if ($friendships instanceof JsonResponse) {
            $status =  $friendships->getStatusCode();
            $data =  $friendships->getData(); // Get the underlying data object from the JSON response

            // Test on response status for errors
            if ($status === 401 || $status === 404) {
                $message = $data->message ?? __('vocab.not_authorized');
                return redirect()->route('error', ['message' => $message]);
            }
        }

        $params = $this->prepareFilterParams($request, $friendships);
        // pass friendships to the view    
        return view('friendships.index', [
            'friendships' => $friendships,
            'totalCount' => $friendships->total(),
            'params' => $params
        ]);
    }

    // store friendship
    public function addFriend(Request $request, V1FriendshipController $controller)
    {
        $response = $controller->store($request);
        // get response message
        $message = $response->getData()->message;
        if ($response->getStatusCode() == 200) {
            // redirect back to the index
            return redirect()->back()->with('success', $message ?? __('vocab.request_sent'));
        } else {
            return redirect()->back()->with('error', $message ??  __('vocab.error_sending_request'));
        }
    }
    // accept friendship
    public function acceptFriend(Request $request, $friend, V1FriendshipController $controller)
    {
        $response = $controller->acceptFriend($request, $friend);
        // get response message
        $message = $response->getData()->message;
        if ($response->getStatusCode() == 200) {
            // redirect back to the index
            return redirect()->back()->with('success', $message ?? __('vocab.friend_request_accepted'));
        } else {
            return redirect()->back()->with('error', $message ??  __('vocab.error_accepting_request'));
        }
    }
    // block friendship
    public function blockFriend(Request $request, $friend, V1FriendshipController $controller)
    {
        $response = $controller->blockFriend($request, $friend);
        // get response message
        $message = $response->getData()->message;
        if ($response->getStatusCode() == 200) {
            // redirect back to the index
            return redirect()->back()->with('success', $message ?? __('vocab.friend_blocked_successfully'));
        } else {
            return redirect()->back()->with('error', $message ??  __('vocab.error_blocking_friend'));
        }
    }
    // reject friendship
    public function rejectFriend(Request $request, $friend, V1FriendshipController $controller)
    {
        $response = $controller->rejectFriend($request, $friend);
        // get response message
        $message = $response->getData()->message;
        if ($response->getStatusCode() == 200) {
            // redirect back to the index
            return redirect()->back()->with('success', $message ?? __('vocab.friend_request_rejected'));
        } else {
            return redirect()->back()->with('error', $message ??  __('vocab.error_rejecting_request'));
        }
    }

    // prepare filter params for view
    public function prepareFilterParams(Request $request, $response)
    {

        $params = [];
        if (!$response || $response->isEmpty())
            return $params;

        if ($request->filled('status')) {
            $params['status'] = __('vocab.status') . ':' . $response->first()->status;
        }
        if ($request->filled('user')) {
            $params['user'] = __('vocab.from') . ':' . $response->first()->user->fullName();
        }
        if ($request->filled('friend')) {
            $params['friend'] = __('vocab.to') . ':' . $response->first()->friend->fullName();
        }
        if ($request->filled('date_friendship')) {
            $params['date_friendship'] = '>> ' . $request->date_friendship;
        }
        return $params;
    }


    // destroy friendship
    public function destroy(Request $request, $friend, V1FriendshipController $controller)
    {
        $response = $controller->destroy($request, $friend);
        // get response message
        $message = $response->getData()->message;
        if ($response->getStatusCode() == 200) {
            // redirect back to the index
            return redirect()->back()->with('success', $message ?? __('vocab.friendship_deleted_successfully'));
        } else {
            return redirect()->back()->with('error', $message ??  __('vocab.error_deleting_friendship'));
        }
    }
}
