<?php
/** 调用易盾反垃圾云服务敏感词查询接口API示例 */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 易盾反垃圾云服务敏感词删除接口地址 */
define("API_URL", "http://as.dun.163.com/v1/keyword/query");
/** api version */
define("VERSION", "v1");
/** API timeout*/
define("API_TIMEOUT", 10);
require("../util.php");

/**
 * 反垃圾请求接口简单封装
 * $params 请求参数
 */
function check($params){
	$params["secretId"] = SECRETID;
	$params["businessId"] = BUSINESSID;
	$params["version"] = VERSION;
	$params["timestamp"] = time() * 1000;// time in milliseconds
	$params["nonce"] = sprintf("%d", rand()); // random int

	$params = toUtf8($params);
	$params["signature"] = gen_signature(SECRETKEY, $params);
	// var_dump($params);

	$result = curl_post($params, API_URL, API_TIMEOUT);
	if($result === FALSE){
		return array("code"=>500, "msg"=>"file_get_contents failed.");
	}else{
		return json_decode($result, true);	
	}
}

// 简单测试
function main(){
    echo "mb_internal_encoding=".mb_internal_encoding()."\n";

	$params = array(
		"id" => "163",
		// 100: 色情，110: 性感，200: 广告，210: 二维码，300: 暴恐，400: 违禁，500: 涉政，600: 谩骂，700: 灌水
		"category" => "100",
		"keyword" => "色情敏感词",
		"orderType" => "1",
		"pageNum" => "1",
		"pageSize" => "20"
	);

	$ret = check($params);
	var_dump($ret);
	if ($ret["code"] == 200 && $ret["result"]) {
		$result = $ret["result"];
		$words = $result["words"];
		$count = $words["count"];
		$rows = $words["rows"];
		foreach($rows as $index => $row){
			$id = $row("id");
			$word = $row("word");
			$category = $row("category");
			$status = $row("status");
			$updateTime = $row("updateTime");
			echo "敏感词查询成功，id: {$id}，keyword: {$word}，category: {$category}，status: {$status}，updateTime: {$updateTime}";
		}
    }else{
    	var_dump($ret);
    }
}

main();
?>
