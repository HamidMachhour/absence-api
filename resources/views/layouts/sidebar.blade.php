  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-primary elevation-4 no-print">
      <!-- Brand Logo -->
      <a href="{{ route('home') }}" class="brand-link">
          <img src="{{ URL::asset('assets/images/logo.png') }}" alt="{{__('vocab.platform').' '.__('vocab.absence')}}" class="brand-image img-circle elevation-2 "
              style="opacity: .8">
          <span class="brand-text font-weight-light">{{__('vocab.platform').' '.__('vocab.absence')}}</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
          @auth

          <div class="user-panel m-3 pb-3 d-flex ">
              <div class="image">
                  <img src="{{ auth()->user()->userImage->uri ?? URL::asset('assets/images/logo.png') }}"
                      class="brand-image img-circle elevation-2" alt="User Image">
              </div>
              <div class="info">
                  <a href="{{ route('users.show', ['user' => auth()->user()]) }}" class="d-block">
                      {{ auth()->user()->fullName() }}
                  </a>
              </div>
          </div>
          @endauth

          <!-- SidebarSearch Form -->
          <div class="form-inline">
              <div class="input-group" data-widget="sidebar-search">
                  <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                      aria-label="Search">
                  <div class="input-group-append">
                      <button class="btn btn-sidebar">
                          <i class="fas fa-search fa-fw"></i>
                      </button>
                  </div>
              </div>
          </div>

          <!-- Sidebar Menu -->
          <nav class="mt-2">
              <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                  data-accordion="false">
                  <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                  <li class="nav-item">
                      <a href="#" class="nav-link ">
                          <i class="nav-icon fas fa-tachometer-alt"></i>
                          <p>
                              {{__('vocab.principal_menu')}}
                              <i class="right fas fa-angle-left"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview">
                          @auth
                          <li class="nav-item">
                              <a href="{{ route('dashboard', ['user' => auth()->user()]) }}" class="nav-link">
                                  <i class="far fa-circle nav-icon"></i>
                                  <p>{{__('vocab.dashboard')}}</p>
                              </a>
                          </li>
                          @if (auth()->user()->hasRole('admin'))
                          <li class="nav-item">
                              <a href="{{ route('users.index') }}" class="nav-link">
                                  <i class="far fa-circle nav-icon"></i>
                                  <p>{{__('vocab.users')}}</p>
                                  @if (($nbUsers = auth()->user()->nbUsers()) > 0)
                                  <span class="badge badge-warning right">
                                      {{ $nbUsers }}
                                  </span>
                                  @endif
                              </a>
                          </li>
                          <li class="nav-item">
                              <a href="{{ route('roles.index') }}" class="nav-link">
                                  <i class="far fa-circle nav-icon"></i>
                                  <p>{{__('vocab.roles')}}</p>
                                  @if (($nbRoles = auth()->user()->nbRoles()) > 0)
                                  <span class="badge badge-warning right">
                                      {{ $nbRoles }}
                                  </span>
                                  @endif
                              </a>
                          </li>
                          <li class="nav-item">
                              <a href="{{ route('subjects.index') }}" class="nav-link">
                                  <i class="far fa-circle nav-icon"></i>
                                  <p>{{__('vocab.subjects')}}</p>
                                  @if (($nbSubjects = auth()->user()->nbSubjects()) > 0)
                                  <span class="badge badge-warning right">
                                      {{ $nbSubjects }}
                                  </span>
                                  @endif
                              </a>
                          </li>
                          @endif
                          @else
                          <li class="nav-item">
                              <a href="{{ route('users.login') }}" class="nav-link">
                                  <i class="fas fa-door-closed nav-icon"></i>
                                  <p>{{__('vocab.login')}}</p>
                              </a>

                          </li>
                          @endauth

                      </ul>
                  </li>
                  <li class="nav-item">
                  <li class="nav-item">
                      <a href="#" class="nav-link">
                          <i class="nav-icon fas fa-table"></i>
                          <p>
                              {{__('vocab.classes')}}
                              <i class="fas fa-angle-left right"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview">
                          <li class="nav-item">
                              <a href="{{ route('classes.index') }}" class="nav-link">
                                  <i class="far fa-circle nav-icon"></i>
                                  <p> {{__('vocab.classes')}}</p>
                                  @auth
                                  @if (($nbClasses = auth()->user()->nbClasses()) > 0)
                                  <span class="badge badge-warning right">
                                      {{ $nbClasses }}
                                  </span>
                                  @endif
                                  @endauth
                              </a>
                          </li>
                          @auth
                          @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                          <li class="nav-item">
                              <a href="{{ route('classes.manage') }}" class="nav-link">
                                  <i class="nav-icon far fa-circle"></i>
                                  <p>
                                      {{__('vocab.my_classes')}}
                                  </p>

                                  @if (($nbMyClasses = auth()->user()->nbMyClasses()) > 0)
                                  <span class="badge badge-warning right">
                                      {{ $nbMyClasses }}
                                  </span>
                                  @endif

                              </a>
                          </li>
                          <li class="nav-item">
                              <a href="{{ route('enrollments.index') }}" class="nav-link">
                                  <i class="far fa-circle nav-icon"></i>
                                  <p> {{__('vocab.enrollments')}} </p>

                                  @if (($nbEnrollments = auth()->user()->nbEnrollments()) > 0)
                                  <span class="badge badge-warning right">{{ $nbEnrollments }}</span>
                                  @endif

                              </a>
                          </li>
                          @endif
                          @endauth
                      </ul>
                  </li>
                  @auth
                  <li class="nav-item">
                      <a href="#" class="nav-link">
                          <i class="nav-icon far fa-calendar-alt"></i>
                          <p>
                              {{__('vocab.absence_management')}}
                              <i class="fas fa-angle-left right"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview">
                          <li class="nav-item">
                              @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                              <a href="{{ route('seances.manage') }}" class="nav-link">
                                  @elseif (auth()->user()->hasRole('teacher'))
                                  <a href="{{ route('seances.index', ['teacherId' => auth()->user()]) }}"
                                      class="nav-link">
                                      @elseif (auth()->user()->hasRole('student'))
                                      <a href="{{ route('seances.index', ['classeId' => auth()->user()->latestClasse()]) }}"
                                          class="nav-link">
                                          @else
                                          <a href="{{ route('seances.index') }}" class="nav-link">
                                              @endif
                                              <i class="far fa-circle nav-icon"></i>
                                              <p>
                                                  {{__('vocab.seances')}}
                                              </p>
                                          </a>
                          </li>
                          @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                          <li class="nav-item">
                              <a href="{{route('absences.index')}}"
                                  class="nav-link">
                                  <i class="far fa-circle nav-icon"></i>
                                  <p> {{__('vocab.absences')}}</p>
                                  @if (($nbAbsences = auth()->user()->latestAbsencesCount() ) > 0)
                                  <span class="badge badge-warning right">
                                      {{ $nbAbsences }}
                                  </span>
                                  @endif
                              </a>
                          </li>
                          @endif
                          <li class="nav-item">
                              <a href="{{route('absences.ofUser',['user'=>auth()->user()])}}"
                                  class="nav-link">
                                  <i class="far fa-circle nav-icon"></i>
                                  <p> {{__('vocab.my_absences')}}</p>
                                  @if (($nbAbs = auth()->user()->myLatestAbsencesCount()) > 0)
                                  <span class="badge badge-warning right">
                                      {{ $nbAbs }}
                                  </span>
                                  @endif
                              </a>
                          </li>
                      </ul>
                  </li>

                  <li class="nav-item has-treeview">
                      <a href="#" class="nav-link">
                          {{-- Icon for Disciplinary/Rule Management --}}
                          <i class="nav-icon fas fa-gavel"></i>
                          <p>
                              {{__('vocab.misconduct_management')}}
                              <i class="right fas fa-angle-left"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview">
                          {{-- Sub-menu 1: Misconduct Acts (What the student did) --}}
                          @if (auth()->user()->hasRole('admin'))
                          <li class="nav-item">
                              <a href="{{ route('misconduct_acts.index') }}" class="nav-link">
                                  <i class="far fa-circle nav-icon"></i>
                                  <p>{{__('vocab.misconduct_acts')}}</p>
                              </a>
                          </li>
                          {{-- Sub-menu 2: Proposed Actions (What the teacher recommends) --}}

                           <li class="nav-item">
                              <a href="{{ route('proposed_actions.index') }}" class="nav-link">
                                  <i class="far fa-circle nav-icon"></i>
                                  <p>{{__('vocab.proposed_actions')}}</p>
                              </a>
                          </li>
                          @endif

                          @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager') 
                          || auth()->user()->hasRole('teacher') || auth()->user()->hasRole('student'))

                         
                          <li class="nav-item">
                              <a href="{{ route('misconduct_reports.index') }}" class="nav-link">
                                  <i class="far fa-circle nav-icon"></i>
                                  <p>{{__('vocab.misconduct_reports')}}</p>
                              </a>
                          </li>
                          @endif
                      </ul>
                  </li>
                  @if(auth()->user()->hasRole('admin'))
                  <li class="nav-item">
                      <a href="{{ route('settings.school.edit') }}" class="nav-link">
                          <i class="nav-icon far fa-cogs"></i>
                          <p>
                              {{__('vocab.school_configuration')}}
                              <i class="fas fa-angle-left right"></i>
                          </p>
                      </a>
                  </li>
                  @endif
                  <li class="nav-item">
                      <a href="{{ route('terminal.selectRoom') }}" class="nav-link">
                          <i class="nav-icon far fa-cam"></i>
                          <p>
                              {{__('vocab.terminal_mode')}}
                              <i class="fas fa-angle-left right"></i>
                          </p>
                      </a>
                  </li>
                  <li class="nav-item">
                      <form method="POST" action="{{ route('users.logout', ['user' => auth()->user()]) }}">
                          @csrf
                          <button type="submit" class="btn-outline-info btn-block btn-sm ">
                              <i class="fas fa-door-open nav-icon"></i>
                              {{__('vocab.logout')}}
                          </button>
                      </form>
                  </li>
                  @endauth

              </ul>
          </nav>
          <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
  </aside>