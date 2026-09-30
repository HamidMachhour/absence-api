  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light no-print">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
          <li class="nav-item">
              <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
          </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
          <!-- Navbar Search -->
          <li class="nav-item">
              <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                  <i class="fas fa-search"></i>
              </a>
              <div class="navbar-search-block">
                  <form action="{{ url()->current() }}" class="form-inline">
                      <div class="input-group input-group-sm">
                          <input class="form-control form-control-navbar" name='search' type="search"
                              placeholder="Search" aria-label="Search">
                          <div class="input-group-append">
                              <button class="btn btn-navbar" type="submit">
                                  <i class="fas fa-search"></i>
                              </button>
                              <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                  <i class="fas fa-times"></i>
                              </button>
                          </div>
                      </div>
                  </form>
              </div>
          </li>

          <!-- Notifications Dropdown Menu -->
          @auth
          @if (auth()->user()->hasRole('admin'))
          <x-admin-dropdown-menu />
          @elseif (auth()->user()->hasRole('manager'))
          <x-admin-dropdown-menu />
          @elseif (auth()->user()->isTeacher())
          <x-teacher-dropdown-menu />
          @elseif(auth()->user()->isStudent())
          <x-student-dropdown-menu />
          @endif
          @endauth

          <li class="nav-item">
              <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                  <i class="fas fa-expand-arrows-alt"></i>
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="{{ route('language.switch', ['lang' => App::getLocale() == 'en' ? 'ar' : (App::getLocale() == 'ar' ? 'fr' : (App::getLocale() == 'fr' ? 'z' : 'en'))]) }}" role="button">
                  <i class="fas fa-language"></i>
                  @if (App::getLocale() == 'en')
                  AR
                  @elseif (App::getLocale() == 'ar')
                  FR
                  @elseif (App::getLocale() == 'fr')
                  ⵜⴰⵎⴰⵣⵉⵖⵜ
                  @else
                  EN
                  @endif
              </a>
          </li>
      </ul>
  </nav>
  <!-- /.navbar -->