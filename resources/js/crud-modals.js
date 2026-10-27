/**

 * A trigger opens a form the server rendered for that action:
 *   <button data-crud-open data-crud-target="#companyCreateModal" data-crud-url="/companies/create">
 *
 * GET .../create  -> create form markup
 * GET .../{id}/edit -> edit form markup
 * POST/PUT        -> submitted by the single handler below
 */
(function () {
    'use strict';

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-crud-open]');
        if (!trigger) return;
        event.preventDefault();

        const modalEl = document.querySelector(trigger.dataset.crudTarget);
        if (!modalEl) return;

        const content = modalEl.querySelector('.modal-content');
        content.innerHTML = '<div class="modal-body text-center py-5">'
            + '<div class="spinner-border text-secondary" role="status"></div></div>';
        bootstrap.Modal.getOrCreateInstance(modalEl).show();

        $.ajax({
            url: trigger.dataset.crudUrl,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        }).done(function (html) {
            content.innerHTML = html;
        }).fail(function (xhr) {
            bootstrap.Modal.getInstance(modalEl)?.hide();
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Unable to load the form.');
        });
    });

    document.addEventListener('submit', function (event) {
        const form = event.target.closest('form.js-crud-form');
        if (!form) return;
        event.preventDefault();

        const errors = form.querySelector('.js-form-errors');
        const submitBtn = form.querySelector('[type="submit"]');
        const methodInput = form.querySelector('input[name="_method"]');
        const method = methodInput ? methodInput.value : 'POST';
        const label = submitBtn ? submitBtn.innerHTML : '';

        if (errors) errors.classList.add('d-none');
        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = 'Saving...'; }

        $.ajax({
            url: form.action,
            method: method,
            data: $(form).serialize(),
        }).done(function (response) {
            const modalEl = form.closest('.modal');
            if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();

            const box = document.getElementById('successMessage');
            if (box && response.message) {
                box.textContent = response.message;
                box.classList.remove('d-none');
                setTimeout(function () { box.classList.add('d-none'); }, 3000);
            }

            document.dispatchEvent(new CustomEvent('crud:saved', { detail: response }));
        }).fail(function (xhr) {
            const body = xhr.responseJSON || {};
            if (xhr.status === 422 && errors && body.errors) {
                errors.innerHTML = Object.values(body.errors).flat().map(function (message) {
                    return '<div>' + message + '</div>';
                }).join('');
                errors.classList.remove('d-none');
            } else {
                alert(body.message || 'Something went wrong.');
            }
        }).always(function () {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = label; }
        });
    });
})();
