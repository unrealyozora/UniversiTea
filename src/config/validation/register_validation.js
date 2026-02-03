document.addEventListener('DOMContentLoaded', function () {
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', validateRegistrationForm);

        const inputs = registrationForm.querySelectorAll('input')
        inputs.forEach(input => {
            input.addEventListener('input', function () {
                clearFieldError(this);
                this.classList.remove('error');
            });
        });
    }
});

function validateRegistrationForm(e) {
    const fields = {
        username: {
            element: document.getElementById('username'),
            rules: [
                {check: (val) => !val, message: "Campo obbligatorio"},
                {check: (val) => val.length < 3, message: "Username deve essere almeno 3 caratteri"},
            ]
        },
        email: {
            element: document.getElementById('email'),
            rules: [
                {check: (val) => !val, message: "Campo obbligatorio"},
            ]
        },
        password: {
            element: document.getElementById('password'),
            rules: [
                {check: (val) => !val, message: "Campo obbligatorio"},
                {check: (val) => val.length < 4, message: "Password deve essere almeno 4 caratteri"},
            ]
        },
        confirm_password: {
            element: document.getElementById('confirm_password'),
            rules: [
                {check: (val) => !val, message: "Campo obbligatorio"},
            ]
        },
        phone: {
            element: document.getElementById('phone'),
            rules: [
                {check: (val) => !val, message: "Campo obbligatorio"},
                {check: (val) => val.length !== 9 && val.length !== 10, message: "Numero di telefono non valido"}
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