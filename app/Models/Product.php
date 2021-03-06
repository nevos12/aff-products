<?php

namespace App\Models;

use App\Models\Vendor;
use App\Models\Category;
use App\Filters\ProductFilters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\VendorsIntegration\Responses\ItemResponses\ItemResponseAbstract;

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
    protected $with = ['categories', 'vendor'];

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
     * get only the ids of the categories
     *
     * @return Collection
     */
    public function getCategoriesIdsAttribute()
    {
        return $this->categories->pluck('id');
    }

    /**
    * get image attribute
    *
    * @param  string $value
    * @return string
    */
    public function getStorageImageAttribute()
    {
        if ($this->attributes['image']) {
            return Storage::url($this->attributes['image']);
        }

        return Storage::url('products/default.png');
    }

    /**
     * Overwrite price attribute
     *
     * @param string $value
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = number_format($value, 2, '.', '');
    }

    /**
     * Reletion between Vendor
     *
     * @return Vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
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
