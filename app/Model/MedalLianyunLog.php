<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Model\MedalLianyunLog
 *
 * @property int $id
 * @property int|null $event_id 事件类型  7：获得勋章
 * @property string|null $account 账户
 * @property int|null $area 大区
 * @property int|null $timestamp 事件产生时间
 * @property int|null $points 勋章数量
 * @property string|null $data 数据 如{
 * "event_id": 7,
 * "account": "aaaaaa@test.com",
 * "area": 10,
 * "timestamp": 1508392993,
 * "points": 1
 * }
 * @property \Carbon\Carbon|null $created_at 记录创建时间
 * @property \Carbon\Carbon|null $updated_at 记录更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyunLog whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyunLog whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyunLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyunLog whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyunLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyunLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyunLog wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyunLog whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model\MedalLianyunLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MedalLianyunLog extends Model
{
    protected $table = 'medal_lianyun_log';

    public static function insertLog($job){
        $model = new self;
        $data = [
            'event_id'=>$job->event_id,
            'account'=>$job->account,
            'area'=>$job->area,
            'timestamp'=>$job->timestamp,
            'points'=>$job->points,
            'data'=>json_encode($job)
        ];
        $model->setRawAttributes($data);
        return $model->save();
    }
}
