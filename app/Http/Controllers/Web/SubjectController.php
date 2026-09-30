<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\SubjectController as V1SubjectController;
use Illuminate\Support\Facades\Auth;
use Exception;


class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, V1SubjectController $v1Controller)
    {
        try {
            if (!Auth::check())
                return redirect()->route('users.login');


            $subjects = $v1Controller->index($request);

            return view('subjects.index', [
                'subjects' => $subjects,
            ]);
        } catch (Exception $e) {
            return view('error', ["message" => __('vocab.no_data')  . $e->getMessage()]);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            if (!Auth::check() || !Auth::user()->hasRole('admin')) {
                return back()->with('message', __('vocab.not_authorized'));
            }
            return view('subjects.create');
        } catch (Exception $e) {
            return view('error', ["message" => __('vocab.action_failure') . $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, V1SubjectController $controller)
    {
        $response = $controller->store($request);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_success');
            return redirect()->route('subjects.index')->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure') . ' ' . __('vocab.try_again');
            return back()->withErrors(['message' => $message]);;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function edit(Subject $subject)
    {
        try {
            if (!Auth::check() || !Auth::user()->hasRole('admin')) {
                return back()->with('message', __('vocab.not_authorized'));
            }
            return view('subjects.edit', ['subject' => $subject]);
        } catch (Exception $e) {
            return view('error', ["message" => __('vocab.action_failure') . $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subject $subject, V1SubjectController $controller)
    {
        $response = $controller->update($request, $subject);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_success');
            return redirect()->route('subjects.index')->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure') . ' ' . __('vocab.try_again');
            return back()->withErrors(['message' => $message]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject, V1SubjectController $controller)
    {
        $response = $controller->destroy($subject);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failed');
        }
        return redirect()->route('subjects.index')->with('message', $message);
    }

    public function destroyAll(V1SubjectController $controller)
    {
        $response = $controller->destroyAll();
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failed');
        }
        return redirect()->route('subjects.index')->with('message', $message);
    }
}
