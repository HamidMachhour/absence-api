@if (session()->has('message'))
    <div id="toastsContainerTopRight" class="toasts-top-right ">
        <div class="toast bg-info fade show" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
            <div class="toast-header">
                <strong class="mr-auto">{{session('messageTitle')}}</strong>
                <small>{{session('messageSubtitle')}}</small>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="toast-body">{{ session('message') }}</div>
        </div>
    </div>
@endif