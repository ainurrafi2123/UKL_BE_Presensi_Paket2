<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $table = 'attendance';
    protected $fillable = ['user_id', 'date', 'time', 'status'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $date = ['date'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Relasi ke model Pengguna
    }

}

