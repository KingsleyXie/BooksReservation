$(document).ready(function() {
	$(".button-collapse").sideNav();
	$(".modal").modal();
	$("select").material_select();
	
	$("#ISBN").bind('keypress',function(e) {
		if(e.keyCode == 13) getData();
	});

	$("#bookID").bind('keypress',function(e) {
		if(e.keyCode == 13) getBook();
	});

	$.post(
		'../assets/API/admin.php',
		'operation=books',
		function(response) {
			if (response.code == 6) {
				alert('请登录系统！');
				window.location.href = './login.html';
			}

			if (response.code == 0) {
				$.each(response, function(i, book) {
					if (i == 'code') return true;
					rowNo = Math.round((parseInt(i) + 2) / 4);
					if (i % 4 == 0)
						$("#books").append('<div class="row" id="row' + rowNo + '"></div>');

					MultipleAuthor = book.isMultipleAuthor == '1' ? ' 等' : '';
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
								'<div class="card-title">' + book.title + '</div>' + 
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
		}
	);

	$("#add").submit(function(e) {
		e.preventDefault();
		if ($("#book-info").css("display") == "block"
			&& Check()
			&& ConfirmInfo()) {
			$.post(
				'../assets/API/admin.php',
				$(this).serialize() + '&operation=add',
				function(response) {
					if (response.code == 0) {
						Materialize.toast('书籍添加成功！', 3000);
						window.setTimeout(function() {
							window.location.href = './';
						}, 3600);
					} else {
						Materialize.toast(response.errMsg, 3000);
					}
				}
			);
		}
	});

	$("#update").submit(function(e) {
		e.preventDefault();
		if ($("#upd-books").css("display") == 'block'
			&& updCheck()
			&& updConfirmInfo()) {
			$.post(
				'../assets/API/admin.php',
				$(this).serialize() + '&operation=update',
				function(response) {
					if (response.code == 0) {
						Materialize.toast('书籍信息更新成功！', 3000);
						window.setTimeout(function() {
							window.location.href = './';
						}, 3600);
					} else {
						Materialize.toast(response.errMsg, 3000);
					}
				}
			);
		}
	});
});

function logout() {
	$.post(
		'../assets/API/admin.php',
		'operation=logout',
		function(response) {
			if (response.code == 0)
				alert('退出系统成功');
			window.location.href = '../';
		}
	);
}

function selectCategory(val) {
	if (val == "categoryA") {
		$("#book-categoryA").show();
		$("#book-categoryB").hide();
	}
	if (val == "categoryB") {
		$("#book-categoryA").hide();
		$("#book-categoryB").show();
	}
}

function updCategory(val) {
	if (val == "upd-categoryA") {
		$("#upd-book-categoryA").show();
		$("#upd-book-categoryB").hide();
	}
	if (val == "upd-categoryB") {
		$("#upd-book-categoryA").hide();
		$("#upd-book-categoryB").show();
	}
}

function inputData() {
	$("#add-init").hide();
	$("#book-info").show();
	$("#book-category").show();
}

function getData() {
	$("#progress").show();
	$.post(
		'../assets/API/ISBN_API.php',
		'ISBN=' + $("#ISBN").val(),
		function(response) {
			$("#progress").hide();
			if (response.code == 1) {
				alert('未找到书籍信息，请手动录入相关数据');
				$("#ISBN").val('');
				inputData();
			}
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
				$("label[for=image]").addClass("active");
				$("#is-multiple-author").prop("checked", parseInt(response.isMultipleAuthor));

				window.setTimeout(function () {
					$("#remaining-amount").focus();
				}, 0);

				$("#add-init").hide();
				$("#book-info").show();
				$("#book-category").show();
			}
		}
	);
}

function getBook() {
	$("#upd-progress").show();
	$.post(
		'../assets/API/admin.php',
		'operation=books&bookID=' + $("#bookID").val(),
		function(response) {
			if (response.code == 0) {
				$("#upd-progress").hide();
				$("#upd-title").val(response[0].title);
				$("label[for=upd-title]").addClass("active");
				$("#upd-author").val(response[0].author);
				$("label[for=upd-author]").addClass("active");
				$("#upd-press").val(response[0].press);
				$("label[for=upd-press]").addClass("active");
				$("#upd-pubdate").val(response[0].pubdate);
				$("label[for=upd-pubdate]").addClass("active");
				$("#upd-image").val(response[0].image);
				$("label[for=upd-image]").addClass("active");
				$("#upd-" + (response[0].bookCategory == 'CategoryA' ? 'categoryA' : 'categoryB')).prop("checked", true);

				if (response[0].bookCategory == "CategoryA") {
					updCategory('upd-categoryA');
					$("#upd-grade").val(response[0].grade).material_select();
					$("#upd-major").val(response[0].major).material_select();
				}
				if (response[0].bookCategory == "CategoryB") {
					updCategory('upd-categoryB');
					$("#upd-extracurricular-category").val(response[0].extracurricularCategory).material_select();
				}

				$("#upd-is-multiple-author").prop("checked", parseInt(response[0].isMultipleAuthor));
				window.setTimeout(function () {
					$("#upd-remaining-amount").val(response[0].remainingAmount);
					$("#upd-remaining-amount").focus();
				}, 0);
				$("#update-init").hide();
				$("#upd-books").show();
			} else {
				Materialize.toast(response.errMsg, 3000);
				$("#upd-progress").hide();
			}
		}
	);
}

function Check() {
	if ($("#title").val() == '' ||
		$("#author").val() == '' ||
		$("#press").val() == '' ||
		$("#pubdate").val() == '' ||
		$("#remaining-amount").val() == '' ||
		!($("#categoryA").prop("checked") ||  
		$("#categoryB").prop("checked"))) {
			alert('请将书籍信息填写完整！');
			return false;
	}
	if ($("#categoryA").prop("checked") && 
		($("#grade").val() == '' ||
		$("#major").val() == '')) {
			alert('请将分类信息填写完整！');
			return false;
	}
	if ($("#categoryB").prop("checked") && 
		$("#extracurricular-category").val() == '') {
			alert('请将分类信息填写完整！');
			return false;
	}
	return true;
}

function updCheck() {
	if ($("#upd-title").val() == '' ||
		$("#upd-author").val() == '' ||
		$("#upd-press").val() == '' ||
		$("#upd-pubdate").val() == '' ||
		$("#upd-remaining-amount").val() == '' ||
		!($("#upd-categoryA").prop("checked") ||  
		$("#upd-categoryB").prop("checked"))) {
			alert('请将书籍信息填写完整！');
			return false;
	}
	if ($("#upd-categoryA").prop("checked") && 
		($("#upd-grade").val() == '' ||
		$("#upd-major").val() == '')) {
			alert('请将分类信息填写完整！');
			return false;
	}
	if ($("#upd-categoryB").prop("checked") && 
		$("#upd-extracurricular-category").val() == '') {
			alert('请将分类信息填写完整！');
			return false;
	}
	return true;
}

function ConfirmInfo() {
	var isMA = $("#is-multiple-author").prop("checked") ? '是' : '否';
	if ($("#categoryA").prop("checked")) {
		var cataInfo = '\n书籍分类：教材课本' +
		'\n年级：' + $("#grade").val() +
		'\n专业：' + $("#major").val();
	}
	if ($("#categoryB").prop("checked")) {
		var cataInfo = '\n书籍分类：课外书籍' +
		'\n详细类别：' + $("#extracurricular-category").val();
	}
	var confirmPrompt = '请再次确认该书籍信息：\n' +
		'\n书名：' + $("#title").val() +
		'\n作者：' + $("#author").val() +
		'\n是否有多位作者：' + isMA +
		'\n出版社：' + $("#press").val() +
		'\n剩余数量：' + $("#remaining-amount").val() +
		'\n图片链接：' + $("#image").val() + cataInfo;
	return confirm(confirmPrompt);
}

function updConfirmInfo() {
	var isMA = $("#upd-is-multiple-author").prop("checked") ? '是' : '否';
	if ($("#upd-categoryA").prop("checked")) {
		var cataInfo = '\n书籍分类：教材课本' +
		'\n年级：' + $("#upd-grade").val() +
		'\n专业：' + $("#upd-major").val();
	}
	if ($("#upd-categoryB").prop("checked")) {
		var cataInfo = '\n书籍分类：课外书籍' +
		'\n详细类别：' + $("#upd-extracurricular-category").val();
	}
	var confirmPrompt = '请再次确认该书籍信息：\n' +
		'\n书籍 ID：' + $("#bookID").val() +
		'\n书名：' + $("#upd-title").val() +
		'\n作者：' + $("#upd-author").val() +
		'\n是否有多位作者：' + isMA +
		'\n出版社：' + $("#upd-press").val() +
		'\n剩余数量：' + $("#upd-remaining-amount").val() +
		'\n图片链接：' + $("#upd-image").val() + cataInfo;
	return confirm(confirmPrompt);
}
