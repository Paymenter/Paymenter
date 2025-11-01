<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Policies;

use App\Models\User;
use App\Policies\BasePolicy;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeCategory;

class KnowledgeCategoryPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->adminPermission($user, 'admin.knowledgebase.categories.view');
    }

    public function view(User $user, KnowledgeCategory $category): bool
    {
        return $this->adminPermission($user, 'admin.knowledgebase.categories.view');
    }

    public function create(User $user): bool
    {
        return $this->adminPermission($user, 'admin.knowledgebase.categories.create');
    }

    public function update(User $user, KnowledgeCategory $category): bool
    {
        return $this->adminPermission($user, 'admin.knowledgebase.categories.update');
    }

    public function delete(User $user, KnowledgeCategory $category): bool
    {
        return $this->adminPermission($user, 'admin.knowledgebase.categories.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $this->adminPermission($user, 'admin.knowledgebase.categories.delete');
    }
}
