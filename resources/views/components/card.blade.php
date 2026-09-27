
@props(['title'])
<div {{ $attributes->merge(['class' => 'card card-primary card-outline p-1 m-2 inline-block']) }}>
    <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">
        <h3 class="card-title">{{ $title ?? '' }}</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-1" >
        {{ $slot }}
    </div>
</div>

