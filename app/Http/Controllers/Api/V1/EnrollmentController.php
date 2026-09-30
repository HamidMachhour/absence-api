<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Student;


use Illuminate\Http\Request;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class EnrollmentController extends Controller
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

            return Enrollment::filter($request)
                ->with('user')
                ->with('classe')
                ->paginate($page_size);
        } catch (Exception $e) {

            return response()->json(['message' => __('vocab.no_data') . $e->getMessage()], 404);
        }
    }

    public function totalRows(Request $request)
    {
        return Enrollment::filter($request)->count();
    }

    public function show(Request $request, $page_size = 40)
    {
        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            return Enrollment::where('classeId', $request->input('classeId'))
                ->filter($request)->latest()
                ->paginate($page_size);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.no_data') . $e->getMessage()], 404);
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
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        // Validate the request data
        $validatedData = $request->validate([
            'classeId' => 'required|string',
            'userId' => 'required|string',
        ]);

        try {
            if (Auth::user()->id !== $validatedData['userId']) {
                return response()->json(['message' =>  __('vocab.not_authorized')], 401);
            }
            $exists = Enrollment::where('classeId', $validatedData['classeId'])
                ->where('userId', $validatedData['userId'])->exists();
            if ($exists) {
                return response()->json(['message' => __('vocab.enrollment_exist')], 201);
            }
            // Create a new Enrollment object and assign the validated data
            $enrollment = Enrollment::create($validatedData);
            // Return a response
            return response()->json(['message' => __('vocab.enrollment_successful'), 'enrollment' => $enrollment], 201);
        } catch (Exception $exception) {
            return response()->json(['message' => __('vocab.task_failed') . ' ' . __('vocab.try_again') . $exception->getMessage()], 404);
        }
    }


    public function accept(Enrollment $enrollment)
    {
        if (!Auth::user()->hasRole('admin') && !$enrollment->isOwnedBy(Auth::user())) {
            return redirect()->route('home')->with('message', __('vocab.not_authorized'));
        }
        try {

            DB::transaction(function () use ($enrollment) {
                $exists = Student::where('classeId', $enrollment->classId)
                    ->where('userId', $enrollment->userId)->exists();
                if ($exists) {
                    $enrollment->delete();
                    return response()->json(['message' => __('vocab.already_enrolled')], 401);
                }
                $count = Student::where('classeId', $enrollment->classeId)->count();
                Student::create([
                    'num' => $count + 1,
                    'classeId' => $enrollment->classeId,
                    'userId' => $enrollment->userId
                ]);
                // Soft delete the enrollment
                $enrollment->delete();
            });
            // Return a response
            return response()->json(['message' => __('vocab.enrollment_successful')], 200);
        } catch (Exception $e) {
            // Return a response
            return response()->json(['message' => __('vocab.action_failure') . ' ' . __('vocab.try_again')], 401);
        }
    }

    public function acceptAll(Request $request)
    {
        if (!Auth::check() || (!Auth::user()->hasRole('manager') && !Auth::user()->hasRole('admin'))) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        try {
            DB::beginTransaction();

            // 1. Fetch all enrollments to be processed once to avoid N+1 query problem
            $enrollments = Enrollment::filter($request)->get();

            if ($enrollments->isEmpty()) {
                DB::rollBack();
                return response()->json(['message' => __('vocab.no_records_found')], 404);
            }

            foreach ($enrollments as $enrollment) {
                // Check for existing student with correct column name
                $exists = Student::where('classeId', $enrollment->classeId)
                    ->where('userId', $enrollment->userId)
                    ->exists();

                if (!$exists) {

                    $maxNum = Student::where('classeId', $enrollment->classeId)->max('num');
                    // If no students exist for the class, max() will return null.
                    $count = $maxNum !== null ? $maxNum : 0;
                    $num = $count + 1;

                    Student::create([
                        'num' => $num,
                        'classeId' => $enrollment->classeId,
                        'userId' => $enrollment->userId
                    ]);
                }
            }
               
            // 2. Perform a bulk delete on enrollments outside the loop
            Enrollment::filter($request)->delete();

            DB::commit();

            return response()->json(['message' => __('vocab.enrollment_successful')], 200);
        } catch (Exception $e) {

             

            DB::rollBack();
            return response()->json(['message' => __('vocab.action_failure') . ' ' . __('vocab.try_again')], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(Enrollment $enrollment)
    {
        if (!Auth::user()->hasRole('admin') && !$enrollment->isOwnedBy(Auth::user())) {
            return redirect()->route('home')->with('message', __('vocab.not_authorized'));
        }
        try {
            // Soft delete the enrollment
            $enrollment->delete();
            // Return a response
            return response()->json(['message' => __('vocab.delete_success')], 200);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.action_failure') . ' ' . __('vocab.try_again')], 401);
        }
    }

    /**
     * Soft deletes all enrollments records.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAll(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        if (!Auth::user()->hasRole('manager') && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // Soft delete all seances by updating the deleted_at timestamp
        Enrollment::filter($request)->update(['deleted_at' => now()]);

        // Return a response
        return response()->json(['message' => __('vocab.delete_success')], 200);
    }
}
