<?php

namespace App\Http\Controllers\Web;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\AddressController as V1AddressController;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $controller = new V1AddressController();
        $response = $controller->index($request);


        return view('addresses.index', [
            'addresses' => $response
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route("users.login");
        }

        return view('addresses.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $controller = new V1AddressController();
        $response = $controller->store($request);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : 'Address created!';
            return redirect()->route('addresses.index')->with('message', $message);
        }else{
            $message = isset($data['message']) ? $data['message'] : 'لم تتم العملية بنجاح، المرجو اعادة المحاولة';
            return back()->withErrors(['message' => $message]);;
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function show(Address $address)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    // Show Edit Form
    public function edit(Address $address) {
        if (Auth::user()->id !== $address->userId) {
            return redirect()->route('home.route')->with('message', 'غير مسموح');
        }
        return view('addresses.edit', ['address' => $address]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Address $address)
    {
        $controller = new V1AddressController();
        $response = $controller->update($request,$address);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : 'Address updated!';
            return redirect()->route('addresses.index')->with('message', $message);
        }else{
            $message = isset($data['message']) ? $data['message'] : 'Not updated';
            return back()->withErrors(['message' => $message]);;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function destroy(Address $address)
    {
        if (Auth::user()->id !== $address->userId) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        $controller = new V1AddressController();
        $response = $controller->destroy($address);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : 'Address deleted successfully';
        } else {
            $message = isset($data['message']) ? $data['message'] : 'الحدف لم تتم بنجاح';
        }
        return redirect()->route('addresses.index')->with('message', $message);
    }

    // set default address
    public function setDefault(Address $address)
    {
        if (Auth::user()->id !== $address->userId) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        $controller = new V1AddressController();
        $response = $controller->setDefault($address);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : 'Address set as default successfully';
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : 'Action failed.';
        }
        return redirect()->route('addresses.index')->with('message', $message);
    }
}
