<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Model\MedalLianyun
 *
 * @property int $id
 * @property string $account
 * @property string $nick
 * @property int $area
 * @property int $points
 * @property int $timestamp
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyun whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyun whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyun whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyun whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyun whereNick($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyun wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyun whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyun whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MedalLianyun extends Model
{
    protected $table = 'medal_lianyun';

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
