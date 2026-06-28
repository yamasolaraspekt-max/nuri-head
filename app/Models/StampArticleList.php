<?php

 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StampArticleList extends Model
{
    protected $fillable = [
        'employee_id',
        'name',
        'slug',
        'color',
        'icon',
        'is_shared',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->slug) {
                $model->slug = Str::slug($model->name) . '-' . Str::random(6);
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function items()
    {
        return $this->hasMany(StampArticleListItem::class, 'stamp_article_list_id');
    }

    public function stampArticles()
    {
        return $this->belongsToMany(StampArticle::class, 'stamp_article_list_items', 'stamp_article_list_id', 'stamp_article_id')
            ->withPivot(['employee_id', 'note'])
            ->withTimestamps();
    }
}
