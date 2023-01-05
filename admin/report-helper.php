<?php 
	include '../header/lib.php';
	function dd($data){
		echo "<pre>";print_r($data);exit;
	}
	function res_json($data){
		header('Content-Type: application/json');
		echo json_encode($data);
		exit;
	}


		
	if(isset($_POST['input'])){
		$type = isset($_POST['type'])?$_POST['type']:'state';
		$input = isset($_POST['input'])?$_POST['input']:'';
		$rc = new reportController();
		if($type == 'district')
			$data = $rc->sbGetTehsilList($_POST);
		else if($type == 'tehsil')
			$data = $rc->sbGetClassList($input);
		else if($type == 'centre')
			$data = $rc->sbGetStateList($input);
		else
			$data = $rc->sbGetDistrictList($_POST);

	
		if(count($data))
			res_json(array('err'=>0,'data'=>$data,'rows'=>count($data)));
		
		res_json(array('err'=>1));
	}

	if(isset($_GET['input'])){
		$type = isset($_GET['type'])?$_GET['type']:'state';
		$input = isset($_GET['input'])?$_GET['input']:'';
		$rc = new reportController();
		if($type == 'tehsil')
			$data = $rc->sbGetClassList($input);
		else if($type == 'centre')
			$data = $rc->sbGetStateList($input);
	

	
		if(count($data))
			res_json(array('err'=>0,'data'=>$data,'rows'=>count($data)));
		
		res_json(array('err'=>1));
	}

	if(isset($_GET['usr'])){
		$rc = new reportController();
		//$s = $rc->sbGetSignedUpUserCount();

		dd($s);
	}


	if(isset($_POST['getFilterData']) || isset($_GET['getFilterData'])){
		$rc = new reportController();
		$client_id = $_SESSION['client_id'];
		$data= $rc->sbGetCenterListByClient($client_id);

		if(count($data))
			res_json(array('err'=>0,'data'=>$data,'rows'=>count($data)));
		
		res_json(array('err'=>1));
	}
	


		

	if(isset($_GET['getUserGraph'])){
		$graphObj = new graphController();
		$client_id = $_SESSION['client_id'];
		$data = $graphObj->regUsersGraphData($client_id);
		if(count($data))
			res_json(array('err'=>0,'data'=>$data,'rows'=>count($data)));
		
		res_json(array('err'=>1));
	}
	

