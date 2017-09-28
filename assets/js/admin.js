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

	$.get(
		'../assets/API/booksAdmin.php',
		function(response) {
			if (response[0].code == 1) {
				alert('请登录系统！');
				window.location.href = './login.html';
			}
			if (response[0].code == 2) {
				Materialize.toast('数据库中暂无书籍信息', 3000);
			}

			if (response[0].code == 0) {
				for (var i = 1; i < response.length; i++) {

					var rowNo = Math.round((i + 1) / 4);
					if (i % 4 == 1) {
						$("#books").append('<div class="row" id="row' + rowNo + '"></div>');
					}

					var MultipleAuthor = response[i].isMultipleAuthor == 1 ? ' 等' : '';
					var Category = response[i].bookCategory == 'CategoryA' ?
					
					'<p>分类：教材课本</p><p>年级：' + response[i].grade +
					'&nbsp;&nbsp;&nbsp;专业：' + response[i].major + '</p>' :
					
					'<p>分类：课外书籍</p><p>详细类别：' + response[i].extracurricularCategory + '</p>';

					if (response[i].image == './assets/pictures/defaultCover.png') {
						response[i].image = '../assets/pictures/defaultCover.png'
					}

					$("#row" + rowNo).append(
					'<div class="col s12 m3">' + 
						'<div class="card blue-grey darken-1">' + 
							'<div class="card-content white-text">' + 
								'<div class="card-title">' + response[i].title + '</div>' + 
								'<div class="card-details">' + 
									'<p>作者：' + response[i].author + MultipleAuthor + '</p>' + 
									'<p>出版社：' + response[i].press + '</p>' + 
									'<p>出版日期：' + response[i].pubdate + '</p>' + 
									'<div class="admin-info">' + 
										'<p><strong>书籍 ID：' + response[i].bookID + '</strong></p>' + 
										'<p>剩余数量：' + response[i].remainingAmount + '</p>' + Category +
										'<p>入库时间：' + response[i].importTime + '</p>' + 
										'<p>更新时间：' + response[i].updateTime + '</p>' + 
									'</div>' + 
								'</div>' + 
							'</div>' + 
							'<div class="card-action center-align">' + 
								'<a class="cover" href=' + response[i].image + '>' + 
									'触碰或点击查看封面图片' + 
									'<div class="cover-image">' + 
										'<img src=' + response[i].image + ' alt="封面图片" >' + 
									'</div>' + 
								'</a>' + 
							'</div>' + 
						'</div>' + 
					'</div>');
				}
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
				'../assets/API/add.php',
				$(this).serialize(),
				function(response) {
					if (response.code == 1) {
						alert('请登录系统！');
						window.location.href = './login';
					}
					if (response.code == 0) {
						Materialize.toast('书籍添加成功！', 3000);
						window.setTimeout(function() {
							window.location.href = './';
						}, 3600);
					} else {
						alert(response.errMsg);
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
				'../assets/API/update.php',
				$(this).serialize(),
				function(response) {
					if (response.code == 1) {
						alert('请登录系统！');
						window.location.href = './login.html';
					}
					if (response.code == 0) {
						Materialize.toast('书籍信息更新成功！', 3000);
						window.setTimeout(function() {
							window.location.href = './';
						}, 3600);
					} else {
						alert(response.errMsg);
					}
				}
			);
		}
	});
});



function logout() {
	$.post(
		'../assets/API/admin.php',
		{ 'type':2 },
		function(response) {
			if (response.code == 0) {
				alert('退出系统成功');
			}
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
		'../assets/API/bookInfoAPI.php',
		{ 'ISBN' : $("#ISBN").val() },
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
				$("#is-multiple-author").prop("checked", response.isMultipleAuthor);

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
		'../assets/API/booksAdmin.php',
		{ "bookID": $("#bookID").val() },
		function(response) {
			if (response[0].code == 1) {
				alert('请登录系统！');
				window.location.href = './login.html';
			};
			if (response[0].code == 2) {
				alert('未找到对应书籍，请检查输入 ID 是否正确！');
				$("#upd-progress").hide();
			};
			if (response[0].code == 0) {
				$("#upd-progress").hide();
				$("#upd-title").val(response[1].title);
				$("label[for=upd-title]").addClass("active");
				$("#upd-author").val(response[1].author);
				$("label[for=upd-author]").addClass("active");
				$("#upd-press").val(response[1].press);
				$("label[for=upd-press]").addClass("active");
				$("#upd-pubdate").val(response[1].pubdate);
				$("label[for=upd-pubdate]").addClass("active");
				$("#upd-image").val(response[1].image);
				$("label[for=upd-image]").addClass("active");
				$("#upd-" + (response[1].bookCategory == 'CategoryA' ? 'categoryA' : 'categoryB')).prop("checked", true);

				if (response[1].bookCategory == "CategoryA") {
					updCategory('upd-categoryA');
					$("#upd-grade").val(response[1].grade);	$("#upd-grade").material_select();
					$("#upd-major").val(response[1].major);	$("#upd-major").material_select();
				}
				if (response[1].bookCategory == "CategoryB") {
					updCategory('upd-categoryB');
					$("#upd-extracurricular-category").val(response[1].extracurricularCategory);
					$("#upd-extracurricular-category").material_select();
				}

				$("#upd-is-multiple-author").prop("checked", response[1].isMultipleAuthor);
				window.setTimeout(function () {
					$("#upd-remaining-amount").val(response[1].remainingAmount);
					$("#upd-remaining-amount").focus();
				}, 0);
				$("#update-init").hide();
				$("#upd-books").show();
			}
		}
	);
}

function Check() {
	if ($("#title").val('') ||
		$("#author").val('') ||
		$("#press").val('') ||
		$("#pubdate").val('') ||
		$("#remaining-amount").val('') ||
		!($("#categoryA").prop("checked") ||  
		$("#categoryB").prop("checked"))) {
			alert('请将书籍信息填写完整！');
			return false;
	}
	if ($("#categoryA").prop("checked") && 
		($("#grade").val('') ||
		$("#major").val(''))) {
			alert('请将分类信息填写完整！');
			return false;
	}
	if ($("#categoryB").prop("checked") && 
		$("#extracurricular-category").val('')) {
			alert('请将分类信息填写完整！');
			return false;
	}
	return true;
}

function updCheck() {
	if ($("#upd-title").val('') ||
		$("#upd-author").val('') ||
		$("#upd-press").val('') ||
		$("#upd-pubdate").val('') ||
		$("#upd-remaining-amount").val('') ||
		!($("#upd-categoryA").prop("checked") ||  
		$("#upd-categoryB").prop("checked"))) {
			alert('请将书籍信息填写完整！');
			return false;
	}
	if ($("#upd-categoryA").prop("checked") && 
		($("#upd-grade").val('') ||
		$("#upd-major").val(''))) {
			alert('请将分类信息填写完整！');
			return false;
	}
	if ($("#upd-categoryB").prop("checked") && 
		$("#upd-extracurricular-category").val('')) {
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
