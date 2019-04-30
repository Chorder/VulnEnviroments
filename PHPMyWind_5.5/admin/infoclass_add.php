<?php require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('infoclass'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>添加栏目</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript" src="templates/js/getuploadify.js"></script>
<script type="text/javascript" src="templates/js/getcatpsize.js"></script>
<script type="text/javascript" src="templates/js/checkf.func.js"></script>
</head>
<body>
<div class="formHeader"> <span class="title">添加栏目</span> <a href="javascript:location.reload();" class="reload">刷新</a> </div>
<form name="form" id="form" method="post" action="infoclass_save.php" onsubmit="return cfm_infoclass();">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formTable">
		<tr>
			<td width="25%" height="40" align="right">栏目类型：</td>
			<td width="380"><select name="infotype" id="infotype">
				<?php

				//初始化类型
				$infotype = isset($infotype) ? $infotype : '';
				foreach(array('0'=>'单页','1'=>'列表','2'=>'图片','3'=>'下载','4'=>'商品') as $k=>$v)
				{
					if($infotype == $k)
						$selected = 'selected="selected"';
					else
						$selected = '';
					
					echo "<option value=\"$k\" $selected>$v</option>";
				}
				
				//循环自定义模型
				$dosql->Execute("SELECT * FROM `#@__diymodel` ORDER BY id ASC");
				while($row = $dosql->GetArray())
				{
					if($infotype == $row['id'])
						$selected = 'selected="selected"';
					else
						$selected = '';
					
					echo "<option value=\"".$row['id']."\" $selected>".$row['modeltitle']."</option>";
				}
				?>
				</select>
				<span class="maroon">*</span></td>
			<td><span class="cnote">带<span class="maroon">*</span>号表示为必填项</span></td>
		</tr>
		<tr>
			<td height="40" align="right">所属栏目：</td>
			<td><select name="parentid" id="parentid" onchange="GetCatpSize(this.value);">
					<option value="0">一级栏目</option>
					<?php GetAllType('#@__infoclass','#@__infoclass','id'); ?>
				</select>
				<span class="maroon">*</span></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="40" align="right">栏目名称：</td>
			<td><input name="classname" type="text" id="classname" class="input" />
				<span class="maroon">*</span></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="40" align="right">缩略图片：</td>
			<td><input name="picurl" type="text" id="picurl" class="input" />
				<span class="cnote"><span class="grayBtn" onclick="GetUploadify('uploadify','缩略图上传','image','image',1,<?php echo $cfg_max_file_size; ?>,'picurl')">上 传</span></span></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="40" align="right">自动缩略：</td>
			<td><input type="text" name="picwidth" id="picwidth" class="inputls" value="<?php echo GetCatpSize(0); ?>" />
				宽度(px)
				&nbsp;&nbsp;&nbsp;
				<input type="text" name="picheight" id="picheight" class="inputls" value="<?php echo GetCatpSize(1); ?>" />
				高度(px)</td>
			<td><span class="cnote">子栏目自动获取上级尺寸；留空不启用；原图：上传文件名_hd.扩展名</span></td>
		</tr>
		<tr>
			<td height="40" align="right">跳转链接：</td>
			<td><input type="text" name="linkurl" id="linkurl" class="input" /></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="40" align="right">SEO标题：</td>
			<td><input type="text" name="seotitle" id="seotitle" class="input" /></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="40" align="right">关键词：</td>
			<td><input type="text" name="keywords" id="keywords" class="input" /></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="118" align="right">栏目描述：</td>
			<td><textarea name="description" id="description" class="textarea"></textarea></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="40" align="right">排列排序：</td>
			<td><input type="text" name="orderid" id="orderid" class="inputs" value="<?php echo GetOrderID('#@__infoclass'); ?>" /></td>
			<td>&nbsp;</td>
		</tr>
		<tr class="nb">
			<td height="40" align="right">隐藏栏目：</td>
			<td><input type="radio" name="checkinfo" value="true" checked="checked"  />
				显示&nbsp;
				<input type="radio" name="checkinfo" value="false" />
				隐藏</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div class="formSubBtn">
		<input type="submit" class="submit" value="提交" />
		<input type="button" class="back" value="返回" onclick="history.go(-1);" />
		<input type="hidden" name="action" id="action" value="add" />
	</div>
</form>
</body>
</html>