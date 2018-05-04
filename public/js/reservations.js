$(document).ready(function() {
	$(".button-collapse").sideNav();
	$(".modal").modal();
	$("#loading").css("display", "flex");

	$.get(
		'../api/admin/reservation/all',
		function(response) {
			if (response.errcode == 0) {
				$.each(response.data, function(index, reservation) {
					$("#reservations").append(
					'<div class="card">' +
						'<div class="card-content">' +
							'<div class="card-title">订单号：' + reservation.id + '</div>' +
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
											'<td>' + reservation.stuname + '</td>' +
											'<td>' + reservation.stuno + '</td>' +
											'<td>' + reservation.contact + '</td>' +
											'<td>' + reservation.dorm + '</td>' +
											'<td>' + reservation.takeday + '</td>' +
											'<td>' + reservation.taketime + '</td>' +
											'<td>' + reservation.submited + '</td>' +
											'<td>' + reservation.updated + '</td>' +
										'</tr>' +
									'</tbody>' +
								'</table>' +
								'<div class="section">' +
									'<div class="card-title">预约书籍信息：</div>' +
									'<div class="row" id="reserved-books-for' + reservation.id + '"></div>' +
								'</div>' +
							'</div>' +
						'</div>' +
					'</div>');

					$.each(reservation.books, function(i, book) {
						$("#reserved-books-for" + reservation.id).append(
						'<div class="col s12 m4">' +
							'<div class="card blue-grey darken-1">' +
								'<div class="card-content white-text">' +
									'<div class="card-title book-title" onclick="showFullText(this)">#' + book.id + ' ' + book.title +
									'</div>' +
									'<div class="card-details">' +
										'<p>作者：' + book.author + '</p>' +
										'<p>出版社：' + book.publisher + '</p>' +
										'<p>出版日期：' + book.pubdate + '</p>' +
									'</div>' +
								'</div>' +
								'<div class="card-action center-align">' +
									'<a class="cover-reservation" href="' + book.cover + '">触碰或点击查看封面图片' +
										'<div class="cover-image-reservation">' +
											'<img src="' + book.cover + '" alt="封面图片">' +
										'</div>' +
									'</a>' +
								'</div>' +
							'</div>' +
						'</div>');
					});
				});
			} else {
				if (response.errcode == -1) window.location.href = 'books';
				Materialize.toast(response.errMsg, 3000);
			}
			$("#loading").hide();
		}
	);
});

function showFullText(val) {
	$("#alert-content").text(val.textContent);
	$("#alert").modal('open');
}

function returnToMainPage() {
	$(".button-collapse").sideNav('hide');
}
