@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-sm-6 mx-auto">
        <div class="card mt-5 py-5 rounded shadow-sm mx-auto" style="max-width: 700px">
            <div class="card-body px-5">
                <h3 class="pb-4 text-center">Create License</h3>

                <div id="user_detail" style="display: none" class="row mb-5">
                    <div class="col-sm-10 mx-auto">
                        <table class="table table-bordered">
                            <tbody id="tbl_user_detail">
                                {{--  --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group row py-2">
                    <label for="user_id" class="col-md-4 col-form-label text-md-right">{{ __('Client ID') }}</label>

                    <div class="col-md-6">
                        <input id="user_id" type="text" class="form-control" name="user_id" value="{{ old('user_id') }}" required autofocus>
                    </div>
                </div>
                <div class="form-group row py-2">
                    <label for="license_key" class="col-md-4 col-form-label text-md-right">{{ __('License Key') }}</label>

                    <div class="col-md-6">
                        <input id="license_key" type="text" class="form-control" name="license_key" value="{{ old('license_key') }}" required>
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn btn-success px-5 mt-3" id="save_license_key">Save</button>
                </div>
                <hr>
                <div class="row pt-4">
                    <label for="licensed_for" class="col-md-4 col-form-label text-md-right">{{ __('License Key') }}</label>

                    <div class="form-group">
                        <select class="form-control" name="licensed_for" id="licensed_for" required>
                            <option value="">Select option</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->type }}">{{ $type->type }}</option>
                            @endforeach
                        </select>
                    </div> &nbsp; months
                    <div class="form-group text-right ml-3 my-1">
                        <button class="btn btn-primary btn-sm px-3" id="create_license">Create Key</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#create_license').on('click', function () {
        createLicense();
    });

    $('#save_license_key').on('click', function () {
        saveLicense();
    });

    $('#user_id').on('keyup', function () {
        $.get('{{ URL("getUserDetails") }}', {user_id: $('#user_id').val()}).then(data => {
            if (typeof(data) == 'object') {
                let res = '';
                $('#tbl_user_detail').html();
                Object.keys(data).map(e => {
                    res += `
                        <tr>
                            <td>${e}</td>
                            <td>${data[e]}</td>
                        </tr>`
                    ;
                });

                $('#tbl_user_detail').html(res);
                $('#user_detail').css('display', 'block')
            }
        }).catch(err => console.log(''));
    });
});

function createLicense() {
    if ($('#user_id').val().length === 0 || $('#licensed_for').val().length === 0) {
        return alertify.warning('<span">Provide User ID and License month!<span>');
    }

    $.ajax({
        url: '{{ URL("licenses/createKey") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            user_id: $('#user_id').val(),
            months: $('#licensed_for').val()
        },
        success: data => {
            if (data['status'] == 'error') {
                alertify.error('<span class="text-white">Error! Please check input!<span>')
            } else {
                $('#license_key').val(data);
                alertify.success('<span class="text-white">License key created!<span>')
            }
        },
        error: err => {
            alertify.error('An error occurred!');
            console.error(err);
        }
    });
}

function saveLicense() {
    let field_empty = $('#user_id').val().length === 0 
                        || $('#licensed_for').val().length === 0
                        || $('#license_key').val().length === 0;

    if (field_empty) {
        return alertify.warning('<span">Provide User ID and License month!<span>');
    }

    $.ajax({
        url: '{{ URL("licenses") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            user_id: $('#user_id').val(),
            license_key: $('#license_key').val(),
            expire_date: $('#licensed_for').val()
        },
        success: data => {
            if (data['status'] == 'error' || data['status'] == 'validation_failed') {
                Object.keys(data['message']).map(err => {
                    alertify.error(`<span class="text-white">${data['message'][err]}<span>`);
                });
            } else if (data['status'] == 'success') {
                alertify.success(`<span class="text-white">${data['message']}<span>`);
                setTimeout(() => location.reload(), 1000);
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
