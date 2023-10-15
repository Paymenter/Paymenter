<form action="{{ route('extensions.proxmox.configure', $orderProduct->id) }}" method="POST" class="flex flex-col">
    @csrf
    <x-input type="text" name="hostname" :value="$config->hostname ?? null" label="Hostname" required />
    <button class="button button-primary self-end relative mt-2" type="submit">Save</button>
</form>
