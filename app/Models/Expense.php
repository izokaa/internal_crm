<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\ExpenseStatus;  
use App\Models\ExpenseCategory;


class Expense extends Model
{
    protected $fillable = [
        'montant_ht',
        'montant_ttc',
        'tva',
        'contact_id',
        'opportunity_id',
        'devise',
        'date_expense',
        'description',
        'category_id',
        'status'
    ];


    protected $casts = [
        'date_expense' => 'date',
        'status' => ExpenseStatus::class,
    ];


    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class, 'opportunity_id');
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

}
