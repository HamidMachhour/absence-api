<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isNull;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $page_size = 6)
    {
        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            $addresses = Address::latest()
                ->where('userId', Auth::user()->id)
                ->filter(request(['id', 'phone', 'search']))
                ->paginate($page_size);
            // return response()->json(['addresses' => $addresses], 200);
            return $addresses;
        } catch (Exception $e) {
            return response()->json(['message' => 'غير متوفر. ' . $e->getMessage()], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'strAddress' => 'required|string',
            'city' => 'required|string',
            'zipCode' => 'required|string',
            'country' => 'required|string',
            'location' => 'required|string',
            'phones' => 'required|string',
            'email' => 'required|string'

        ]);


        try {

            $validatedData['userId'] = auth()->user()->id;
            // Create a new Address object and assign the validated data
            $address = Address::create($validatedData);

            // Return a response
            return response()->json(['message' => 'تمت الاضافة بنجاح', 'address' => $address], 201);
        } catch (Exception $exception) {
            return response()->json(['message' => 'العملية لم تتم بنجاح، المرجو اعادة المحاولة' . $exception->getMessage()], 404);
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
    public function edit(Address $address)
    {
        //
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
        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }
        if (auth()->user()->id !== $address->userId) {
            return response()->json(['message' => 'Unauthorized, not current user address!'], 403);
        }
        // Validate the request data
        $validatedData = $request->validate([
            'strAddress' => 'required|string',
            'city' => 'required|string',
            'zipCode' => 'required|string',
            'country' => 'required|string',
            'location' => 'required|string',
            'phones' => 'required|string',
            'email' => 'required|string'

        ]);


        try {
            $address->update($validatedData);

            return response()->json(['message' => 'تم التعديل بنجاح', 'address' => $address], 201);
        } catch (Exception $exception) {
            return response()->json(['message' => 'لم يتم التعديل بنجاح، المرجو اعادة المحاولة' . $exception->getMessage()], 404);
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
        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }

        if (Auth::user()->id !== $address->userId) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }
        // Soft delete the address
        $address->delete();
        // Return a response
        return response()->json(['message' => 'Address deleted successfully'], 200);
    }

    /**
     * set address as default for current user
     *
     */
    public function setDefault(Address $address)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }

        if (Auth::user()->id !== $address->userId) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }
        try {
            $user = User::findOrFail(Auth::user()->id);
            $user->update(['addressId' => $address->id]);
            // Return a response
            return response()->json(['message' => 'Address set as default successfully'], 200);
        } catch (Exception $exception) {
            return response()->json(['message' => 'لم يتم التعديل بنجاح، المرجو اعادة المحاولة' . $exception->getMessage()], 404);
        }
    }
}
