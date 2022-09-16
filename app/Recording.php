<?php

namespace App;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Recording extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agent_account',
        'extension',
        'dni',
        'queue',
        'id_interaction',
        'id',
        'call_type',
        'ani',
        'dnis',
        'duration',
        'audio_file',
        'created_at',
    ];

    protected $hidden = array('updated_at');

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y');
    }
}
