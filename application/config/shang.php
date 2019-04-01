<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| SHANG
| -------------------------------------------------------------------
| This file contains four arrays of user agent data. It is used by the
| User Agent Class to help identify browser, platform, robot, and
| mobile device data. The array keys are used to identify the device
| and the array values are used to set the actual name of the item.
*/

$config = [
    'task_behaviors_1' => [
        '收藏竞品1','收藏竞品2','加购竞品1','加购竞品2','货比三家'
    ],

    'task_behaviors_2' => [
        '加购物车','收藏店铺','浏览副宝贝','领优惠券','浏览好评','点赞好评','浏览问大家','评论问大家','关注问大家',
        '提问问大家','下单不付款','收藏下单','聊天截图'
    ],


    'task_behaviors' => [
        '收藏竞品1','收藏竞品2','加购竞品1','加购竞品2','货比三家',
        '加购物车','收藏店铺','浏览副宝贝','领优惠券','浏览好评', '点赞好评','浏览问大家','评论问大家','关注问大家',
        '提问问大家','下单不付款','收藏下单','聊天截图','收藏宝贝'
    ],

    'task_ways' => [
        '手淘搜索','猜你喜欢','购物车','收藏夹'
    ],

    'reject_reasons' => [
        '1' => '拍错链接/店铺',
        '2' => '账号存在风险',
        '3' => '订单金额错误',
        '4' => '买手串号',
        '5' => '做单不规范(秒拍，图片上传错误）',
        '99' => '其他原因'
    ]



];





