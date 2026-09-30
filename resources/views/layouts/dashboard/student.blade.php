<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-6">
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
            <div class="col-lg-3">
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
            <div class="col-lg-3">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ auth()->user()->nbMyAbsences() }}</h3>

                        <p>{{__('vocab.absences')}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ route('absences.ofUser',['user'=>auth()->user()]) }}" class="small-box-footer">{{__('vocab.more')}}<i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ count(auth()->user()->addresses) }}</h3>

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
        </div>
        <!-- /.row -->

        <!-- Main row -->
        <section class="col-lg-12">
            <div class=" mx-2 mb-4">
                <x-absence-filter /> 
            </div>
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card  mx-2">
                <div class="card-header gap-2">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        {{__('vocab.absence')}}
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="card-body col-lg-4">
                            <canvas id="trendChart" height="300"></canvas>
                        </div>
                        <div class="card-body col-lg-4">
                            <canvas id="classesAbsenceChart" height="300"></canvas>
                        </div>
                        <div class="card-body col-lg-4">
                            <canvas id="studentsAbsenceChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card  mx-2">

                <div class="card-header gap-2">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        {{__('vocab.misconduct_reports')}}
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="card-body col-lg-4">
                            <canvas id="reportTrendChart" height="300"></canvas>
                        </div>
                        <div class="card-body col-lg-4">
                            <canvas id="classesReportsChart" height="300"></canvas>
                        </div>
                        <div class="card-body col-lg-4">
                            <canvas id="studentsReportsChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div><!-- /.container-fluid -->
</section>