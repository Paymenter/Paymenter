<div class="container mt-14">
    <x-navigation.breadcrumb />
    <div class="px-2 flex flex-col gap-4">

        <div class="bg-background-secondary rounded-lg p-4">
            <h5 class="text-lg font-bold pb-3">{{ __('account.general') }}</h5>
            <div class="grid md:grid-cols-2 gap-3">

                <x-form.input name="first_name" type="text" :label="__('general.input.first_name')"
                    :placeholder="__('general.input.first_name_placeholder')" wire:model="first_name" required dirty />
                <x-form.input name="last_name" type="text" :label="__('general.input.last_name')"
                    :placeholder="__('general.input.last_name_placeholder')" wire:model="last_name" required dirty />

                <x-form.input name="email" type="email" :label="__('general.input.email')"
                    :placeholder="__('general.input.email_placeholder')" required wire:model="email" dirty />

                <x-form.properties :custom_properties="$custom_properties" :properties="$properties" dirty />
            </div>

            <x-button.primary wire:click="submit" class="w-full mt-4">
                {{ __('general.update') }}
            </x-button.primary>
        </div>

        
        @if (config('settings.avatar_source') === 'custom')
            
            {{-- Dieser x-data Block enthält jetzt wieder 'currentAvatarUrl' --}}
            <div class="bg-background-secondary rounded-lg p-4"
                 x-data="{ 
                     isUploading: false, 
                     progress: 0, 
                     newAvatar: null, 
                     fileName: '',
                     currentAvatarUrl: '{{ Auth::user()->avatar }}'
                 }"
                 x-on:livewire-upload-start="isUploading = true"
                 x-on:livewire-upload-finish="isUploading = false" 
                 x-on:livewire-upload-error="isUploading = false"
                 x-on:livewire-upload-progress="progress = $event.detail.progress"
                 
                 {{-- Dieser Event-Listener aktualisiert 'currentAvatarUrl' nach dem Speichern --}}
                 x-on:avatar-upload-updated.window="
                    newAvatar = null; 
                    fileName = ''; 
                    $refs.avatarInput.value = '';
                    if ($event.detail.newAvatarUrl) {
                        currentAvatarUrl = $event.detail.newAvatarUrl;
                    }
                 ">
                        
                <h5 class="text-lg font-bold pb-3">{{ __('account.avatar') }}</h5>
                
                <div class="flex items-center space-x-4">
                    
                    <img x-show="!newAvatar" :src="currentAvatarUrl" alt="Current Avatar" class="h-16 w-16 rounded-full object-cover flex-shrink-0">
                    <template x-if="newAvatar">
                        <img :src="newAvatar" alt="Avatar Preview" class="h-16 w-16 rounded-full object-cover flex-shrink-0">
                    </template>

                    <div class="w-full">
                        {{-- Das versteckte Input-Feld (korrekt) --}}
                        <input type="file" 
                               id="avatar_upload" 
                               wire:model="avatar_upload" 
                               x-ref="avatarInput"
                               @change="
                                   if ($refs.avatarInput.files.length) {
                                       fileName = $refs.avatarInput.files[0].name;
                                       const reader = new FileReader();
                                       reader.onload = (e) => { newAvatar = e.target.result; };
                                       reader.readAsDataURL($refs.avatarInput.files[0]);
                                   } else {
                                       fileName = '';
                                       newAvatar = null;
                                   }
                               "
                               class="hidden"
                               accept="image/*">

                        <label for="avatar_upload"
                               class="relative flex items-center w-full text-base bg-background-secondary border border-neutral rounded-md shadow-sm transition-all duration-300 ease-in-out cursor-pointer overflow-hidden"
                               wire:dirty.class="!border-yellow-600">
                            
                            <span class="inline-flex items-center px-3 py-2.5 text-sm text-gray-500 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 border-r border-neutral whitespace-nowrap">
                                {{ __('Datei auswählen') }}
                            </span>
                            
                            <span class="py-2.5 px-2.5 text-sm text-base/70 truncate" x-show="!fileName">
                                {{ __('Keine Datei ausgewählt') }}
                            </span>
                            <span class="py-2.5 px-2.5 text-sm text-base truncate" x-show="fileName" x-text="fileName">
                            </span>
                        </label>

                        <div x-show="isUploading" class="mt-2 w-full bg-gray-200 rounded-full dark:bg-gray-700">
                            <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" :style="`width: ${progress}%`" x-text="`${progress}%`"></div>
                        </div>

                        @error('avatar_upload')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <x-button.primary wire:click="submitAvatar" class="w-full mt-4">
                    {{ __('general.update') }}
                </x-button.primary>
            </div>
        @endif
    
    </div>
</div>
