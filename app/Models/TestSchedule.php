<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSchedule extends Model
{
    use HasFactory;

    protected $table='test_schedules';
    protected $primaryKey='id';

    protected $fillable = [
        'test_name',  // Kolom yang diizinkan untuk mass assignment
        'start_time', // Tambahkan kolom start_time
        'end_time',   // Tambahkan kolom end_time
        'image_path',
        'status'
    ];
    
    public function items(){
        return $this->hasMany(Item::class, 'test_schedule_id', 'id');
    }
}
