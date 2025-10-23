<div>
    <div class="row">
        <div class="col-md-2">
            <label for="from">{{__('vocab.from_date')}}</label>
            <input type="date" id="from" name="from" class="form-control" />
        </div>
        <div class="col-md-2">
            <label for="to">{{__('vocab.to_date')}}</label>
            <input type="date" id="to" name="to" class="form-control" />
        </div>

        <div class="col-md-2 align-self-end">
            @if ($student)
            <input hidden id="classe" value="{{ $student->classeId }}" />
            <input hidden id="student" value="{{ $student->id }}" />
            @endif
            <button type="button" class="btn btn-success" id="btn_filter" name="btn_filter">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>
</div>