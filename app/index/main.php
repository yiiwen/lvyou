<?php
/**
 * Created by PhpStorm.
 * User: 11641
 * Date: 2018/10/18
 * Time: 13:59
 */

if (!defined('IN_ANWSION'))
{
    die;
}

class main extends AWS_CONTROLLER
{
    public function index_action()
    {
        TPL::output('index/index');
    }
}