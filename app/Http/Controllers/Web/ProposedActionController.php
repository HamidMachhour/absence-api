<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProposedAction; // Ensure you import the model
use Illuminate\Support\Facades\DB;
use Exception;
class ProposedActionController extends Controller
{
    /**
     * Display a listing of the resource.
     * Corresponds to: GET /proposed_actions
     */
    public function index()
    {
        // Retrieve all misconduct acts for display in a listing table
        $proposed_actions = ProposedAction::all();
        
        return view('proposed_actions.index', compact('proposed_actions'));
    }

    /**
     * Show the form for creating a new resource.
     * Corresponds to: GET /proposed_actions/create
     */
    public function create()
    {
        // Display the form to create a new misconduct act
        return view('proposed_actions.create');
    }

    /**
     * Store a newly created resource in storage.
     * Corresponds to: POST /proposed_actions
     */
    public function store(Request $request)
    {
        // 1. Validation for multilingual fields
        $validatedData = $request->validate([
            'name_ar' => ['required', 'string', 'max:255', 'unique:ProposedAction,name_ar'],
            'name_fr' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'name_z' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            // 2. Creation
            ProposedAction::create($validatedData);

            return redirect()->route('proposed_actions.index')
                             ->with('success', __('vocab.proposed_action_created_successfully'));
        } catch (Exception $e) {
             return redirect()->back()->with('error', __('vocab.error_creating_proposed_action') . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Corresponds to: GET /proposed_actions/{proposed_action}/edit
     *
     * @param  \App\Models\ProposedAction  $proposed_action
     */
    public function edit(ProposedAction $proposed_action)
    {
        // Display the form pre-filled with the existing act data
        return view('proposed_actions.edit', compact('proposed_action'));
    }

    /**
     * Update the specified resource in storage.
     * Corresponds to: PUT /proposed_actions/{proposed_action}
     *
     * @param  \App\Models\ProposedAction  $proposed_action
     */
    public function update(Request $request, ProposedAction $proposed_action)
    {
        // 1. Validation (must ignore the current model's name_ar value)
        $validatedData = $request->validate([
            'name_ar' => ['required', 'string', 'max:255', 
                          'unique:ProposedAction,name_ar,' . $proposed_action->id],
            'name_fr' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'name_z' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            // 2. Update
            $proposed_action->update($validatedData);

            return redirect()->route('proposed_actions.index')
                             ->with('success', __('vocab.proposed_action_updated_successfully'));
        } catch (Exception $e) {
             return redirect()->back()->with('error', __('vocab.error_updating_proposed_action') . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Corresponds to: DELETE /proposed_actions/{proposed_action}
     *
     * @param  \App\Models\ProposedAction  $proposed_action
     */
    public function destroy(ProposedAction $proposed_action)
    {
        try {
            // Before deleting, consider checking if the act is linked to any existing reports.
            // If it is, you might prevent deletion or handle the cascade.
            
            $proposed_action->delete();

            return redirect()->route('proposed_actions.index')
                             ->with('success', __('vocab.proposed_action_deleted_successfully'));
        } catch (Exception $e) {
             return redirect()->back()->with('error', __('vocab.error_deleting_proposed_action') . $e->getMessage());
        }
    }

    /**
     * Remove multiple resources from storage.
     * Corresponds to: DELETE /proposed_actions/destroyAll
     */
    public function destroyAll(Request $request)
    {

        try {
            // Use a transaction to ensure integrity during bulk deletion
            
            
            DB::beginTransaction();

             $proposedActs = DB::table('ReportProposedAction')->pluck('proposedActionId')->unique();

            // Delete all roles whose IDs are NOT in the list of used roles
            ProposedAction::whereNotIn('id', $proposedActs)->delete();

            DB::commit();

            return redirect()->route('proposed_actions.index')
                             ->with('success', __('vocab.proposed_actions_deleted_successfully'));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', __('vocab.error_deleting_proposed_actions') . $e->getMessage());
        }
    }
}
