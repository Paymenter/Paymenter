<x-admin-layout>
    <x-slot name="title">
        {{ __('Migrate') }}
    </x-slot>
	<style>
		select, input, option {
			color: black;
		}
	</style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20">
                    <div class="mt-8 text-2xl text-white" style="padding-bottom: 10px">
						{{ __('Migrate from WHMCS') }}
                    </div>
					<div class="mb-4 text-gray-500 dark:text-darkmodetext text-sm">
                            {{ __('- To get the API credentials, go to System Settings > API Credentials in WHMCS.') }}
							<br />
							{{ __('- You will need to Whitelist your Server IP Address in System Settings > General Settings > Security > API IP Access Restriction, for the migration to work.') }}
                        </div>
					<div id="outer-div">
						<x-auth-validation-errors class="mb-4" :errors="$errors" />

						<form action="{{ route('admin.migrate.whmcs.import')}}" method="POST">
							@csrf
							<div class="grid">
								<div class="relative m-4 group">
            					    <input type="text" class="form-input peer @error('host') border-red-500 @enderror" placeholder=" "
										name="host" value="" required>
            					    <label class="form-label">WHMCS URL</label>
            					</div>
								<div class="relative m-4 group">
								    <input type="password" class="form-input peer @error('dbUsername') border-red-500 @enderror" placeholder=" "
										name="dbUsername" value="" required/>
									<label class="form-label">WHMCS API Identifier</label>
								</div>
								<div class="relative m-4 group">
									<input type="password" class="form-input peer @error('dbPassword') border-red-500 @enderror" placeholder=" "
										name="dbPassword" value="" required/>
									<label class="form-label">WHMCS API Secret</label>
								</div>
								<div class="relative m-4 group">
									<select class="text-white w-full @error('chosenOption') border-red-500 @enderror" name="chosenOption"
										style="background-color: transparent;" required>
										<option value="">--Select an option--</option>
										<option value="all">All</option>
										<option value="products">Products</option>
										<option value="orders">Orders</option>
										<option value="clients">Clients</option>
										<option value="invoices">Invoices</option>
									</select>
									<label class="form-label">Data To Import</label>
								</div>
								<div class="relative m-4 group">
									<select class="text-white w-full @error('replace') border-red-500 @enderror" name="replace"
										style="background-color: transparent;" required>
										<option value="">--Select an option--</option>
										<option value="yes">Yes</option>
										<option value="no">No</option>
									</select>
									<label class="form-label">Replace existing data?</label>
								</div>
								<div class="relative m-4 group toBeHidden">
									<select class="text-white w-full @error('currency') border-red-500 @enderror" name="currency"
										style="background-color: transparent;" required>
										<option value="">--Select an option--</option>
										<option value="gbp">GBP</option>
										<option value="usd">USD</option>
										<option value="eur">EUR</option>
									</select>
									<label class="form-label">Currency</label>
								</div>
							</div>
							<button id="submitMigrate" type="submit" class="float-right form-submit">{{ __('Submit') }}</button>
						</form>
					</div>
                </div>
            </div>
        </div>
    </div>

<script>

$(document).ready(function() {
	$('select[name="replace"]').change(function() {
		if ($(this).val() == 'yes') {
			if (confirm("Are you sure you want to replace all existing data?")) {
				// Do nothing
			} else {
				$(this).val('');
			}
		}
	});
});

document.getElementById("submitMigrate").addEventListener(
	"click",
	function() {
		document.getElementById("outer-div").style.display = "none";
		document.getElementById("loading-div").style.display = "block";
	},
	false
);

$('select[name="chosenOption"]').change(function() {
	if ($(this).val() == 'products' || $(this).val() == 'all' || $(this).val() == 'invoices') {
		if ($('.toBeHidden').is(':visible')) {
			// Do nothing
		} else {
			$('.grid').append('<div class="relative m-4 group toBeHidden"><select class="text-white w-full @error(`currency`) border-red-500 @enderror" name="currency" style="background-color: transparent;" required><option value="">--Select an option--</option><option value="gbp">GBP</option><option value="usd">USD</option><option value="eur">EUR</option></select><label class="form-label">Currency</label></div>');
		}
	} else {
		$('.toBeHidden').remove();
	}
});


</script>

<?php
    // validate the GET variables
    if (isset($_GET) && !empty($_GET)) {
        if (!filter_var($_GET['host'], FILTER_VALIDATE_IP)) {
            return false;
        }
        if (!filter_var($_GET['dbUsername'])) {
            return false;
        }
        if (!filter_var($_GET['dbPassword'])) {
            return false;
        }
        if (!filter_var($_GET['chosenOption'])) {
            return false;
        }
        if (!filter_var($_GET['currency'])) {
            return false;
        }
        if (!filter_var($_GET['replace'])) {
            return false;
        }
    }
?>

</x-admin-layout>