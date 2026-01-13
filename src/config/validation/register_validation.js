document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('confirm_password');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
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
        if (username.length < 3 || username.length > 32) {
            return 'Il campo Username deve essere compreso tra 3 e 32 caratteri'
        }
        return null;
    }

    function validateEmail(email) {
        if (email.length < 3) {
            return 'Email non valida';
        }
    }

    function validatePassword(password) {
        if (!password || password.trim() === '') {
            return 'Il campo Password è obbligatorio';
        }
        if (password.length < 6) {
            return 'Il campo Password deve essere compreso tra 6 e 32 caratteri';
        }
        return null;
    }

    function checkPasswordMatch(password, confirmPassword) {
        if (!(confirmPassword === confirmPassword)) {
            return 'Le due password non corrispondono';
        }
    }

    function validatePhoneNumber(phoneNumber) {
        if (phoneNumber.length !== 9 || phoneNumber.length !== 10) {
            return 'Numero di telefono non valido';
        }
    }

    form.addEventListener('submit', function (e) {
        hideError();

        const username = usernameInput.value.trim();
        const password = passwordInput.value;
        const passwordConfirm = passwordConfirmInput.value;
        const email = emailInput.value;
        const phoneNumber = phoneInput.value;

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

        const passwordConfirmError = checkPasswordMatch(password);
        if (passwordConfirmError) {
            e.preventDefault();
            showError(passwordConfirmError);
            passwordConfirmInput.focus();
            return false;
        }

        const emailError = validateEmail(password);
        if (emailError) {
            e.preventDefault();
            showError(emailError);
            emailInput.focus();
            return false;
        }

        const phoneNumberError = validatePhoneNumber(phoneNumber);
        if (phoneNumberError) {
            e.preventDefault();
            showError(phoneNumberError);
            phoneInput.focus();
            return false;
        }
        return true;
    });

    //Rimuovi il messaggio d'errore quando l'utente inizia a digitare
    usernameInput.addEventListener('input', hideError);
    passwordInput.addEventListener('input', hideError);
});
