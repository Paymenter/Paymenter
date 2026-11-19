<?php

namespace App\Livewire\Domains;

use App\Livewire\Component;
use App\Models\Domain;
use App\Services\Domain\DomainRenewalService;
use App\Services\Domain\DomainService;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Throwable;

class Show extends Component
{
    public Domain $domain;

    public array $nameservers = [];

    #[Validate('required|string')]
    public string $password = '';

    public ?string $eppCode = null;

    public function mount(Domain $domain): void
    {
        $this->authorize('view', $domain);
        $this->domain = $domain->load('service');
        $existing = array_values($domain->nameservers ?? []);
        $this->nameservers = array_pad($existing, max(4, count($existing)), '');
    }

    public function toggleAutoRenew(): void
    {
        $this->domain->update(['auto_renew' => !$this->domain->auto_renew]);
    }

    public function renewNow()
    {
        $invoice = DomainRenewalService::createInvoice($this->domain);

        if (!$invoice) {
            return $this->notify('No renewal price is available for this domain.', 'error');
        }

        return redirect()->route('invoices.show', [$invoice, 'pay' => true]);
    }

    public function updateNameservers(): void
    {
        $registrar = $this->registrar();

        if (!$registrar?->supports('nameservers')) {
            $this->notify('This registrar does not support nameserver changes.', 'error');

            return;
        }

        $nameservers = array_values(array_filter(
            array_map(fn ($nameserver) => trim((string) $nameserver), $this->nameservers),
            fn ($nameserver) => $nameserver !== ''
        ));

        try {
            if (!$registrar->setNameservers($this->domain, $nameservers)) {
                $this->notify('The registrar did not accept the nameserver changes.', 'error');

                return;
            }

            $this->domain->update(['nameservers' => $nameservers]);
            $this->notify('Nameservers updated.', 'success');
        } catch (Throwable $e) {
            $this->notify($e->getMessage(), 'error');
        }
    }

    public function toggleLock(): void
    {
        $registrar = $this->registrar();

        if (!$registrar?->supports('lock')) {
            $this->notify('This registrar does not support domain locking.', 'error');

            return;
        }

        try {
            $locked = !$this->domain->locked;

            if (!$registrar->setLock($this->domain, $locked)) {
                $this->notify('The registrar did not accept the lock change.', 'error');

                return;
            }

            $this->domain->update(['locked' => $locked]);
            $this->notify('Domain lock updated.', 'success');
        } catch (Throwable $e) {
            $this->notify($e->getMessage(), 'error');
        }
    }

    public function togglePrivacy(): void
    {
        $registrar = $this->registrar();

        if (!$registrar?->supports('privacy')) {
            $this->notify('This registrar does not support privacy changes.', 'error');

            return;
        }

        try {
            $privacy = !$this->domain->privacy;

            if (!$registrar->setPrivacy($this->domain, $privacy)) {
                $this->notify('The registrar did not accept the privacy change.', 'error');

                return;
            }

            $this->domain->update(['privacy' => $privacy]);
            $this->notify('Privacy setting updated.', 'success');
        } catch (Throwable $e) {
            $this->notify($e->getMessage(), 'error');
        }
    }

    public function revealEpp(): void
    {
        $this->validate();

        if (!Hash::check($this->password, auth()->user()->password)) {
            $this->addError('password', 'The password is incorrect.');

            return;
        }

        $registrar = $this->registrar();

        if (!$registrar?->supports('epp')) {
            $this->notify('This registrar does not provide EPP codes.', 'error');

            return;
        }

        try {
            $this->eppCode = $registrar->getEppCode($this->domain);
        } catch (Throwable $e) {
            $this->notify($e->getMessage(), 'error');
        }
    }

    public function supports(string $capability): bool
    {
        return (bool) $this->registrar()?->supports($capability);
    }

    public function render()
    {
        return view('domains.show')->layoutData([
            'title' => __('navigation.domains'),
            'sidebar' => true,
        ]);
    }

    private function registrar()
    {
        return DomainService::registrarForDomain($this->domain);
    }
}
