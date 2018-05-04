$(document).ready(function() {
	$(".button-collapse").sideNav();
	$(".modal").modal();
	$("select").material_select();
	
	updating = false;
	$("#loading").css("display", "flex");
	$("#ISBN").bind('keypress',function(e) { if(e.keyCode == 13) inputDataViaISBN(); });
	$("#bookID").bind('keypress',function(e) { if(e.keyCode == 13) getBookByID(); });
	$("#book").modal({complete: function() {window.location.href = './';}});

	$.post(
		'../assets/API/admin.php',
		'operation=books',
		function(response) {
			if (response.code == 0) {
				$.each(response, function(i, book) {
					if (i == 'code') return true;
					rowNo = Math.round((parseInt(i) + 2) / 4);
					if (i % 4 == 0)
						$("#books").append('<div class="row" id="row' + rowNo + '"></div>');

					MultipleAuthor = book.isMultipleAuthor == 1 ? ' 等' : '';
					Category = book.bookCategory == 'CategoryA' ?
						'<p>分类：教材课本</p><p>年级：' + book.grade +
						'&nbsp;&nbsp;&nbsp;专业：' + book.major + '</p>' :
						'<p>分类：课外书籍</p><p>详细类别：' + book.extracurricularCategory + '</p>';

					if (book.image == './assets/pictures/defaultCover.png') {
						book.image = '../assets/pictures/defaultCover.png'
					}

					$("#row" + rowNo).append(
					'<div class="col s12 m3">' + 
						'<div class="card blue-grey darken-1">' + 
							'<div class="card-content white-text">' + 
								'<div class="card-title book-title" onclick="showFullText(this)">' + book.title + '</div>' + 
								'<div class="card-details">' + 
									'<p>作者：' + book.author + MultipleAuthor + '</p>' + 
									'<p>出版社：' + book.press + '</p>' + 
									'<p>出版日期：' + book.pubdate + '</p>' + 
									'<div class="admin-info">' + 
										'<p>书籍 ID：' + book.bookID + '</p>' + 
										'<p>剩余数量：' + book.remainingAmount + '</p>' + Category +
										'<p>入库时间：' + book.importTime + '</p>' + 
										'<p>更新时间：' + book.updateTime + '</p>' + 
									'</div>' + 
								'</div>' + 
							'</div>' + 
							'<div class="card-action center-align">' + 
								'<a class="cover" href=' + book.image + '>' + 
									'触碰或点击查看封面图片' + 
									'<div class="cover-image">' + 
										'<img src=' + book.image + ' alt="封面图片" >' + 
									'</div>' + 
								'</a>' + 
							'</div>' + 
						'</div>' + 
					'</div>');
				});
			} else {
				Materialize.toast(response.errMsg, 3000);
			}
			$("#loading").hide();

			if (response.code == 5) $("#login").show(1700);
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
						Materialize.toast(response.errMsg, 3000);
					}
				}
			);
		}
	});

	$("#login").submit(function(e) {
		e.preventDefault();
		$.post(
			'../assets/API/admin.php',
			$(this).serialize() + '&operation=login',
			function(response) {
				if (response.code == 0) {
					Materialize.toast('登录成功！', 1700);
					setTimeout(function () {
						window.location.href = './';
					}, 2000);
				} else {
					Materialize.toast(response.errMsg, 3000);
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
			if (response.code == 0) {
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
				Materialize.toast(response.errMsg, 3000);
				$("#ISBN").val('');
			}
			$("#progress").hide();
			inputDataManually();
		}
	);
}

function selectCategory(val) {
	switch(val) {
		case 'categoryA':
			$("#book-categoryB").hide(300);
			$("#book-categoryA").show(300);
			break;
		case 'categoryB':
			$("#book-categoryA").hide(300);
			$("#book-categoryB").show(300);
			break;
	}
	$("#form-btn").show();
}

function getBookByID() {
	$("#progress").show();
	$.post(
		'../assets/API/admin.php',
		'operation=books&bookID=' + $("#bookID").val(),
		function(response) {
			if (response.code == 0) {
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

				switch(book.bookCategory) {
					case 'CategoryA':
						selectCategory('categoryA');
						$("#grade").val(book.grade).material_select();
						$("#major").val(book.major).material_select();
						break;
					case 'CategoryB':
						selectCategory('categoryB');
						$("#extracurricular-category").val(book.extracurricularCategory).material_select();
						break;
				}

				$("#is-multiple-author").prop("checked", parseInt(book.isMultipleAuthor));
				
				inputDataManually();
			} else {
				Materialize.toast(response.errMsg, 3000);
			}
			$("#progress").hide();
		}
	);
}

function check() {
	if ($("#title").val() == ''
		|| $("#author").val() == ''
		|| $("#press").val() == ''
		|| $("#pubdate").val() == ''
		|| $("#remaining-amount").val() == ''
		|| !($("#categoryA").prop("checked")
		|| $("#categoryB").prop("checked"))) {
			alert('请将书籍信息填写完整！');
			return false;
	}
	if ($("#categoryA").prop("checked")
		&& ($("#grade").val() == ''
		|| $("#major").val() == '')) {
			alert('请将分类信息填写完整！');
			return false;
	}
	if ($("#categoryB").prop("checked")
		&& $("#extracurricular-category").val() == '') {
			alert('请将分类信息填写完整！');
			return false;
	}
	return true;
}

function showFullText(val) {
	$("#alert-content").text(val.textContent);
	$("#alert").modal('open');
}

function returnToMainPage() {
	$(".button-collapse").sideNav('hide');
}

function logout() {
	$.post(
		'../assets/API/admin.php',
		'operation=logout',
		function(response) {
			Materialize.toast('退出系统成功', 1700);
			setTimeout(function () {
				window.location.href = './';
			}, 2000);
		}
	);
}
