//The following three lines of code are wrote for reservation or modification
modifying = false, count = 0
list = ['0', '0', '0'];
preList = ['0', '0', '0'];

$(document).ready(function() {
	$(".modal").modal();
	$(".button-collapse").sideNav();
	$("select").material_select();

	$("#welcome").modal('open');
	$("#loading").hide();

	$("#all").submit(function(e) {
		e.preventDefault();
		display(this, 0);
	});

	$("#search-form").submit(function(e) {
		e.preventDefault();
		display(this, 1);
	});

	$("#category-form").submit(function(e) {
		e.preventDefault();
		$("#loading").css("display", "flex");
		if (!($("#categoryA").prop("checked") ||
			$("#categoryB").prop("checked"))) {
				alert('请选择书籍分类！');
				$("#loading").hide();
		} else {
			display(this, 2);
		}
	});

	$("#reserve-form").submit(function(e) {
		e.preventDefault();
		if (reserveCheck()) {
			if (modifying) {
				commitModification(this); return;
			}
			$("#loading").css("display", "flex");
			$.post(
				'./assets/API/reserve.php',
				$(this).serialize() + '&operation=new&count=' + count +
				'&list0=' + list[0] + '&list1=' + list[1] + '&list2=' + list[2],
				function(response) {
					$("#loading").hide();
					if (response.code == 1) {
						alert('请将预约信息填写完整！');
					}
					if (response.code == 2) {
						alert('该学号已存在预约订单！');
					}
					if (response.code == 3) {
						alert('不要调皮哦');
					}
					if (response.code == 4) {
						alert('订单提交失败，请联系管理员或重试');
					}
					if (response.code == 5) {
						alert('列表中有书籍已被他人预约，请重新选择\n\n预约信息不需要重新填写O(∩_∩)O');
						$("#list-data").empty();
						$("#display").empty();
						count = 0;
						list = ['0', '0', '0'];
						$("#reserve").modal('close');
						$("#welcome").modal('open');
					}
					if (response.code == 0) {
						Materialize.toast('订单提交成功！', 3000);
						window.setTimeout(function () {
							$("#reserve").modal('close');
							$("#stu-number").val($("#student-number").val());
							$("label[for=stu-number]").addClass("active");
							searchReservation();
						}, 1000);
					}
				}
			);
		}
	});	

	$("#search-reservation").submit(function(e) {
		e.preventDefault();
		searchReservation();
	});
});

function display(data, type) {
	$("#loading").css("display", "flex");
	operation = ['all', 'search', 'category'][type];
	$.post(
		'./assets/API/books.php',
		$(data).serialize() + '&operation=' + operation,
		function(response) {
			if (response[0].code == 1) {
				Materialize.toast([
					'数据库还是空的，过段时间再来看看吧',
					'未找到相关书籍，换个关键词试试吧',
					'未找到相关书籍，换个分类试试吧'
				][type], 3000);
			}
			if (response[0].code == 0) {
				$("#display").empty();
				for (var i = 1; i < response.length; i++) {
					MultipleAuthor = response[i].isMultipleAuthor ? ' 等' : '';
					btnAttr = response[i].remainingAmount == 0 ?
						'<a class="btn-floating waves-effect waves-light grey center-align btn-add">0</a>' :
						'<a class="btn-floating waves-effect waves-light red center-align btn-add" ' +
						'data-id=' + response[i].bookID + ' onclick="addToList(this)">' +
						response[i].remainingAmount + '</a>';
					
					$("#display").append(
					'<div class="card horizontal">' +
						'<div class="card-image">' +
							'<img class="z-depth-3" src=' + response[i].image +
							' onclick="window.location.href=this.src">' +
						'</div>' +
						'<div class="card-stacked">' +
							'<div class="card-content">' +
								'<div class="card-title" onclick="fullText(this)">' + response[i].title + '</div>' +
								'<div class="card-details">' +
									'<p>作者：' + response[i].author + MultipleAuthor + '</p>' +
									'<p>出版社：' + response[i].press + '</p>' +
									'<p>出版时间：' + response[i].pubdate + '</p>' +
								'</div>' +
							'</div>' +
						'</div>' + btnAttr +
					'</div>');
				}
				$("#placeholder").show();
				$("#book-confirm").show();
				$("#" + ['welcome', 'search', 'category'][type]).modal('close');
				$(".button-collapse").sideNav('hide');
			}
			$("#loading").hide();
		}
	);
}

function searchReservation() {
	$("#progress").show();
	stuNo = $("#stu-number").val();
	$.post(
		'./assets/API/reservation.php',
		'operation=search&stuNo=' + stuNo,
		function(response) {
			if (response[0].code == 1) {
				Materialize.toast('未查询到订单', 3000);
			};	
			if (response[0].code == 0) {
				$("#reservation").html(
				'<div class="card-content">' +
					'<div class="reservation-title">订单详情</div>' +
					'<div class="card-title">订单号：' + response[1].reservationNo + '</div>' +
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
									'<td>' + response[1].stuName + '</td>' +
									'<td>' + response[1].stuNo + '</td>' +
									'<td>' + response[1].contact + '</td>' +
									'<td>' + response[1].dormitory + '</td>' +
									'<td>' + response[1].date + '</td>' +
									'<td>' + response[1].timePeriod + '</td>' +
									'<td>' + response[1].sbmTime + '</td>' +
									'<td>' + response[1].updTime + '</td>' +
								'</tr>' +
							'</tbody>' +
						'</table>' +
						'<div class="section">' +
							'<div class="card-title">预约书籍信息：</div>' +
							'<div class="row" id="reserved-books"></div>' +
							'<div class="reservation">' +
								'<button class="btn waves-effect waves-light red lighten-2" onclick="modifyReservation();">更改</button>' +
								'<button class="btn waves-effect waves-light red lighten-2 modal-close" onclick="back();">返回</button>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</div>');

				for (var i = 0; i < response[1].books.length; i++) {
					var MultipleAuthor = response[1].books[i].isMultipleAuthor == 1 ? ' 等' : '';
					$("#reserved-books").append(
					'<div class="col s12 m4">' +
						'<div class="card blue-grey darken-1">' +
							'<div class="card-content white-text">' +
								'<div class="card-title">' + response[1].books[i].title +
								'</div>' +
								'<div class="card-details">' +
									'<p>作者：' + response[1].books[i].author + MultipleAuthor + '</p>' +
									'<p>出版社：' + response[1].books[i].press + '</p>' +
									'<p>出版日期：' + response[1].books[i].pubdate + '</p>' +
								'</div>' +
							'</div>' +
							'<div class="card-action center-align">' +
								'<a class="cover" href=' + response[1].books[i].image + '>触碰或点击查看封面图片' +
									'<div class="cover-image">' +
										'<img src=' + response[1].books[i].image + ' alt="封面图片">' +
									'</div>' +
								'</a>' +
							'</div>' +
						'</div>' +
					'</div>');
				}
				$("#search-reservation").modal('close');
				$(".button-collapse").sideNav('hide');
				$("#reservation").modal('open');
			}
			$("#progress").hide();
		}
	);
}

function selectCategory(val) {
	if (val == 'categoryA') {
		$("#book-categoryA").show();
		$("#book-categoryB").hide();
	}
	if (val == 'categoryB') {
		$("#book-categoryA").hide();
		$("#book-categoryB").show();
	}
}

function reserveCheck() {
	if ($("#student-name").val() == '' ||
		$("#student-number").val() == '' ||
		$("#dormitory").val() == '' ||
		$("#contact").val() == '' ||
		$("#date").val() == '' ||
		$("#time-period").val() == '') {
			alert('请将预约信息填写完整！');
			return false;
	}
	$("#loading").css("display", "flex");
	return true;
}

function confirmChoose() {
	if (count == 0) {
		$("#alert-content").text('请选择预约书籍');
		$("#alert").modal('open');
		return;
	}
	$("#reserve").modal('open');
}

function back() {
	$("#reservation").modal('close');
}

function fullText(val) {
	$("#full-text-content").text(val.textContent);
	$("#full-text").modal('open');
}

function modifyReservation() {
	$("#loading").css("display", "flex");
	$("#submit").text('确定修改');
	stuNo = $("#stu-number").val();

	modifying = true, count = 0;
	list = ['0', '0', '0'];
	preList = ['0', '0', '0'];
	
	$.post(
		'./assets/API/reservation.php',
		'operation=search&stuNo=' + stuNo,
		function(response) {
			if (response[0].code == 0) {
				$("#list-data").empty();
				
				$("label[for=student-name]").addClass("active");
				$("#student-name").val(response[1].stuName);
				$("label[for=student-number]").addClass("active");
				$("#student-number").val(response[1].stuNo);
				$("label[for=contact]").addClass("active");
				$("#contact").val(response[1].contact);

				$("#student-number").prop('disabled', true);
				$("#dormitory").val(response[1].dormitory);	$("#dormitory").material_select();
				$("#date").val(response[1].date);	$("#date").material_select();
				$("#time-period").val(response[1].timePeriod);	$("#time-period").material_select();

				for (var i = 0; i < response[1].books.length; i++) {
					list[list.indexOf('0')] = response[1].books[i].bookID;
					preList[preList.indexOf('0')] = response[1].books[i].bookID;
					count ++;

					MultipleAuthor = response[1].books[i].isMultipleAuthor ? ' 等' : '';
					
					$("#list-data").append(
					'<div class="card horizontal list-card">' +
						'<div class="card-image">' +
							'<img class="z-depth-3" src=' + response[1].books[i].image + '>' +
						'</div>' +
						'<div class="card-stacked">' +
							'<div class="card-content">' +
								'<div class="card-title" onclick="fullText(this)">' + response[1].books[i].title + '</div>' +
								'<div class="card-details">' +
									'<p>' + response[1].books[i].author + MultipleAuthor + '</p>' +
									'<p>' + response[1].books[i].press + '</p>' +
									'<p>' + response[1].books[i].pubdate + '</p>' +
								'</div>' +
							'</div>' +
						'</div><a class="btn-floating waves-effect waves-light red center-align btn-del" ' +
						'data-id=' + response[1].books[i].bookID + ' onclick="deleteFromList(this)">&times</a>' +
					'</div>');
				}
				$("#reservation").modal('close');
				$("#loading").hide();
				$("#list").modal('open');
			}
		}
	);
}

function commitModification(val) {
	$("#loading").css("display", "flex");
	$.post(
		'./assets/API/reserve.php',
		$(val).serialize() + '&operation=modify&count=' + count +
		'&list0=' + list[0] + '&list1=' + list[1] + '&list2=' + list[2] +
		'&preList0=' + preList[0] + '&preList1=' + preList[1] + '&preList2=' + preList[2] +
		'&studentNo=' + $("#student-number").val(),
		function(response) {
			$("#loading").hide();
			if (response.code == 1) {
				alert('请将预约信息填写完整！');
			}
			if (response.code == 2) {
				alert('不要调皮哦');
			}
			if (response.code == 3) {
				alert('列表中有书籍已被他人预约，请重新选择\n\n预约信息不需要重新填写O(∩_∩)O');
				$("#list-data").empty();
				$("#display").empty();
				count = 0; list = ['0', '0', '0'];
				$("#reserve").modal('close');
				$("#welcome").modal('open');
			}
			if (response.code == 4) {
				alert('订单修改失败，请联系管理员或重试');
			}
			if (response.code == 0) {
				Materialize.toast('订单修改成功！', 3000);
				window.setTimeout(function () {
					$("#reserve").modal('close');
					searchReservation();
				}, 1000);
			}
		}
	);
}

function addToList(val) {
	if (count >= 3) {
		$("#alert-content").text('列表书籍已达到选择上限');
		$("#alert").modal('open');
		return;
	}

	if (list.indexOf(val.dataset.id) != -1) {
		$("#alert-content").text('每种书籍仅限选择一本哦');
		$("#alert").modal('open');
		return;
	}

	list[list.indexOf('0')] = val.dataset.id;
	count ++;
	val.innerText--;

	var image = val.previousSibling.previousSibling.children[0].src;
	var title = val.previousSibling.children[0].children[0].innerText;
	var author = val.previousSibling.children[0].children[1].children[0].innerText;
	var press = val.previousSibling.children[0].children[1].children[1].innerText;
	var pubdate = val.previousSibling.children[0].children[1].children[2].innerText;

	if (val.innerText == 0) {
		val.outerHTML = '<a class="btn-floating waves-effect waves-light grey center-align btn-add">0</a>';
	}

	$("#list-data").append(
	'<div class="card horizontal list-card">' +
		'<div class="card-image">' +
			'<img class="z-depth-3" src=' + image + '>' +
		'</div>' +
		'<div class="card-stacked">' +
			'<div class="card-content">' +
				'<div class="card-title" onclick="fullText(this)">' + title + '</div>' +
				'<div class="card-details">' +
					'<p>' + author + '</p>' +
					'<p>' + press + '</p>' +
					'<p>' + pubdate + '</p>' +
				'</div>' +
			'</div>' +
		'</div><a class="btn-floating waves-effect waves-light red center-align btn-del" ' +
		'data-id=' + val.dataset.id + ' onclick="deleteFromList(this)">x</a>' +
	'</div>');
}

function deleteFromList(val) {
	count --;
	list[list.indexOf(val.dataset.id)] = '0';
	val.parentNode.outerHTML = '';
}
