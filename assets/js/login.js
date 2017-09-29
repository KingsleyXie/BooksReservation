$(document).ready(function() {
	$("#login-form").submit(function(e) {
		e.preventDefault();
		$.post(
			'../assets/API/admin.php',
			$(this).serialize() + '&operation=login',
			function(response) {
				if (response.code == 0) {
					alert('登录成功！');
					window.location.href = './';
				} else {
					alert(response.errMsg);
				}
			}
		);
	});
});
