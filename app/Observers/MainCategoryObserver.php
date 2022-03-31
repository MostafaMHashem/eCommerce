<?php

namespace App\Observers;

use App\Models\Main_Category;

class MainCategoryObserver
{
    /**
     * Handle the Main_Category "created" event.
     *
     * @param  \App\Models\Main_Category  $main_Category
     * @return void
     */
    public function created(Main_Category $main_Category)
    {
        //
    }

    /**
     * Handle the Main_Category "updated" event.
     *
     * @param  \App\Models\Main_Category  $main_Category
     * @return void
     */
    public function updated(Main_Category $main_Category)
    {
        $main_Category -> vendors() -> update(['active' => $main_Category->active]);
    }

    /**
     * Handle the Main_Category "deleted" event.
     *
     * @param  \App\Models\Main_Category  $main_Category
     * @return void
     */
    public function deleted(Main_Category $main_Category)
    {
        $main_Category -> main_categories_rel() -> delete();
    }

    /**
     * Handle the Main_Category "restored" event.
     *
     * @param  \App\Models\Main_Category  $main_Category
     * @return void
     */
    public function restored(Main_Category $main_Category)
    {
        //
    }

    /**
     * Handle the Main_Category "force deleted" event.
     *
     * @param  \App\Models\Main_Category  $main_Category
     * @return void
     */
    public function forceDeleted(Main_Category $main_Category)
    {
        //
    }
}
