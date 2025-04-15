<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'employee_id',
		 'street_line','city','country','coutry_devision_code','quickbook_id','quickbook_res',
        'register_email_sent',
        'register_sms_sent',
		'current_amount',
		'total_income',
        'first_name',
        'last_name',
        'hourly_rate',
        'load_per',
        'name',
        'type',
        'email',
        'wsib_quarterly',
        'incorporation_name',
		'license_number',
        'company_corporation_name',
		'country_code',
        'phone',
        'password',
        'address',
        'zip_code',
        'vehicle_id',
        'hst',
        'status',
		'password_string',
		'expo_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	public function getVehicle(){
        return $this->belongsTo(Vehicle::class,'vehicle_id');
    }
	public function getDispatches(){
        return $this->hasMany(AssignDispatch::class,'user_id');
    }
	public function getNotification(){
        return $this->hasMany(Notification::class,'user_id');
    }
}
