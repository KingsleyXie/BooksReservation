$(document).ready(function() {
	$(".button-collapse").sideNav();
	document.getElementById("loading").style.display = 'flex';
	$.ajax({
		type: 'POST',
		url: '../assets/API/reservation.php',
		data: ({ 'type': 0 }),
		success: function(response)
		{
			if (response[0].code == 1) {
				Materialize.toast('未查询到订单', 3000);
				document.getElementById("loading").style.display = 'none';
			};
			if (response[0].code == 2) {
				alert('请登录系统！');
				window.location.href = './login';
			};
			if (response[0].code == 0) {
				for (var res = 1; res < response.length; res++) {
					document.getElementById("reservation").innerHTML += 
					'<div class="card">' + 
						'<div class="card-content">' + 
							'<div class="card-title">订单号：' + response[res].reservationNo + '</div>' + 
							'<div class="card-details">' + 
								'<table class="highlight responsive-table">' + 
									'<thead>' + 
										'<tr>' + 
											'<th>姓名</th>' + 
											'<th>学号</th>' + 
											'<th>联系方式</th>' + 
											'<th>宿舍楼</th>' + 
											'<th>领取日期</th>' + 
											'<th>领取时段</th>' + 
											'<th>订单提交时间</th>' + 
											'<th>订单更新时间</th>' + 
										'</tr>' + 
									'</thead>' + 
									'<tbody>' + 
										'<tr>' + 
											'<td>' + response[res].stuName + '</td>' + 
											'<td>' + response[res].stuNo + '</td>' + 
											'<td>' + response[res].contact + '</td>' + 
											'<td>' + response[res].dormitory + '</td>' + 
											'<td>' + response[res].date + '</td>' + 
											'<td>' + response[res].timePeriod + '</td>' + 
											'<td>' + response[res].sbmTime + '</td>' + 
											'<td>' + response[res].updTime + '</td>' + 
										'</tr>' + 
									'</tbody>' + 
								'</table>' + 
								'<div class="section">' + 
									'<div class="card-title">预约书籍信息：</div>' + 
									'<div class="row" id="reservedBooksFor' + response[res].reservationNo + '"></div>' + 
								'</div>' + 
							'</div>' + 
						'</div>';
					'</div>';

					for (var i = 0; i < response[res].books.length; i++) {

						var MultipleAuthor = response[res].books[i].isMultipleAuthor == 1 ? ' 等' : '';

						if (response[res].books[i].image == './assets/pictures/defaultCover.png') {
							response[res].books[i].image = '../assets/pictures/defaultCover.png'
						}

						document.getElementById('reservedBooksFor' + response[res].reservationNo).innerHTML += 
							'<div class="col s12 m4">' + 
								'<div class="card blue-grey darken-1">' + 
									'<div class="card-content white-text">' + 
										'<div class="card-title">#' + response[res].books[i].bookID + ' ' + response[res].books[i].title + 
										'</div>' + 
										'<div class="card-details">' + 
											'<p>作者：' + response[res].books[i].author + MultipleAuthor + '</p>' + 
											'<p>出版社：' + response[res].books[i].press + '</p>' + 
											'<p>出版日期：' + response[res].books[i].pubdate + '</p>' + 
										'</div>' + 
									'</div>' + 
									'<div class="card-action center-align">' + 
										'<a class="cover-reservation" href="' + response[res].books[i].image + '">触碰或点击查看封面图片' + 
											'<div class="cover-image-reservation">' + 
												'<img src="' + response[res].books[i].image + '" alt="封面图片">' + 
											'</div>' + 
										'</a>' + 
									'</div>' + 
								'</div>' + 
							'</div>';
					}
					document.getElementById("loading").style.display = 'none';
				}
			}
		}
	});
});



function logout() {
	$.ajax({
		type: 'POST',
		url: '../assets/API/admin.php',
		data: ({ 'type':2 }),
		success: function(response) {
			if (response.code == 0) {
				alert('退出系统成功');
			}
			window.location.href = '../';
		}
	});
}
