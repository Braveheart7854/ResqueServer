<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Model\Medal
 *
 * @property int $id
 * @property string $account
 * @property string $nick
 * @property int $area
 * @property int $points
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Medal whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Medal whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Medal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Medal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Medal whereNick($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Medal wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Medal whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $timestamp
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\Medal whereTimestamp($value)
 */
class Medal extends Model
{
    protected $table = 'medal';

    const EVENT_MEDAL = 7; //获取勋章事件

    public static function insertMedal($job,$nick){
        $model = new self;
        $data = [
            'account'=>$job->account,
            'nick'=>$nick,
            'area'=>$job->area,
            'points'=>$job->points,
            'timestamp'=>$job->timestamp
        ];
        $model->setRawAttributes($data);
        return $model->save();
    }
}
