<?php
namespace Mobile\Controller;
use Think\Controller;

class EmptyController extends Controller
{
    // 初始化函数
    public function _initialize()
    {
        $this->run(CONTROLLER_NAME,ACTION_NAME);
    }
    public function run($controller,$action){
        switch($controller){
            case 'Storerecruit':
                $classname = '\\Store\\Controller\\StorerecruitController';
                break;
            case 'Storetenement':
                $classname = '\\Store\\Controller\\StoretenementController';
                break;
            case 'Storetransfer':
                $classname = '\\Store\\Controller\\StoretransferController';
                break;
            default:
                $classname = '\\'.$controller.'\\Controller\\MobileController';
                break;
        }
        $c = new $classname;
        $c->$action();
    }
}

?>