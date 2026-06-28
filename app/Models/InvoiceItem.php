<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'article_product_id',
        'component_id',
        'distributor_id',
        'distributor_price_id',
        'distributor_article_no',
        'source_item_type',
        'source_item_id',
        'source_payload',
        'print_hidden',
        'group_title',
        'title',
        'description',
        'qty',
        'unit',
        'unit_price',
        'tax_rate',
        'line_total',
        'sort_order',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:3',
        'line_total' => 'decimal:2',
        'sort_order' => 'integer',
        'source_payload' => 'array',
        'print_hidden' => 'boolean',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    // Your existing invoice_items.product_id still points to article_groups.id
    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function productGroup()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function articleProduct()
    {
        return $this->belongsTo(Product::class, 'article_product_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }

    public function distributorPrice()
    {
        return $this->belongsTo(DistributorPrice::class, 'distributor_price_id');
    }
}
