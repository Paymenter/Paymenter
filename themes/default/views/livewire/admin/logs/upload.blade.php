<div>
    <button class="button button-secondary mb-4" wire:click="uploadLogs" data-modal-show="upload" data-modal-target="upload">
        {{ __('Upload logs to support') }}
    </button>

    @if($uploaded)
        <div class="alert alert-success">
            {{ __('Logs uploaded successfully!') }}
            <br />
            {{ __('Please send this link in the discord server:') }}
            <a href="{{ $link }}" target="_blank" class="underline">{{ $link }}</a>
        </div>
    @endif
</div>
