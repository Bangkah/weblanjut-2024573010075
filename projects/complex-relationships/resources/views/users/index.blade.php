@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <h2>Users List</h2>
    <ul class="list-group">
        @foreach($users as $user)
            <li class="list-group-item">
                <a href="{{ route('users.show', $user->id) }}">{{ $user->name }}</a> ({{ $user->email }})
            </li>
        @endforeach
    </ul>
@endsection
