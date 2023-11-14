<?php

namespace App\Livewire\Admin\Announcements;

use App\Models\Announcement;
use App\Traits\Tables\DesignTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

class Index extends DataTableComponent
{
    use DesignTrait;

    public array $bulkActions = [
        'deleteAnnouncements' => 'Delete Announcements',
        'unpublishAnnouncements' => 'Unpublish Announcements',
        'publishAnnouncements' => 'Publish Announcements',
    ];

    public function deleteAnnouncements(): void
    {
        $announcements = Announcement::query()
            ->whereIn('id', $this->getSelected())
            ->get();

        foreach ($announcements as $announcement) {
            $announcement->delete();
        }

        $this->clearSelected();
    }

    public function unpublishAnnouncements(): void
    {
        $announcements = Announcement::query()
            ->whereIn('id', $this->getSelected())
            ->get();

        foreach ($announcements as $announcement) {
            $announcement->published = false;
            $announcement->save();
        }

        $this->clearSelected();
    }

    public function publishAnnouncements(): void
    {
        $announcements = Announcement::query()
            ->whereIn('id', $this->getSelected())
            ->get();

        foreach ($announcements as $announcement) {
            $announcement->published = true;
            $announcement->save();
        }

        $this->clearSelected();
    }

    public function builder(): Builder
    {
        return Announcement::query();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')->setTableRowUrl(fn ($row) => route('admin.announcements.edit', $row));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()->searchable(),
            Column::make('Title', 'title')
                ->sortable()->searchable(),
            BooleanColumn::make('Published', 'published')
                ->sortable()->searchable(),
            Column::make('Created At', 'created_at')
                ->sortable()->searchable(),
        ];
    }
}
