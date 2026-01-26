// Gestione animazioni
const teaContainer = document.querySelector('.tea-content');
const animCheck = document.getElementById('disable-anim');

if (animCheck) {
    animCheck.addEventListener('change', function() {
        if (this.checked) {
            teaContainer.classList.add('no-anim');
        } else {
            teaContainer.classList.remove('no-anim');
        }
    });
}

// Gestione Quiz
const submitBtn = document.getElementById('submit-quiz');
if (submitBtn) {
    submitBtn.addEventListener('click', function() {
        const form = document.querySelector('.tea-quiz');
        const result = document.getElementById('quiz-result');
        const labels = form.querySelectorAll('label');
        
        labels.forEach(label => label.classList.remove('correct-answer', 'wrong-selection'));

        const q1 = form.elements['q1'].value;
        const q2 = form.elements['q2'].value;

        if (!q1 || !q2) {
            result.innerHTML = "Seleziona una risposta per ogni domanda!";
            result.classList.remove('hidden');
            result.style.color = "#dc3545";
            return;
        }

        const inputs = form.querySelectorAll('input[type="radio"]');
        inputs.forEach(input => {
            const parentLabel = input.parentElement;
            if (input.value === "correct") {
                parentLabel.classList.add('correct-answer');
            } else if (input.checked && input.value === "wrong") {
                parentLabel.classList.add('wrong-selection');
            }
        });

        let score = 0;
        if (q1 === "correct") score++;
        if (q2 === "correct") score++;

        result.classList.remove('hidden');
        if (score === 2) {
            result.innerHTML = "Risultato: 2/2! Complimenti, sei un vero esperto!";
            result.style.color = "#28a745";
        } else {
            result.innerHTML = `Risultato: ${score}/2. Rileggi i dettagli sopra e riprova!`;
            result.style.color = "#dc3545";
        }
    });
}