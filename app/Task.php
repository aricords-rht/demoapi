<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    /**
     * The name of the primary key on the table, if different than "id".
     *
     * @var string
     */
    public $primaryKey = 'task_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','task_type_id','status','request_details','response_details'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at','user_id','task_type_id'];

    public function id()
    {
        return $this->getAttribute($this->primaryKey);
    }

    public function taskType()
    {
        return $this->belongsTo(TaskType::class, 'task_type_id')->select(['task_type_id','name']);
    }

    // Always force returning only tasks which belong to the current user.
    // Also return the name of the task type.
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('ownedByUser', function ($query) {
            $query->where('tasks.user_id', '=', request()->user()->id)->with(['taskType']);
        });
    }

    // Return most recent tasks first.
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
