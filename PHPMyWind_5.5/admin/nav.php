<?php require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('nav'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>导航菜单管理</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript" src="templates/js/forms.func.js"></script>
</head>
<body>
<div class="topToolbar"> <span class="title">导航菜单管理</span> <a href="javascript:location.reload();" class="reload">刷新</a></div>
<form name="form" id="form" method="post" action="">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
		<tr align="left" class="head">
			<td width="5%" height="36" class="firstCol"><input type="checkbox" name="checkid" onclick="CheckAll(this.checked);"></td>
			<td width="3%">ID</td>
			<td width="27%">导航名称</td>
			<td width="15%">跳转链接</td>
			<td width="15%" align="center">排序</td>
			<td width="25%" class="endCol">操作</td>
		</tr>
	</table>
	<?php
	function Show($id=0, $i=0)
	{
		global $dosql,$cfg_siteid;
		$dosql->Execute("SELECT * FROM `#@__nav` WHERE `siteid`='$cfg_siteid' AND `parentid`=$id ORDER BY `orderid` ASC", $id);
		$i++;
	
		while($row = $dosql->GetArray($id))
		{
			switch($row['checkinfo'])
			{
				case 'true':
					$checkinfo = '显示';
					break;  
				case 'false':
					$checkinfo = '隐藏';
					break;
				default:
					$checkinfo = '没有获取到参数';
			}
	
			//设置$classname
			$classname = '';


			//设置空格
			for($n = 1; $n < $i; $n++)
				$classname .= '&nbsp;&nbsp;';


			//设置折叠
			if($row['parentid'] == '0')
				$classname .= '<span class="minusSign" id="rowid_'.$row['id'].'" onclick="DisplayRows('.$row['id'].');">';
			else
				$classname .= '<span class="subType">';


			$classname .= $row['classname'].'</span>';

	?>
	<div rel="rowpid_<?php echo GetTopID($row['parentstr']) ; ?>">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
			<tr align="left" class="dataTr">
				<td width="5%" height="36" class="firstCol"><input type="checkbox" name="checkid[]" value="<?php echo $row['id']; ?>" /></td>
				<td width="3%"><?php echo $row['id']; ?>
					<input type="hidden"  name="id[]"id="id[]" value="<?php echo $row['id']; ?>" /></td>
				<td width="27%"><?php echo $classname; ?></td>
				<td width="15%" class="number"><?php echo $row['linkurl']; ?></td>
				<td width="15%" align="center"><a href="nav_save.php?action=up&id=<?php echo $row['id']; ?>&parentid=<?php echo $row['parentid']; ?>&orderid=<?php echo $row['orderid']; ?>" class="leftArrow" title="提升排序"></a>
					<input type="text" name="orderid[]" id="orderid[]" class="inputls" value="<?php echo $row['orderid']; ?>" />
					<a href="nav_save.php?action=down&id=<?php echo $row['id']; ?>&parentid=<?php echo $row['parentid']; ?>&orderid=<?php echo $row['orderid']; ?>" class="rightArrow" title="下降排序"></a></td>
				<td width="25%" class="action endCol"><span><a href="nav_add.php?id=<?php echo $row['id']; ?>">添加子导航</a></span> | <span><a href="nav_save.php?action=check&id=<?php echo $row['id']; ?>&checkinfo=<?php echo $row['checkinfo']; ?>" title="点击进行显示与隐藏操作"><?php echo $checkinfo; ?></a></span> | <span><a href="nav_update.php?id=<?php echo $row['id']; ?>">修改</a></span> | <span class="nb"><a href="nav_save.php?action=del&id=<?php echo $row['id'] ?>" onclick="return ConfDel(2);">删除</a></span></td>
			</tr>
		</table>
	</div>
	<?php
			Show($row['id'], $i+2);
		}
	}

	Show();


	//判断无记录样式
	if($dosql->GetTotalRow(0) == 0)
	{
		echo '<div class="dataEmpty">暂时没有相关的记录</div>';
	}
	
	
	//判断类别页是否折叠
	if($cfg_typefold == 'Y')
	{
		echo '<script>HideAllRows();</script>';
	}
	?>
</form>
<div class="bottomToolbar"> <span class="selArea"><span>选择：</span> <a href="javascript:CheckAll(true);">全部</a> - <a href="javascript:CheckAll(false);">无</a> - <a href="javascript:DelAll('nav_save.php');" onclick="return ConfDelAll(1);">删除</a>　<span>操作：</span><a href="javascript:UpOrderID('nav_save.php');">排序</a> - <a href="javascript:ShowAllRows();">展开</a> - <a href="javascript:HideAllRows();">隐藏</a></span> <a href="nav_add.php" class="dataBtn">添加导航菜单</a> </div>
<div class="page">
	<div class="pageText">共有<span><?php echo $dosql->GetTableRow('#@__nav',$cfg_siteid); ?></span>条记录</div>
</div>
<?php

//判断是否启用快捷工具栏
if($cfg_quicktool == 'Y')
{
?>
<div class="quickToolbar">
	<div class="qiuckWarp">
		<div class="quickArea"><span class="selArea"><span>选择：</span> <a href="javascript:CheckAll(true);">全部</a> - <a href="javascript:CheckAll(false);">无</a> - <a href="javascript:DelAll('nav_save.php');" onclick="return ConfDelAll(1);">删除</a>　<span>操作：</span><a href="javascript:UpOrderID('nav_save.php');">排序</a> - <a href="javascript:ShowAllRows();">展开</a> - <a href="javascript:HideAllRows();">隐藏</a></span> <a href="nav_add.php" class="dataBtn">添加导航菜单</a><span class="pageSmall">
			<div class="pageText">共有<span><?php echo $dosql->GetTableRow('#@__nav',$cfg_siteid); ?></span>条记录</div>
			</span></div>
		<div class="quickAreaBg"></div>
	</div>
</div>
<?php
}
?>
</body>
</html>