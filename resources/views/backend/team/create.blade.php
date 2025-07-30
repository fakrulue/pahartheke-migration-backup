@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="align-items-center">
		<h1 class="h3">{{ translate('Team') }}</h1>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="card">
		    <div class="card-header row gutters-5">
				<div class="col text-center text-md-left">
					<h5 class="mb-md-0 h6">{{ isset($team) ? translate('Edit Member') : translate('Add Member') }}</h5>
				</div>
		    </div>
		    
		    <div class="card-body">
		        <form id="teamForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $team->id ?? '' }}">

                    <div class="form-group">
                        <label for="name">{{ translate('Name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="position">{{ translate('position') }}</label>
                        <input type="text" name="position" class="form-control" required>
                    </div>

                    

                    <div class="form-group">
                        <label for="twitter_url">{{ translate('twitter_url ') }}</label>
                        <input type="url" name="twitter_url" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="linkedin_url">{{ translate('linkedin_url Link') }}</label>
                        <input type="url" name="linkedin_url" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="instagram_url">{{ translate('instagram_url Link') }}</label>
                        <input type="url" name="instagram_url" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="original_image">{{ translate('Original image') }}</label>
                        <input type="file" name="original_image" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="hover_image">{{ translate('Hover image') }}</label>
                        <input type="file" name="hover_image" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-success">
                        {{ translate('Create') }}
                    </button>
                </form>

                <div id="response-message" class="mt-3"></div>

		    </div>
		</div>
	</div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
@section('script')
<script>
    $(document).ready(function () {
        $('#teamForm').on('submit', function (e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: '{{ route("b.team.store") }}', // for create
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#response-message').html(
                        '<div class="alert alert-success">Team member created successfully!</div>'
                    );
                    $('#teamForm')[0].reset(); // reset form
                },
                error: function (xhr) {
                    let errors = xhr.responseJSON?.errors;
                    let message = '<div class="alert alert-danger"><ul>';
                    if (errors) {
                        $.each(errors, function (key, val) {
                            message += '<li>' + val[0] + '</li>';
                        });
                    } else {
                        message += '<li>Something went wrong.</li>';
                    }
                    message += '</ul></div>';
                    $('#response-message').html(message);
                }
            });
        });
    });
</script>
@endsection

