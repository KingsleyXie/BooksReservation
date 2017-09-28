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
						document.getElementById("books").innerHTML += 
							'<div class="row" id="row' + rowNo + '"></div>';
					}

					var MultipleAuthor = response[i].isMultipleAuthor == 1 ? ' 等' : '';
					var Category = response[i].bookCategory == 'CategoryA' ?
					'<p>分类：教材课本</p><p>年级：' + response[i].grade +
					'&nbsp;&nbsp;&nbsp;专业：' + response[i].major + '</p>' :
					'<p>分类：课外书籍</p><p>详细类别：' + response[i].extracurricularCategory + '</p>';

					if (response[i].image == './assets/pictures/defaultCover.png') {
						response[i].image = '../assets/pictures/defaultCover.png'
					}

					document.getElementById("row" + rowNo).innerHTML += 
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
						'</div>';
					}
			}
			$("#loading").hide();
		}
	);
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
				document.getElementById("ISBN").value = '';
				inputData();
			}
			if (response.code == 0) {
				document.querySelector("#title + label").className = 'active';
				document.getElementById("title").value = response.title;
				document.querySelector("#author + label").className = 'active';
				document.getElementById("author").value = response.author;
				document.querySelector("#press + label").className = 'active';
				document.getElementById("press").value = response.press;
				document.querySelector("#pubdate + label").className = 'active';
				document.getElementById("pubdate").value = response.pubdate;
				document.querySelector("#image + label").className = 'active';
				document.getElementById("image").value = response.image;

				var isMA = response.isMultipleAuthor == 1 ? true: false;
				document.getElementById("is-multiple-author").checked = isMA;
				window.setTimeout(function () {
					document.getElementById("remaining-amount").focus();
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
				var bookCategory = response[1].bookCategory == 'CategoryA' ? 'categoryA' : 'categoryB';
				$("#upd-progress").hide();
				document.querySelector("#upd-title + label").className = 'active';
				document.getElementById("upd-title").value = response[1].title;
				document.querySelector("#upd-author + label").className = 'active';
				document.getElementById("upd-author").value = response[1].author;
				document.querySelector("#upd-press + label").className = 'active';
				document.getElementById("upd-press").value = response[1].press;
				document.querySelector("#upd-pubdate + label").className = 'active';
				document.getElementById("upd-pubdate").value = response[1].pubdate;
				document.querySelector("#upd-image + label").className = 'active';
				document.getElementById("upd-image").value = response[1].image;
				document.getElementById("upd-" + bookCategory).checked = true;

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

				var isMA = response[1].isMultipleAuthor == 1 ? true: false;
				document.getElementById("upd-is-multiple-author").checked = isMA;
				window.setTimeout(function () {
					document.getElementById("upd-remaining-amount").value = response[1].remainingAmount;
					document.getElementById("upd-remaining-amount").focus();
				}, 0);
				$("#update-init").hide();
				$("#upd-books").show();
			}
		}
	);
}

function Check() {
	if (document.getElementById("title").value == '' || 
		document.getElementById("author").value == '' || 
		document.getElementById("press").value == '' || 
		document.getElementById("pubdate").value == '' || 
		document.getElementById("remaining-amount").value == '' || 
		!(document.getElementById("categoryA").checked ||  
		document.getElementById("categoryB").checked)) {
			alert('请将书籍信息填写完整！');
			return false;
	}
	if (document.getElementById("categoryA").checked && 
		(document.getElementById("grade").value == '' || 
		document.getElementById("major").value == '')) {
			alert('请将分类信息填写完整！');
			return false;
	}
	if (document.getElementById("categoryB").checked && 
		document.getElementById("extracurricular-category").value == '') {
			alert('请将分类信息填写完整！');
			return false;
	}
	return true;
}

function updCheck() {
	if (document.getElementById("upd-title").value == '' || 
		document.getElementById("upd-author").value == '' || 
		document.getElementById("upd-press").value == '' || 
		document.getElementById("upd-pubdate").value == '' || 
		document.getElementById("upd-remaining-amount").value == '' || 
		!(document.getElementById("upd-categoryA").checked ||  
		document.getElementById("upd-categoryB").checked)) {
			alert('请将书籍信息填写完整！');
			return false;
	}
	if (document.getElementById("upd-categoryA").checked && 
		(document.getElementById("upd-grade").value == '' || 
		document.getElementById("upd-major").value == '')) {
			alert('请将分类信息填写完整！');
			return false;
	}
	if (document.getElementById("upd-categoryB").checked && 
		document.getElementById("upd-extracurricular-category").value == '') {
			alert('请将分类信息填写完整！');
			return false;
	}
	return true;
}

function ConfirmInfo() {
	var isMA = document.getElementById("is-multiple-author").checked ? '是' : '否';
	if (document.getElementById("categoryA").checked) {
		var cataInfo = '\n书籍分类：教材课本' +
		'\n年级：' + document.getElementById("grade").value +
		'\n专业：' + document.getElementById("major").value;
	}
	if (document.getElementById("categoryB").checked) {
		var cataInfo = '\n书籍分类：课外书籍' +
		'\n详细类别：' + document.getElementById("extracurricular-category").value;
	}
	var confirmPrompt = '请再次确认该书籍信息：\n' +
		'\n书名：' + document.getElementById("title").value +
		'\n作者：' + document.getElementById("author").value +
		'\n是否有多位作者：' + isMA +
		'\n出版社：' + document.getElementById("press").value +
		'\n剩余数量：' + document.getElementById("remaining-amount").value +
		'\n图片链接：' + document.getElementById("image").value + cataInfo;
	return confirm(confirmPrompt);
}

function updConfirmInfo() {
	var isMA = document.getElementById("upd-is-multiple-author").checked ? '是' : '否';
	if (document.getElementById("upd-categoryA").checked) {
		var cataInfo = '\n书籍分类：教材课本' +
		'\n年级：' + document.getElementById("upd-grade").value +
		'\n专业：' + document.getElementById("upd-major").value;
	}
	if (document.getElementById("upd-categoryB").checked) {
		var cataInfo = '\n书籍分类：课外书籍' +
		'\n详细类别：' + document.getElementById("upd-extracurricular-category").value;
	}
	var confirmPrompt = '请再次确认该书籍信息：\n' +
		'\n书籍 ID：' + document.getElementById("bookID").value +
		'\n书名：' + document.getElementById("upd-title").value +
		'\n作者：' + document.getElementById("upd-author").value +
		'\n是否有多位作者：' + isMA +
		'\n出版社：' + document.getElementById("upd-press").value +
		'\n剩余数量：' + document.getElementById("upd-remaining-amount").value +
		'\n图片链接：' + document.getElementById("upd-image").value + cataInfo;
	return confirm(confirmPrompt);
}
