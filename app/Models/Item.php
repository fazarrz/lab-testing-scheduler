<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    
    protected $table='test_schedules_detail';
    protected $primaryKey='id';

    protected $casts = [
        'image_details' => 'array',
    ];

    protected $fillable = [
        'test_schedule_id',
        'user_id',
        'nama_subitem',
        'start_time',
        'end_time',
        'description',
        'image_detail',
        'status',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function testSchedule(){
        return $this->belongsTo(TestSchedule::class, 'test_schedule_id', 'id');
    }
}
