document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', validateLoginForm);

        const inputs = loginForm.querySelectorAll('input')
        inputs.forEach(input => {
            input.addEventListener('input', function () {
                clearError();
                this.classList.add('error');
            });
        });
    }
});

function validateLoginForm(e) {
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    const usernameValue = username.value.trim();
    const passwordValue = password.value.trim();

    // Reset errori precedenti
    clearError();
    username.classList.remove('error');
    password.classList.remove('error');

    // Validazione campi vuoti
    if (!usernameValue || !passwordValue) {
        e.preventDefault();

        if (!usernameValue) {
            username.classList.add('error');
        }
        if (!passwordValue) {
            password.classList.add('error');
        }

        showError('Username e password sono obbligatori');
        return false;
    }

    // Validazione lunghezza minima username
    if (usernameValue.length < 3) {
        e.preventDefault();
        username.classList.add('error');
        showError('L\'username deve essere di almeno 3 caratteri');
        return false;
    }

    // Validazione lunghezza minima password
    if (passwordValue.length < 6) {
        e.preventDefault();
        password.classList.add('error');
        showError('La password deve essere di almeno 6 caratteri');
        return false;
    }

    return true;
}

function showError(message) {
    let errorDiv = document.getElementById('errorMessage');

    // Se non esiste, crealo dinamicamente
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'errorMessage';
        errorDiv.className = 'error-message';

        const form = document.getElementById('loginForm');
        form.insertBefore(errorDiv, form.firstChild);
    }

    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    errorDiv.setAttribute('role', 'alert');

    // Scroll verso l'errore per visibilità
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
