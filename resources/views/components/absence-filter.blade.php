<div> 
    <div class="row">
        <div class="col-md-3">
            <label for="classe">{{__('vocab.class')}}</label>
            <select class="form-control" id="classe">
                <option value=""></option>
                @foreach ($classes as $classe)
                    <option value="{{ $classe->id }}">{{ $classe->classeName() }} </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="classe">{{__('vocab.student')}}</label>
            <select class="form-control" id="student">

            </select>
        </div>
        <div class="col-md-2">
            <label for="from">{{__('vocab.from')}}</label>
            <input type="date" id="from" name="from" class="form-control" />
        </div>
        <div class="col-md-2">
            <label for="to">{{__('vocab.to_date')}}</label>
            <input type="date" id="to" name="to" class="form-control" />
        </div>

        <div class="col-md-2 align-self-end">

            <button type="button" class="btn btn-success" id="btn_filter" name="btn_filter" >
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>
</div>
