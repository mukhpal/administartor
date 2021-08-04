@extends('layouts.app')

@section('content')    
    <div class="container">
        <h1>Users List</h1>
 
        <form class="form-inline" method="GET">
            <div class="form-group mb-2">
                <input type="text" class="form-control" id="filter" name="filter" placeholder="User name..." value="{{$filter}}">
            </div>
            <button type="submit" class="btn btn-default mb-2">Search</button>
        </form>
        @if(session()->has('edit_user_error'))
            <p class="text-danger">
                {{ session()->get('edit_user_error') }}
            </p>
        @endif
        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>@sortablelink('first_name', 'Name')</th>
                    <th>@sortablelink('email', 'Email')</th>
                    <th>@sortablelink('email_verified', 'Verified')</th>
                    <th width="300px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($data) && $data->count())
                    @foreach($data as $key => $value)
                        <tr>
                            <td>{{ $value->first_name }}</td>
                            <td>{{ $value->email }}</td>
                            <td>{{ $value->email_verified ? 'Verified' : 'Not Verified' }}</td>
                            <td>
                                <a href="{{ url('/user/' . $value->id . '/edit') }}" class="btn btn-xs btn-info pull-right">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="10">There are no data.</td>
                    </tr>
                @endif
            </tbody>
        </table>
            
        {!! $data->links() !!}
    </div>

    <p>
        Displaying {{$data->count()}} of {{ $data->total() }} User(s).
    </p>
@endsection