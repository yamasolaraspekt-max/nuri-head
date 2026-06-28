<?php

 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StampArticleListItem extends Model
{
    protected $fillable = [
        'stamp_article_list_id',
        'stamp_article_id',
        'employee_id',
        'note',
    ];

    public function list()
    {
        return $this->belongsTo(StampArticleList::class, 'stamp_article_list_id');
    }

    public function stampArticle()
    {
        return $this->belongsTo(StampArticle::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
