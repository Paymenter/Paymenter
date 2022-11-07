<style>
<?php include '../themes/default/css/home.css'; ?>	
</style>

<x-app-layout>
	<x-slot name="title">
		{{ __('Home') }}
	</x-slot>
	<div class="py-12">
		<x-success class="mt-4" />
		<div class="mx-auto max-w-7xl sm:px-6 lg:px-8" style="padding-bottom: 20px;">
			<div class="bg-white dark:bg-darkmode2 overflow-hidden shadow-xl sm:rounded-lg">
				<div class="p-6 dark:bg-darkmode2 border-gray-200">
					<div class="flex items-center">
						<div class="flex-shrink-0 h-12 w-12" style="display: flex;">
							<img class="h-8 w-8 rounded-md" style="align-self: center; width: 3rem; height: 3rem;" src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp" />
						</div>
						<div class="ml-4 text-lg leading-7 font-semibold">
							{{ __('Welcome back') }}, {{ Auth::user()->name }}
						</div>
					</div>
				</div>
			</div>
		</div>
		<div>
			<div class="mx-auto max-w-7xl sm:px-6 lg:px-8" style="width: 53.5%; margin-right: 16.6%; float: right; padding-bottom: 1rem;">
				<div class="bg-white dark:text-white dark:bg-darkmode2 overflow-hidden shadow-xl sm:rounded-lg" style="height: 5%;">
					<div class="p-6 dark:text-white dark:bg-darkmode2 border-b border-gray-200" style="padding: 1%;--tw-border-opacity: 0;">
						<div class="flex items-center">
							<div class="ml-4 text-lg leading-7 font-semibold">
								<a style="font-size: 0.8em;">Showing 1 to 2 of 2 entries</a>
							</div>
						</div>
					</div>
				</div>
			</div>
	    	<table id="tableServicesList" class="table table-list" style="margin-right: 18.3% !important;">
	    	    <thead>
	    	        <tr>
	    	            <th class="dark:text-white sorting_asc dark:bg-darkmode2">Product/Service</th>
	    	            <th class="dark:text-white sorting_asc dark:bg-darkmode2">Pricing</th>
	    	            <th class="dark:text-white sorting_asc dark:bg-darkmode2">Next Due Date</th>
	    	            <th class="dark:text-white sorting_asc dark:bg-darkmode2">Status</th>
	    	        </tr>
	    	    </thead>
	    	    <tbody>
					@if(Auth::user()->is_admin == '1') <!-- If the user is an admin, show temporary data -->
						<tr>
							<td colspan="4" class="dark:text-white dark:bg-darkmode2" style="text-align: center;">No services found.</td>
						</tr>
					@elseif(Auth::user()->is_admin == '0') <!-- If the user is not an admin, show their services -->
						@if (count($services) > 0) <!-- If the array is empty, then we don't want to show the table -->
							@foreach($services as $service)
								@foreach($service->products as $product)
									@php $product = App\Models\Products::where("id", $product["id"])->get()->first() @endphp
									@if(substr_count($product->price, ".") == 1) <!-- If the price has a decimal point and only has one decimal point -->
										@php $product->price = $product->price . "0" @endphp <!-- Add a zero to the end of the price -->
										@php $service->expiry_date = date("l jS F Y", strtotime($service->expiry_date)) @endphp <!-- Format the expiry date to be more readable -->
									@elseif(substr_count($product->price, ".") == 0) <!-- If the price has no decimal point -->
										@php $product->price = $product->price . ".00" @endphp <!-- Add a decimal point and two zeros -->
									@endif 
	    	    					<tr onclick="window.location.href = '/products';">
	    	        				    <td class="dark:text-white dark:bg-darkmode2"><strong>{{ ucfirst($product->name) }}</strong></td>
	    	        				    <td class="text-center dark:text-white dark:bg-darkmode2" data-order="0.00">£{{ $product->price }} GBP<br />Free Account</td>
	    	        				    <td class="text-center dark:text-white dark:bg-darkmode2">{{ $service->expiry_date }}</td>
	    	        				    <!-- <td class="text-center dark:text-white dark:bg-darkmode2"><span class="label status status-active dark:bg-darkmode2">{{ ucfirst($service->status) }}</span></td> -->
										<td class="text-center dark:text-white dark:bg-darkmode2">
											@if($service->status === 'paid')
												<span class="label status status-active dark:bg-darkmode2">Active</span>
											@elseif($service->status === 'pending')
												<span class="label status status-active dark:bg-darkmode2">Pending</span>
											@elseif($service->status === 'cancelled')
												<span class="label status status-active dark:bg-darkmode2">Expired</span>
											@endif
										</td>
	    	        				</tr>
	    	        			@endforeach
							@endforeach
						@elseif (count($services) < 0) <!-- If the array is empty, then don't show any data -->
							<tr>
								<td colspan="4" class="dark:text-white dark:bg-darkmode2" style="text-align: center;">No services found.</td>
							</tr>
						@endif
					@endif
	    	    </tbody>
	    	</table>
		</div>
	</div>
</div>

</x-app-layout>
