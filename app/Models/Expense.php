<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\ExpenseStatus;
use App\Models\ExpenseCategory;
use App\Enums\ModePayment;

class Expense extends Model
{
    protected $fillable = [
        'montant_ht',
        'montant_ttc',
        'tva',
        'client_id',
        'supplier_id',
        'devise',
        'date_expense',
        'description',
        'category_id',
        'status',
        'mode_payment'
    ];


    protected $casts = [
        'date_expense' => 'date',
        'status' => ExpenseStatus::class,
        'mode_payment' => ModePayment::class
    ];


    public function supplier()
    {
        return $this->belongsTo(Contact::class, 'supplier_id');
    }


    public function client()
    {
        return $this->belongsTo(Contact::class, 'client_id');
    }


    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class, 'opportunity_id');
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function piecesJointes()
    {
        return $this->hasMany(PieceJointe::class, 'expense_id');
    }
}
