<div class="flex flex-col gap-8 animate-in fade-in slide-in-from-bottom-2 duration-500">
    <div class="border-l-2 border-error/50 pl-4 py-1">
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-error/60 mb-1">
            System Decommissioning
        </p>
        <p class="text-xs font-bold text-base/80 leading-relaxed italic">
            {{ __('services.cancel_are_you_sure') }}
        </p>
    </div>

    <div class="grid gap-6 p-1">
        <x-form.select 
            name="type" 
            label="{{ __('services.cancel_type') }}" 
            required 
            wire:model.live="type"
            class="!bg-white/5 !backdrop-blur-md !border-neutral/20 !rounded-xl"
        >
            <option value="end_of_period">{{ __('services.cancel_end_of_period') }}</option>
            <option value="immediate">{{ __('services.cancel_immediate') }}</option>
        </x-form.select>

        <x-form.textarea 
            name="reason" 
            label="{{ __('services.cancel_reason') }}" 
            required 
            wire:model="reason" 
            placeholder="Please specify the reason for termination..."
            class="!bg-white/5 !backdrop-blur-md !border-neutral/20 !rounded-xl min-h-[100px]"
        />
    </div>

    <div x-show="$wire.type === 'immediate'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="relative overflow-hidden bg-amber-500/10 border border-amber-500/30 p-5 rounded-2xl shadow-[0_0_20px_rgba(245,158,11,0.05)]">
        
        <div class="flex items-start gap-4">
            <div class="flex items-center justify-center size-8 rounded-lg bg-amber-500/20 text-amber-500 animate-pulse">
                <x-ri-error-warning-fill class="size-5" />
            </div>
            <div>
                <h4 class="text-[10px] font-black uppercase tracking-widest text-amber-500 mb-1">Critical Data Warning</h4>
                <p class="text-[11px] font-bold text-amber-200/70 leading-normal">
                    {{ __('services.cancel_immediate_warning') }}
                </p>
            </div>
        </div>
        
        <div class="absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-amber-500/40 to-transparent"></div>
    </div>

    <div class="pt-2">
        <x-button.danger 
            wire:confirm="Confirm irreversible termination of this module?" 
            wire:click="cancelService"
            class="w-full !py-4 !rounded-xl !text-xs !font-black !uppercase !tracking-[0.2em] shadow-[0_10px_30px_rgba(239,68,68,0.15)] group"
        >
            <div class="flex items-center justify-center gap-2">
                <x-ri-delete-bin-7-line class="size-4 group-hover:rotate-12 transition-transform" />
                <span>{{ __('services.cancel') }}</span>
            </div>
        </x-button.danger>
    </div>
</div>