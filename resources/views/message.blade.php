@extends('layouts.app')

@section('content')
<div class="container">	
<div class="row">
	<div class="col-3">
		    <ul>
			    @foreach( $users as $user )
			    	<li  ><a href="{{ route('t',['user'=>$user->id]) }}">{{ $user->name }}</a></li>
			    @endforeach
			</ul>
	</div>
	<div class="col-9 border">
		<ul> 
		@foreach($messages as $ms)

		<li class="mes {{ auth()->user()->id === $ms->user_id ? 'sender':' ' }}"> <span> {{ $ms->message }} </span></li>
		@endforeach
	</ul>
	<!-- {{ $receiverid->id }} -->
	<form action="{{ route('sender',['conversation'=>$conv]) }}" method="POST" class="p-20">
		@csrf
		<div class="px-sm-4  py-sm-4">
			
		<input type="text" name="message" class="form-control">
		<button type="submit" class="btn btn-primary mt-2">Send</button>
		</div>
	</form>	
	</div>
</div>
</div>
@endsection