<div> 
    <div class="row d-flex justify-content-center {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">
        <div class="col-lg-2 m-2">
            <label for="classe">{{__('vocab.class')}}</label>
            <select class="form-control" id="classe">
                <option value=""></option>
                @foreach ($classes as $classe)
                    <option value="{{ $classe->id }}">{{ $classe->classeName() }} </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 m-2">
            <label for="classe">{{__('vocab.student')}}</label>
            <select class="form-control" id="student">

            </select>
        </div>
        <div class="col-lg-2 m-2">
            <label for="from">{{__('vocab.from')}}</label>
            <input type="date" id="from" name="from" class="form-control" />
        </div>
        <div class="col-lg-2 m-2">
            <label for="to">{{__('vocab.to_date')}}</label>
            <input type="date" id="to" name="to" class="form-control" />
        </div>

        <div class="col-lg-2 m-2">
            <div>&nbsp;</div>
            <button type="button" class="btn btn-success text-gray-800 mt-2" id="btn_chart_filter" name="btn_chart_filter" >
                <i class="fas fa-filter"></i>
                {{__('vocab.apply_filters')}}
            </button>
        </div>
        
    </div>
</div>
