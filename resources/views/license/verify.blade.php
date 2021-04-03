@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-sm-6 mx-auto">
        <div class="card mt-5 py-5 rounded shadow-sm mx-auto" style="max-width: 700px">
            <div class="card-body px-5">
                <h3 class="pb-4 text-center">Verify License</h3>
                <div class="form-group row py-2">
                    <label for="license_key" class="col-md-4 col-form-label text-md-right">{{ __('License key') }}</label>

                    <div class="col-md-6">
                        <input id="license_key" type="text" class="form-control" name="license_key" required autofocus>
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn btn-success px-5 mt-3" id="verify_license">Verify</button>
                </div>
                <div class="text-right">
                    Return to <a href="{{ URL('login') }}">Login</a> page
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#verify_license').on('click', function () {
        verifyLicense();
    });
});

function verifyLicense() {
    if ($('#license_key').val().length === 0) {
        return alertify.warning('<span>Please fill fill input!<span>');
    }

    $.ajax({
        url: '{{ URL("licenses/verify") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            license_key: $('#license_key').val()
        },
        success: data => {
            if (data['status'] == 'error' || data['status'] == 'validation_failed') {
                Object.keys(data['message']).map(err => {
                    alertify.error(`<span class="text-white">${data['message'][err]}<span>`);
                });
            } else if (data['status'] == 'success') {
                let expire_date = new Date(data['message']);
                let expire_day = String(expire_date.getDate()).padStart(2, '0');
                let expire_month = String(expire_date.getMonth() + 1).padStart(2, '0');
                let expire_year = String(expire_date.getFullYear()).padStart(4, '0');
                let expire_formatted_date = `${expire_day}/${expire_month}/${expire_year}`;

                alertify.alert(
                    "Verified!", 
                    `Congratulations!! Your License Has Been Activated. It will work till ${expire_formatted_date}`, 
                    function() {
                        alertify.message(`Congratulations!! Your License Has Been Activated. It will work till ${expire_formatted_date}`);
                        setTimeout(() => location.reload(), 1000);
                    }
                );
            }
        },
        error: err => {
            alertify.error('An error occurred!');
            console.error(err);
        }
    });
}
</script>
@endsection