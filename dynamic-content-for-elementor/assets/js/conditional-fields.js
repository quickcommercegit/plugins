"use strict";

const getFieldsValues = (fields) => {
	const values = {};
	for (const id in fields) {
		let $inputs = fields[id].inputs;
		if ($inputs.length === 1) {
			if( $inputs.attr('type') === 'checkbox') {
				if ($inputs.prop('checked')) {
					values[id] = $inputs.val();
				} else {
					values[id] = '';
				}
			} else {
				values[id] = $inputs.val();
			}
		} else { // field with multiple inputs, like checkbox:
			let checked = [];
			$inputs.each( (_, e) => {
				if (e.checked) {
					checked.push(e.value);
				}
			})
			if ($inputs.attr('type') === "radio") { // unwrap value from array if type is radio:
				values[id] = checked[0];
			} else { // it's a list of checkboxes:
				values[id] = checked.length ? checked : '';
			}
		}
	}
	return values;
};

// Returns a JS object with the HTML ids of all the input fields as keys and the
// jQuery input elements and wrapper elements obtained from the ids as values.
const getAllFields = ($form, fieldIds) => {
	const fields = {};
	for (const id of fieldIds) {
		const $group = $form.find('.elementor-field-group-' + id);
		if ( $group.length ) {
			fields[id] = { wrapper: $group };
			let $field = $group.find(`[name=form_fields\\[${id}\\]]`);
			if ($field.length) { // it's a simple field with only one input.
				fields[id].inputs = $field;
			} else {
				// multiple inputs, like in the case of the checkbox field:
				fields[id].inputs = $group.find(`[name=form_fields\\[${id}\\]\\[\\]]`);
			}
		}
	}
	return fields;
}

// Receive a jQuery list of input elements. Returns true if they are all disabled.
const areAllInputsDisabled = ( $inputs ) => {
	for (const input of $inputs) {
		const $input = jQuery(input);
		if (! $input.prop('disabled')) {
			return false;
		}
	}
	return true;
}

// If all the inputs in a step are inactive because of conditional-fields we
// want to automatically jump over them. We do this by adding handlers to the
// next and previous buttons. The handler for next buttons will check if all the
// field on the next step are disabled, if so it will trigger the click of the
// next button of the next step. Same but opposite for previous buttons.
const initializeStepsJumping = ($form) => {
	if (! $form.hasClass('dce-form-has-conditions')) {
		return; // no conditions, nothing to do.
	};
	let $steps = $form.find('.elementor-field-type-step');
	if ($steps.length < 3) {
		return; // two steps or less, nothing to jump over.
	}
	let steps = [];
	for (const step of $steps) {
		const $step = jQuery(step);
		const $inputs = $step.find('input,select');
		const $next = $step.find('.elementor-field-type-next button');
		const $previous = $step.find('.elementor-field-type-previous button');
		steps.push({
			nextButton: $next,
			previousButton: $previous,
			inputs: $inputs
		})
	}
	// Set click handlers for next buttons:
	// length - 2 because we never jump over the last step.
	for (let i = 0; i < (steps.length - 2); i++) {
		const nextStep = steps[i+1];
		steps[i].nextButton.on('click', () => {
			if (areAllInputsDisabled(nextStep.inputs)) {
				nextStep.nextButton.trigger('click');
			}
		});
	}
	// Set click handlers for previous buttons:
	// i = 2 because we never jump over the first step.
	for (let i = 2; i < steps.length; i++) {
		const prevStep = steps[i-1];
		steps[i].previousButton.on('click', () => {
			if (areAllInputsDisabled(prevStep.inputs)) {
				prevStep.previousButton.trigger('click');
			}
		});
	}
}

function initializeConditionalFields($form) {
	const $outWrapper = $form.find('.elementor-form-fields-wrapper');
	let conditions = $outWrapper.attr('data-conditions-expressions');
	let fieldIds = $outWrapper.attr('data-field-ids');
	if (conditions === undefined) {
		return; // no conditional fields.
	}
	$form.addClass('dce-form-has-conditions');
	const lang = new expressionLanguage.ExpressionLanguage();
	fieldIds = JSON.parse(fieldIds);
	conditions = JSON.parse(conditions);
	const fields = getAllFields($form, fieldIds);
	const onChange = () => {
		const values = getFieldsValues(fields);
		for (const cond of conditions) {
			let result;
			try {
				result = lang.evaluate(cond.condition, values);
			} catch (error) {
				$form.prepend(`<div class="error">Conditional Fields v2 Error (the error is on the conditions of field ""<code>${cond.id}</code>"): ${error}</div>`);
			}
			const $fieldInputs = fields[cond.id].inputs;
			const $fieldWrapper = fields[cond.id].wrapper;
			const isActive = cond.mode === 'show' ? result : ! result;
			if ( isActive ) {
				$fieldInputs.prop('disabled', false);
				$fieldInputs.show();
				$fieldWrapper.show();
			} else {
				// if inactive we don't want its value to influence further conditions:
				values[cond.id] = '';
				$fieldInputs.prop('disabled', true)
				if (! cond.disableOnly) {
					$fieldInputs.hide();
					$fieldWrapper.hide();
				}
			}
		}
	}
	onChange();
	let $all = $form.find('input, select');
	$all.on('change', onChange);
}

jQuery(window).on('elementor/frontend/init', function() {
	elementorFrontend.hooks.addAction('frontend/element_ready/form.default', initializeConditionalFields);
	// We delay adding the hook so it is run after the steps ares initialized:
	elementorFrontend.hooks.addAction('frontend/element_ready/global', () => {
		elementorFrontend.hooks.addAction('frontend/element_ready/form.default', initializeStepsJumping);
	});
});
