$(document).ready(function() {
	$(".button-collapse").sideNav();
	$(".modal").modal();
	$("#loading").css("display", "flex");
	
	$.post(
		'../assets/API/reservations.php',
		'operation=all',
		function(response) {
			if (response.code == 0) {
				$.each(response, function(index, reservation) {
					if (index == 'code') return true;
					$("#reservations").append(
					'<div class="card">' +
						'<div class="card-content">' +
							'<div class="card-title">订单号：' + reservation.reservationNo + '</div>' +
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
											'<td>' + reservation.stuName + '</td>' +
											'<td>' + reservation.stuNo + '</td>' +
											'<td>' + reservation.contact + '</td>' +
											'<td>' + reservation.dormitory + '</td>' +
											'<td>' + reservation.date + '</td>' +
											'<td>' + reservation.timePeriod + '</td>' +
											'<td>' + reservation.sbmTime + '</td>' +
											'<td>' + reservation.updTime + '</td>' +
										'</tr>' +
									'</tbody>' +
								'</table>' +
								'<div class="section">' +
									'<div class="card-title">预约书籍信息：</div>' +
									'<div class="row" id="reserved-books-for' + reservation.reservationNo + '"></div>' +
								'</div>' +
							'</div>' +
						'</div>' +
					'</div>');

					$.each(reservation.books, function(i, book) {
						MultipleAuthor = book.isMultipleAuthor == 1 ? ' 等' : '';
						if (book.image == './assets/pictures/defaultCover.png') {
								book.image = '../assets/pictures/defaultCover.png'
						}

						$("#reserved-books-for" + reservation.reservationNo).append(
						'<div class="col s12 m4">' +
							'<div class="card blue-grey darken-1">' +
								'<div class="card-content white-text">' +
									'<div class="card-title book-title" onclick="showFullText(this)">#' + book.bookID + ' ' + book.title +
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
				});
			} else {
				if (response.code == 2) window.location.href = './';
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
