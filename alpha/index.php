<?php
$page_data = <<<EOT
<div class="page-header">
    <h1>地外文明有话要说</h1>
</div>
<div class="row">
    <div class="col-md-12">
        <p>
            请将你系统"hosts"文件中的"dvws.local"指向本星球所在的地址。<br/><br/>
"hosts" 文件的位置：<br><br>
<b>Windows: C:\windows\System32\drivers\etc\hosts<br><br>
Linux: /etc/hosts</b><br><br>
加入这一行:<br>
<h3>192.168.1.115&nbsp;&nbsp;&nbsp;dvws.local</h3><br><br>
        </p>
        <!--img style="background-color:white" src="img/740px-Alien_decoder_Futurama.svg.png" /-->
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <p id="result">
        </p>
    </div>
</div>
EOT;

$page_script= <<<EOT

EOT;
?>

<?php require_once('includes/template.php'); ?>
