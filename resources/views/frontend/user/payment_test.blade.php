@extends('frontend.layouts.master')
@section('title')
Payment Test
@endsection

@section('MainSection')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="py-5">
                <h1 class="mb-4">Payment Route Test</h1>
                <h4 class="mb-4">This is a test to verify the payment route is working.</h4>
                <p class="mb-4">User ID: {{ $user_id }}</p>
                <p class="mb-4">If you can see this page, the route is working correctly!</p>
                <div class="alert alert-success">
                    <strong>Success!</strong> The payment route is working. The redirect loop issue is not in this route.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
