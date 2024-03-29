"use strict";

const initializeHiddenLabelField = (field, form) => {
	const input = field.getElementsByTagName('input')[0];
	const fieldId = input.getAttribute('data-field-id');
	const hiddenField = form.getElementsByClassName('elementor-field-group-' + fieldId)[0];
	if (! hiddenField) {
		let span = document.createElement('span');
		span.textContent = 'Hiddel Label: Field not found.';
		form.prepend(span);
		return;
	}
	const hiddenInputs = hiddenField.querySelectorAll(`[name^=form_fields]`)
	const getLabel = () => {
		if (hiddenInputs.length === 1) { // Select:
			return hiddenInputs[0].options[hiddenInputs[0].selectedIndex].innerHTML;
		} else { // Checkbox os Radio:
			let labels = [];
			for (let input of hiddenInputs) {
				if (input.checked) {
					let id = input.getAttribute('id');
					let label = form.querySelector(`label[for=${id}]`).innerHTML;
					labels.push(label);
				}
			}
			return labels.join(', ');
		}
	}
	const updateLabel = () => {
		input.value = getLabel();
	};
	updateLabel();
	if (hiddenField) {
		hiddenField.addEventListener('change', updateLabel);
	} else {
		alert(`Hidden Label, Could not find selector ${fieldId}`);
	}
}

const initializeHiddenLabelFields = ($form) => {
	$form.find('.elementor-field-type-hidden_label').each((_, f) => initializeHiddenLabelField(f, $form[0]));
};

jQuery(window).on('elementor/frontend/init', function() {
	elementorFrontend.hooks.addAction('frontend/element_ready/form.default', initializeHiddenLabelFields);
});
