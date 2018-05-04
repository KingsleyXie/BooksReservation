$(document).ready(function() {
	$(".button-collapse").sideNav();
	$(".modal").modal();
	$("select").material_select();

	updating = false;
	$("#loading").css("display", "flex");
	$("#ISBN").bind('keypress',function(e) {
		if(e.keyCode == 13) inputDataViaISBN();
	});

	$("#bookID").bind('keypress',function(e) {
		if(e.keyCode == 13) getBookByID();
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

	$("#book").modal({
		complete: function() {
			window.location.href = 'books';
		}
	});

	$.get(
		'../api/admin/book/all',
		function(response) {
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
		}
	);

	$("#book").submit(function(e) {
		e.preventDefault();
		if ($("#book-info").css("display") == "block"
			&& check()) {
			$.post(
				'../assets/API/admin.php',
				$(this).serialize() + '&operation=' + (updating ? 'update' : 'add'),
				function(response) {
					if (response.code == 0) {
						Materialize.toast('书籍' + (updating ? '信息更新' : '添加')  + '成功！', 1700);
						window.setTimeout(function() {
							window.location.href = './';
						}, 2000);
					} else {
						Materialize.toast(response.errmsg, 3000);
					}
				}
			);
		}
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
						window.location.href = 'books';
					}, 2000);
				} else {
					Materialize.toast(response.errmsg, 3000);
				}
			}
		);
	});
});

function update() {
	updating = true;
	$("#update-init").show();
	$("#add-init").hide();
	$("#form-btn").text('确认更新');
	$("#form-title").text('更新书籍信息');
	$("#book").modal('open');
}

function inputDataManually() {
	$("#add-init").hide(700);
	$("#update-init").hide(700);
	$("#book-info").show(700);
	window.setTimeout(function () {
		$("#remaining-amount").focus();
	}, 0);
}

function inputDataViaISBN() {
	$("#progress").show();
	$.post(
		'../assets/API/ISBN_API.php',
		'ISBN=' + $("#ISBN").val(),
		function(response) {
			if (response.errcode == 0) {
				$("#title").val(response.title);
				$("label[for=title]").addClass("active");
				$("#author").val(response.author);
				$("label[for=author]").addClass("active");
				$("#press").val(response.press);
				$("label[for=press]").addClass("active");
				$("#pubdate").val(response.pubdate);
				$("label[for=pubdate]").addClass("active");
				$("#image").val(response.image);
				$("#is-multiple-author").prop("checked", parseInt(response.isMultipleAuthor));
			} else {
				Materialize.toast(response.errmsg, 3000);
				$("#ISBN").val('');
			}
			$("#progress").hide();
			inputDataManually();
		}
	);
}

function getBookByID() {
	$("#progress").show();
	$.get(
		'../api/admin/book/id/' + $("#bookID").val(),
		function(response) {
			if (response.errcode == 0) {
				book = response[0];
				$("#title").val(book.title);
				$("label[for=title]").addClass("active");
				$("#author").val(book.author);
				$("label[for=author]").addClass("active");
				$("#press").val(book.press);
				$("label[for=press]").addClass("active");
				$("#pubdate").val(book.pubdate);
				$("label[for=pubdate]").addClass("active");
				$("#image").val(book.image);
				$("#remaining-amount").val(book.remainingAmount);
				$("#" + (book.bookCategory == 'CategoryA' ? 'categoryA' : 'categoryB')).prop("checked", true);

				$("#is-multiple-author").prop("checked", parseInt(book.isMultipleAuthor));
				
				inputDataManually();
			} else {
				Materialize.toast(response.errmsg, 3000);
			}
			$("#progress").hide();
		}
	);
}

function showFullText(val) {
	$("#alert-content").text(val.textContent);
	$("#alert").modal('open');
}

function returnToMainPage() {
	$(".button-collapse").sideNav('hide');
}

function logout() {
	$.get(
		'../api/admin/logout',
		function(response) {
			if (response.errcode == 0) {
				Materialize.toast('退出系统成功', 1700);
				setTimeout(function () {
					window.location.href = 'books';
				}, 2000);
			} else {
				Materialize.toast(response.errmsg, 3000);
			}
		}
	);
}
