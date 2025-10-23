<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-2 col-6">
                <!-- small box -->
                <div class="small-box">
                    <div class="inner">
                        <h3>{{ auth()->user()->nbUsers() }}</h3>
                        <p>{{ __('vocab.users') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person"></i>
                    </div>
                    <a href="{{ route('users.index') }}" class="small-box-footer">{{__('vocab.more')}}<i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-2 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ auth()->user()->nbEnrollments() }}</h3>
                        <p>{{__('vocab.enrollments')}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="{{ route('enrollments.index') }}" class="small-box-footer">{{__('vocab.more')}}<i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-2 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ auth()->user()->nbClasses() }}</h3>
                        <p>{{__('vocab.classes')}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-home"></i>
                    </div>
                    <a href="{{ route('classes.index') }}" class="small-box-footer">{{__('vocab.more')}}<i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->

            <div class="col-lg-2 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ auth()->user()->nbUnreadMessages() }}</h3>

                        <p>{{__('vocab.inbox')}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-email"></i>
                    </div>
                    <a href="{{ route('messages.inbox') }}" class="small-box-footer">{{__('vocab.more')}}<i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- ./col -->
            <div class="col-lg-2 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ auth()->user()->nbMyAdresses() }}</h3>

                        <p>{{__('vocab.my_addresses')}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-location"></i>
                    </div>
                    <a href="{{ route('addresses.index') }}" class="small-box-footer">{{__('vocab.more')}}<i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-2 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ auth()->user()->nbSubjects() }}</h3>

                        <p>{{__('vocab.subjects')}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-ios-book"></i>
                    </div>
                    <a href="{{ route('subjects.index') }}" class="small-box-footer">{{__('vocab.more')}}<i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-2 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ auth()->user()->nbRoles() }}</h3>

                        <p>{{__('vocab.roles')}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-key"></i>
                    </div>
                    <a href="{{ route('roles.index') }}" class="small-box-footer">{{__('vocab.more')}}<i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-2 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <form method="GET" class="btn-group  flex justify-center">
                        <select name="days" class="form-control" onchange="this.form.submit()">
                            <option value="7"> {{__('vocab.absence_in_week')}}
                            </option>
                            <option value="30" {{ request()->get('days') == 30 ? 'selected' : '' }}>{{__('vocab.absence_in_month')}}</option>
                            <option value="365" {{ request()->get('days') == 365 ? 'selected' : '' }}>{{__('vocab.absence_in_year')}}</option>
                        </select>
                    </form>
                    <div class="inner d-flex gap-2">
                        <div class="inner">
                            <h3>{{ $latestAbsences }} </h3>
                            <p> {{__('vocab.absences')}}</p>
                        </div>
                        <div class="inner">
                            <h3>{{ $myLatestAbsences }} </h3>
                            <p>{{__('vocab.my_absences')}}</p>
                        </div>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a>
                        <a href="{{ route('absences.index') }}" class="small-box-footer">{{__('vocab.absences')}}<i
                                class="fas fa-arrow-circle-right"></i>
                        </a>
                        <a href="{{ route('absences.ofUser',['user'=>auth()->user()]) }}" class="small-box-footer">{{__('vocab.my_absences')}}<i
                                class="fas fa-arrow-circle-right"></i>
                        </a>
                    </a>
                </div>
            </div>
            <!-- ./col -->
        </div>
        <!-- /.row -->

        <!-- Main row -->
        <section class="col-lg-12">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card mx-2">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        {{__('vocab.absence')}}
                    </h3>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <x-absence-filter />
                </div>
                <div class="row">
                    <div class="card-body col-lg-6">
                        <canvas id="trendChart" height="300"></canvas>
                    </div>
                    <div class="card-body col-lg-6">
                        <canvas id="classesAbsenceChart" height="300"></canvas>
                    </div>
                </div>
                <div class="row">
                    <div class="card-body col-lg-12">
                        <canvas id="studentsAbsenceChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </div><!-- /.container-fluid -->
</section>