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

	$("#category").submit(function(e) {
		e.preventDefault();
		if (!($("#categoryA").prop("checked")
			|| $("#categoryB").prop("checked"))) {
				alert('请选择书籍分类！');
		} else {
			display(this, 2);
		}
	});

	$("#reserve").submit(function(e) {
		e.preventDefault();
		if (reserveCheck()) {
			$("#loading").css("display", "flex");
			
			data = $(this).serialize() + 
				'&list0=' + list[0] + '&list1=' + list[1] + '&list2=' + list[2] +
				(!modifying ? '&operation=new' :
				'&operation=modify' + '&preList0=' + preList[0] +
				'&preList1=' + preList[1] + '&preList2=' + preList[2] +
				'&studentNo=' + $("#student-number").val());
			
			$.post(
				'./assets/API/reserve.php',
				data,
				function(response) {
					$("#loading").hide();
					switch(response.code) {
						case 0:
							Materialize.toast('订单' + (modifying ? '修改' : '提交') + '成功！', 3000);
							window.setTimeout(function () {
								$("#reserve").modal('close');
								$("#stu-number").val($("#student-number").val());
								$("label[for=stu-number]").addClass("active");
								searchReservation();
							}, 1000);
							break;
						case 3:
						case 7:
							modalAlert('列表中有书籍已被他人预约，请重新选择<br><br>预约信息不需要重新填写୧(﹒︠ᴗ﹒︡)୨');
							$("#list-data").empty();
							$("#display").empty();
							count = 0; list = ['0', '0', '0'];
							$("#reserve").modal('close');
							$("#all").modal('open');
							break;
						default:
							Materialize.toast(response.errMsg, 3000);
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

function display(data,type) {
	$("#loading").css("display", "flex");
	operation = ['all', 'search', 'category'][type];
    $.post(
		'./assets/API/books.php',
		$(data).serialize() + '&operation=' + operation,
		function(response) {
			if (response.code == 0) {
				$("#display").empty();
				$.each(response, function(i, book) {
					if (i == 'code') return true;
					MultipleAuthor = book.isMultipleAuthor ? ' 等' : '';
					btnAttr = book.remainingAmount == 0 ?
						'<a class="btn-floating waves-effect waves-light grey center-align btn-add">0</a>' :
						'<a class="btn-floating waves-effect waves-light red center-align btn-add" ' +
						'data-id=' + book.bookID + ' onclick="addToList(this)">' +
						book.remainingAmount + '</a>';
					
					$("#display").append(
					'<div class="card horizontal">' +
						'<div class="card-image">' +
							'<img class="z-depth-3" src=' + book.image +
							' onclick="window.location.href=this.src">' +
						'</div>' +
						'<div class="card-stacked">' +
							'<div class="card-content">' +
								'<div class="card-title" onclick="showFullText(this)">' + book.title + '</div>' +
								'<div class="card-details">' +
									'<p>作者：' + book.author + MultipleAuthor + '</p>' +
									'<p>出版社：' + book.press + '</p>' +
									'<p>出版时间：' + book.pubdate + '</p>' +
								'</div>' +
							'</div>' +
						'</div>' + btnAttr +
					'</div>');
				});
				$("#placeholder").show();
				$("#book-confirm").show();
				$("#" + operation).modal('close');
				$(".button-collapse").sideNav('hide');
			} else {
				Materialize.toast(response.errMsg, 3000);
			}
			$("#loading").hide();
		}
	);
}

function selectCategory(val) {
	switch(val) {
		case 'categoryA':
			$("#book-categoryB").hide(700);
			$("#book-categoryA").show(700);
			break;
		case 'categoryB':
			$("#book-categoryA").hide(700);
			$("#book-categoryB").show(700);
			break;
	}
}

function searchReservation() {
	$("#progress").show();
	stuNo = $("#stu-number").val();
	$.post(
		'./assets/API/reservations.php',
		'operation=search&stuNo=' + stuNo,
		function(response) {
			if (response.code == 0) {
				$("#reservation").html(
				'<div class="card-content">' +
					'<div class="reservation-title">订单详情</div>' +
					'<div class="card-title">订单号：' + response[0].reservationNo + '</div>' +
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
									'<td>' + response[0].stuName + '</td>' +
									'<td>' + response[0].stuNo + '</td>' +
									'<td>' + response[0].contact + '</td>' +
									'<td>' + response[0].dormitory + '</td>' +
									'<td>' + response[0].date + '</td>' +
									'<td>' + response[0].timePeriod + '</td>' +
									'<td>' + response[0].sbmTime + '</td>' +
									'<td>' + response[0].updTime + '</td>' +
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

				$.each(response[0].books, function(i, book) {
					MultipleAuthor = book.isMultipleAuthor == 1 ? ' 等' : '';
					$("#reserved-books").append(
					'<div class="col s12 m4">' +
						'<div class="card blue-grey darken-1">' +
							'<div class="card-content white-text">' +
								'<div class="card-title">' + book.title +
								'</div>' +
								'<div class="card-details">' +
									'<p>作者：' + book.author + MultipleAuthor + '</p>' +
									'<p>出版社：' + book.press + '</p>' +
									'<p>出版日期：' + book.pubdate + '</p>' +
								'</div>' +
							'</div>' +
							'<div class="card-action center-align">' +
								'<a class="cover" href=' + book.image + '>触碰或点击查看封面图片' +
									'<div class="cover-image">' +
										'<img src=' + book.image + ' alt="封面图片">' +
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
				Materialize.toast(response.errMsg, 3000);
			}
			$("#progress").hide();
		}
	);
}

function modifyReservation() {
	$("#loading").css("display", "flex");
	$("#submit").text('确定修改');
	stuNo = $("#stu-number").val();

	modifying = true, count = 0;
	list = ['0', '0', '0'];
	preList = ['0', '0', '0'];
	
	$.post(
		'./assets/API/reservations.php',
		'operation=search&stuNo=' + stuNo,
		function(response) {
			if (response.code == 0) {
				$("#list-data").empty();
				
				$("label[for=student-name]").addClass("active");
				$("#student-name").val(response[0].stuName);
				$("label[for=student-number]").addClass("active");
				$("#student-number").val(response[0].stuNo);
				$("label[for=contact]").addClass("active");
				$("#contact").val(response[0].contact);

				$("#student-number").prop('disabled', true);
				$("#dormitory").val(response[0].dormitory).material_select();
				$("#date").val(response[0].date).material_select();
				$("#time-period").val(response[0].timePeriod).material_select();

				$.each(response[0].books, function(i, book) {
					list[list.indexOf('0')] = book.bookID;
					preList[preList.indexOf('0')] = book.bookID;
					count++;

					MultipleAuthor = book.isMultipleAuthor ? ' 等' : '';
					$("#list-data").append(
					'<div class="card horizontal list-card">' +
						'<div class="card-image">' +
							'<img class="z-depth-3" src=' + book.image + '>' +
						'</div>' +
						'<div class="card-stacked">' +
							'<div class="card-content">' +
								'<div class="card-title" onclick="showFullText(this)">' + book.title + '</div>' +
								'<div class="card-details">' +
									'<p>' + book.author + MultipleAuthor + '</p>' +
									'<p>' + book.press + '</p>' +
									'<p>' + book.pubdate + '</p>' +
								'</div>' +
							'</div>' +
						'</div><a class="btn-floating waves-effect waves-light red center-align btn-del" ' +
						'data-id=' + book.bookID + ' onclick="deleteFromList(this)">&times</a>' +
					'</div>');
				});
				$("#reservation").modal('close');
				$("#list").modal('open');
			} else {
				Materialize.toast(response.errMsg, 3000);
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

	if (val.innerText == 0) {
		val.outerHTML = '<a class="btn-floating waves-effect waves-light grey center-align btn-add">0</a>';
	}

	ele = val.previousSibling.children[0].children[1];
	$("#list-data").append(
	'<div class="card horizontal list-card">' +
		'<div class="card-image">' +
			'<img class="z-depth-3" src=' +
				val.previousSibling.previousSibling.children[0].src +
			'>' +
		'</div>' +
		'<div class="card-stacked">' +
			'<div class="card-content">' +
				'<div class="card-title" onclick="showFullText(this)">' +
					val.previousSibling.children[0].children[0].innerText +
				'</div>' +
				'<div class="card-details">' +
					'<p>' + ele.children[0].innerText + '</p>' +
					'<p>' + ele.children[1].innerText + '</p>' +
					'<p>' + ele.children[2].innerText + '</p>' +
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

function modalAlert(content, title = '系统提示') {
	$("#alert-title").text(title);
	$("#alert-content").html(content);
	$("#alert").modal('open');
}

function showFullText(val) {
	modalAlert(val.textContent, '书籍标题全文');
}

function confirmChoose() {
	if (count == 0) {
		modalAlert('请选择预约书籍');
		return;
	}
	$("#reserve").modal('open');
}

function reserveCheck() {
	if ($("#student-name").val() == ''
		|| $("#student-number").val() == ''
		|| $("#dormitory").val() == ''
		|| $("#contact").val() == ''
		|| $("#date").val() == ''
		|| $("#time-period").val() == '') {
			alert('请将预约信息填写完整！');
			return false;
	}
	return true;
}

function back() {
	$("#reservation").modal('close');
}
