<div class="hidden mt-3" id="tab-mail">
    <form method="POST" enctype="multipart/form-data" class="mb-3" action="{{ route('admin.settings.email') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2">
            <div class="relative m-4 group">
                <input type="checkbox" class="w-fit form-input peer @error('mail_encryption') is-invalid @enderror"
                    placeholder=" " name="mail_disabled" value="1"
                    {{ config('settings::mail_disabled') == 1 ? 'checked' : '' }} />
                <label class="form-label" style="position: unset">{{ __('Disable all mails') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('mail_username') is-invalid @enderror"
                    placeholder=" " name="mail_username" value="{{ config('mail.username') }}" />
                <label class="form-label">{{ __('Mail Username') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="password" class="form-input peer @error('mail_password') is-invalid @enderror"
                    placeholder=" " name="mail_password" value="{{ config('mail.password') }}" />
                <label class="form-label">{{ __('Mail Password') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('mail_host') is-invalid @enderror" placeholder=" "
                    name="mail_host" required value="{{ config('mail.host') }}" />
                <label class="form-label">{{ __('Mail Host') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('mail_port') is-invalid @enderror" placeholder=" "
                    name="mail_port" required value="{{ config('mail.port') }}" />
                <label class="form-label">{{ __('Mail Port') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('mail_from_address') is-invalid @enderror"
                    placeholder=" " name="mail_from_address" required value="{{ config('mail.from.address') }}" />
                <label class="form-label">{{ __('Mail From Address') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('mail_from_name') is-invalid @enderror"
                    placeholder=" " name="mail_from_name" required value="{{ config('mail.from.name') }}" />
                <label class="form-label">{{ __('Mail From Name') }}</label>
            </div>
            <div class="relative m-4 group">
                <select class="form-input peer @error('mail_encryption') is-invalid @enderror" name="mail_encryption">
                    <option value="tls" {{ config('mail.encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ config('mail.encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="none" {{ config('mail.encryption') == '' ? 'selected' : '' }}>None</option>
                </select>
                <label class="form-label">{{ __('Mail Encryption') }}</label>
            </div>
            <div class="relative m-4 group" data-popover-target="bcc">
                <input type="text" class="form-input peer @error('bcc') is-invalid @enderror" placeholder=" "
                    name="bcc" value="{{ config('settings::bcc') }}" />
                <label class="form-label">{{ __('BCC Messages') }}</label>
            </div>
            <div id="bcc" role="tooltip" data-popover
                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                {{ __('Email addresses to BCC all outgoing emails to. Separate multiple email addresses using commas.') }}
                <div data-popper-arrow></div>
            </div>
            <div class="relative m-4 group">
                <input type="checkbox" class="w-fit form-input peer @error('must_verify_email') is-invalid @enderror"
                    placeholder=" " name="must_verify_email" value="1"
                    {{ config('settings::must_verify_email') == 1 ? 'checked' : '' }} />
                <label class="form-label" style="position: unset">{{ __('Must Verify Email') }}</label>
            </div>
        </div>
        <div class="float-right">
            <button class="form-submit">{{ __('Submit') }}</button>
            <button type="button" class="ml-2 bg-green-500 form-submit" id="test">{{ __('Test') }}</button>
        </div>
        <script>
            $('#test').click(function() {
                $('#test').attr('disabled', true)
                $('#test').html('Sending...')
                $.ajax({
                    url: "{{ route('admin.settings.email.test') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        mail_username: $('input[name="mail_username"]').val(),
                        mail_password: $('input[name="mail_password"]').val(),
                        mail_host: $('input[name="mail_host"]').val(),
                        mail_port: $('input[name="mail_port"]').val(),
                        mail_from_address: $('input[name="mail_from_address"]').val(),
                        mail_from_name: $('input[name="mail_from_name"]').val(),
                        mail_encryption: $('input[name="mail_encryption"]').val(),
                    },
                    success: function(data) {
                        if (data.success) {
                            Swal.fire('Email send succesfully!')
                        } else {
                            Swal.fire('Something went wrong!\n' + data.error);
                        }
                        $('#test').attr('disabled', false)
                        $('#test').html('Test')
                    }
                }).catch(function(error) {
                    console.log(error)
                    Swal.fire('Something went wrong!\n\n' + error.responseJSON.error);
                    $('#test').attr('disabled', false)
                    $('#test').html('Test')
                });
            });
        </script>

    </form>
</div>
