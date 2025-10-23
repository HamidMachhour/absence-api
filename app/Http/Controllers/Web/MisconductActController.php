<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MisconductAct; // Ensure you import the model
use Illuminate\Support\Facades\DB;
use Exception;

class MisconductActController extends Controller
{
    /**
     * Display a listing of the resource.
     * Corresponds to: GET /misconduct_acts
     */
    public function index()
    {
        // Retrieve all misconduct acts for display in a listing table
        $misconduct_acts = MisconductAct::all();
        
        return view('misconduct_acts.index', compact('misconduct_acts'));
    }

    /**
     * Show the form for creating a new resource.
     * Corresponds to: GET /misconduct_acts/create
     */
    public function create()
    {
        // Display the form to create a new misconduct act
        return view('misconduct_acts.create');
    }

    /**
     * Store a newly created resource in storage.
     * Corresponds to: POST /misconduct_acts
     */
    public function store(Request $request)
    {
        // 1. Validation for multilingual fields
        $validatedData = $request->validate([
            'name_ar' => ['required', 'string', 'max:255', 'unique:MisconductAction,name_ar'],
            'name_fr' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'name_z' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            // 2. Creation
            MisconductAct::create($validatedData);

            return redirect()->route('misconduct_acts.index')
                             ->with('success', __('vocab.misconduct_act_created_successfully'));
        } catch (Exception $e) {
             return redirect()->back()->with('error', __('vocab.error_creating_misconduct_act') . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Corresponds to: GET /misconduct_acts/{misconduct_act}/edit
     *
     * @param  \App\Models\MisconductAct  $misconduct_act
     */
    public function edit(MisconductAct $misconduct_act)
    {
        // Display the form pre-filled with the existing act data
        return view('misconduct_acts.edit', compact('misconduct_act'));
    }

    /**
     * Update the specified resource in storage.
     * Corresponds to: PUT /misconduct_acts/{misconduct_act}
     *
     * @param  \App\Models\MisconductAct  $misconduct_act
     */
    public function update(Request $request, MisconductAct $misconduct_act)
    {
        // 1. Validation (must ignore the current model's name_ar value)
        $validatedData = $request->validate([
            'name_ar' => ['required', 'string', 'max:255', 
                          'unique:MisconductAction,name_ar,' . $misconduct_act->id],
            'name_fr' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'name_z' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            // 2. Update
            $misconduct_act->update($validatedData);

            return redirect()->route('misconduct_acts.index')
                             ->with('success', __('vocab.misconduct_act_updated_successfully'));
        } catch (Exception $e) {
             return redirect()->back()->with('error', __('vocab.error_updating_misconduct_act') . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Corresponds to: DELETE /misconduct_acts/{misconduct_act}
     *
     * @param  \App\Models\MisconductAct  $misconduct_act
     */
    public function destroy(MisconductAct $misconduct_act)
    {
        try {
            // Before deleting, consider checking if the act is linked to any existing reports.
            // If it is, you might prevent deletion or handle the cascade.
            
            $misconduct_act->delete();

            return redirect()->route('misconduct_acts.index')
                             ->with('success', __('vocab.misconduct_act_deleted_successfully'));
        } catch (Exception $e) {
             return redirect()->back()->with('error', __('vocab.error_deleting_misconduct_act') . $e->getMessage());
        }
    }

    /**
     * Remove multiple resources from storage.
     * Corresponds to: DELETE /misconduct_acts/destroyAll
     */
    public function destroyAll(Request $request)
    {

        try {
            // Use a transaction to ensure integrity during bulk deletion
            
            
            DB::beginTransaction();

             $usedActs = DB::table('ReportMisconductAction')->pluck('misconductActionId')->unique();

            // Delete all roles whose IDs are NOT in the list of used roles
            MisconductAct::whereNotIn('id', $usedActs)->delete();

            DB::commit();

            return redirect()->route('misconduct_acts.index')
                             ->with('success', __('vocab.misconduct_acts_deleted_successfully'));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', __('vocab.error_deleting_misconduct_acts') . $e->getMessage());
        }
    }
}