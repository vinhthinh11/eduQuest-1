<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;

class admin extends  Model
{
    protected $table = 'admins';
    protected $fillable = [
        'admin_id',
        'username',
        'email',
        'password',
        'name',
        'permission',
        'last_login',
        'gender_id',
        'avatar',
        'birthday'
    ];
    public $timestamps = false;
    protected $primaryKey = 'admin_id';

    //
    function getAdmin()
    {
        $getAllAdmin = DB::select('select * from admins');
        return $getAllAdmin;
    }

}
