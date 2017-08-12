$(document).ready(function() {
	$(".button-collapse").sideNav();
	$('.modal').modal();
	$('select').material_select();
	
	$('#ISBN').bind('keypress',function(e){
		if(e.keyCode == "13") {
			getData();
		}
	});
	$('#bookID').bind('keypress',function(e){
		if(e.keyCode == "13") {
			getBook();
		}
	});
	

	$('#add').submit(function(e) {
		e.preventDefault();
		if (document.getElementById("bookInfo").style.display == "block" && Check() && ConfirmInfo()) {
			$.ajax({
				type: "POST",
				url: '../assets/API/add.php',
				data: $(this).serialize(),
				success: function(response)
				{
					if (response.code == 1) {
						alert("请登录系统！");
						window.location.href = "./login";
					}
					if (response.code == 2) {
						alert("请将书籍信息填写完整！");
					}
					if (response.code == 3) {
						alert("添加书籍失败，请联系管理员");
					}
					if (response.code == 0) {
						Materialize.toast("书籍添加成功！", 3000);
						window.setTimeout(function ()
						{
							window.location.href = "./";
						}, 3600);
					}
				}
			});
		}
	});

	$('#update').submit(function(e) {
		e.preventDefault();
		if (document.getElementById("updBooks").style.display == "block" && updCheck() && updConfirmInfo()) {
			$.ajax({
				type: "POST",
				url: '../assets/API/update.php',
				data: $(this).serialize(),
				success: function(response)
				{
					if (response.code == 1) {
						alert("请登录系统！");
						window.location.href = "./login.html";
					}
					if (response.code == 2) {
						alert("请将书籍信息填写完整！");
					}
					if (response.code == 3) {
						alert("更新书籍信息失败，请联系管理员");
					}
					if (response.code == 0) {
						Materialize.toast("书籍信息更新成功！", 3000);
						window.setTimeout(function ()
						{
							window.location.href = "./";
						}, 3600);
					}
				}
			});
		}
	});

	document.getElementById("loading").style.display = "flex";

	$.ajax({
		type: "POST",
		url: "../assets/API/booksAdmin.php",
		success: function(response)
		{
			if (response[0].code == 1) {
				alert("请登录系统！");
				window.location.href = "login.html";
			}
			if (response[0].code == 2) {
				Materialize.toast("数据库中暂无书籍信息", 3000);
			}

			if (response[0].code == 0) {
				for (var i = 1; i < response.length; i++) {

					var rowNo = Math.round((i + 1) / 4);
					if (i % 4 == 1) {
						document.getElementById("books").innerHTML += 
							"<div class=\"row\" id = \"row" + rowNo + "\"></div>";
					}

					var MultipleAuthor = response[i].isMultipleAuthor == 1 ? " 等" : "";
					var Category = response[i].bookCategory == "CategoryA" ? "<p>分类：教材课本</p><p>年级：" + response[i].grade + "&nbsp;&nbsp;&nbsp;专业：" + response[i].major + "</p>" : "<p>分类：课外书籍</p><p>详细类别：" + response[i].extracurricularCategory + "</p>";

					if (response[i].image == "./assets/pictures/defaultCover.png") {
						response[i].image = "../assets/pictures/defaultCover.png"
					}

					document.getElementById("row" + rowNo).innerHTML += 
						"<div class=\"col s12 m3\">" + 
							"<div class=\"card blue-grey darken-1\">" + 
								"<div class=\"card-content white-text\">" + 
									"<div class=\"card-title\">" + response[i].title + "</div>" + 
									"<div class=\"card-details\">" + 
										"<p>作者：" + response[i].author + MultipleAuthor + "</p>" + 
										"<p>出版社：" + response[i].press + "</p>" + 
										"<p>出版日期：" + response[i].pubdate + "</p>" + 
										"<div class=\"adminInfo\">" + 
											"<p><strong>书籍 ID：" + response[i].bookID + "</strong></p>" + 
											"<p>剩余数量：" + response[i].remainingAmount + "</p>" + Category +
											"<p>入库时间：" + response[i].importTime + "</p>" + 
											"<p>更新时间：" + response[i].updateTime + "</p>" + 
										"</div>" + 
									"</div>" + 
								"</div>" + 
								"<div class=\"card-action center-align\">" + 
									"<a class=\"cover\" href=\"" + response[i].image + "\">" + 
										"触碰或点击查看封面图片" + 
										"<div class=\"coverImage\">" + 
											"<img src=\"" + response[i].image + "\" alt=\"封面图片\" >" + 
										"</div>" + 
									"</a>" + 
								"</div>" + 
							"</div>" + 
						"</div>";
					}
			}
			document.getElementById("loading").style.display = "none";
		}
	});

});



function logout() {
	$.ajax({
		type: "POST",
		url: "../assets/API/admin.php",
		data: ({ "type":2 }),
		success: function(response) {
			if (response.code == 0) {
				alert("退出系统成功");
			}
			window.location.href = "../"
		}
	});
}

function selectCategory(val) {
	if (val == "CategoryA") {
		document.getElementById("bookCategoryA").style.display = "block";
		document.getElementById("bookCategoryB").style.display = "none";
	}
	if (val == "CategoryB") {
		document.getElementById("bookCategoryA").style.display = "none";
		document.getElementById("bookCategoryB").style.display = "block";
	}
}

function updCategory(val) {
	if (val == "updCategoryA") {
		document.getElementById("updBookCategoryA").style.display = "block";
		document.getElementById("updBookCategoryB").style.display = "none";
	}
	if (val == "updCategoryB") {
		document.getElementById("updBookCategoryA").style.display = "none";
		document.getElementById("updBookCategoryB").style.display = "block";
	}
}

function inputData() {
	document.getElementById("addInit").style.display = "none";
	document.getElementById("bookInfo").style.display = "block";
	document.getElementById("bookCategory").style.display = "block";
}

function getData() {
	document.getElementById("progress").style.display = "block";
	var ISBN = document.getElementById("ISBN").value;
	$.ajax({
		type:"POST",
		url: "../assets/API/bookInfoAPI.php",
		data: ({ "ISBN":ISBN }),
		success: function(response)
		{
			document.getElementById("progress").style.display = "none";
			if (response.code == 1) {
				alert("未找到书籍信息，请手动录入相关数据");
				document.getElementById("ISBN").value = "";
				inputData();
			}
			if (response.code == 0) {
				document.querySelector("#title + label").className ="active";
				document.getElementById("title").value = response.title;
				document.querySelector("#author + label").className ="active";
				document.getElementById("author").value = response.author;
				document.querySelector("#press + label").className ="active";
				document.getElementById("press").value = response.press;
				document.querySelector("#pubdate + label").className ="active";
				document.getElementById("pubdate").value = response.pubdate;
				document.querySelector("#image + label").className ="active";
				document.getElementById("image").value = response.image;

				var isMA = response.isMultipleAuthor == 1 ? true: false;
				document.getElementById("isMultipleAuthor").checked = isMA;
				window.setTimeout(function ()
				{
					document.getElementById("remainingAmount").focus();
				}, 0);

				document.getElementById("addInit").style.display = "none";
				document.getElementById("bookInfo").style.display = "block";
				document.getElementById("bookCategory").style.display = "block";
			}
		}
	});
}

function getBook() {
	document.getElementById("updProgress").style.display = "block";
	var bookID = document.getElementById("bookID").value;
	$.ajax({
		type:"POST",
		url: "../assets/API/booksAdmin.php",
		data: ({ "bookID": bookID }),
		success: function(response)
		{
			if (response[0].code == 1) {
				alert("请登录系统！");
				window.location.href = "./login.html";
			};
			if (response[0].code == 2) {
				alert("未找到对应书籍，请检查输入 ID 是否正确！");
				document.getElementById("updProgress").style.display = "none";
			};
			if (response[0].code == 0) {
				document.getElementById("updProgress").style.display = "none";
				document.querySelector("#updTitle + label").className ="active";
				document.getElementById("updTitle").value = response[1].title;
				document.querySelector("#updAuthor + label").className ="active";
				document.getElementById("updAuthor").value = response[1].author;
				document.querySelector("#updPress + label").className ="active";
				document.getElementById("updPress").value = response[1].press;
				document.querySelector("#updPubdate + label").className ="active";
				document.getElementById("updPubdate").value = response[1].pubdate;
				document.querySelector("#updImage + label").className ="active";
				document.getElementById("updImage").value = response[1].image;
				document.getElementById("upd" + response[1].bookCategory).checked = true;

				if (response[1].bookCategory == "CategoryA") {
					updCategory('updCategoryA');
					$('#updGrade').val(response[1].grade);	$('#updGrade').material_select();
					$('#updMajor').val(response[1].major);	$('#updMajor').material_select();
				}
				if (response[1].bookCategory == "CategoryB") {
					updCategory('updCategoryB');
					$('#updExtracurricularCategory').val(response[1].extracurricularCategory);
					$('#updExtracurricularCategory').material_select();
				}

				var isMA = response[1].isMultipleAuthor == 1 ? true: false;
				document.getElementById("updIsMultipleAuthor").checked = isMA;
				window.setTimeout(function ()
				{
					document.getElementById("updRemainingAmount").value = response[1].remainingAmount;
					document.getElementById("updRemainingAmount").focus();
				}, 0);

				document.getElementById("updateInit").style.display = "none";
				document.getElementById("updBooks").style.display = "block";
			}
		}
	});
}

function Check() {
	if (document.getElementById("title").value == "" || 
		document.getElementById("author").value == "" || 
		document.getElementById("press").value == "" || 
		document.getElementById("pubdate").value == "" || 
		document.getElementById("remainingAmount").value == "" || 
		!(document.getElementById("CategoryA").checked ||  
		document.getElementById("CategoryB").checked)) {
			alert("请将书籍信息填写完整！");
			return false;
	}
	if (document.getElementById("CategoryA").checked && 
		(document.getElementById("grade").value == "" || 
		document.getElementById("major").value == "")) {
			alert("请将分类信息填写完整！");
			return false;
	}
	if (document.getElementById("CategoryB").checked && 
		document.getElementById("extracurricularCategory").value == "") {
			alert("请将分类信息填写完整！");
			return false;
	}
	return true;
}

function updCheck() {
	if (document.getElementById("updTitle").value == "" || 
		document.getElementById("updAuthor").value == "" || 
		document.getElementById("updPress").value == "" || 
		document.getElementById("updPubdate").value == "" || 
		document.getElementById("updRemainingAmount").value == "" || 
		!(document.getElementById("updCategoryA").checked ||  
		document.getElementById("updCategoryB").checked)) {
			alert("请将书籍信息填写完整！");
			return false;
	}
	if (document.getElementById("updCategoryA").checked && 
		(document.getElementById("updGrade").value == "" || 
		document.getElementById("updMajor").value == "")) {
			alert("请将分类信息填写完整！");
			return false;
	}
	if (document.getElementById("updCategoryB").checked && 
		document.getElementById("updExtracurricularCategory").value == "") {
			alert("请将分类信息填写完整！");
			return false;
	}
	return true;
}

function ConfirmInfo() {
	var isMA = document.getElementById("isMultipleAuthor").checked ? "是" : "否";
	if (document.getElementById("CategoryA").checked) {
		var cataInfo = "\n书籍分类：教材课本" +
		"\n年级：" + document.getElementById("grade").value +
		"\n专业：" + document.getElementById("major").value;
	}
	if (document.getElementById("CategoryB").checked) {
		var cataInfo = "\n书籍分类：课外书籍" +
		"\n详细类别：" + document.getElementById("extracurricularCategory").value;
	}
	var confirmPrompt = "请再次确认该书籍信息：\n" +
		"\n书名：" + document.getElementById("title").value +
		"\n作者：" + document.getElementById("author").value +
		"\n是否有多位作者：" + isMA +
		"\n出版社：" + document.getElementById("press").value +
		"\n剩余数量：" + document.getElementById("remainingAmount").value +
		"\n图片链接：" + document.getElementById("image").value + cataInfo;
	return confirm(confirmPrompt);
}

function updConfirmInfo() {
	var isMA = document.getElementById("updIsMultipleAuthor").checked ? "是" : "否";
	if (document.getElementById("updCategoryA").checked) {
		var cataInfo = "\n书籍分类：教材课本" +
		"\n年级：" + document.getElementById("updGrade").value +
		"\n专业：" + document.getElementById("updMajor").value;
	}
	if (document.getElementById("updCategoryB").checked) {
		var cataInfo = "\n书籍分类：课外书籍" +
		"\n详细类别：" + document.getElementById("updExtracurricularCategory").value;
	}
	var confirmPrompt = "请再次确认该书籍信息：\n" +
		"\n书籍 ID：" + document.getElementById("bookID").value +
		"\n书名：" + document.getElementById("updTitle").value +
		"\n作者：" + document.getElementById("updAuthor").value +
		"\n是否有多位作者：" + isMA +
		"\n出版社：" + document.getElementById("updPress").value +
		"\n剩余数量：" + document.getElementById("updRemainingAmount").value +
		"\n图片链接：" + document.getElementById("updImage").value + cataInfo;
	return confirm(confirmPrompt);
}
