  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-primary elevation-4">
      <!-- Brand Logo -->
      <a href="{{ route('home.route') }}" class="brand-link">
          <img src="{{ URL::asset('assets/img/logo.png') }}" alt="Manech logo" class="brand-image img-circle elevation-2 "
              style="opacity: .8">
          <span class="brand-text font-weight-light">منصة الغياب</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
          @auth

              <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                  <div class="image">
                      <img src="{{ auth()->user()->userImage->uri ?? URL::asset('assets/img/logo.png') }}"
                          class="brand-image img-circle elevation-2" alt="User Image">
                  </div>
                  <div class="info">
                      <a href="{{ route('users.show', ['user' => auth()->user()]) }}" class="d-block">
                          {{ auth()->user()->firstName . ' ' . auth()->user()->lastName }}
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
                              الرئيسية
                              <i class="right fas fa-angle-left"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview">
                          @auth
                              <li class="nav-item">
                                  <a href="{{ route('dashboard.route', ['user' => auth()->user()]) }}" class="nav-link">
                                      <i class="far fa-circle nav-icon"></i>
                                      <p>لوحة التحكم</p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="#" class="nav-link">
                                      <i class="far fa-circle nav-icon"></i>
                                      <p>لائحة المستعملين</p>
                                      <span class="badge badge-info right">2</span>
                                  </a>
                              </li>
                          @else
                              <li class="nav-item">
                                  <a href="{{ route('users.login') }}" class="nav-link">
                                      <i class="fas fa-door-closed nav-icon"></i>
                                      <p>تسجيل الدخول</p>
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
                              الاقسام
                              <i class="fas fa-angle-left right"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview">
                          <li class="nav-item">
                              <a href="{{ route('classes.index') }}" class="nav-link">
                                  <i class="far fa-circle nav-icon"></i>
                                  <p>كل الاقسام</p>
                              </a>
                          </li>
                          @auth
                              @if (auth()->user()->hasRole('admin'))
                                  <li class="nav-item">
                                      <a href="{{ route('classes.manage') }}" class="nav-link">
                                          <i class="nav-icon far fa-circle"></i>
                                          <p>
                                              اقسامي
                                          </p>
                                      </a>
                                  </li>

                                  <li class="nav-item">
                                      <a href="{{ route('candidates.index') }}" class="nav-link">
                                          <i class="far fa-circle nav-icon"></i>
                                          <p>طلبات التسجيل</p>
                                          <span class="badge badge-info right">2</span>
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
                                  الغياب
                                  <i class="fas fa-angle-left right"></i>
                              </p>
                          </a>
                          <ul class="nav nav-treeview">
                              <li class="nav-item">
                                  <a href="#" class="nav-link">
                                      <i class="far fa-circle nav-icon"></i>
                                      <p>
                                          الحصص الزمنية
                                      </p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="#" class="nav-link">
                                      <i class="far fa-circle nav-icon"></i>
                                      <p>حالات الغياب</p>
                                      <span class="badge badge-info right">2</span>
                                  </a>
                              </li>
                          </ul>
                      </li>
                  @endauth

                  <li class="nav-item">
                      <a href="#" class="nav-link">
                          <i class="nav-icon fas fa-book"></i>
                          <p>
                              مستجدات
                              <span class="badge badge-info right">2</span>
                          </p>
                      </a>
                  </li>
                  @auth
                      <li class="nav-item">
                          <form method="POST" action="{{ route('users.logout', ['user' => auth()->user()]) }}">
                              @csrf
                              <button type="submit" class="btn-outline-info btn-block btn-sm ">
                                  <i class="fas fa-door-open nav-icon"></i>
                                  <p>تسجيل الخروج</p>
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
