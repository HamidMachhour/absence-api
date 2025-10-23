<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-6">
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

            <div class="col-lg-3 col-6">
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
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ auth()->user()->myLatestAbsencesCount() }}</h3>

                        <p>{{__('vocab.absence_in_week')}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ route('absences.index') }}" class="small-box-footer">{{__('vocab.more')}}<i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ count(auth()->user()->addresses) }}</h3>

                        <p>{{__('vocab.my_addresses')}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-email"></i>
                    </div>
                    <a href="{{ route('addresses.index') }}" class="small-box-footer">{{__('vocab.more')}}<i
                            class="fas fa-arrow-circle-right"></i></a>
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
