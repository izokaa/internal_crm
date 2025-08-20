<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Expense;


class ExpenseCategory extends Model
{

    // specify the table
    protected $table = 'expense_categories';
    
    protected $fillable = [
        'nom'
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id');
    }
}
