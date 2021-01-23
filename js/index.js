$(async function () {
	// elements for easy access
	let $table = $('#table'),
		$tbody = $('#table-body'),
		$popup = $('#pop-up'),
		$closeForm = $popup.find('#close-form'),
		$form = $popup.find('form'),
		$empId = $form.find('#emp-id'),
		$empName = $form.find('#emp-name'),
		$empDob = $form.find('#emp-dob'),
		$empCtc = $form.find('#emp-ctc'),
		$empTech = $form.find('#emp-tech'),
		$empTechEx = $form.find('#emp-tech-ex'),
		$selectedTech = $form.find('#selected-tech'),
		$addTech = $form.find('#add-tech'),
		$addEmployee = $('#add-emp'),
		$noData = $('#no-data');


	// hide all elements that needs to toggle visibility
	$noData.hide();
	$table.hide();
	$popup.hide();


	// initialize jqueryui datepicker
	$empDob.datepicker({ dateFormat: 'dd-mm-yy' });


	// fetching data from server and adding to DOM
	let employeeEndPoint = '/api/employees.php',
		techEndPoint = 'api/technologies.php'

	let getEmployees = $.ajax({ url: employeeEndPoint }).promise(),
		getTechs = $.ajax({ url: techEndPoint }).promise();

	let employees, technologies;

	try {
		employees = await getEmployees;
		technologies = await getTechs;
	} catch (err) {
		alert(err.responseText);
	}


	// functions to manipulate table
	function addToTabale(employee) {
		let $tr = $('<tr/>').data('employee', employee),
			$id = $('<td/>').text(employee.id),
			$name = $('<td/>').text(employee.name),
			$dob = $('<td/>').text(employee.dob),
			$ctc = $('<td/>').text(employee.ctc),
			$techs = $('<td/>'),
			$edit = $('<td/>'),
			$delete = $('<td/>'),
			$editBtn = $('<button/>').addClass('btn btn-secondary btn-sm').text('edit'),
			$deleteBtn = $('<button/>').addClass('btn btn-danger btn-sm').text('delete');

		for (let t of Object.keys(employee.tech)) {
			let $tech = $('<span/>').addClass('badge badge-pill badge-info px-2 mr-1');
			$tech.text(`${t}: ${employee.tech[t]}`);
			$techs.append($tech);
		}

		$editBtn.on('click', () => { openForm($tr); });
		$deleteBtn.on('click', () => { deleteRow($tr) });

		$editBtn.appendTo($edit);
		$deleteBtn.appendTo($delete);

		$tr.append($id, $name, $dob, $ctc, $techs, $edit, $delete);
		$tbody.append($tr);
		$table.show();
		$noData.hide();
	}

	function updateRow($row, data) {
		$row.data('employee', data);
		let td = $row.find('td');
		td.eq(0).text(data.id);
		td.eq(1).text(data.name);
		td.eq(2).text(data.dob);
		td.eq(3).text(data.ctc);
		td.eq(4).text('');

		for (let t of Object.keys(data.tech)) {
			let $tech = $('<span/>').addClass('badge badge-pill badge-info px-2 mr-1');
			$tech.text(`${t}: ${data.tech[t]}`);
			td.eq(4).append($tech);
		}

		td.eq(5).find('button').off();
		td.eq(5).find('button').on('click', () => { openForm($row); });

		td.eq(6).find('button').off();
		td.eq(6).find('button').on('click', () => { deleteRow($row) });
	}

	function deleteRow($row) {
		let id = $row.data('employee').id;

		$.ajax({
			url: `${employeeEndPoint}?id=${id}`,
			method: 'DELETE',
			success: function () {
				$row.remove();
				if (!$tbody.find('tr').length) {
					$table.hide();
					$noData.show();
				}
			},
			error: function (xhr) {
				alert(xhr.responseText);
			}
		});
	}


	// functions to manipulate form
	function populateSelect(technologies) {
		$empTech.html('');
		$empTech.append($('<option/>'));
		for (let tech of technologies) {
			$option = $('<option/>').text(tech);

			$empTech.append($option);
		}
	}

	function resetForm() {
		$empName.val('');
		$empDob.val('');
		$empCtc.val('');
		$empId.val('');
		$selectedTech.data('selected', null);
		$selectedTech.find('span').remove();
		$form.data('row', null);
	}

	function openForm($row = null) {
		let data = $row ? $row.data('employee') : {};

		$popup.show();
		$form.data('row', $row);
		$empName.val(data.name);
		$empDob.val(data.dob);
		$empCtc.val(data.ctc);
		$empId.val(data.id);

		let tech = [...technologies];
		if (data.tech) {
			$selectedTech.data('selected', data.tech);
			for (let key of Object.keys(data.tech)) {
				let yrs = data.tech[key];

				addTechBadge(key, yrs);

				tech.splice(tech.indexOf(key), 1);
			}
		}

		populateSelect(tech);
	}

	function addTechBadge (tech, yrs) {
		let $badge = $('<span/>').addClass('tech badge badge-warning ml-2'),
			$i = $('<i/>').text('×');

		$badge.text(`${tech}: ${yrs}`);
		$i.on('click', () => {
			$badge.remove();
			let selected = $selectedTech.data('selected');
			delete selected[tech];
			$selectedTech.data('selected', selected);
		});

		$badge.append($i);
		$selectedTech.append($badge);
	}


	// populate table
	for (let employee of employees) {
		addToTabale(employee);
	}

	// initialize select2
	$empTech.select2();

	// bind function to add technology button
	$addTech.on('click', function (event) {
		let tech = $empTech.val(),
			yrs = $empTechEx.val();

		$empTech.val('');
		$empTechEx.val(0);

		addTechBadge(tech, yrs);

		$empTech.find('option').map(function () {
			$el = $(this);
			if ($el.text() == tech) {
				$el.remove();
			}
		});

		let selected = $selectedTech.data('selected') || {};

		selected[tech] = yrs;
		$selectedTech.data('selected', selected);

		event.preventDefault();
	});


	// remove default behaviour of form and make ajax request on submit to update or add data
	$form.on('submit', async function (event) {
		event.preventDefault();

		let payload = {}, method = 'POST';

		payload.name = $empName.val();
		payload.dob = $empDob.val();
		payload.ctc = $empCtc.val();
		payload.tech = $selectedTech.data('selected');

		if ($empId.val()) {
			payload.id = $empId.val();
			method = 'PUT';
		}

		let ajaxPromise = $.ajax({ url: employeeEndPoint, method: method, data: payload }).promise();
		try {
			let data = await ajaxPromise;

			if ($form.data('row')) {
				updateRow($form.data('row'), data);
			} else {
				addToTabale(data);
			}

			resetForm();
			$popup.hide();
		} catch (err) {
			alert(err.responseText);
		}
	});

	// bind function to add employee button
	$addEmployee.on('click', () => openForm());

	// event handler for close form button: close and reset form
	$closeForm.on('click', () => {
		$popup.hide()
		resetForm();
	})
});