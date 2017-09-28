$(document).ready(function() {
	$(".button-collapse").sideNav();
	$.post(
		'../assets/API/reservation.php',
		{'type': 0},
		function(response) {
			if (response[0].code == 1) {
				Materialize.toast('未查询到订单', 3000);
				$("#loading").hide();
			};
			if (response[0].code == 2) {
				alert('请登录系统！');
				window.location.href = './login';
			};
			if (response[0].code == 0) {
				for (var index = 1; index < response.length; index++) {
					$("#reservation").append(
					'<div class="card">' +
						'<div class="card-content">' +
							'<div class="card-title">订单号：' + response[index].reservationNo + '</div>' +
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
											'<td>' + response[index].stuName + '</td>' +
											'<td>' + response[index].stuNo + '</td>' +
											'<td>' + response[index].contact + '</td>' +
											'<td>' + response[index].dormitory + '</td>' +
											'<td>' + response[index].date + '</td>' +
											'<td>' + response[index].timePeriod + '</td>' +
											'<td>' + response[index].sbmTime + '</td>' +
											'<td>' + response[index].updTime + '</td>' +
										'</tr>' +
									'</tbody>' +
								'</table>' +
								'<div class="section">' +
									'<div class="card-title">预约书籍信息：</div>' +
									'<div class="row" id="reserved-books-for' + response[index].reservationNo + '"></div>' +
								'</div>' +
							'</div>' +
						'</div>' +
					'</div>');

					$.each(response[index].books, function(i, book) {
						var MultipleAuthor = book.isMultipleAuthor == 1 ? ' 等' : '';
						if (book.image == './assets/pictures/defaultCover.png') {
								book.image = '../assets/pictures/defaultCover.png'
						}

						$("#reserved-books-for" + response[index].reservationNo).append(
						'<div class="col s12 m4">' +
							'<div class="card blue-grey darken-1">' +
								'<div class="card-content white-text">' +
									'<div class="card-title">#' + book.bookID + ' ' + book.title +
									'</div>' +
									'<div class="card-details">' +
										'<p>作者：' + book.author + MultipleAuthor + '</p>' +
										'<p>出版社：' + book.press + '</p>' +
										'<p>出版日期：' + book.pubdate + '</p>' +
									'</div>' +
								'</div>' +
								'<div class="card-action center-align">' +
									'<a class="cover-reservation" href="' + book.image + '">触碰或点击查看封面图片' +
										'<div class="cover-image-reservation">' +
											'<img src="' + book.image + '" alt="封面图片">' +
										'</div>' +
									'</a>' +
								'</div>' +
							'</div>' +
						'</div>');
					});
					$("#loading").hide();
				}
			}
		}
	);
});

function logout() {
	$.post(
		'../assets/API/admin.php',
		{'type': 2},
		function(response) {
			if (response.code == 0) {
				alert('退出系统成功');
			}
			window.location.href = '../';
		}
	);
}
