document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const errorMessage = document.getElementById('errorMessage');

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
    }

    function hideError() {
        errorMessage.textContent = '';
        errorMessage.style.display = 'none';
    }

    function validateUsername(username) {
        if (!username || username.trim() === '') {
            return 'Il campo Username o Email è obbligatorio';
        }
        return null;
    }

    function validatePassword(password) {
        if (!password || password.trim() === '') {
            return 'Il campo Password è obbligatorio';
        }
        if (password.length < 6) {
            return 'La password deve contenere almeno 6 caratteri';
        }
        return null;
    }

    form.addEventListener('submit', function (e) {
        hideError();

        const username = usernameInput.value.trim();
        const password = passwordInput.value;

        const usernameError = validateUsername(username);
        if (usernameError) {
            e.preventDefault();
            showError(usernameError);
            usernameInput.focus();
            return false;
        }

        const passwordError = validatePassword(password);
        if (passwordError) {
            e.preventDefault();
            showError(passwordError);
            passwordInput.focus();
            return false;
        }

        return true;
    });

    //Rimuovi il messaggio d'errore quando l'utente inizia a digitare
    usernameInput.addEventListener('input', hideError);
    passwordInput.addEventListener('input', hideError);
});