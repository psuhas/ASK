@extends('app')

@section('content')
<div class="container">
	<div class="row">
		@foreach ($data as $d)
		<div class="col-xs-6 col-lg-4">
			<div class="panel panel-default">
				<div class="panel-heading">{{ $d['Title'] }}</div>
				<div class="panel-body">
					<img src="{{ $d['LargeImage'] }}" />
				</div>
			</div>
      	</div>
      	@endforeach
	</div>
</div>
@endsection
