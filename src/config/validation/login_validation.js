document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', validateLoginForm);

        const inputs = loginForm.querySelectorAll('input')
        inputs.forEach(input => {
            input.addEventListener('input', function () {
                clearFieldError(this);
                this.classList.remove('error');
            });
        });
    }
});

function validateLoginForm(e) {
    const fields = {
        username: {
            element: document.getElementById('username'),
            rules: [
                {check: (val) => !val, message: "Campo obbligatorio"},
            ]
        },
        password: {
            element: document.getElementById('password'),
            rules: [
                {check: (val) => !val, message: "Campo obbligatorio"},
            ]
        }
    };
    clearAllErrors();

    Object.values(fields).forEach(field => field.element.classList.remove('error'));
    let hasError = false;
    for (const [fieldName, field] of Object.entries(fields)) {
        const value = field.element.value.trim();
        for (const rule of field.rules) {
            if (rule.check(value)) {
                e.preventDefault();
                field.element.classList.add('error');
                showFieldError(field.element, rule.message);
                hasError = true;
                break;
            }
        }
    }
    return !hasError;
}

function showFieldError(fieldElement, message) {
    let errorDiv = fieldElement.parentElement.querySelector('.error-msg');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-msg';
        fieldElement.parentElement.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
    errorDiv.setAttribute('role', 'alert');
}

function clearFieldError(fieldElement) {
    const errorDiv = fieldElement.parentElement.querySelector('.error-msg');
    if (errorDiv && !errorDiv.hasAttribute('data-server-error')) {
        errorDiv.remove();
    }
}

function clearAllErrors() {
    const errorDivs = document.querySelectorAll(".error-msg");
    errorDivs.forEach(errorDiv => {
        if (!errorDiv.hasAttribute('data-server-error')) {
            errorDiv.remove();
        }
    });
}