<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    /**
     * The name of the primary key on the table, if different than "id".
     *
     * @var string
     */
    public $primaryKey = 'task_type_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','description','example_request'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at','updated_at'];

    public function id()
    {
        return $this->getAttribute($this->primaryKey);
    }
}
