// Currently there is no time to refactory these......
searching = false;
modifying = false;

count = 0;
list = ['0', '0', '0'];
preList = ['0', '0', '0'];

perpage = 10, pages = 0;
loadimg = true;
defaultImg = './pictures/default.png';



$(document).ready(function() {
	$(".modal").modal();
	$(".button-collapse").sideNav();
	$("select").material_select();

	$("#search-reservation").submit(function(e) {
		e.preventDefault();
		searchReservation();
	});

	reserveBind();

	$("#page-limit").on('input', function() {
		perpage = parseInt($(this).val());
	});

	$("#settings-loadimg").click(function() {
		loadimg = $(this).prop('checked');
	});

	$("#settings-confirm").click(function() {
		$("#settings").modal('close');
		$(".button-collapse").sideNav('hide');
		refreshPages();
	});

	$("#return").click(function() {
		$(".button-collapse").sideNav('hide');
	});

	$(".list-all").click(function() {
		$("#all").submit();
		$(".button-collapse").sideNav('hide');
	});

	$("#all").submit(function(e) {
		e.preventDefault();
		searching = false;

		refreshPages();

		$('#all').modal('close');
		$(".button-collapse").sideNav('hide');
	});

	$("#search").submit(function(e) {
		e.preventDefault();
		searching = true;

		refreshPages();

		$(".button-collapse").sideNav('hide');
	});

	$("#all").modal('open');
});

function reserveBind() {
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
				'&stuno=' + $("#stuno").val() +
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
}

function refreshPages() {
	if (searching) {
		$.post(
			'api/user/book/search/count',
			$("#search").serialize(),
			function(response) {
				if (response.data == 0) {
					Materialize.toast('未找到相关书籍，换个关键词试试吧', 3000);
				} else {
					$('#search').modal('close');
					resetPagination(response.data);
				}
			}
		);
	} else {
		$.get(
			'api/user/book/all/count',
			function(response) {
				if (response.data == 0) {
					Materialize.toast('数据库还是空的，晚点再来看看吧', 3000);
				} else {
					resetPagination(response.data);
				}
			}
		);
	}
}

function resetPagination(rows) {
	pages = Math.ceil(rows / perpage);

	var links = (pages > 5 ? 5 : pages);
	$(".page-item:gt(1):lt(-2)>a").remove();

	for (var i = 0; i < links; i++) {
		$(".page-item:eq(-2)").before(
			'<li class="page-item">' +
				'<a class="page-link">' + (i + 1) + '</a>' +
			'</li>'
		);
	}

	togglePage(1);
	$(".page-link").click(function() {
		togglePage($(this).text());
	});
}

function togglePage(page) {
	var curr = parseInt($(".page-item.active").text());
	switch(page) {
		case '«':
			togglePage(1);
			break;
		case '‹':
			togglePage(curr - 1);
			break;
		case '›':
			togglePage(curr + 1);
			break;
		case '»':
			togglePage(pages);
			break;

		default:
			page = parseInt(page);
			if (page <= 0 || page > pages || page == curr) return;
			freshPagination(page);

			if (searching) {
				Materialize.toast('正在加载第 ' + page + ' 页搜索结果', 1700);
				$.post(
					'api/user/book/search/page/' + page + '/limit/' + perpage,
					$("#search").serialize(),
					function(response) {
						display(response.data);
					}
				);
			} else {
				Materialize.toast('正在加载第 ' + page + ' 页书籍数据', 1700);
				$.get(
					'api/user/book/all/page/' + page + '/limit/' + perpage,
					function(response) {
						display(response.data);
					}
				);
			}
			break;
	}
}

function freshPagination(newPage) {
	$(".page-item.disabled").removeClass("disabled");

	if (newPage == 1) {
		$(".page-item:eq(1)").addClass("disabled");
	}

	if (newPage == pages) {
		$(".page-item:eq(-2)").addClass("disabled");
	}

	// IF pages < 5: just display all the page buttons
	// ELSE: display 5 page buttons and make sure the active page is centered
	var start = ((pages < 5 || newPage < 3) ? 1 :
		(
			(pages - newPage) < 3 ? (pages - 4) : (newPage - 2))
		);

	$.each($(".page-item:gt(1):lt(-2)>a"), function(i) {
		var n = start + i;
		$(this).text(n);
		if (n == newPage) {
			$(".page-item.active").removeClass("active");
			$(this).parent().addClass("active");
		}
	});
}

function display(data) {
	$("#loading").css("display", "flex");

	$("#display").empty();
	$.each(data, function(i, book) {
		var btnAttr = book.quantity == 0 ?
			'<a class="btn-floating waves-effect waves-light grey center-align btn-add">0</a>' :
			'<a class="btn-floating waves-effect waves-light red center-align btn-add" ' +
			'data-id=' + book.id + ' onclick="addToList(this)">' +
			book.quantity + '</a>';

		$("#display").append(
		'<div class="card horizontal">' +
			'<div class="card-image">' +
				'<img class="z-depth-3" onerror="replaceCover(this)" src=' +
				(loadimg ? book.cover.replace('..', '.') : defaultImg) +
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

	$("#loading").hide();
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
								'<button class="btn waves-effect waves-light red lighten-2 modal-close">' +
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
										' src=' + (loadimg ? book.cover : defaultImg) +
										' alt="封面图片">' +
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
							' src=' + (loadimg ? book.cover : defaultImg) + '>' +
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
						'<i class="material-icons">delete_forever</i></a>' +
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
		'<i class="material-icons">delete_forever</i></a>' +
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

function replaceCover(ele) {
	ele.src = defaultImg;
}

function modalAlert(content, title =
	'<i class="material-icons left">warning</i>系统提示') {
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
