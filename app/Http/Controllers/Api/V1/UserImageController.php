<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\User;
use App\Models\UserImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserImageController extends Controller
{

    public function setDefault(User $user, UserImage $image)
    {
        if (Auth::user()->id !== $user->id) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }
        try {
            $user->update(['imageId' => $image->id]);
            return response()->json(['message' => 'Update successfully', 'user' => $user], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'لم يتم التعديل بنجاح، المرجو اعادة المحاولة' . $e->getMessage()], 404);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'uri' => 'required|string',
            'userId' => 'required|string',
            'id' => 'nullable|string',
        ]);

        try {
            $userId = $validatedData['userId'];
            $user = User::findOrFail($userId);

            if ($user->id===auth()->user()->id) {
                // Create a new UserImage object and assign the validated data
                $userImage = UserImage::create($validatedData);
                if (!$user->imageId) {
                    $user->update(['imageId' => $userImage->id]);
                }
                // Return a response
                return response()->json(['message' => 'تمت الاضافة بنجاح', 'userImage' => $userImage], 201);
            }
            return response()->json(['message' => 'لم تتم العملية بنجاح، المرجو اعادة المحاولة'], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => 'العملية لم تتم بنجاح، المرجو اعادة المحاولة' . $exception->getMessage()], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'uri' => 'required|string',
            'id' => 'required|string',
        ]);
        try {
            $id = $validatedData['id'];
            unset($validatedData['id']);
            $userImage = UserImage::where('userId', auth()->user()->id)
            ->findOrFail($id);
            // Save the userImage
            $userImage->update($validatedData);
            return response()->json(['message' => 'تم التعديل بنجاح', 'userImage' => $userImage], 201);
        } catch (Exception $exception) {
            return response()->json(['message' => 'لم يتم التعديل بنجاح، المرجو اعادة المحاولة' . $exception->getMessage()], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     */

    public function destroy(UserImage $image)
    {
        try {
            // Soft delete the userImage
            $image->delete();
            // Return a response
            return response()->json(['message' => 'Deleted successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'غير متوفر. ' . $e->getMessage()], 404);
        }
    }
}
