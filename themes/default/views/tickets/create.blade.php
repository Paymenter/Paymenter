<div class="bg-primary-800 p-6 rounded-lg mt-2">
    <h1 class="text-2xl font-semibold text-white mb-2">{{ __('ticket.create_ticket') }}</h1>
    <div class="grid grid-cols-2 gap-4">
        <x-form.input wire:model="subject" label="{{ __('ticket.subject') }}" name="subject" required />
        @if (count($departments) > 0)
            <x-form.select wire:model="department" label="{{ __('ticket.department') }}" name="department" required>
                <option value="">{{ __('ticket.select_department') }}</option>
                @foreach ($departments as $department)
                    <option value="{{ $department }}">{{ $department }}</option>
                @endforeach
            </x-form.select>
        @endif
        <x-form.select wire:model="priority" label="{{ __('ticket.priority') }}" name="priority" required>
            <option value="">{{ __('ticket.select_priority') }}</option>
            <option value="low" selected>{{ __('ticket.low') }}</option>
            <option value="medium">{{ __('ticket.medium') }}</option>
            <option value="high">{{ __('ticket.high') }}</option>
        </x-form.select>
        <!-- Select the product -->
        <x-form.select wire:model="service" label="{{ __('ticket.service') }}" name="service">
            <option value="">{{ __('ticket.select_service') }}</option>
            @foreach ($services as $product)
                <option value="{{ $product->id }}">{{ $product->product->name }} ({{ ucfirst($product->status) }})
                    @if ($product->expires_at)
                        - {{ $product->expires_at->format('Y-m-d') }}
                    @endif
                </option>
            @endforeach
        </x-form.select>
        <div class="col-span-2">

            <div class="mt-4">
                <form wire:submit.prevent="create" wire:ignore>
                    <textarea id="editor" placeholder="Initial message"></textarea>
                    <x-button.primary class="mt-2 !w-fit float-right">
                        {{ __('ticket.create') }}
                    </x-button.primary>
                </form>
                <x-easymde-editor />
            </div>
        </div>
    </div>
</div>
