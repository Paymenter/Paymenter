<section class="mx-auto w-full max-w-lg py-14">
    <div class="card">
        <div class="flex flex-col items-center gap-2 p-5 sm:p-8 md:p-12">
            <div class="flex items-center justify-center mb-4">
                <x-ri-lock-password-fill class="size-8" />
            </div>
            <h1 class="mb-2 text-2xl font-bold">{{ __('auth.verify_2fa') }}</h1>
            <p class="mb-6 text-base text-base/60">
                {{ __('account.input.two_factor_code') }}
            </p>
            <form
                x-on:submit.prevent="submit"
                x-data="{
                    isNumber(value) { return value.match(/^[0-9]$/g); },
                    getCode() {
                        return [
                            $refs.num1.value, $refs.num2.value, $refs.num3.value,
                            $refs.num4.value, $refs.num5.value, $refs.num6.value
                        ].join('');
                    },
                    submit() {
                        let code = this.getCode();
                        @this.set('code', code).then(() => {
                            $wire.verify();
                        });
                    },
                    fillInputs(val) {
                        $refs.num1.value = val[0] || '';
                        $refs.num2.value = val[1] || '';
                        $refs.num3.value = val[2] || '';
                        $refs.num4.value = val[3] || '';
                        $refs.num5.value = val[4] || '';
                        $refs.num6.value = val[5] || '';
                    },
                    handlePaste(e) {
                        let num = e.clipboardData.getData('text/plain').trim();
                        if (num.length === 6 && num.match(/^[0-9]+$/g)) {
                            e.preventDefault();
                            this.fillInputs(num);
                            this.submit();
                        }
                    },
                    handleAutofill(e) {
                        let val = e.target.value;
                        if (val.length === 6) {
                            this.fillInputs(val);
                            this.submit();
                        } else {
                            if (val.length > 1) e.target.value = val.charAt(0);
                            if (this.isNumber(e.target.value)) $refs.num2.focus();
                            else e.target.value = '';
                        }
                    }
                }"
                class="space-y-6"
            >
                <div class="inline-flex items-center gap-1.5" wire:ignore>
                    <input
                        x-ref="num1"
                        id="otp-input"
                        autocomplete="one-time-code"
                        x-on:input="handleAutofill($event)"
                        x-on:paste="handlePaste"
                        type="text"
                        autofocus
                        class="block w-9 rounded-lg border border-neutral px-2 py-1.5 text-center text-sm/6 placeholder-neutral/80 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-background"
                    />

                    <input x-ref="num2" x-on:input="isNumber($refs.num2.value) ? $refs.num3.focus() : $refs.num2.value = ''" x-on:keydown.backspace="$refs.num2.value === '' ? $refs.num1.focus() : null" type="text" maxlength="1" class="block w-9 rounded-lg border border-neutral px-2 py-1.5 text-center text-sm/6 placeholder-neutral/80 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-background" />
                    <input x-ref="num3" x-on:input="isNumber($refs.num3.value) ? $refs.num4.focus() : $refs.num3.value = ''" x-on:keydown.backspace="$refs.num3.value === '' ? $refs.num2.focus() : null" type="text" maxlength="1" class="block w-9 rounded-lg border border-neutral px-2 py-1.5 text-center text-sm/6 placeholder-neutral/80 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-background" />

                    <span class="text-sm text-base/60">-</span>

                    <input x-ref="num4" x-on:input="isNumber($refs.num4.value) ? $refs.num5.focus() : $refs.num4.value = ''" x-on:keydown.backspace="$refs.num4.value === '' ? $refs.num3.focus() : null" type="text" maxlength="1" class="block w-9 rounded-lg border border-neutral px-2 py-1.5 text-center text-sm/6 placeholder-neutral/80 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-background" />
                    <input x-ref="num5" x-on:input="isNumber($refs.num5.value) ? $refs.num6.focus() : $refs.num5.value = ''" x-on:keydown.backspace="$refs.num5.value === '' ? $refs.num4.focus() : null" type="text" maxlength="1" class="block w-9 rounded-lg border border-neutral px-2 py-1.5 text-center text-sm/6 placeholder-neutral/80 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-background" />
                    <input x-ref="num6" x-on:input="if(isNumber($refs.num6.value)) { if(getCode().length === 6) submit(); } else { $refs.num6.value = '' }" x-on:keydown.backspace="$refs.num6.value === '' ? $refs.num5.focus() : null" type="text" maxlength="1" class="block w-9 rounded-lg border border-neutral px-2 py-1.5 text-center text-sm/6 placeholder-neutral/80 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-background" />
                </div>
                @error('code')
                    <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                @enderror
                <div class="mt-2">
                    <x-button.primary type="submit" class="">
                        {{ __('auth.verify') }}
                    </x-button.primary>
                </div>
            </form>
        </div>
    </div>
</section>
