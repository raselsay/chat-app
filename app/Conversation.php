<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'user_one',
        'user_two',
        'status',
    ];

    public function messages()
    {
    	return $this->hasMany(Messages::class,'conversation_id','id');
    }

        /*
     * make a relation between first user from conversation
     *
     * return collection
     * */
    public function userone()
    {
        return $this->belongsTo('App\User','user_one','id');
    }

    /*
   * make a relation between second user from conversation
   *
   * return collection
   * */
    public function usertwo()
    {
        return $this->belongsTo('App\User',  'user_two','id');
    }
}
