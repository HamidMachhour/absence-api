<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Seance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Validation\Rule;

use Exception;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if (!Auth::check() && !Auth::user()->hasRole('admin')) {
                return response()->json(['message' => __('vocab.not_authorized')], 404);
            }

            $subjects = Subject::get();

            return $subjects;
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.empty') . $e->getMessage()], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'nameFr' => 'nullable|string',
            'nameEn' => 'nullable|string',
            'nameZ' => 'nullable|string',
        ]);

        try {
            // 💡 Check if we already have this subject
            $existingSubject = Subject::where('nameEn', $validatedData['nameEn'])
                ->where('name', $validatedData['name'])
                ->first();

            if ($existingSubject) {
                return response()->json([
                    'message' => __('vocab.subject_exist')
                ], 409); // Use 409 Conflict for duplicate resource
            }

            // Create a new Subject object and assign the validated data
            $subject = Subject::create($validatedData);

            // Return a response
            return response()->json([
                'message' => __('vocab.action_success'),
                'subject' => $subject
            ], 201);
        } catch (Exception $exception) {
            return response()->json([
                'message' => __('vocab.task_failed') . ' ' . __('vocab.try_again') . $exception->getMessage()
            ], 404);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */


    public function update(Request $request, Subject $subject)
    {
        // Check for authorization first. Use a 403 status code for permission errors.
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // Validate the request data. Use the 'Rule::unique' to check for uniqueness
        // while ignoring the current subject's ID.
        $validatedData = $request->validate([
            'name' => ['required', 'string', Rule::unique('Subject', 'name')->ignore($subject->id)],
            'nameFr' => ['nullable', 'string', Rule::unique('Subject', 'nameFr')->ignore($subject->id)],
            'nameEn' => ['nullable', 'string', Rule::unique('Subject', 'nameEn')->ignore($subject->id)],
            'nameZ' => ['nullable', 'string', Rule::unique('Subject', 'nameZ')->ignore($subject->id)],

        ]);

        // Update the subject with the validated data
        $subject->update($validatedData);

        // Return a success response. Use a 200 status code for a successful update
        // and a relevant success message.
        return response()->json(['message' => __('vocab.update_success')], 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject)
    {
        // Check for authorization first
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // Check if the subject is currently in use by any user
        $isSubjectUsed = Seance::where('subjectId', $subject->id)->exists();

        if ($isSubjectUsed) {
            return response()->json([
                'message' => __('vocab.delete_subject_failed')
            ], 409); // 409 Conflict is appropriate for this kind of error
        }

        // If the subject is not used, safely delete it
        $subject->delete();

        // Return a successful response. 200 OK or 204 No Content are good choices.
        return response()->json(['message' => __('vocab.delete_success')], 200);
    }



    public function destroyAll()
    {
        // Check for authorization first
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        try {
            // Find all subjects that are currently in use
            $usedSubjectIds = DB::table('Seance')->pluck('subjectId')->unique();

            // Delete all subjects whose IDs are NOT in the list of used subjects
            Subject::whereNotIn('id', $usedSubjectIds)->delete();

            return response()->json(['message' => __('vocab.delete_success')], 200);
        } catch (\Exception $e) {
            // Return a generic error message for any exceptions
            return response()->json(['message' => __('vocab.delete_failure') . $e->getMessage()], 500);
        }
    }
}
