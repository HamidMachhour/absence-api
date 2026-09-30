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
use Whoops\Util\Misc;

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
            $params['status'] = __('vocab.status') . ':' .  __("vocab.{$response->first()->status}");
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
            })
            ->where('status', '!=', MisconductReport::STATUS_DRAFT);

        } elseif ($user->hasRole('teacher')) {
            // Teacher can see reports for seances they teach
            $query->whereHas('seance', function ($q) use ($user) {
                $q->where('teacherId', $user->id);
            });
        } elseif ($user->hasRole('admin')) {
            // Admins see all reports, that is not drafts
            $query->where('status', '!=', MisconductReport::STATUS_DRAFT);
        } elseif ($user->hasRole('student')) {
            $studentIds = $user->students->pluck('id')->toArray();
            // Students can see their own reports
            $query->whereIn('studentId', $studentIds);
        }
        else {
            // Other roles are not authorized to view misconduct reports.
            // Return 403 instead of an empty result to avoid redirect loops.
            redirect()->route('home')->with('message', __('vocab.not_authorized'));
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
    public function studentList(Seance $seance)
    {
        // check if current user is a teacher in the seance
        if ($seance->teacherId != auth()->user()->id) {
            return redirect()->route('home');
        }

        $students = $seance->classe->students()
            ->orderBy('num')
            ->paginate(40);
        return view('misconduct_reports.student_list', compact('students', 'seance'));
    }

    /**
     * Store a newly created resource in storage.
     * Corresponds to: POST /misconduct_reports
     */
    public function store(Request $request)
    {

        $seance = Seance::find($request->seanceId);

        // just teacher can create report
        if (!auth()->user()->hasRole('teacher') || $seance?->teacherId != auth()->user()->id) {
            return back()->with('error', __('vocab.not_authorized'));
        }


        // check if current user is a teacher in the seance

        $validatedData = $request->validate([
            'studentId' => ['required', 'exists:Student,id'],
            'seanceId' => ['required', 'exists:Seance,id'],
            'reportDate' => ['required', 'date'],
            'otherActs' => ['nullable', 'string'],
            'otherPropositions' => ['nullable', 'string'],

        ]);


        try {
            DB::beginTransaction();

            $misconductReport = MisconductReport::create([
                'studentId' => $validatedData['studentId'],
                'seanceId' => $validatedData['seanceId'],
                'reportDate' => $validatedData['reportDate'],
                'otherActs' => $validatedData['otherActs'],
                'otherPropositions' => $validatedData['otherPropositions'],
            ]);

            DB::commit();

            // go back to index page with success message
            return redirect()->route('misconduct_reports.edit', $misconductReport)->with('success', __('vocab.misconduct_report_created'));
        } catch (Exception $e) {
            DB::rollBack();
            // go back to create page with error message
            return back()->with('error', __('vocab.misconduct_reports_create_error') . ':' . $e->getMessage());
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

        $statuses = [
            MisconductReport::STATUS_DRAFT,
            MisconductReport::STATUS_SUBMITTED,
            MisconductReport::STATUS_REVIEWED,
            MisconductReport::STATUS_ARCHIVED,
        ];


        return view('misconduct_reports.edit', compact('misconduct_report', 'misconductActs', 'proposedActions', 'misconduct_act_ids', 'proposed_action_ids', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     * Corresponds to: PUT /misconduct_reports/{misconduct_report}
     */
    public function update(Request $request, MisconductReport $misconduct_report)
    {
        $user = auth()->user();
        $oldStatus = $misconduct_report->status;
        $newStatus = $request->input('status');

        // General authorization: only teacher of seance, manager of class, or admin can update.
        $isTeacherOfSeance = $user->hasRole('teacher') && $misconduct_report->seance->teacherId == $user->id;
        $isManagerOfClass = $user->hasRole('manager') && $misconduct_report->student->classe->userId == $user->id;
        $isAdmin = $user->hasRole('admin');

        if (!$isTeacherOfSeance && !$isManagerOfClass && !$isAdmin) {
            return back()->with('error', __('vocab.not_authorized'));
        }

        // Specific authorization for status changes
        if ($oldStatus !== $newStatus) {
            $canUpdateStatus = false;
            // Teacher can submit a draft
            if ($oldStatus === MisconductReport::STATUS_DRAFT && $newStatus === MisconductReport::STATUS_SUBMITTED && $isTeacherOfSeance) {
                $canUpdateStatus = true;
            }
            // Manager can review a submitted report
            if ($oldStatus === MisconductReport::STATUS_SUBMITTED && $newStatus === MisconductReport::STATUS_REVIEWED && ($isManagerOfClass || $isAdmin)) {
                $canUpdateStatus = true;
            }
            // Admin can archive a reviewed report
            if ($oldStatus === MisconductReport::STATUS_REVIEWED && $newStatus === MisconductReport::STATUS_ARCHIVED && $isAdmin) {
                $canUpdateStatus = true;
            }

            if (!$canUpdateStatus) {
                return back()->with('error', __('vocab.not_authorized') . ' ' . __('vocab.to_change_status'));
            }
        }

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

            $updateData = [];

            if ($oldStatus !== $newStatus) $updateData['status'] = $validatedData['status'];
            // Only update fields that are allowed for the user
            if ($isTeacherOfSeance && $oldStatus === MisconductReport::STATUS_DRAFT) {
                $updateData['otherActs'] = $validatedData['otherActs'];
                $updateData['otherPropositions'] = $validatedData['otherPropositions'];
                // Sync the many-to-many relationships
                $misconduct_report->misconductActs()->sync($request->input('misconduct_act_ids', []));
                $misconduct_report->proposedActions()->sync($request->input('proposed_action_ids', []));
            }

            $misconduct_report->update($updateData);



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
    public function destroy(MisconductReport $misconduct_report)
    {


        // report can be deleted if draft or archived
        if ($misconduct_report->status !== MisconductReport::STATUS_DRAFT && $misconduct_report->status !== MisconductReport::STATUS_ARCHIVED) {
            return back()->with('error', __('vocab.not_authorized'));
        }

        $user = auth()->user();
        $isTeacherOfSeance = $user->hasRole('teacher') && $misconduct_report->seance->teacherId == $user->id;
        $isAdmin = $user->hasRole('admin');

        if (!$isTeacherOfSeance && !$isAdmin) {
            return back()->with('error', __('vocab.not_authorized'));
        }

        // Logic to delete the specified misconduct report
        try {
            if ($misconduct_report->status === MisconductReport::STATUS_DRAFT && $isTeacherOfSeance) {
                $misconduct_report->delete();
            } elseif ($misconduct_report->status === MisconductReport::STATUS_ARCHIVED && $isAdmin) {
                $misconduct_report->delete();
            } else {
                return back()->with('error', __('vocab.not_authorized'));
            }
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
    public function destroyAll() {}



    /**
     * show the preview of misconduct report
     */
    public function show(MisconductReport $misconduct_report)
    {
        // General authorization: only teacher of seance, manager of class, or admin can view.
        $user = auth()->user();
        $isTeacherOfSeance = $user->hasRole('teacher') && $misconduct_report->seance->teacherId == $user->id;
        $isManagerOfClass = $user->hasRole('manager') && $misconduct_report->student->classe->userId == $user->id;
        $isAdmin = $user->hasRole('admin');

        if (!$isTeacherOfSeance && !$isManagerOfClass && !$isAdmin) {
            return back()->with('error', __('vocab.not_authorized'));
        }

        // get all misconduct acts
        $misconductActs = $misconduct_report->misconductActs;
        // get all proposed actions
        $proposedActions = $misconduct_report->proposedActions;

        $statuses = [
            MisconductReport::STATUS_DRAFT,
            MisconductReport::STATUS_SUBMITTED,
            MisconductReport::STATUS_REVIEWED,
            MisconductReport::STATUS_ARCHIVED,
        ];
        return view('misconduct_reports.show', compact('misconduct_report', 'misconductActs', 'proposedActions', 'statuses'));
    }
}
