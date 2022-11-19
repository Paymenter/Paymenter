<div class="hidden mt-3" id="tab-mail">
    <form method="POST" enctype="multipart/form-data" class="mb-3" action="">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-1 md:grid-cols-2">
            <div class="relative m-4 group">
                <input type="text" class="form-input peer" placeholder=" " name="username" />
                <label class="form-label">Mail Username</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer" placeholder=" " name="password" />
                <label class="form-label">Mail Password</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer" placeholder=" " name="host" />
                <label class="form-label">Mail Host</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer" placeholder=" " name="port" />
                <label class="form-label">Mail Port</label>
            </div>
        </div>
        <div class="float-right">
            <button class="form-submit">{{ __('Submit') }}</button>
            <button type="button" class="ml-2 bg-green-500 form-submit">{{ __('Test') }}</button>
        </div>
    </form>
</div>
