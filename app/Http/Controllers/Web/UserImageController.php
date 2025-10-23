<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\UserImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\UserImageController as V1UserImageController;

class UserImageController extends Controller
{
       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


    }

        /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserImage  $userImage
     * @return \Illuminate\Http\Response
     */
    public function show(User $user,$page_size=6)
    {
        return view('userImages.show', [
            'user' => $user,
            'user_images' => $user->userImages()->paginate($page_size)
        ]);
    }

    public function setDefault(User $user, UserImage $image)
    {
        if (Auth::user()->id !== $user->id) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }
        $controller = new V1UserImageController();
        $response = $controller->setDefault($user,$image);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : 'Image set as default successfully';
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure');
        }
        return redirect()->route('userImages.show',['user'=>$user])->with('message', $message);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $user) {
        if (Auth::user()->id !== $user->id) {
            return redirect()->route('home')->with('message', __('vocab.not_authorized'));
        }

        return view('userImages.create',['user' =>$user]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $controller = new V1UserImageController();
        $response = $controller->store($request);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : 'User Image added!';
            return redirect()->route('userImages.show',['user'=>$request->get('userId')])->with('message', $message);
        }else{
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure').' '.__('vocab.try_again');
            return back()->withErrors(['message' => $message]);;
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserImage  $userImage
     * @return \Illuminate\Http\Response
     */
    public function edit(UserImage $userImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserImage  $userImage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserImage $userImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserImage  $userImage
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserImage $image)
    {
        if (Auth::user()->id !== $image->userId) {
            return redirect()->route('home')->with('message', __('vocab.not_authorized'));
        }
        $controller = new V1UserImageController();
        $response = $controller->destroy($image);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : 'Image deleted successfully';
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failed');
        }
        return redirect()->route('userImages.show',['user'=>$image->userId])->with('message', $message);

    }
}
