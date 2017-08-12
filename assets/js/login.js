$(document).ready(function() {
	$('#loginform').submit(function(e) {
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "../assets/API/admin.php",
			data: $(this).serialize() + "&type=" + 1,
			success: function(response)
			{
				if (response.code == 1)
					alert("用户名或密码错误！");
				if (response.code == 2)
					alert("请输入用户名和密码！");
				if (response.code == 0) {
					alert("登录成功！");
					window.location.href = "./";
				}
			}
		});
	});
});
