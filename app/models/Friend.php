<?php

class Friend extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

    // Set table
    protected $table = 'friends';

    protected $updated_at = null;

    protected $created_at = null;


	// Don't forget to fill this array
	protected $fillable = ['friend_id', 'user_id'];


    function users() {
        return $this->belongsTo('User');
    }

}