@extends('layouts.app')

@section('content')    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Update User Details</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('updateuser') }}">
                            @csrf 

                            @foreach ($errors->all() as $error)
                                <p class="text-danger">{{ $error }}</p>
                            @endforeach 

                            @if(session()->has('pass_success'))
                                <div class="alert alert-success">
                                    {{ session()->get('pass_success') }}
                                </div>
                            @endif
                            <input id="user" type="hidden" class="form-control" name="user" value="{{$user->id}}">
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Name</label>

                                <div class="col-md-6">
                                    <input id="first_name" type="text" class="form-control" name="first_name" value="{{$user->first_name}}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">E-mail</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{$user->email}}">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Update User
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