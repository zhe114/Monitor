<?php
require("config.php");
    
    //echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    header("Content-Type:application/json;charset=utf-8");
    $channel = $_POST['channel'];
    
    switch($_POST['action']){
        case "getKeys":
            echo getData($channel);
            break;
    }

    function getData($channel){
        global $ip,$port;
        try{
            $redis = new Redis();
            $redis->connect($ip,$port);
        }catch(connectException $e){
            echo json_encode(array("error"=>"DB connection error"));
        }
        
        $redis->select($channel);
        $arkeys = $redis->KEYS('*');
	$arkeys = quickSort($arkeys);
        $countKeys = count($arkeys);
        $countField = $redis->Hlen($arkeys[0]);
        $data = array("");
        $re[0] = $countKeys;
        $re[1] = $countField;
        $n = 2;
        for($i=$countKeys;$i>($countKeys-99);$i--){
            $j = strval($arkeys[$i]);
            $arList = $redis->hVAls($j);
            if ($arList != 'nil'){
                foreach ($arList as $key => $value) {
                    $re[$n] = $value;
                    $n++;
                }
            }else{
                echo json_encode("failure");
            }
	    if($channel == '12'){
		$re[$n] = $arkeys[$i];
            	$n++;
      	    }
        }
        echo json_encode($re);
        
    }
	function quickSort($arr)
{
    $count = count($arr);

    if ($count < 2) {
        return $arr;
    }

    $leftArray = $rightArray = array();
    $middle = $arr[0];

    for ($i = 1; $i < $count; $i++) {
        if ($arr[$i] < $middle) {
            $leftArray[] = $arr[$i];
        } else {
            $rightArray[] = $arr[$i];
        }
    }

    $leftArray = quickSort($leftArray);
    $rightArray = quickSort($rightArray);

    return array_merge($leftArray, array($middle), $rightArray);
}
    ?>



