$(document).ready(function() {
	$("#login-form").submit(function(e) {
		e.preventDefault();
		$.post(
			'../assets/API/admin.php',
			$(this).serialize() + '&type=' + 1,
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
