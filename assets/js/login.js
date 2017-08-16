$(document).ready(function() {
	$('#login-form').submit(function(e) {
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: '../assets/API/admin.php',
			data: $(this).serialize() + '&type=' + 1,
			success: function(response)
			{
				if (response.code == 0) {
					alert('登录成功！');
					window.location.href = './';
				} else {
					alert(response.errMsg);
				}
			}
		});
	});
});
