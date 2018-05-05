//The following three lines of code are wrote for reservation or modification
modifying = false, count = 0;
list = ['0', '0', '0'];
preList = ['0', '0', '0'];

$(document).ready(function() {
	$(".modal").modal();
	$(".button-collapse").sideNav();
	$("select").material_select();

	$("#all").modal('open');
	$("#all").submit(function(e) {
		e.preventDefault();
		display(this, 0);
	});

	$("#search").submit(function(e) {
		e.preventDefault();
		display(this, 1);
	});

	$("#reserve").submit(function(e) {
		e.preventDefault();
		if ($("#stuname").val() == ''
			|| $("#stuno").val() == ''
			|| $("#dorm").val() == ''
			|| $("#contact").val() == ''
			|| $("#takeday").val() == ''
			|| $("#taketime").val() == '') {
				modalAlert('请将预约信息填写完整！');
				return;
		}

		$("#loading").css("display", "flex");

		var data = $(this).serialize();
		data +=
			'&book0=' + list[0] +
			'&book1=' + list[1] +
			'&book2=' + list[2];

		if (modifying)
			data +=
				'&prebook0=' + preList[0] +
				'&prebook1=' + preList[1] +
				'&prebook2=' + preList[2];

		var url = 'api/user/reserve/' +
			(modifying ? 'modify' : 'add');

		$.post(url, data, function(response) {
			$("#reserve").modal('close');
			$("#loading").hide();
			switch(response.errcode) {
				case 0:
					Materialize.toast(response.data, 3000);
					setTimeout(function () {
						$("#search-stuno").val($("#stuno").val());
						$("label[for=search-stuno]").addClass("active");
						searchReservation();
					}, 700);
					break;
				case -1:
					if (modifying) {
						$("#search-stuno").val($("#stuno").val());
						$("label[for=search-stuno]").addClass("active");
						searchReservation();
						modifyReservation();
					} else {
						$("#list-data").empty();
						$("#display").empty();
						count = 0; list = ['0', '0', '0'];
					}
					$("#all").modal('open');
					modalAlert(
						'列表中有书籍已被他人预约，请重新选择<br><br>' +
						'预约信息不需要重新填写୧(﹒︠ᴗ﹒︡)୨'
					);
					break;
				default:
					Materialize.toast(response.errmsg, 3000);
			}
		});
	});

	$("#search-reservation").submit(function(e) {
		e.preventDefault();
		searchReservation();
	});
});

function display(data,type) {
	$("#loading").css("display", "flex");
	$.get(
		'api/user/book/all',
		function(response) {
			if (response.errcode == 0) {
				$("#display").empty();
				$.each(response.data, function(i, book) {
					var btnAttr = book.quantity == 0 ?
						'<a class="btn-floating waves-effect waves-light grey center-align btn-add">0</a>' :
						'<a class="btn-floating waves-effect waves-light red center-align btn-add" ' +
						'data-id=' + book.id + ' onclick="addToList(this)">' +
						book.quantity + '</a>';

					$("#display").append(
					'<div class="card horizontal">' +
						'<div class="card-image">' +
							'<img class="z-depth-3" onerror="replaceCover(this)" src=' +
							book.cover.replace('..', '.') +
							' onclick="window.location.href=this.src">' +
						'</div>' +
						'<div class="card-stacked">' +
							'<div class="card-content">' +
								'<div class="card-title book-title" onclick="showFullText(this)">' + book.title + '</div>' +
								'<div class="card-details">' +
									'<p>作者：' + book.author + '</p>' +
									'<p>出版社：' + book.publisher + '</p>' +
									'<p>出版时间：' + book.pubdate + '</p>' +
								'</div>' +
							'</div>' +
						'</div>' + btnAttr +
					'</div>');
				});
				$("#placeholder").show();
				$("#book-confirm").show();
				// $("#" + operation).modal('close');
				$(".button-collapse").sideNav('hide');
			} else {
				Materialize.toast(response.errmsg, 3000);
			}
			$("#loading").hide();
		}
	);
}

function searchReservation() {
	$("#progress").show();
	$.get(
		'api/user/reservation/stuno/' + $("#search-stuno").val(),
		function(response) {
			if (response.errcode == 0) {
				var reservation = response.data;
				$("#reservation").html(
				'<div class="card-content">' +
					'<div class="reservation-title">订单详情</div>' +
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
							'<div class="row" id="reserved-books"></div>' +
							'<div class="reservation">' +
								'<button class="btn waves-effect waves-light red lighten-2" onclick="modifyReservation();">' +
									'<i class="material-icons right">edit</i>更改' +
								'</button>' +
								'<button class="btn waves-effect waves-light red lighten-2 modal-close" id="back">' +
									'<i class="material-icons right">replay</i>返回' +
								'</button>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</div>');

				$.each(reservation.books, function(i, book) {
					$("#reserved-books").append(
					'<div class="col s12 m4">' +
						'<div class="card blue-grey darken-1">' +
							'<div class="card-content white-text">' +
								'<div class="card-title book-title" onclick="showFullText(this)">' + book.title +
								'</div>' +
								'<div class="card-details">' +
									'<p>作者：' + book.author + '</p>' +
									'<p>出版社：' + book.publisher + '</p>' +
									'<p>出版日期：' + book.pubdate + '</p>' +
								'</div>' +
							'</div>' +
							'<div class="card-action center-align">' +
								'<a class="cover" href=' + book.cover + '>触碰或点击查看封面图片' +
									'<div class="cover-image">' +
										'<img onerror="replaceCover(this)"' +
										' src=' + book.cover + ' alt="封面图片">' +
									'</div>' +
								'</a>' +
							'</div>' +
						'</div>' +
					'</div>');
				});
				$("#search-reservation").modal('close');
				$(".button-collapse").sideNav('hide');
				$("#reservation").modal('open');
			} else {
				Materialize.toast(response.errmsg, 3000);
			}
			$("#progress").hide();
		}
	);
}

function modifyReservation() {
	$("#loading").css("display", "flex");
	$("#submit").html('<i class="material-icons right">send</i>确定修改');

	modifying = true, count = 0;
	list = ['0', '0', '0'];
	preList = ['0', '0', '0'];

	$.get(
		'api/user/reservation/stuno/' + $("#search-stuno").val(),
		function(response) {
			if (response.errcode == 0) {
				var reservation = response.data;

				$("#list-data").empty();
				
				$("label[for=stuname]").addClass("active");
				$("#stuname").val(reservation.stuname);
				$("label[for=stuno]").addClass("active");
				$("#stuno").val(reservation.stuno);
				$("label[for=contact]").addClass("active");
				$("#contact").val(reservation.contact);

				$("#stuno").prop('disabled', true);
				$("#dorm").val(reservation.dorm).material_select();
				$("#takeday").val(reservation.takeday).material_select();
				$("#taketime").val(reservation.taketime).material_select();

				$.each(reservation.books, function(i, book) {
					list[list.indexOf('0')] = book.id;
					preList[preList.indexOf('0')] = book.id;
					count++;

					$("#list-data").append(
					'<div class="card horizontal list-card">' +
						'<div class="card-image">' +
							'<img class="z-depth-3" onerror="replaceCover(this)"' +
							' src=' + book.cover + '>' +
						'</div>' +
						'<div class="card-stacked">' +
							'<div class="card-content">' +
								'<div class="card-title book-title" onclick="showFullText(this)">' + book.title + '</div>' +
								'<div class="card-details">' +
									'<p>' + book.author + '</p>' +
									'<p>' + book.publisher + '</p>' +
									'<p>' + book.pubdate + '</p>' +
								'</div>' +
							'</div>' +
						'</div><a class="btn-floating waves-effect waves-light red center-align btn-del" ' +
						'data-id=' + book.id + ' onclick="deleteFromList(this)">' +
						'<i class="material-icons">clear</i></a>' +
					'</div>');
				});
				$("#reservation").modal('close');
				$("#list-data").modal('open');
			} else {
				Materialize.toast(response.errmsg, 3000);
			}
			$("#loading").hide();
		}
	);
}

function addToList(val) {
	if (count >= 3) {
		modalAlert('列表书籍已达到选择上限');
		return;
	}

	if (list.indexOf(val.dataset.id) != -1) {
		modalAlert('每种书籍仅限选择一本哦');
		return;
	}

	list[list.indexOf('0')] = val.dataset.id;
	count++;
	val.innerText--;

	ele = val.previousSibling.children[0].children[1];
	$("#list-data").append(
	'<div class="card horizontal list-card">' +
		'<div class="card-image">' +
			'<img class="z-depth-3" onerror="replaceCover(this)" src=' +
				val.previousSibling.previousSibling.children[0].src +
			'>' +
		'</div>' +
		'<div class="card-stacked">' +
			'<div class="card-content">' +
				'<div class="card-title book-title" onclick="showFullText(this)">' +
					val.previousSibling.children[0].children[0].innerText +
				'</div>' +
				'<div class="card-details">' +
					'<p>' + ele.children[0].innerText + '</p>' +
					'<p>' + ele.children[1].innerText + '</p>' +
					'<p>' + ele.children[2].innerText + '</p>' +
				'</div>' +
			'</div>' +
		'</div><a class="btn-floating waves-effect waves-light red center-align btn-del" ' +
		'data-id=' + val.dataset.id + ' onclick="deleteFromList(this)">' +
		'<i class="material-icons">clear</i></a>' +
	'</div>');

	if (val.innerText == 0) {
		val.outerHTML = '<a class="btn-floating waves-effect waves-light grey center-align btn-add">0</a>';
	}
}

function deleteFromList(val) {
	count --;
	list[list.indexOf(val.dataset.id)] = '0';
	val.parentNode.outerHTML = '';
}

function modalAlert(content, title = '<i class="material-icons left">warning</i>系统提示') {
	$("#alert-title").html(title);
	$("#alert-content").html(content);
	$("#alert").modal('open');
}

function showFullText(val) {
	modalAlert(val.textContent, '<i class="material-icons left">book</i>书籍标题全文：');
}

function confirmChoose() {
	if (count == 0) {
		modalAlert('请选择预约书籍');
		return;
	}
	$("#reserve").modal('open');
}

$("#back").click(function() {
	$("#reservation").modal('close');
});

$("#return").click(function() {
	$(".button-collapse").sideNav('hide');
});

function replaceCover(ele) {
	ele.src = './pictures/default.png';
}
