/**
 * ��֤������
 * @���� qinggan <admin@phpok.com>
 * @��Ȩ �������螿Ƽ����޹�˾
 * @��ҳ http://www.phpok.com
 * @�汾 5.x
 * @��Ȩ http://www.phpok.com/lgpl.html ��Դ��ȨЭ�飺GNU Lesser General Public License
 * @ʱ�� 2018��08��26��
**/
layui.use(['form','layer'], function(){
	let form = layui.form;
	let layer = layui.layer;
	form.on('checkbox', function(data){
		if(data.elem.checked){
			$(data.elem).attr("checked",true);
		}else{
			$(data.elem).removeAttr("checked");
		}
	});
});