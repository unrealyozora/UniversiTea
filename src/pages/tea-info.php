<?php
session_start();



$score = 0;
$q1_answer = '';
$q2_answer = '';
$showResult = false;
$resultMessage = '';

// Array per le classi CSS
$classes = [
    'Q1_WRONG1_CLASS' => '',
    'Q1_CORRECT_CLASS' => '',
    'Q1_WRONG2_CLASS' => '',
    'Q2_WRONG1_CLASS' => '',
    'Q2_CORRECT_CLASS' => '',
    'Q2_WRONG2_CLASS' => '',
];
$user_action = '';

// Array per i checked
$checked = [
    'Q1_WRONG1_CHECKED' => '',
    'Q1_CORRECT_CHECKED' => '',
    'Q1_WRONG2_CHECKED' => '',
    'Q2_WRONG1_CHECKED' => '',
    'Q2_CORRECT_CHECKED' => '',
    'Q2_WRONG2_CHECKED' => '',
];

// Controlla se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
    $showResult = true;

    // Verifica le risposte
    if (isset($_POST['q1'])) {
        $q1_answer = $_POST['q1'];
        if ($q1_answer === 'correct') {
            $score++;
        }
    }

    if (isset($_POST['q2'])) {
        $q2_answer = $_POST['q2'];
        if ($q2_answer === 'correct') {
            $score++;
        }
    }

    // Verifica che tutte le domande abbiano risposta
    if (empty($q1_answer) || empty($q2_answer)) {
        $resultMessage = '<p id="quiz-result">Seleziona una risposta per ogni domanda!</p>';
    } else {
        // Applica le classi alle risposte
        // Domanda 1
        if ($q1_answer === 'wrong1') {
            $classes['Q1_WRONG1_CLASS'] = 'wrong-selection';
            $checked['Q1_WRONG1_CHECKED'] = 'checked';
        } elseif ($q1_answer === 'correct') {
            $checked['Q1_CORRECT_CHECKED'] = 'checked';
        } elseif ($q1_answer === 'wrong2') {
            $classes['Q1_WRONG2_CLASS'] = 'wrong-selection';
            $checked['Q1_WRONG2_CHECKED'] = 'checked';
        }
        $classes['Q1_CORRECT_CLASS'] = 'correct-answer';

        // Domanda 2
        if ($q2_answer === 'wrong1') {
            $classes['Q2_WRONG1_CLASS'] = 'wrong-selection';
            $checked['Q2_WRONG1_CHECKED'] = 'checked';
        } elseif ($q2_answer === 'correct') {
            $checked['Q2_CORRECT_CHECKED'] = 'checked';
        } elseif ($q2_answer === 'wrong2') {
            $classes['Q2_WRONG2_CLASS'] = 'wrong-selection';
            $checked['Q2_WRONG2_CHECKED'] = 'checked';
        }
        $classes['Q2_CORRECT_CLASS'] = 'correct-answer';

        // Messaggio risultato
        if ($score === 2) {
            $resultMessage = '<p id="quiz-result">Risultato: 2/2! Complimenti, sei un vero esperto!</p>';
        } else {
            $resultMessage = '<p id="quiz-result">Risultato: ' . $score . '/2. Rileggi i dettagli sopra e riprova!</p>';
        }
    }
}

// Carica il template HTML
$html = file_get_contents("tea-info.html");

// Sostituisci i placeholder con i valori
$replacements = array_merge(
    ['{{RESULT_MESSAGE}}' => $resultMessage],
    array_map(function ($value) {
        return $value;
    }, $classes),
    array_map(function ($value) {
        return $value;
    }, $checked)
);

foreach ($replacements as $placeholder => $value) {
    $html = str_replace($placeholder, $value, $html);
}

$template = file_get_contents('templates/tea-info.html');
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $user_action = '<li>
                        <a href="dashboard.php" class="nav-profile-link">
                             <img src="../../assets/images/user.png" alt="Il tuo profilo">
                            <span class="mobile-only-text">Profilo</span>
                        </a>
                    </li>';
} else {
    $user_action = '<li>
                        <a href="register.php" class="btn-join" aria-label="Accedi ora al tuo account">
                            Accedi Ora
                        </a>
                    </li>';
}

echo str_replace('{{USER_ACTION}}', $user_action, $template);

echo $html;

?>