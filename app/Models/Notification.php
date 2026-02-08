<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    // table name
    protected $table = 'notifications';

    protected $fillable = ["title", "body", "type", "send_to", "sender_id", "receiver_id", "class_id"];

    public $timestamps = true;
}
