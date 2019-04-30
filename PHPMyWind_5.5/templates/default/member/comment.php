<?php if(!defined('IN_MEMBER')) exit('Request Error!'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $cfg_webname; ?> - 会员中心 - 我的评论</title>
<link href="<?php echo $cfg_webpath; ?>/templates/default/style/member.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo $cfg_webpath; ?>/templates/default/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $cfg_webpath; ?>/templates/default/js/member.js"></script>
</head>

<body>
<div class="header">
	<?php require_once(dirname(__FILE__).'/header.php'); ?>
</div>
<div class="mainbody">
	<div class="leftarea">
		<?php require_once(dirname(__FILE__).'/lefter.php'); ?>
	</div>
	<div class="rightarea">
		<h3 class="subtitle">我的评论</h3>
		<?php
		$dopage->GetPage("SELECT * FROM `#@__usercomment` WHERE uname='$c_uname' ORDER BY id DESC",9);
		if($dosql->GetTotalRow() > 0)
		{
		?>
		<form name="form" id="form" method="post">
		<ul class="msglist">
			<?php
			while($row = $dosql->GetArray())
			{
				if($row['molds'] == 1)
					$tbname = 'infolist';
				else if($row['molds'] == 2)
					$tbname = 'infoimg';
				else if($row['molds'] == 3)
					$tbname = 'soft';
				else if($row['molds'] == 4)
					$tbname = 'goods';
				else
					$tbname = '';

				$r = $dosql->GetOne("SELECT * FROM `#@__$tbname` WHERE id=".$row['aid']." AND delstate=''");
				if(isset($r) && is_array($r))
				{
			?>
			<li><p><input type="checkbox" name="checkid[]" id="checkid[]" value="<?php echo $row['id']; ?>" />&nbsp;&nbsp;<?php echo ClearHtml($row['body']); ?></p><span class="from">评论自 <a href="<?php echo $row['link']; ?>" target="_blank"><?php echo ReStrLen($r['title'],20); ?></a></span><span class="time"><?php echo GetDateTime($row['time']); ?></span>
<div class="cl"></div></li>
			<?php
				}
				else
				{
					echo '<li><input type="checkbox" name="checkid[]" id="checkid[]" value="'.$row['id'].'" />&nbsp;&nbsp;此条评论的信息已不存在！(ID:'.$row['id'].')</li>';
				}	
			}
			?>
		</ul>
		</form>
		<div class="options_b">选择： <a href="javascript:CheckAll(true);">全部</a> - <a href="javascript:CheckAll(false);">无</a> - <a href="javascript:DelAllNone('?a=delcomment');" onclick="return ConfDelAll(0);">删除</a></div>
		<?php echo $dopage->GetList(); ?>
		<?php
		}
		else
		{
		?>
		<div class="nonelist">您还没有评论哦！</div>
		<?php
		}
		?>
	</div>
	<div class="cl"></div>
</div>
<div class="footer"><?php echo $cfg_copyright; ?></div>
</body>
</html>
