@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ Auth::user()->first_name }} Profile</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <img style="float: right;" src="{{ $profile_pic }}" alt="Girl in a jacket" width="70" height="70">

                    Name : {{  $first_name }}
                    <br/>
                    Email : {{  $email }}
                </div>
            </div>
            
            <br/><br/>
            <div class="card">
                <div class="card-header">Change Password</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('change-password') }}">
                        @csrf 

                        @foreach ($errors->all() as $error)
                            <p class="text-danger">{{ $error }}</p>
                        @endforeach 

                        @if(session()->has('pass_success'))
                            <div class="alert alert-success">
                                {{ session()->get('pass_success') }}
                            </div>
                        @endif

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Current Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="current_password" autocomplete="current-password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">New Password</label>

                            <div class="col-md-6">
                                <input id="new_password" type="password" class="form-control" name="new_password" autocomplete="current-password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">New Confirm Password</label>
    
                            <div class="col-md-6">
                                <input id="new_confirm_password" type="password" class="form-control" name="new_confirm_password" autocomplete="current-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <br/><br/>
            <div class="card">
                <div class="card-header">Change Profile Image</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('change-profile-img') }}" enctype="multipart/form-data" >
                        @csrf 

                        @if(session()->has('pp_success'))
                            <div class="alert alert-success">
                                {{ session()->get('pp_success') }}
                            </div>
                        @endif

                        <div class="form-group row">
                            <label for="profile_pic" class="col-md-4 col-form-label text-md-right">Change Image</label>

                            <div class="col-md-6">
                                <input id="profile_pic" type="file" class="form-control" name="profile_pic" autocomplete="current-password">
                                @if(session()->has('error'))
                                    <p class="text-danger">
                                        {{ session()->get('error') }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Upload
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
