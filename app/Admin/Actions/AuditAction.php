<?php

namespace App\Admin\Actions;

use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class AuditAction extends Action
{
    public $auditChildren = [];

    public function auditChildren($children): self
    {
        $this->auditChildren = $children;

        // Array of children of the model to also show audits for
        return $this;
    }

    public static function getDefaultName(): ?string
    {
        return 'audits';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Audits')
            ->color('gray')
            ->slideOver()
            ->modalContent(fn (Model $record): View => view(
                'admin.actions.audits',
                ['record' => $record, 'children' => $this->auditChildren]
            ))
            ->modalHeading('test')
            ->modalWidth(MaxWidth::Large)
            ->modalSubmitAction(false)
            ->modalCancelAction(false);
    }
}
