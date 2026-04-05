<div class="space-y-4 animate-in fade-in slide-in-from-bottom-2 duration-500">
    @foreach ($services as $service)
    <a href="{{ route('services.show', $service) }}" wire:navigate class="group block">
        <div class="relative overflow-hidden bg-white/5 backdrop-blur-md border border-neutral/20 p-5 rounded-2xl transition-all duration-300 group-hover:bg-white/[0.08] group-hover:border-primary/30 group-hover:-translate-y-0.5 shadow-sm">
            
            <div class="absolute left-0 top-0 h-full w-1 
                @if ($service->status == 'active') bg-success/50 shadow-[0_0_10px_rgba(34,197,94,0.4)] 
                @elseif($service->status == 'suspended') bg-error/50 shadow-[0_0_10px_rgba(239,68,68,0.4)]
                @else bg-warning/50 shadow-[0_0_10px_rgba(245,158,11,0.4)] 
                @endif">
            </div>

            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-4">
                    <div class="bg-primary/10 p-2.5 rounded-xl border border-primary/20 text-primary group-hover:scale-110 transition-transform">
                        <x-ri-instance-line class="size-5" />
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-base group-hover:text-primary transition-colors">
                            {{ $service->product->name }}
                        </h3>
                        <p class="text-[10px] font-bold text-base/30 uppercase tracking-[0.2em] mt-0.5">
                            ID-REF: {{ substr(md5($service->id), 0, 8) }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 px-3 py-1 rounded-full border 
                    @if ($service->status == 'active') border-success/20 bg-success/5 text-success
                    @elseif($service->status == 'suspended') border-error/20 bg-error/5 text-error
                    @else border-warning/20 bg-warning/5 text-warning @endif">
                    
                    <span class="relative flex size-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-current"></span>
                        <span class="relative inline-flex rounded-full size-1.5 bg-current"></span>
                    </span>
                    
                    <span class="text-[9px] font-black uppercase tracking-tighter">
                        {{ $service->status }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-3 border-t border-white/5">
                <div class="flex items-center gap-1.5">
                    <span class="text-[10px] font-bold text-base/20 uppercase tracking-widest">{{ __('services.product') }}:</span>
                    <span class="text-[10px] font-bold text-base/60 uppercase">{{ $service->product->category->name }}</span>
                </div>

                @if(in_array($service->plan->type, ['recurring']))
                <div class="flex items-center gap-1.5">
                    <span class="text-[10px] font-bold text-base/20 uppercase tracking-widest">Cycle:</span>
                    <span class="text-[10px] font-bold text-primary/60 uppercase">
                        {{ __('services.every_period', [
                            'period' => $service->plan->billing_period > 1 ? $service->plan->billing_period : '',
                            'unit' => trans_choice(__('services.billing_cycles.' . $service->plan->billing_unit), $service->plan->billing_period)
                        ]) }}
                    </span>
                </div>
                @endif

                @if($service->expires_at)
                <div class="flex items-center gap-1.5 ml-auto">
                    <span class="text-[10px] font-bold text-base/20 uppercase tracking-widest">{{ __('services.expires_at') }}:</span>
                    <span class="text-[10px] font-black text-base/70 italic uppercase tracking-tighter">{{ $service->expires_at->format('M d, Y') }}</span>
                </div>
                @endif
            </div>
        </div>
    </a>
    @endforeach
</div>