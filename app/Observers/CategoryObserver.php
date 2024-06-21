<?php

namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{
    /**
     * Handle the Category "creating" event.
     *
     * @return void
     */
    public function creating(Category $category)
    {
        $parent = $category->parent;
        $full_slug = $category->slug;
        // Set full_slug
        while ($parent) {
            $fullSlug = $parent->slug;
            $full_slug = $fullSlug . '/' . $full_slug;
            $parent = $parent->parent;
        }

        $category->full_slug = $full_slug;
    }

    /**
     * Handle the Category "updating" event.
     *
     * @return void
     */
    public function updating(Category $category)
    {
        // Did parent or slug change?
        if ($category->isDirty('parent_id') || $category->isDirty('slug')) {
            $parent = $category->parent;
            $full_slug = $category->slug;
            // Set full_slug
            while ($parent) {
                $fullSlug = $parent->slug;
                $full_slug = $fullSlug . '/' . $full_slug;
                $parent = $parent->parent;
            }

            $category->full_slug = $full_slug;

            // Update children
            $category->children->each(function ($child) use ($category) {
                $child->update([
                    'full_slug' => $category->full_slug . '/' . $child->slug,
                ]);
            });
        }
    }
}
