
#let project(title: "", authors: (), course: "", date: none, login: (), body) = {
  let tea-dark = rgb("#386641")
  let tea-light = rgb("#6A994E")
  let tea-bg = rgb("#F2F8F2")

  set page(paper: "a4", margin: (x: 2.5cm, y: 2.5cm), numbering: "1")
  set text(font: "Lato", lang: "it", size: 11pt, fill: rgb("#1a1a1a"))
  set par(justify: true)


  show heading.where(level: 1): it => block(below: 1em)[
    #set text(font: "Lato", size: 18pt, weight: "bold", fill: tea-dark)
    #it
    #v(-0.2em)
    #line(length: 100%, stroke: 2pt + tea-light)
  ]

  show heading.where(level: 2): it => block(above: 1.5em, below: 1em)[
    #set text(font: "Lato", size: 14pt, weight: "bold", fill: tea-dark)
    #it
  ]

  // Copertina
  align(center)[
    #v(3cm)
    // Titolo in Playfair Display, molto grande e verde scuro
    #text(font: "Atkinson Hyperlegible", size: 28pt, weight: "bold", fill: tea-dark, title)
    #v(1cm)
    #text(size: 16pt, style: "italic", fill: tea-light, "Relazione di Progetto")
    #v(0.5cm)
    #text(size: 14pt, course)
    #v(2cm)

    #text(size: 14pt, weight: "bold", fill: tea-dark, "Il Team")
    #v(0.5em)
    #grid(
      columns: (1fr,),
      gutter: 1.5em,
      ..authors.map(author => strong(author)),
    )
    #v(1cm)
    #text(size: 14pt, weight: "bold", fill: tea-dark, "Credenziali di Accesso")
    #v(0.5em)
    #grid(
      columns: (1fr,),
      gutter: 0.8em,
      ..login.map(l => text(l)),
    )
    #v(2cm)
    #if date != none {
      text(date)
    }
    #v(1fr)
    #image(width: 30%, "../assets/images/universitea_logo.svg")
    #v(2cm)
  ]

  pagebreak()

  

  body
}

// Funzione helper per le righe della tabella WCAG
#let wcag-row(criterio, livello, stato, strumento, note) = {
  (criterio, align(center)[#livello], align(center)[#stato], align(center)[#strumento], note)
}

// --- INIZIO DEL DOCUMENTO ---
#show: doc => project(
  title: "UniversiTea",
  course: "Tecnologie Web - A.A. 2025/2026",
  authors: (
    "Ceron Tommaso (Matr. 2101045) tommaso.ceron@studenti.unipd.it",
    "Marchioro Elisa (Matr. 2111941)",
    "Parolin Dennis (Matr. 2113203)",
    "Soligo Lorenzo (Matr. 2101057)",
  ),
  date:"2026 Febbraio 23",
  login: ("Link: caa.studenti.math.unipd.it/tceron/", "Venditore: admin  admin", "Utente: user  user"),

  doc,
)

= 1. Obiettivi del Sito
L'obiettivo principale del sito è creare un'esperienza utente immersiva che guidi il visitatore dalla scoperta delle varietà di prodotti, dalle bevande ai servizi tra studenti,fino all'acquisto.
Il sito mira a:
- Fornire un'interfaccia intuitiva e accattivante per esplorare i prodotti disponibili.
- Aiutare gli studenti fornendo una piattaforma per la vendita di servizi legati alla vita universitaria.
- Garantire l'accessibilità a tutti gli utenti, rispettando le linee guida WCAG 2.1 (Livello AA).
- Offrire contenuti informativi e guide per avvicinare gli utenti al mondo del tè.

= 2. Target di Riferimento
L'analisi dell'utenza ha identificato tre profili principali:
- *Il Neofita Curioso:* Utente che vuole avvicinarsi al mondo del tè ma necessita di guide passo-passo.
- *Lo Studente preoccupato:* Utente che cerca soluzioni pratiche e veloci per la propria vita quotidiana, target principale dei "_Servizi_".
- *Il Regalo Perfetto:* Utente che cerca consigli per regali unici e di qualità, che possa essere guidato nella scelta da contenuti informativi.

= 3. Contenuti e Struttura
Il sito è strutturato secondo la seguente sitemap:

/ Home Page: Hero section emozionale, prodotti in evidenza, ultime notizie dal blog.
/ Catalogo (Shop): Filtri per tipologia di prodotto, prezzo e disponibilità e per nome/descrizione.
/ Scheda Prodotto: Foto ad alta risoluzione, descrizione con tutti i dettagli, pulsante "Aggiungi al carrello".
/ I nostri tè: Guida alle varietà di tè con specifica delle differenze.
/ Chi Siamo: Storia dell'azienda.
/ Il mio Profilo: Area personale: per i Venditori un Pannello di Gestione, per il cliente comune un informativa sui Punti Fedeltà e punto di accesso per la Pagina dei preferiti.




= 3. Accessibilità (Monitoraggio WCAG 2.1)
In questa sezione viene analizzata la conformità del sito alle linee guida WCAG 2.1 (Livello AA), come richiesto dalle specifiche di progetto. La seguente tabella riassume lo stato di validazione dei criteri principali.

#align(center)[
  #text(weight: "bold", size: 12pt, fill: rgb("#386641"))[Tabella 1: Checklist di conformità WCAG 2.1 AA Completa]
]
#v(0.5em)

#table(
  columns: (25%, 13%, 12%, 20%, 35%),
  inset: 8pt,
  align: (x, y) => if y == 0 { center + horizon } else { left },

  fill: (x, y) => if y == 0 { rgb("#dcecdb") } else { none },
  stroke: 0.5pt + rgb("#6A994E"),

  table.header(
    [*Criterio di Successo*], [*Livello*], [*Stato*], [*Strumento di Monitoraggio*], [*Note di Implementazione*]
  ),

  // 1. PERCETTIBILE
  table.cell(colspan: 5, fill: rgb("#F2F8F2"))[*1. Percettibile*],

  ..wcag-row(
    "1.1.1 Contenuti non testuali",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "NVDA",
    "Attributi alt presenti su logo e prodotti. Modelli 3D nascosti agli screen reader.",
  ),
  ..wcag-row(
    "1.3.1 Informazioni e correlazioni",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Struttura semantica (header, main, footer, nav, article) corretta.",
  ),
  ..wcag-row(
    "1.3.2 Sequenza significativa",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Silktide e controllo manuale",
    "L'ordine del DOM rispecchia l'ordine visivo anche nei layout grid/flex.",
  ),
  ..wcag-row(
    "1.3.5 Identificazione dello scopo degli input", "AA",
    text(fill: rgb("#2d4f1e"), strong("Pass")), "Controllo manuale",
    "I campi dei form (login/register) usano l'attributo autocomplete appropriato."
  ),
  ..wcag-row(
    "1.4.1 Uso del colore",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "WCAG Contrast Checker",
    "I link sono distinguibili non solo per colore ma anche tramite sottolineatura, icone e/o aspetto da bottone.",
  ),
  ..wcag-row(
    "1.4.3 Contrasto minimo",
    "AA",
    text(fill: rgb("#d97706"), strong("Parziale")),
    "WCAG Contrast Checker",
    "Testo su sfondo scuro verificato (Ratio > 4.5:1 per testo normale). L'unico punto in cui il contrasto non è rispettato è una sezione in cui i link visitati risultano non sufficientemente contrastati (Ratio < 3:1).",
  ),
  ..wcag-row(
    "1.4.4 Ridimensionamento del testo",
    "AA",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale e W3C Css Validator",
    "Layout fluido, supporta zoom senza perdita di contenuto.",
  ),
  ..wcag-row(
    "1.4.11 Contrasto contenuti non testuali",
    "AA",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "WCAG Contrast Checker",
    "Icone social nel footer migliorate, verificata visibilità focus outline.",
  ),
  ..wcag-row(
    "1.4.12 Spaziatura del testo",
    "AA",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Nessuna altezza fissa sui contenitori di testo, line-height impostato a 1.6.",
  ),

  // 2. UTILIZZABILE
  table.cell(colspan: 5, fill: rgb("#F2F8F2"))[*2. Utilizzabile*],

  ..wcag-row(
    "2.1.1 Tastiera",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Intero sito navigabile via Tab. Il carrello è attivabile via Enter.",
  ),
  ..wcag-row(
    "2.1.2 Nessuna trappola da tastiera",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Il menu mobile e i modali permettono la chiusura via tastiera (Esc).",
  ),
  ..wcag-row(
    "2.4.1 Salto di blocchi",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Link che riporta all'inizio della pagina in basso a destra e indice nella pagina Il nostro tè",
  ),
  ..wcag-row(
    "2.4.2 Titolazione della pagina",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Ogni pagina ha un tag <title> unico e descrittivo (es. 'Shop - UniversiTea').",
  ),
  ..wcag-row(
    "2.4.3 Ordine del focus",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "L'apertura del menu mobile sposta correttamente il focus al suo interno.",
  ),
  ..wcag-row(
    "2.4.4 Scopo del collegamento",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Link generici 'Scopri di più' integrati con aria-label descrittivi.",
  ),
  ..wcag-row(
    "2.4.6 Intestazioni ed etichette", "AA",
    text(fill: rgb("#2d4f1e"), strong("Pass")), "Controllo manuale/TotalValidator",
    "Gerarchia H1-H6 rispettata, label dei form chiare e visibili."
  ),
  ..wcag-row(
    "2.4.7 Focus visibile", "AA",
    text(fill: rgb("#2d4f1e"), strong("Pass")), "Controllo manuale/SilkTide",
    "Outline CSS personalizzato (verde chiaro) su tutti gli elementi interattivi."
  ),

  // 3. COMPRENSIBILE

  table.cell(colspan: 5, fill: rgb("#F2F8F2"))[*3. Comprensibile*],

  ..wcag-row(
    "3.1.1 Lingua della pagina",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Attributo lang='it' impostato sul tag html.",
  ),
  ..wcag-row(
    "3.1.2 Lingua di parti", "AA",
    text(fill: rgb("#2d4f1e"), strong("Pass")), "Controllo manuale/NVDA",
    "Termini inglesi (es. 'Join Now') marcati con <span lang='en'>."
  ),
  ..wcag-row(
    "3.2.1 Al focus",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Nessun elemento avvia azioni o cambi di contesto al solo focus.",
  ),
  ..wcag-row(
    "3.3.1 Identificazione errori",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Errori nei form segnalati testualmente vicino al campo errato.",
  ),
  ..wcag-row(
    "3.3.2 Etichette o istruzioni",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Campi obbligatori segnalati chiaramente prima dell'invio.",
  ),

  // ============================================================
  // 4. ROBUSTO
  // ============================================================
  table.cell(colspan: 5, fill: rgb("#F2F8F2"))[*4. Robusto*],

  ..wcag-row(
    "4.1.1 Parsing",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "PHPStorm Validator",
    "Codice validato W3C, ID univoci garantiti.",
  ),
  ..wcag-row(
    "4.1.2 Nome, ruolo, valore",
    "A",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Componenti custom (es. carrello) usano ARIA roles corretti.",
  ),
  ..wcag-row(
    "4.1.3 Messaggi di stato",
    "AA",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Feedback aggiunta al carrello gestito con aria-live='polite'.",
  ),
)

== Note aggiuntive sull'Accessibilità
Per verificare i contrasti cromatici è stato utilizzato lo strumento *WebAIM Contrast Checker*. Le icone puramente decorative sono state nascoste agli screen reader utilizzando `aria-hidden="true"`.\
Il controllo della correttezza del codice è stato validato in due modalità automatiche. La prima l'utilizzo di *TotalValidator*, la seconda l'utilizzo di *PHPStorm* il quale indica errori formali, link non funzionanti (a livello statico) e _best practice_ non rispettate. Particolarità di PHPStorm è inoltre la possibilità di impostare un server remoto su cui riversare il progetto, rendendo automatico il caricamento sul server _tecweb_.
Inoltre, sono stati fatti ulteriori check anche con W3C Css Validator e W3C HTML Validator.
