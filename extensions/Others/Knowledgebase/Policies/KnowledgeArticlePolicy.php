<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Policies;

use App\Models\User;
use App\Policies\BasePolicy;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeArticle;

class KnowledgeArticlePolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->adminPermission($user, 'admin.knowledgebase.articles.view');
    }

    public function view(User $user, KnowledgeArticle $article): bool
    {
        return $this->adminPermission($user, 'admin.knowledgebase.articles.view');
    }

    public function create(User $user): bool
    {
        return $this->adminPermission($user, 'admin.knowledgebase.articles.create');
    }

    public function update(User $user, KnowledgeArticle $article): bool
    {
        return $this->adminPermission($user, 'admin.knowledgebase.articles.update');
    }

    public function delete(User $user, KnowledgeArticle $article): bool
    {
        return $this->adminPermission($user, 'admin.knowledgebase.articles.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $this->adminPermission($user, 'admin.knowledgebase.articles.delete');
    }
}
