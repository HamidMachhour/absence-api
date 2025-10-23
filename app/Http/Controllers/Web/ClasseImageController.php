<?php

namespace App\Http\Controllers\Web;

use App\Models\ClasseImage;
use App\Models\Classe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\ClasseImageController as V1ClasseImageController;
class ClasseImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Classe $classe,$page_size=6)
    {

        return view('classeImages.index', [
            'classe' =>$classe,
            'classe_images' => $classe->classeImages()->paginate($page_size)
        ]);
    }


    public function setDefault(Classe $classe, ClasseImage $image)
    {
        if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }
        $controller = new V1ClasseImageController();
        $response = $controller->setDefault($classe,$image);
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
        return redirect()->route('classeImages.index',['classe'=>$classe])->with('message', $message);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Classe $classe) {
        if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->with('message', __('vocab.not_authorized'));
        }

        return view('classeImages.create',['classe' =>$classe]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $controller = new V1ClasseImageController();
        $response = $controller->store($request);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : 'Store Image added!';
            return redirect()->route('classeImages.index',['classe'=>$request->get('classeId')])->with('message', $message);
        }else{
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure').' '.__('vocab.try_again');
            return back()->withErrors(['message' => $message]);;
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClasseImage  $classeImage
     * @return \Illuminate\Http\Response
     */
    public function show(ClasseImage $classeImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ClasseImage  $classeImage
     * @return \Illuminate\Http\Response
     */
    public function edit(ClasseImage $classeImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClasseImage  $classeImage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClasseImage $classeImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClasseImage  $classeImage
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClasseImage $image)
    {
        if (Auth::user()->id !== $image->classe->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->with('message', __('vocab.not_authorized'));
        }
        $controller = new V1ClasseImageController();
        $response = $controller->destroy($image);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 201) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : 'Image deleted successfully';
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failed');
        }
        return redirect()->route('classeImages.index',['classe'=>$image->classeId])->with('message', $message);

    }


}
