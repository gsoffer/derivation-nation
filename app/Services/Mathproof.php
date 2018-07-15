<?php namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class Mathproof extends Model {

	protected $fillable = array('slug_id', 'user_id', 'username', 'theorem_words', 'theorem_symbolic', 'branches', 'proof');

}
