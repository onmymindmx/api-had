<?php namespace App;

use Hash;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;
	protected $appends = array('countPlaces', 'places');

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['email', 'password', 'username', 'first_name', 'last_name'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token', 'reset_token', 'reset_code'];

	/**
	 * Passwords must always be hashed
	 *
	 * @param $password
	 */
	public function setPasswordAttribute($password)
	{
		$this->attributes['password'] = Hash::make($password);
	}

	/**
	 * Retornamos la cantidad de lugares que tiene el usuario
	 * @return int $count
	 */
	public function getCountPlacesAttribute()
	{
		$count = Lugar::where('user', $this->attributes['id'])->count();
		return $count;
	}

	/**
	 * Retornamos los id de los lugares que tiene el usuario
	 * @return array $placesA
	 */
	public function getPlacesAttribute()
	{
		$places = Lugar::where('user', $this->attributes['id'])->select('id')->get();

		$placesA = array();

		foreach ($places as $place) {
			$placesA[] = $place['id'];
		}

		return $placesA;
	}

}
