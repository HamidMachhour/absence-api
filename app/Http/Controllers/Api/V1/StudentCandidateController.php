<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Student;


use Illuminate\Http\Request;
use App\Models\StudentCandidate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class StudentCandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param int $page_size
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $page_size = 40)
    {
        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");
            $query = StudentCandidate::filter($request)->latest();

            return $query->paginate($page_size);
        } catch (Exception $e) {
            return response()->json(['message' => 'غير متوفر. ' . $e->getMessage()], 404);
        }
    }

    public function show(Request $request, $page_size = 40)
    {
        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            return StudentCandidate::where('classeId', $request->input('classeId'))
                ->filter($request)->latest()
                ->paginate($page_size);
        } catch (Exception $e) {
            return response()->json(['message' => 'غير متوفر. ' . $e->getMessage()], 404);
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
        if (!Auth::check() || !Auth::user()->hasRole('student')) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }
        // Validate the request data
        $validatedData = $request->validate([
            'classeId' => 'required|string',
            'userId' => 'required|string',
        ]);

        try {
            if(Auth::user()->id !==$validatedData['userId'] ){
                return response()->json(['message' =>  'غير مسموح، المرجو تسجيل الدخول'], 401);
            }
            $exists = StudentCandidate::where('classeId', $validatedData['classeId'])
                ->where('userId', $validatedData['userId'])->exists();
            if ($exists) {
                return response()->json(['message' => 'لقد تم رفع هدا الطلب سابقا'], 201);
            }
            // Create a new StudentCandidate object and assign the validated data
            $student = StudentCandidate::create($validatedData);
            // Return a response
            return response()->json(['message' => 'تم ارسال الطلب بنجاح!', 'student' => $student], 201);
        } catch (Exception $exception) {
            return response()->json(['message' => 'العملية لم تتم بنجاح، المرجو اعادة المحاولة' . $exception->getMessage()], 404);
        }
    }


    public function accept(StudentCandidate $student)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $student->classe->userId) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }
        try {

            DB::transaction(function () use ($student) {
                $exists = Student::where('classeId', $student->classId)
                    ->where('userId', $student->userId)->exists();
                if ($exists) {
                    $student->delete();
                    return response()->json(['message' => 'لقد تم قبول هدا الطلب سابقا'], 401);
                }
                $all = Student::where('classeId', $student->classeId)->get();
                Student::create([
                    'num' => count($all) + 1,
                    'classeId' => $student->classeId,
                    'userId' => $student->userId
                ]);
                // Soft delete the student
                $student->delete();
            });
            // Return a response
            return response()->json(['message' => 'تم قبول طلب الالتحاق بنجاح'], 200);
        } catch (Exception $e) {
            // Return a response
            return response()->json(['message' => 'لم تتم العملية بنجاح، المرجو اعادة المحاولة'], 401);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(StudentCandidate $student)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $student->classe->userId) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }
        try {
            // Soft delete the student
            $student->delete();
            // Return a response
            return response()->json(['message' => 'تم الحدف بنجاح'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'لم تتم العملية بنجاح، المرجو اعادة المحاولة'], 401);
        }
    }
}
