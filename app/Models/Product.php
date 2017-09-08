<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Vendor;
use App\Models\Category;
use App\Filters\ProductFilters;

class Product extends Model
{
    /**
     * guarded columns
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * default reletion come with product
     *
     * @var array
     */
    protected $with = ['categories'];

    /**
     * hide props in searlize to json
     *
     * @var array
     */
    protected $hidden = ['pivot'];

    /**
     * Reletion with Category Models
     *
     * @return Collection
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Reletion between Vendor
     *
     * @return Vendor
     */
    public function vendor()
    {
        $this->belongsTo(Vendor::class);
    }

    /**
     * activate Filters class
     * @param  Builder        $builder
     * @param  ProductFilters $filters
     * @return Builder
     */
    public function scopeFilter(Builder $builder, ProductFilters $filters)
    {
        return $filters->apply($builder);
    }
}