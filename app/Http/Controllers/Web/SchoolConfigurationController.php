<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Models\SchoolConfiguration;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Exception;

class SchoolConfigurationController extends Controller
{
    /**
     * Display the school configuration settings for viewing or editing.
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Ensure only authorized users (e.g., admins) can access this
        try {
            if (!Auth::check() || !Auth::user()->hasRole('admin')) {
                return redirect()->route('home')->with('message', __('vocab.not_authorized'));
            }

            // Retrieve the single configuration row (or null if it doesn't exist)
            $config = SchoolConfiguration::getSettings();
            return view('settings.school.edit', compact('config'));
        } catch (Exception $e) {
            return view('error', ["message" => __('vocab.action_failure') . $e->getMessage()]);
        }
    }

    /**
     * Store or update the single school configuration settings row.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Ensure authorization check here as well
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->with('message', __('vocab.not_authorized'));
        }

        // 1. Validation
        $validatedData = $request->validate([
            // Use 'unique:school_configurations,code' to ensure the code is unique,
            // ignoring the current row's code if it exists (for updates).
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('school_configurations', 'code')->ignore(1, 'id')
            ],

            'name' => ['required', 'string', 'max:255'],
            'name_fr' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'name_z' => ['nullable', 'string', 'max:255'],

            'city' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'director_name' => ['nullable', 'string', 'max:150'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'current_academic_start' => ['nullable', 'date'],
            'current_academic_end' => ['nullable', 'date', 'after:current_academic_start'],
            'logo_path' => ['nullable', 'string', 'max:255'  ],
            'process_attendances_enabled' => ['nullable', 'boolean'],
        ]);

        // 2. Update or Create the single row (always using ID 1 as the key)
        // Ensure the checkbox value is persisted as boolean (checkbox absence => false)
        $validatedData['process_attendances_enabled'] = $request->has('process_attendances_enabled') ? true : false;

        SchoolConfiguration::updateOrCreate(
            ['id' => 1], // Key to identify the single row
            $validatedData
        );

        return redirect()->back()->with('success', __('vocab.update_success'));
    }
}
