<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MisconductAct; // Ensure you import the model
use App\Models\MisconductReport; // Ensure you import the model
use App\Models\ProposedAction; // Ensure you import the model
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Validation\Rule;
use App\Models\Seance;

class MisconductReportController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function prepareFilterParams(Request $request, $response)
    {

        $params = [];
        if (!$response || count($response) == 0)
            return $params;

        if ($request->filled('studentId')) {
            $params['studentId'] = __('vocab.student') . ':' . $response->first()->student->user->fullName();
        }
        if ($request->filled('seanceId')) {
            $params['seanceId'] = __('vocab.seance') . ':' . $response->first()->seance->title();
        }
        if ($request->filled('status')) {
            $params['status'] = __('vocab.status') . ':' . $response->first()->status;
        }

        if ($request->filled('reportDate')) {
            $params['reportDate'] = __('vocab.date') . ':' . $response->first()->reportDate;
        }
        if ($request->filled('search')) {
            $params['search'] = __('vocab.search') . ':' .  $request->search;
        }


        return $params;
    }
    /**
     * Display a listing of the resource.
     * Corresponds to: GET /misconduct_reports
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        // Start building the query with eager loading to prevent N+1 issues
        $query = MisconductReport::with(['student.user', 'seance']);

        // Apply role-based access rules
        if ($user->hasRole('manager')) {
            // Manager can see reports for students in their classes
            $query->whereHas('student.classe', function ($q) use ($user) {
                $q->where('userId', $user->id);
            });
        } elseif ($user->hasRole('teacher')) {
            // Teacher can see reports for seances they teach
            $query->whereHas('seance', function ($q) use ($user) {
                $q->where('teacherId', $user->id);
            });
        } elseif (!$user->hasRole('admin')) {
            // If not an admin, manager, or teacher, they see no reports.
            $query->whereRaw('1 = 0'); // No results
        }
        // Admins see all reports, so no additional constraints are needed.

        // Apply filters from the request
        if ($request->filled('studentId')) {
            $query->where('studentId', $request->studentId);
        }
        if ($request->filled('seanceId')) {
            $query->where('seanceId', $request->seanceId);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('reportDate')) {
            $query->whereDate('reportDate', $request->reportDate);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('otherActs', 'like', '%' . $request->search . '%')
                    ->orWhere('otherPropositions', 'like', '%' . $request->search . '%');
            });
        }

        $misconduct_reports = $query->latest()->paginate(10);
        $params = $this->prepareFilterParams($request, $misconduct_reports);

        // Return the view with the misconduct reports data
        return view('misconduct_reports.index', compact('misconduct_reports', 'params'));
    }

    /**
     * show student list to select student for misconduct report
     */
    public function studentList(Seance $seance){
        // check if current user is a teacher in the seance
        if($seance->teacherId != auth()->user()->id){
            return redirect()->route('home');
        }

        $students = $seance->classe->students()->paginate(40);
        return view('misconduct_reports.student_list', compact('students', 'seance'));
    }
    /**
     * Show the form for creating a new resource.
     * Corresponds to: GET /misconduct_reports/create
     */
    public function create()
    {
        $misconductActs = MisconductAct::all();
        $proposedActions = ProposedAction::all();
        // Logic to display the form for creating a new misconduct report
        return view('misconduct_reports.create', compact('misconductActs', 'proposedActions'));
    }

    /**
     * Store a newly created resource in storage.
     * Corresponds to: POST /misconduct_reports
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'studentId' => ['required', 'exists:Student,id'],
            'seanceId' => ['required', 'exists:Seance,id'],
            'reportDate' => ['required', 'date'],
            'otherActs' => ['nullable', 'string'],
            'otherPropositions' => ['nullable', 'string'],
            'status' => [
                'required',
                Rule::in([
                    MisconductReport::STATUS_DRAFT,
                    MisconductReport::STATUS_SUBMITTED,
                    MisconductReport::STATUS_REVIEWED,
                    MisconductReport::STATUS_ARCHIVED
                ])
            ],
            'misconduct_act_ids' => ['nullable', 'array'],
            'misconduct_act_ids.*' => ['exists:MisconductAction,id'],
            'proposed_action_ids' => ['nullable', 'array'],
            'proposed_action_ids.*' => ['exists:ProposedAction,id'],
            'proposed_action_ids.*' => ['exists:proposed_actions,id'],
        ]);

        dd($validatedData);

        try {
            DB::beginTransaction();

            $misconductReport = MisconductReport::create([
                'studentId' => $validatedData['studentId'],
                'seanceId' => $validatedData['seanceId'],
                'reportDate' => $validatedData['reportDate'],
                'otherActs' => $validatedData['otherActs'],
                'otherPropositions' => $validatedData['otherPropositions'],
                'status' => $validatedData['status'],
            ]);
            // Attach the many-to-many relationships
            $misconductReport->misconductActs()->attach($request->input('misconduct_act_ids', []));
            $misconductReport->proposedActions()->attach($request->input('proposed_action_ids', []));
 
            DB::commit();
             // Always redirect to the edit page after creation for a better user experience.
            return redirect()->route('misconduct_reports.edit', $misconductReport)->with('success', __('vocab.misconduct_reports_created'));
        } catch (Exception $e) {
            DB::rollBack();
            // go back to create page with error message
            return redirect()->route('misconduct_reports.create')->with('error', __('vocab.misconduct_reports_create_error') . ':' . $e->getMessage());
        }
    }


    /**
     * Show the form for editing the specified resource.
     * Corresponds to: GET /misconduct_reports/{misconduct_report}/edit
     */
    public function edit(MisconductReport $misconduct_report)
    {
        // Logic to retrieve and display the form for editing a misconduct report
        // get all misconduct acts
        $misconductActs = MisconductAct::all();
        // get all proposed actions
        $proposedActions = ProposedAction::all();
        // get miconduct acts ids of misconduct report
        $misconduct_act_ids = $misconduct_report->misconductActs()->pluck('id');
        // get proposed actions ids of misconduct report
        $proposed_action_ids = $misconduct_report->proposedActions()->pluck('id');


        return view('misconduct_reports.edit', compact('misconduct_report', 'misconductActs', 'proposedActions', 'misconduct_act_ids', 'proposed_action_ids'));
    }

    /**
     * Update the specified resource in storage.
     * Corresponds to: PUT /misconduct_reports/{misconduct_report}
     */
    public function update(Request $request, MisconductReport $misconduct_report)
    {
        // Logic to validate and update the specified misconduct report
        $validatedData = $request->validate([
            'otherActs' => ['nullable', 'string'],
            'otherPropositions' => ['nullable', 'string'],
            'status' => [
                'required',
                Rule::in([
                    MisconductReport::STATUS_DRAFT,
                    MisconductReport::STATUS_SUBMITTED,
                    MisconductReport::STATUS_REVIEWED,
                    MisconductReport::STATUS_ARCHIVED
                ])
            ],
            'misconduct_act_ids' => ['nullable', 'array'],
            'misconduct_act_ids.*' => ['exists:MisconductAction,id'],
            'proposed_action_ids' => ['nullable', 'array'],
            'proposed_action_ids.*' => ['exists:ProposedAction,id'],
        ]);
        try {
            DB::beginTransaction();

            $misconduct_report->update([
                'otherActs' => $validatedData['otherActs'],
                'otherPropositions' => $validatedData['otherPropositions'],
                'status' => $validatedData['status'],
            ]);

            // Sync the many-to-many relationships
            $misconduct_report->misconductActs()->sync($request->input('misconduct_act_ids', []));
            $misconduct_report->proposedActions()->sync($request->input('proposed_action_ids', []));

            DB::commit();
            // go back to index page with success message
            return redirect()->route('misconduct_reports.index')->with('success', __('vocab.misconduct_reports_updated'));
        } catch (Exception $e) {
            DB::rollBack();
            // go back to edit page with error message
            return redirect()->route('misconduct_reports.edit', $misconduct_report)->with('error', __('vocab.misconduct_reports_update_error') . ':' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Corresponds to: DELETE /misconduct_reports/{misconduct_report}
     */
    public function destroy($misconduct_report)
    {
        // Logic to delete the specified misconduct report
        try {
            $misconduct_report->delete();
            // go back to index page with success message
            return redirect()->route('misconduct_reports.index')->with('success', __('vocab.misconduct_reports_deleted'));
        } catch (Exception $e) {
            // go back to index page with error message
            return redirect()->route('misconduct_reports.index')->with('error', __('vocab.misconduct_reports_delete_error') . ':' . $e->getMessage());
        }
    }

    /**
     * Remove multiple resources from storage.
     * Corresponds to: DELETE /misconduct_reports/destroy
     */
    public function destroyAll()
    {
        // Logic to delete multiple misconduct reports
        try {
            MisconductReport::all()->delete();
            // go back to index page    
            return redirect()->route('misconduct_reports.index')->with('success', __('vocab.misconduct_reports_deleted'));
        } catch (Exception $e) {
            // go back to index page with error message
            return redirect()->route('misconduct_reports.index')->with('error', __('vocab.misconduct_reports_delete_error') . ':' . $e->getMessage());
        }
    }

    /**
     * update status
     */
    public function updateStatus($misconduct_report, $status)
    {
        // check if not authenticated
        if (!auth()->user()) {
            return redirect()->route('login');
        }
        // check if user is admin or manager
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('manager')) {
            return redirect()->route('home');
        }
        // check if status is valid
        if (
            $status !== MisconductReport::STATUS_DRAFT &&
            $status !== MisconductReport::STATUS_SUBMITTED &&
            $status !== MisconductReport::STATUS_REVIEWED &&
            $status !== MisconductReport::STATUS_ARCHIVED
        ) {
            return redirect()->route('misconduct_reports.edit', $misconduct_report)->with('error', __('vocab.misconduct_reports_invalid_status'));
        } else {
            // Logic to update the status of the specified misconduct report
            $misconduct_report->status = $status;
            $misconduct_report->save();
            // go back to edit page with success message
            return redirect()->route('misconduct_reports.edit', $misconduct_report)->with('success', __('vocab.misconduct_reports_status_updated'));
        }
    }

    /**
     * show the preview of misconduct report
     */
    public function show($misconduct_report)
    {
        // Logic to print the preview of the specified misconduct report
        return view('misconduct_reports.show', compact('misconduct_report'));
    }

    /**
     * print the misconduct report to pdf file
     */
    public function print($misconduct_report)
    {
        // Logic to print the specified misconduct report to PDF
        dd($misconduct_report);
    }
}
