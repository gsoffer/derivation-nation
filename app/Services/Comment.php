<?php namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {

	protected $fillable = array('mathproof_id', 'user_id', 'username', 'comment');

}
