document.addEventListener('DOMContentLoaded', function () {
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', validateRegistrationForm);

        const inputs = registrationForm.querySelectorAll('input')
        inputs.forEach(input => {
            input.addEventListener('input', function () {
                clearError();
                this.classList.add('error');
            });
        });
    }
});

function validateRegistrationForm(e) {
    const username = document.getElementById('username');
    const usernameValue = username.value.trim();
    const password = document.getElementById('password');
    const passwordValue = password.value.trim();
    const email = document.getElementById('email');
    const emailValue = email.value.trim();
    const passwordConfirm = document.getElementById('confirm_password');
    const passwordConfirmValue = passwordConfirm.value.trim();
    const phoneNumber = document.getElementById('phone')
    const phoneNumberValue = phoneNumber.value.trim();

    clearError();
    username.classList.remove('error');
    password.classList.remove('error');
    email.classList.remove('error');
    passwordConfirm.classList.remove('error');
    phoneNumber.classList.remove('error');

    if (!usernameValue || !passwordValue || !emailValue || !passwordConfirmValue || !phoneNumberValue) {
        e.preventDefault();

        if (!usernameValue) {
            username.classList.add('error');
        }
        if (!passwordValue) {
            password.classList.add('error');
        }
        if (!emailValue) {
            email.classList.add('error');
        }
        if (!passwordConfirmValue) {
            passwordConfirm.classList.add('error');
        }
        if (!phoneNumberValue) {
            phoneNumber.classList.add('error');
        }
        showError("Inserire i campi obbligatori")
    }

    if (usernameValue.length < 3) {
        e.preventDefault();
        username.classList.add('error');
        showError("Username deve essere almeno di 3 caratteri");
        return false;
    }

    if (passwordValue.length < 6) {
        e.preventDefault();
        password.classList.add('error');
        showError("Password deve essere almeno di 6 caratteri");
        return false;
    }

    if (phoneNumberValue.length !== 9 && phoneNumberValue.length !== 10) {
        e.preventDefault();
        phoneNumber.classList.add('error');
        showError("Numero di telefono non valido gay");
        return false;
    }
    return true;
}

function showError(message) {
    let errorDiv = document.getElementById('errorMessage');

    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = "errorMessage";
        errorDiv.className = ('error-message');

        const form = document.getElementById('registrationForm');
        form.insertBefore(errorDiv, form.firstChild);
    }

    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    errorDiv.setAttribute('role', 'alert');

    errorDiv.scrollIntoView({behavior: 'smooth', block: 'center'});
}

function clearError() {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv && !errorDiv.hasAttribute('data-server-error')) {
        errorDiv.textContent = '';
        errorDiv.style.display = 'none';
        errorDiv.removeAttribute('role');
    }
}