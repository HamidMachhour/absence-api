
<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        @if (($nbNotifications = auth()->user()->absenceStudents()->count()) > 0)
        <span class="badge bg-warning navbar-badge">{{ $nbNotifications }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header"> {{__('vocab.notification')}} {{ $nbNotifications }} </span>
        <div class="dropdown-divider"></div>
        <a href="{{route('absences.ofUser',['user'=>auth()->user()])}}" class="dropdown-item">
            <i class="fas fa-person-running mr-2"></i> {{__('vocab.absence')}} {{ $nbNotifications }}
        </a>
    </div>
</li>
  