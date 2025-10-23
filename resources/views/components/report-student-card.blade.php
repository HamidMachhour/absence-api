@props(['student','seance'])



<div class="card card-widget widget-user-2 shadow-sm mx-2">
    <!-- Add the bg color to the header using any of the bg-* classes -->
    <div class="widget-user-header bg-info">
        <a href="{{ route('students.show', ['student' => $student]) }}" class="widget-user-image">
            <img class="img-circle elevation-1"
                alt="{{ $user_name = $student->num . '-' . $student->user->fullName() }}"
                src="{{ $student->studentImage->uri ?? asset('/assets/images/no-image.png') }}">
        </a>
        <!-- /.widget-user-image -->
        <h3 class="widget-user-username">{{ $user_name }}</h3>
    </div>
    <div class="card-footer p-0">
        <form action="{{ route('misconduct_reports.store') }}" method="POST">
            @csrf            
            <input type="hidden" name="status" value="{{ \App\Models\MisconductReport::STATUS_DRAFT }}">
            <input type="hidden" name="otherActs" value="">
            <input type="hidden" name="otherPropositions" value="">
            <input type="hidden" name="misconduct_act_ids[]" value="">
            <input type="hidden" name="proposed_action_ids[]" value="">
            <input type="hidden" name="reportDate" value="{{ date('Y-m-d') }}">
            <input type="hidden" name="studentId" value="{{ $student->id }}">
            <input type="hidden" name="seanceId" value="{{ $seance->id }}">

            <div class="btn-group">
                <button class="btn btn-app bg-success" type="submit">
                    <i class="fas fa-plus"></i>
                    {{ __('vocab.new') }}
                </button>
            </div>
        </form>
    </div>
</div>
