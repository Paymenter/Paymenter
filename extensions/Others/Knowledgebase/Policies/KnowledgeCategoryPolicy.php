<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Policies;

use App\Models\User;
use App\Policies\BasePolicy;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeCategory;

class KnowledgeCategoryPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->checkAdminAccess($user, 'admin.knowledgebase.categories.view');
    }

    public function view(User $user, KnowledgeCategory $category): bool
    {
        return $this->checkAdminAccess($user, 'admin.knowledgebase.categories.view');
    }

    public function create(User $user): bool
    {
        return $this->checkAdminAccess($user, 'admin.knowledgebase.categories.create');
    }

    public function update(User $user, KnowledgeCategory $category): bool
    {
        return $this->checkAdminAccess($user, 'admin.knowledgebase.categories.update');
    }

    public function delete(User $user, KnowledgeCategory $category): bool
    {
        return $this->checkAdminAccess($user, 'admin.knowledgebase.categories.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $this->checkAdminAccess($user, 'admin.knowledgebase.categories.delete');
    }

    protected function checkAdminAccess(User $user, string $permission): bool
    {
        $request = request();

        if ($request && ($request->is('admin') || $request->is('admin/*') || $request->routeIs('filament.admin.*'))) {
            return $user->hasPermission($permission);
        }

        return $this->adminPermission($user, $permission);
    }
}
