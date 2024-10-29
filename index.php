<?php
error_reporting(0);
?>
<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SHSH Checker</title>
<script src="./3des.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
<script src="https://cdn.staticfile.net/popper.js/2.9.3/umd/popper.min.js"></script>
<script src="https://cdn.staticfile.net/twitter-bootstrap/5.1.1/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdn.staticfile.net/twitter-bootstrap/5.1.1/css/bootstrap.min.css">

<?php

if(!isset($ecid)){
    $style_hidden = "hidden";
    $notify_class="";
    
}
?>
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

function get_i4_shsh_downlink(){
}

</script>


<div class="container mt-3">
  <h2>SHSH Checker</h2>
  <form action="/index.php" method="post" onsubmit="post_encrypt_data()">
  <input type="hidden" id="encrypt_param" name="encrypt_param" value="<?php echo $_POST['encrypt_param']?>">
	<input type="hidden" id="intecid" name="intecid" value="<?php echo $_POST['intecid']?>">
	<input type="hidden" id="post_model" name="post_model" value="<?php echo $_POST['post_model']?>">
	  <div class="form-floating mb-3 mt-3">
		<input type="ecid" class="form-control" id="ecid" placeholder="Enter ECID" name="ecid">
		<label for="ecid" class="form-label">ecid(hex)</label>
	  </div>
	  <div class="form-floating mb-3 mt-3">
		<select id="model" class="form-control">
		<option value="iPhone1,1">iPhone 2G - iPhone1,1</option>
		<option value="iPhone2,1">iPhone 3G - iPhone2,1</option>
		<option value="iPhone3,1">iPhone 4 - iPhone3,1</option>
		<option value="iPhone3,2">iPhone 4 (REVA) - iPhone3,2</option>
		<option value="iPhone3,3">iPhone 4 (CDMA) - iPhone3,3</option>
		<option value="iPhone4,1">iPhone 4[S] - iPhone4,1</option>
		<option value="iPhone5,1">iPhone 5[GSM] - iPhone5,1</option>
		<option value="iPhone5,2">iPhone 5 - iPhone5,2</option>
		<option value="iPhone5,3">iPhone 5c[GSM] - iPhone5,3</option>
		<option value="iPhone5,4">iPhone 5c - iPhone5,4</option>
		<option value="iPhone6,1">iPhone 5s[GSM] - iPhone6,1</option>
		<option value="iPhone6,2">iPhone 5s - iPhone6,2</option>
		<option value="iPhone7,1">iPhone 6 - iPhone7,1</option>
		<option value="iPhone7,2">iPhone 6 Plus - iPhone7,2</option>
		<option value="iPhone8,1">iPhone 6s - iPhone8,1</option>
		<option value="iPhone8,2">iPhone 6s Plus - iPhone8,2</option>
		<option value="iPhone8,4">iPhone SE - iPhone8,4</option>
		</select>
		<label for="model" class="form-label">model</label>

	  </div>
	  <button type="submit" class="btn btn-primary">提交</button>
	</form>
</div>

<br>

<div class="container mt-3">
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
    $notify_class="alert alert-success";
    $notify_content = "<strong>Cydia:</strong>查询成功,存在以下iOS版本的SHSH备份\n";
    foreach ($cydia_data as $item) {
        if (isset($item['model']) && $item['model'] === $model) {
            $notify_content = $notify_content ."<button id='cydia_".$item['firmware']."(".$item['build'].")"."' "."type='button' class='btn btn-primary bg-success' data-bs-toggle='modal' data-bs-target='#myModal'>" .$item['firmware'] . "(" . $item['build'] . ")". "</button>"."\n";
        }
    }
} else {
    $notify_content = "<strong>Cydia:</strong>查询无结果";
}
?>

<div style="<?php echo $style_hidden;?>" class="<?php echo $notify_class;?>" >
<?php 
    if(isset($ecid)){
        echo $notify_content;
    }

    ?>
  </div>  

</div>

<div class="container mt-3">
<?php
$i4_url = 'https://i4tool2.i4.cn/requestBackupSHSHList.xhtml?param=';
$i4_response = file_get_contents($i4_url.$encrypt_param);
$i4_data = json_decode($i4_response,true);


if ($i4_data !== null && isset($i4_data['list'])) {
    // 遍历'list'数组
    $notify_class="alert alert-success";
    $notify_content = "<strong>爱思:</strong>查询成功,存在以下iOS版本的SHSH备份 ";
		foreach ($i4_data['list'] as $item) {
        // 打印每个'ios_order'的值
        if (isset($item['ios'])) {
            $notify_content = $notify_content . "<button id='i4_".$item['ios']."' "."type='button' class='btn btn-primary bg-success' data-bs-toggle='modal' data-bs-target='#myModal'>" .$item['ios'] . "</button>" . "\n";
        }
}
} else {
    $notify_content = "<strong>爱思:</strong>查询无结果";
}

?>

<div style="<?php echo $style_hidden;?>" class="<?php echo $notify_class;?>" >
    <?php 
    if(isset($ecid)){
        echo $notify_content;
    }

    ?>
  </div>  

</div>

<!-- 模态框 -->
<div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
 
      <!-- 模态框头部 -->
      <div class="modal-header">
        <h4 class="modal-title">下载 SHSH 文件</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
 
      <!-- 模态框内容 -->
      <div class="modal-body">
	  <p id="modal-content"></p>
	<button id="download_shsh_button" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
  <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"></path>
  <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"></path>
</svg>下载 SHSH 文件</button>
      </div>
 
      <!-- 模态框底部 -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">关闭</button>
      </div>
 
    </div>
  </div>
</div>

<script>
	
var buttons = document.getElementsByClassName('btn btn-primary bg-success');
for (var i = 0; i < buttons.length; i++) {
    buttons[i].addEventListener('click', function(event) {
        var button = event.target;
        var pContent = document.getElementById('modal-content');
		var button_id = button.id;
		var model = document.getElementById('post_model');
		if(button_id.includes("cydia")){
			var Content = "您正在从 Cydia 服务器 下载 " + model.value + " 的 iOS" + button_id.replace("cydia_", "") + " 的 SHSH 文件";
		}else if(button_id.includes("i4")){
			var Content = "您正在从 爱思服务器 下载 " + model.value + " 的 iOS" + button_id.replace("i4_", "") + " 的 SHSH 文件"	;
		}
        pContent.textContent = Content; // 或者使用 innerText
    });
}

var download_button = document.getElementById('download_shsh_button');
download_button.addEventListener('click', function(event) {
		

		
		
});

</script>

</body>
