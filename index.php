<?php
error_reporting(0);
?>
<head>
    <title>SHSH Checker</title>
<script src="./3des.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
</head>

<body>
<script>
function post_encrypt_data(){
	hexecid = document.getElementById('ecid').value;
	ecid = parseInt(hexecid, 16); 
	model = document.getElementById('model').value;
	encrypt_object = {
		ecid: ecid,
		model: model,
		time : Date.now()
	};
	encrypt_param = encrypt_3DES(JSON.stringify(encrypt_object),'2015aisi1234sj7890smartflashi4pc',0,1,0);
	document.getElementById('encrypt_param').value = encrypt_param;
	document.getElementById('intecid').value = ecid;
	document.getElementById('post_model').value = model;
}

</script>

<h1>SHSH Checker</h1>
<div>
<label for="name">ECID:</label>
<input type="text" id="ecid" name="ecid">
<br>
<label for="name">model:</label>
<input type="text" id="model" name="model">
<form action="/index.php" method="post" onsubmit="post_encrypt_data()">
	<input type="hidden" id="encrypt_param" name="encrypt_param" value="0">
	<input type="hidden" id="intecid" name="intecid" value="0">
	<input type="hidden" id="post_model" name="post_model" value="0">
    <br>
    <input type="submit" value="提交">
</form>
</div>

<br>

<div>
<?php
$encrypt_param = $_POST['encrypt_param'];
$ecid = $_POST['intecid'];
$model = $_POST['post_model'];
$encrypt_param = urlencode($encrypt_param);
$cydia_url = 'https://cydia.saurik.com/tss@home/api/check/';
$cydia_response = file_get_contents($cydia_url.$ecid);

$cydia_data = json_decode($cydia_response,true);
if ($cydia_data !== null) {
    // 遍历数组
    echo "Cydia:查询成功,存在以下iOS版本的SHSH备份\n";
    foreach ($cydia_data as $item) {
        if (isset($item['model']) && $item['model'] === $model) {
            echo $item['firmware'] . "(" . $item['build'] . ")\n";
        }
    }
} else {
    echo "cydia:未输入ecid或查询无结果\n";
}
?>
</div>

<div>
<?php
$i4_url = 'https://i4tool2.i4.cn/requestBackupSHSHList.xhtml?param=';
$i4_response = file_get_contents($i4_url.$encrypt_param);
$i4_data = json_decode($i4_response,true);
if ($i4_data !== null && isset($i4_data['list'])) {
    // 遍历'list'数组
    echo "\n爱思:查询成功,存在以下iOS版本的SHSH备份\n";
		foreach ($i4_data['list'] as $item) {
        // 打印每个'ios_order'的值
        if (isset($item['ios'])) {
            echo $item['ios'] . "\n";
        }
}
} else {
    echo "爱思:没有输入ECID或者查询无结果\n";
}

?>
</div>

</body>
