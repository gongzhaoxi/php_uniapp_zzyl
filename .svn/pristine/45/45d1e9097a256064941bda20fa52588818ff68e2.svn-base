<?php
declare (strict_types = 1);
namespace app\index\controller;
use app\index\logic\BoardLogic;

class Board extends \app\BaseController
{
	
	
    public function index()
    {
        return  view('index',BoardLogic::getIndex());
    }
	
    public function waitOrderProduce()
    {
		if ($this->request->isAjax()) {
			return $this->getJson(BoardLogic::getWaitOrderProduce($this->request->param(),$this->request->param('limit')));
		}
        return  view('');
    }	
	
    public function todayOrderProduce()
    {
		if ($this->request->isAjax()) {
			return $this->getJson(BoardLogic::getTodayOrderProduce($this->request->param(),$this->request->param('limit')));
		}
        return  view('',['title'=>'当日生产进度列表']);
    }	
	
	public function materiaQuality()
    {
		if ($this->request->isAjax()) {
			return $this->getJson(BoardLogic::getMateriaQuality($this->request->param(),$this->request->param('limit')));
		}
        return  view('',['title'=>'来料合格率列表']);
    }
	
	public function todayProductStock()
    {
		if ($this->request->isAjax()) {
			return $this->getJson(BoardLogic::getTodayProductStock($this->request->param(),$this->request->param('limit')));
		}
        return  view('',['title'=>'当日生产入库列表']);
    }	
	
	
	public function todayProduceNoFinish()
    {
		if ($this->request->isAjax()) {
			return $this->getJson(BoardLogic::getTodayProduceNoFinish($this->request->param(),$this->request->param('limit')));
		}
        return  view('',['title'=>'当日排产计划列表']);
    }

	
	public function todayProduceProcess()
    {
		if ($this->request->isAjax()) {
			return $this->getJson(BoardLogic::getTodayProduceProcess($this->request->param(),$this->request->param('limit')));
		}
        return  view('',['title'=>'当日生产进度列表']);
    }	
	
	public function weekProduceProcessPassRate()
    {
		if ($this->request->isAjax()) {
			return $this->getJson(BoardLogic::getProduceProcessPassRate($this->request->param(),$this->request->param('limit')));
		}
        return  view('produce_process_pass_rate',['title'=>'生产合格率列表','start_date'=>date('Y-m-d', strtotime('-6 days')),'end_date'=>date('Y-m-d')]);
    }	
	
	public function monthProduceProcessPassRate()
    {
		if ($this->request->isAjax()) {
			return $this->getJson(BoardLogic::getProduceProcessPassRate($this->request->param(),$this->request->param('limit')));
		}
        return  view('produce_process_pass_rate',['title'=>'生产合格率列表','start_date'=>date('Y-m-01'),'end_date'=>date('Y-m-d')]);
    }	
	
	public function weekProduceStock()
    {
		if ($this->request->isAjax()) {
			return $this->getJson(BoardLogic::getWeekProduceStock($this->request->param(),$this->request->param('limit')));
		}
        return  view('week_produce_stock',['title'=>'生产机型统计列表','start_date'=>date('Y-m-d', strtotime('-6 days')),'end_date'=>date('Y-m-d')]);
    }	
	
	
	
	
    public function produce()
    {
		if ($this->request->isAjax()) {
			return $this->getJson(BoardLogic::getList($this->request->param()));
		}
        return  view('produce',['title'=>'生产管理看板','process_id'=>$this->request->param('process_id/d',''),'count'=>BoardLogic::getBoardCount(),'month'=>BoardLogic::getBoardMonthStat(),'pass_produce'=>BoardLogic::getBoardPass(),'wait_produce'=>BoardLogic::getBoardWait()]);
    }
	
    public function errors()
    {
        return  view('errors',['title'=>'物料&制程看板','list1'=>BoardLogic::getBoardError1(),'list2'=>BoardLogic::getBoardError2()]);
    }
	
	public function summary($act=2)
    {
        return  view('summary',['title'=>'汇总分析总看板','act'=>$act,'list1'=>BoardLogic::getProduct($act),'list2'=>BoardLogic::getShipping($act),'count'=>BoardLogic::getSummaryCount($act),'count2'=>BoardLogic::getBoardCount()]);
    }
	
}
