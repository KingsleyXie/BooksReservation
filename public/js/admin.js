$(document).ready(function() {
	updating = false;
	prepare();
	bind();

	$.get('../api/admin/pms-status', function(response) {
		if (!response['books.view']) $(".pms-books-import").remove();
		if (!response['books.import']) $(".pms-books-import").remove();
		if (!response['books.update']) $(".pms-books-update").remove();
		if (!response['reservations.view']) $(".pms-reservations-view").remove();
	});

	$.get('../api/admin/book/all', function(response) {
		if (response.errcode == 0) {
			$.each(response.data, function(i, book) {
				var row = Math.round((parseInt(i) + 2) / 4);
				if (i % 4 == 0)
					$("#books").append(
					'<div class="row" id="row' + row + '"></div>'
					);

				$("#row" + row).append(
				'<div class="col s12 m3">' +
					'<div class="card blue-grey darken-1">' +
						'<div class="card-content white-text">' +
							'<p class="right-align">#' + book.id + '</p>' +
							'<div class="card-title book-title"' +
							'onclick="showFullText(this)">' + book.title + '</div>' +
							'<div class="card-details">' +
								'<p>作者：' + book.author + '</p>' +
								'<p>出版社：' + book.publisher + '</p>' +
								'<p>出版日期：' + book.pubdate + '</p>' +
								'<div class="admin-info">' +
									'<p>剩余数量：' + book.quantity + '</p>' +
									'<p>入库时间：' + book.imported + '</p>' +
									'<p>更新时间：' + book.updated + '</p>' +
								'</div>' +
							'</div>' +
						'</div>' +
						'<div class="card-action center-align">' +
							'<a class="cover" href=' + book.cover + '>' +
								'触碰或点击查看封面图片' +
								'<div class="cover-image">' +
									'<img src=' + book.cover + ' alt="封面图片" >' +
								'</div>' +
							'</a>' +
						'</div>' +
					'</div>' +
				'</div>');
			});
		} else {
			Materialize.toast(response.errmsg, 3000);
		}
		$("#loading").hide();

		if (response.errcode == -1) $("#login").show(1700);
	});
});

function prepare() {
	$(".button-collapse").sideNav();
	$(".modal").modal();
	$("select").material_select();

	$("#loading").css("display", "flex");

	$("#bookID").bind('keypress',function(e) {
		if(e.keyCode == 13) getBookByID();
	});

	$("#return").click(function() {
		$(".button-collapse").sideNav('hide');
	});

	$("#multi").change(function () {
		var txt = $("#author").val();

		if ($(this).prop('checked')) {
			$("label[for=author]").addClass("active");
			$("#author").val(txt + ' 等');
		} else {
			var len = txt.length - 2;
			if (txt.indexOf(' 等') == len) {
				$("#author").val(txt.substr(0, len));
			}
		}
	});

	$("#barcode").modal({
		ready: function() {
			$("#books").slideUp(700);
			window.setTimeout(function () {
				$("#isbn").focus();
			}, 0);
		},
		complete: function() {
			location.reload();
		}
	});

	$("#book").modal({
		ready: function() {
			$("#books").slideUp(700);
		},
		complete: function() {
			location.reload();
		}
	});
}

function bind() {
	$("#barcode").submit(function(e) {
		e.preventDefault();
		$("#progress").show();

		$.post(
			'../api/admin/book/add/isbn',
			$(this).serialize(),
			function(response) {
				if (response.errcode == 0) {
					Materialize.toast(response.data, 1700);
				} else {
					Materialize.toast(response.errmsg, 2000);
				}

				$("#isbn").val('');
				$("#progress").hide();
			}
		);
	});

	$("#book").submit(function(e) {
		e.preventDefault();
		$.post(
			'../api/admin/book/' +
			(updating ? 'update/' + $("#bookID").val() : 'add/raw'),
			$(this).serialize(),
			function(response) {
				if (response.errcode == 0) {
					Materialize.toast(response.data, 1700);
					window.setTimeout(function() {
						location.reload();
					}, 2000);
				} else {
					Materialize.toast(response.errmsg, 3000);
				}
			}
		);
	});

	$("#login").submit(function(e) {
		e.preventDefault();
		$.post(
			'../api/admin/login',
			$(this).serialize(),
			function(response) {
				if (response.errcode == 0) {
					Materialize.toast('登录成功！', 1700);
					setTimeout(function () {
						location.reload();
					}, 2000);
				} else {
					Materialize.toast(response.errmsg, 3000);
				}
			}
		);
	});

	$("#logout").click(function() {
		$.get(
			'../api/admin/logout',
			function(response) {
				if (response.errcode == 0) {
					Materialize.toast('退出系统成功', 1700);
					setTimeout(function () {
						location.reload();
					}, 2000);
				} else {
					Materialize.toast(response.errmsg, 3000);
				}
			}
		);
	});
}

function getBookByID() {
	$("#progress").show();
	$.get(
		'../api/admin/book/id/' + $("#bookID").val(),
		function(response) {
			if (response.errcode == 0) {
				var book = response.data;
				$("#title").val(book.title);
				$("label[for=title]").addClass("active");
				$("#author").val(book.author);
				$("label[for=author]").addClass("active");
				$("#publisher").val(book.publisher);
				$("label[for=publisher]").addClass("active");
				$("#pubdate").val(book.pubdate);
				$("label[for=pubdate]").addClass("active");
				$("#cover").val(book.cover);
				$("#quantity").val(book.quantity);
				$("label[for=quantity]").addClass("active");

				$("#multi").prop("checked",
					book.author.indexOf(' 等') == book.author.length - 2
				);

				updating = true;
				$("#form-btn").text('确认更新');
				$("#form-title").text('更新书籍信息');
				$("#progress").hide();
				$("#update").modal('close');
				$("#book").modal('open');

				window.setTimeout(function () {
					$("#quantity").focus();
				}, 0);
			} else {
				Materialize.toast(response.errmsg, 3000);
			}
		}
	);
}

function showFullText(val) {
	$("#alert-content").text(val.textContent);
	$("#alert").modal('open');
}
