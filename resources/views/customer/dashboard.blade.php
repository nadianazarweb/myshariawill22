@extends('customer.layouts.master')
@section('css')
@stop

@section('title')
<title>Dashboard</title>
@stop
@section('body')
<div class="content-wrapper container-xxl p-0">
    <div class="content-header row"></div>
    <div class="content-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        @if($report!=null)
                            <div class="media">
                                <div class="avatar bg-light-success mr-2">
                                    <div class="avatar-content">
                                        <i data-feather="check" class="avatar-icon"></i>
                                    </div>
                                </div>
                                <div class="media-body my-auto">
                                    <h4 class="font-weight-bolder mb-0">Your Report Is Approved</h4>
                                    <p class="card-text font-small-3 mb-0"><a href="{{ Storage::disk('reports')->url($report->folder.'/'.$report->file) }}" target="_blank">View Report</a></p>
                                </div>
                            </div>
                        @else
                            <div class="media">
                                <div class="avatar bg-light-danger mr-2">
                                    <div class="avatar-content">
                                        <i data-feather="x" class="avatar-icon"></i>
                                    </div>
                                </div>
                                <div class="media-body my-auto">
                                    <h4 class="font-weight-bolder mb-0">Your Report Is Pending</h4>
                                    <p class="card-text font-small-3 mb-0">In Process</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Dashboard Ecommerce Starts -->
        <section id="dashboard-ecommerce">
            <div class="row match-height">
                <div class="col-12">
                    <div class="card card-statistics">
                        <div class="card-header">
                            <h4 class="card-title">Statistics</h4>
                            <!-- <div class="d-flex align-items-center">
                                <p class="card-text font-small-2 mr-25 mb-0">Updated 1 month ago</p>
                            </div> -->
                        </div>
                        <div class="card-body statistics-body">
                            <div class="row">

                                <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-sm-0">
                                    <div class="media">
                                        <div class="avatar bg-light-success mr-2">
                                            <div class="avatar-content">
                                                <i data-feather="git-pull-request" class="avatar-icon"></i>
                                            </div>
                                        </div>
                                        <div class="media-body my-auto">
                                            <h4 class="font-weight-bolder mb-0">{{ $fulfilledRequestsCount }}</h4>
                                            <p class="card-text font-small-3 mb-0">FulFilled Requests</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-sm-0">
                                    <div class="media">
                                        <div class="avatar bg-light-danger mr-2">
                                            <div class="avatar-content">
                                                <i data-feather="git-pull-request" class="avatar-icon"></i>
                                            </div>
                                        </div>
                                        <div class="media-body my-auto">
                                            <h4 class="font-weight-bolder mb-0">{{ $pendingRequestsCount }}</h4>
                                            <p class="card-text font-small-3 mb-0">Pending Requests</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Dashboard Ecommerce ends -->

    </div>
</div>
@endsection

@section('javascript')
@stop