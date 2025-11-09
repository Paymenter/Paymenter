<?php

namespace App\Livewire\Client;

use App\Livewire\ComponentWithProperties;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class Account extends ComponentWithProperties
{
    use WithFileUploads;

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public $avatar_upload;

    public function mount()
    {
        $user = Auth::user();

        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;

        $this->initializeProperties($user, $user::class);
    }

    public function rules()
    {
        return array_merge([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'avatar_upload' => 'nullable|image|max:1024',
        ], $this->getRulesForProperties());
    }

    public function validationAttributes()
    {
        return $this->getAttributesForProperties();
    }

    public function submit()
    {
        $validatedData = $this->validate();

        /** @var User $user */
        $user = Auth::user();

        if (config('settings.avatar_source') === 'custom' && $this->avatar_upload) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            
            $validatedData['avatar_path'] = $this->avatar_upload->store('avatars', 'public');
        }

        $user->update($validatedData);

        if (array_key_exists('properties', $validatedData)) {
            $this->updateProperties($user, $validatedData['properties']);
        }

        $this->avatar_upload = null;
        $this->dispatch('avatar-updated');

        $this->notify(__('Account updated successfully.'));
    }

    public function submitAvatar()
    {
        $validatedData = $this->validate([
            'avatar_upload' => 'nullable|image|max:1024',
        ]);

        $user = Auth::user();

        if (config('settings.avatar_source') === 'custom' && $this->avatar_upload) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            
            $validatedData['avatar_path'] = $this->avatar_upload->store('avatars', 'public');
            
            $user->update(['avatar_path' => $validatedData['avatar_path']]);

            $user->refresh();
        }

        $this->avatar_upload = null;
        
        $this->dispatch('avatar-upload-updated', newAvatarUrl: $user->avatar);

        $this->notify(__('Avatar updated successfully.'));
    }

    public function render()
    {
        return view('client.account.index')->layoutData([
            'sidebar' => true,
            'title' => 'Account',
        ]);
    }
}
