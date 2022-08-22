<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SentNotification
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SentNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SentNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SentNotification query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $type
 * @property string $created_at
 * @property string $locale
 * @property string $channel
 * @property string $route
 * @property mixed $variables
 * @method static \Illuminate\Database\Eloquent\Builder|SentNotification whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentNotification whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentNotification whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentNotification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentNotification whereVariables($value)
 * @property string $lang
 * @method static \Illuminate\Database\Eloquent\Builder|SentNotification whereLang($value)
 */
class SentNotification extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'variables' => 'array'
    ];
}
