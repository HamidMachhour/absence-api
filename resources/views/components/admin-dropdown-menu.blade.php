
<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        @if (
        ($nbNotifications =
        ($nvAbsences =  auth()->user()->latestAbsencesCount() ) +
        ($nvRapports = auth()->user()->nbLatestRapports(7)) +
        ($nbEnrollments = auth()->user()->nbEnrollments())) > 0)
        <span class="badge bg-warning navbar-badge">{{ $nbNotifications }}</span>
        @endif

    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header"> {{__('vocab.notification')}} {{ $nbNotifications }} </span>
        <div class="dropdown-divider"></div>
        <a href="{{route('absences.index')}}" class="dropdown-item">
            <i class="fas fa-person-running mr-2"></i>{{__('vocab.absence')}} {{ $nvAbsences }}
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i>{{__('vocab.report')}} {{ $nvRapports }}
        </a>
        <div class="dropdown-divider"></div>
        <a href="{{ route('enrollments.index') }}" class="dropdown-item">
            <i class="fas fa-user-plus mr-2"></i> {{__('vocab.enrollment')}} {{ $nbEnrollments }}
        </a>
    </div>
</li>
 
