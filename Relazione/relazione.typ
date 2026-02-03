
#let project(title: "", authors: (), course: "", date: none, login:(), body) = {

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

  show outline.entry: it => text(font: "Lato", it)
  outline(title: text(font: "Lato", fill: tea-dark)[Indice dei Contenuti], indent: auto)
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
      "Ceron Tommaso (Matr. 2101045)",
      "Marchioro Elisa (Matr. 2111941)",
      "Parolin Dennis (Matr. 2113203)",
      "Soligo Lorenzo (Matr. 2101057)"
  ),
  date: "30 Dicembre 2025",
  login: ("Link: tecweb.studenti.math.unipd.it/tceron/",
          "Venditore: admin  admin",
          "Utente: user  user"),

  doc,
)

= 1. Introduzione al Progetto
Il presente documento descrive la progettazione e lo sviluppo del sito web "UniversiTea", una piattaforma e-commerce dedicata alla vendita e alla cultura del tè pregiato rivolta agli studenti universitari, visti come il Target principale. \ 
Il progetto è stato realizzato nell'ambito del corso di Tecnologie Web, applicando le best practices di sviluppo frontend, design responsivo e accessibilità.

= 2. Obiettivi del Sito
L'obiettivo principale del sito è creare un'esperienza utente immersiva che guidi il visitatore dalla scoperta delle varietà di prodotti, dalle bevande ai servizi tra studenti,fino all'acquisto.
Il sito mira a:
- Fornire un'interfaccia intuitiva e accattivante per esplorare i prodotti disponibili.
- Aiutare gli studenti fornendo una piattaforma per la vendita di servizi legati alla vita universitaria.
- Garantire l'accessibilità a tutti gli utenti, rispettando le linee guida WCAG 2.1 (Livello AA).
- Offrire contenuti informativi e guide per avvicinare gli utenti al mondo del tè.

= 3. Target di Riferimento
L'analisi dell'utenza ha identificato tre profili principali:
- *Il Neofita Curioso:* Utente che vuole avvicinarsi al mondo del tè ma necessita di guide passo-passo.
- *Lo Studente preoccupato:* Utente che cerca soluzioni pratiche e veloci per la propria vita quotidiana, target principale dei "_Servizi_".
- *Il Regalo Perfetto:* Utente che cerca consigli per regali unici e di qualità, che possa essere guidato nella scelta da contenuti informativi.

= 4. Contenuti e Struttura
Il sito è strutturato secondo la seguente sitemap:

/ Home Page: Hero section emozionale, prodotti in evidenza, ultime notizie dal blog.
/ Catalogo (Shop): Filtri per tipologia di prodotto, prezzo e disponibilità e per nome/descrizione.
/ Scheda Prodotto: Foto ad alta risoluzione, descrizione con tutti i dettagli, pulsante "Aggiungi al carrello".
/ I nostri tè: Guida alle varietà di tè con specifica delle differenze.
/ Chi Siamo: Storia dell'azienda.
/ Il mio Profilo: Area personale: per i Venditori un Pannello di Gestione, per il cliente comune un informativa sui Punti Fedeltà e punto di accesso per la Pagina dei preferiti.

Il sito si appoggia di un database MySQL per la persistenza di dati quali account e prodotti, seguendo il seguente schema:
#figure(
  image("BasiDati_TecWeb.png", width: 80%),
  caption: [
    Schema ER del database utilizzato
  ],
)




= 5. Accessibilità (Monitoraggio WCAG 2.1)
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
    [*Criterio di Successo*], [*Livello*], [*Stato*], [*Strumento di Monitoraggio*],[*Note di Implementazione*],
  ),

  
  // 1. PERCETTIBILE
  table.cell(colspan: 5, fill: rgb("#F2F8F2"))[*1. Percettibile*],
  
  ..wcag-row(
    "1.1.1 Contenuti non testuali", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "NVDA",
    "Attributi alt presenti su logo e prodotti. Modelli 3D nascosti agli screen reader."
  ),
  ..wcag-row(
    "1.2.1 Solo audio/video (preregistrato)", "A", 
    text(fill: rgb("#888888"), "N/A"), "placeholder",
    "Il sito non presenta contenuti multimediali temporali."
  ),
  ..wcag-row(
    "1.3.1 Informazioni e correlazioni", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Struttura semantica (header, main, footer, nav, article) corretta."
  ),
  ..wcag-row(
    "1.3.2 Sequenza significativa", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "Silktide e controllo manuale",
    "L'ordine del DOM rispecchia l'ordine visivo anche nei layout grid/flex."
  ),
  ..wcag-row(
    "1.3.5 Identificazione dello scopo degli input", "AA", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "I campi dei form (login/register) usano l'attributo autocomplete appropriato."
  ),
  ..wcag-row(
    "1.4.1 Uso del colore", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "I link sono distinguibili non solo per colore ma anche tramite sottolineatura, icone e/o aspetto da bottone."
  ),
  ..wcag-row(
    "1.4.3 Contrasto minimo", "AA", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "WCAG Contrast Checker",
    "Testo su sfondo scuro verificato (Ratio > 4.5:1 per testo normale)."
  ),
  ..wcag-row(
    "1.4.4 Ridimensionamento del testo", "AA", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Layout fluido, supporta zoom fino al 200% senza perdita di contenuto."
  ),
  ..wcag-row(
    "1.4.11 Contrasto contenuti non testuali", "AA", 
    text(fill: rgb("#d97706"), strong("Parziale")), "placeholder",
    "Icone social nel footer migliorate, verificata visibilità focus outline."
  ),
  ..wcag-row(
    "1.4.12 Spaziatura del testo", "AA", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Nessuna altezza fissa sui contenitori di testo, line-height impostato a 1.6."
  ),

 
  // 2. UTILIZZABILE
  table.cell(colspan: 5, fill: rgb("#F2F8F2"))[*2. Utilizzabile*],

  ..wcag-row(
    "2.1.1 Tastiera", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Intero sito navigabile via Tab. Il carrello è attivabile via Enter."
  ),
  ..wcag-row(
    "2.1.2 Nessuna trappola da tastiera", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Il menu mobile e i modali permettono la chiusura via tastiera (Esc)."
  ),
  ..wcag-row(
    "2.4.1 Salto di blocchi", "A", 
    text(fill: rgb("#d97706"), strong("ToDo")), "placeholder",
    "Manca un link 'Skip to content' all'inizio della pagina."
  ),
  ..wcag-row(
    "2.4.2 Titolazione della pagina", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Ogni pagina ha un tag <title> unico e descrittivo (es. 'Shop - UniversiTea')."
  ),
  ..wcag-row(
    "2.4.3 Ordine del focus", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "L'apertura del menu mobile sposta correttamente il focus al suo interno."
  ),
  ..wcag-row(
    "2.4.4 Scopo del collegamento", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Link generici 'Scopri di più' integrati con aria-label descrittivi."
  ),
  ..wcag-row(
    "2.4.6 Intestazioni ed etichette", "AA", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Gerarchia H1-H6 rispettata, label dei form chiare e visibili."
  ),
  ..wcag-row(
    "2.4.7 Focus visibile", "AA", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Outline CSS personalizzato (verde chiaro) su tutti gli elementi interattivi."
  ),

  // 3. COMPRENSIBILE
  
  table.cell(colspan: 5, fill: rgb("#F2F8F2"))[*3. Comprensibile*],

  ..wcag-row(
    "3.1.1 Lingua della pagina", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Attributo lang='it' impostato sul tag html."
  ),
  ..wcag-row(
    "3.1.2 Lingua di parti", "AA", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Termini inglesi (es. 'Join Now') marcati con <span lang='en'>."
  ),
  ..wcag-row(
    "3.2.1 Al focus", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Nessun elemento avvia azioni o cambi di contesto al solo focus."
  ),
  ..wcag-row(
    "3.3.1 Identificazione errori", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Errori nei form segnalati testualmente vicino al campo errato."
  ),
  ..wcag-row(
    "3.3.2 Etichette o istruzioni", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Campi obbligatori segnalati chiaramente prima dell'invio."
  ),

  // ============================================================
  // 4. ROBUSTO
  // ============================================================
  table.cell(colspan: 5, fill: rgb("#F2F8F2"))[*4. Robusto*],

  ..wcag-row(
    "4.1.1 Parsing", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "PHPStorm Validator",
    "Codice validato W3C, ID univoci garantiti."
  ),
  ..wcag-row(
    "4.1.2 Nome, ruolo, valore", "A", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Componenti custom (es. carrello) usano ARIA roles corretti."
  ),
  ..wcag-row(
    "4.1.3 Messaggi di stato", "AA", 
    text(fill: rgb("#2d4f1e"), strong("Pass")), "placeholder",
    "Feedback aggiunta al carrello gestito con aria-live='polite'."
  ),
)

== Note aggiuntive sull'Accessibilità
Per verificare i contrasti cromatici è stato utilizzato lo strumento *WebAIM Contrast Checker*. Le icone puramente decorative sono state nascoste agli screen reader utilizzando `aria-hidden="true"`.\
Il controllo della correttezza del codice è stato validato in due modalità automatiche. La prima l'utilizzo di *TotalValidator*, la seconda l'utilizzo di *PHPStorm* il quale indica errori formali, link non funzionanti (a livello statico) e _best practice_ non rispettate. Particolarità di PHPStorm è inoltre la possibilità di impostare un server remoto su cui riversare il progetto, rendendo automatico il caricamento sul server _tecweb_.


= 6. Suddivisione dei compiti
La suddivisione dei compiti è stata gestita cercando di coniugare gli impegni e le preferenze di ognuno dei membri del team. In particolare la spartizione da seguito questa struttura:
== Soligo Lorenzo
- Design del Database
- Pagina SHOP: HTML, PHP, JS e CSS
- Pagina Prodotto: HTML, PHP, JS e CSS
- Pagina Profilo Venditore/Administrator:  HTML, PHP, JS e CSS
- Pagina Aggiunta/Modifica Prodotto: HTML, PHP, JS e CSS
- Menù Mobile
- Controllo con NVDA

== Ceron Tommaso
- Pagina Registrazione: HTML, JS, PHP
- Pagina Login: HTML, JS, PHP
- Pagina Profilo Compratore/Utente: HTML, JS, PHP 
- Gestione logica login/logout/registrazione e sessioni PHP
- Gestione logica aggiunta al carrello/ aggiunta ai preferiti
- Pagina carrello: HTML, JS, PHP

= 7. Funzionalità Aggiuntive
== Filtri Shop
Nella pagina _Shop_ sono stati implementati filtri per categoria e prezzo, permettendo agli utenti di restringere i risultati in base alle proprie preferenze. I filtri sono realizzati utilizzando PHP come base di partenza e JavaScript per aggiornare dinamicamente la visualizzazione dei prodotti senza ricaricare la pagina, rispettando il principio di _*Progressive Enhancement*_.
== Gestione attenta della creazione dei prodotti
Per la creazione dei prodotti, è stata implementata una pagina dedicata che consente ai venditori di inserire tutte le informazioni necessarie in modo strutturato. Questa pagina include campi per il nome del prodotto, la descrizione, il prezzo, la categoria e l'immagine del prodotto. Inoltre, sono stati implementati controlli di validazione per garantire che tutti i dati inseriti siano corretti e completi prima della pubblicazione sul server. \
I dettagli specifici del tipo di prodotto vengono mostrati in base alla categoria selezionata, migliorando l'esperienza utente evitando il sovraccarico cognitivo.

== Gestione form e Funzionalità e-commerce
Al fine di non limitare l' utilizzo del sito a coloro che non utilizzano JavaScript all' interno del loro browser, abbiamo 
cercato di implementare tutte funzionalità legate al negozio, quali registrazione, accesso, aggiunta al carrello e ai preferiti, indipendenti da codice JavaScript.

== Gestione pagine di errore
Le pagine di errore sono posizionate al path _src/pages/errors_, per poter renderle disponibili a prescindere dalla posizione
della pagina che genera l'errore, abbiamo dovuto creare un file _.htaccess_ indicante il path delle pagine di errore corrispondenti 
e utilizzare path assoluti per i link e i file CSS, seppur sia una scelta non ottimale che non siamo però riusciti a mitigare.

= 8. Uso dell'AI nel progetto
Durante lo sviluppo del progetto, l'Intelligenza Artificiale è stata utilizzata in modo limitato e mirato. In particolare, sono stati impiegati strumenti di AI per:
- Generazione delle immagini per evitare di incappare in problemi di copyright.
- Codice per la gestione del Parallasse nella pagina HOME.
L'uso dell'AI è stato sempre supervisionato e integrato con il lavoro manuale del team, assicurando che il prodotto finale rispecchiasse le competenze acquisite durante il corso e rispettasse gli standard di qualità richiesti.
= 7. Conclusioni
Il progetto ha permesso di approfondire le tecnologie HTML, CSS e JavaScript, con un focus particolare sull'inclusività. Il sito risultante è non solo esteticamente gradevole, ma utilizzabile da una vasta gamma di utenti, rispettando gli standard universitari e internazionali.
