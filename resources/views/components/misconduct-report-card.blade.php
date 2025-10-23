@props(['misconduct_report'])

<div class="card card-primary card-outline collapsed-card m-1" :title="">

    <div class="card-header">
        <div class="card-title h3 d-flex gap-2">
            <a href="{{ request()->fullUrlWithQuery(['reportDate' => $misconduct_report->reportDate, 'page' => 1]) }}"
                class="flex text-blue-600 hover:text-gray-700 justify-center">
                {{ $misconduct_report->reportDate }}
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => $misconduct_report->status, 'page' => 1]) }}"
                class="flex text-blue-600 hover:text-gray-700 justify-center">
                {{ $misconduct_report->status }}
            </a>
        </div>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-1">

        <div class="d-flex flex-column justify-between">
            <x-student-card :student="$misconduct_report->student" />
            <x-seance-card :seance="$misconduct_report->seance" />
            <x-misconduct-report-actions-card :misconduct_report="$misconduct_report" />
        </div>

    </div>
</div>