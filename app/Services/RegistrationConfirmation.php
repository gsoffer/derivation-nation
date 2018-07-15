<?php namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class RegistrationConfirmation extends Model {

	protected $fillable = array('email', 'confirmation_code');

}
