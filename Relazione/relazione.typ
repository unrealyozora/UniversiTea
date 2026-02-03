
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
    "Soligo Lorenzo (Matr. 2101057)",
  ),
  date: "30 Dicembre 2025",
  login: ("Link: tecweb.studenti.math.unipd.it/tceron/", "Venditore: admin  admin", "Utente: user  user"),

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
    "1.3.5 Identificazione dello scopo degli input",
    "AA",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "placeholder",
    "I campi dei form (login/register) usano l'attributo autocomplete appropriato.",
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
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "WCAG Contrast Checker",
    "Testo su sfondo scuro verificato (Ratio > 4.5:1 per testo normale).",
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
    text(fill: rgb("#d97706"), strong("Parziale")),
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
    "2.4.6 Intestazioni ed etichette",
    "AA",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Gerarchia H1-H6 rispettata, label dei form chiare e visibili.",
  ),
  ..wcag-row(
    "2.4.7 Focus visibile",
    "AA",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Outline CSS personalizzato (verde chiaro) su tutti gli elementi interattivi.",
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
    "3.1.2 Lingua di parti",
    "AA",
    text(fill: rgb("#2d4f1e"), strong("Pass")),
    "Controllo manuale",
    "Termini inglesi (es. 'Join Now') marcati con <span lang='en'>.",
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
- Relazione

== Ceron Tommaso
- Pagina Registrazione: HTML, JS, PHP
- Pagina Login: HTML, JS, PHP
- Pagina Profilo Compratore/Utente: HTML, JS, PHP
- Gestione logica login/logout/registrazione e sessioni PHP
- Gestione logica aggiunta al carrello/ aggiunta ai preferiti
- Pagina carrello: HTML, JS, PHP
- Relazione

== Parolin Dennis
- Pagina Home: HTML, CSS, contenuto, JS
- Pagina Login/logout/registrazione: HTML, CSS
- Footer: HTML, CSS
- Header: HTML, CSS, PHP
- Controllo contrasti con WCAG Color Contrast Checker
- Relazione

== Marchioro Elisa
- Pagina Il nostro tè: HTML, CSS, contenuto
- Pagina About: HTML, CSS
- Pagine Errori: HTML, CSS, contenuto
- Controllo con W3C Css Validator
- Controllo con W3C Html Validator
- Relazione


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

== Carosello nella Home Page
L'idea era quella di inserire un carosello nella homepage il quale mostrasse i prodotti più venduti (idealmente, non esiste nessun contatore per il numero di acquisti). Inizialmente era stato fatto solo tramite JS ma effettivamente non era la cosa migliore da fare in quanto un utente potrebbe tranquillamente disabilitare JS e non vedere né navigare nel carosello. Quindi ho deciso di creare un carosello che funzionasse di base solo con CSS e nel caso JS fosse attivo, il carosello cambia comportamento (ad esempio i bottoni non sono una coppia per slide). Infatti sono presenti nell'html della home delle parti riguardanti la navigazione con JS e altre che riguardano la navigazione con CSS.\
Partendo dal carosello di base solo con CSS, ho trovato utile il breve tutorial al link: https://developer.chrome.com/blog/carousels-with-css?hl=it. Questo tutorial ha illustrato brevemente la funzionalità "Snap".\
I comandi principali utilizzati sono:
- scroll-snap-type: x mandatory; che dichiara la presenza di "punti di aggancio" lungo l'asse orizzontale dell'elemento in cui ho dichiarato la proprietà
- scroll-snap-align: center; che definisce effettivamente dove si trova il "punto di aggancio" nell'elemento a cui viene applicata la proprietà
- scroll-snap-stop: always; che definisce che lo scroll si ferma sempre su ogni punto di aggancio, senza saltarne più di uno con un singolo scroll.
- scroll-behavior: smooth; trovato nel sito https://www.w3schools.com/cssref/pr_scroll-behavior.php per animare in modo fluido lo scroll tra una slide e l'altra.

Semplicemente il contenitore con classe "bestsellers-content-wrapper" contiene tutti i prodotti allineati sulla stessa riga e su questo contenitore dichiaro che ci saranno dei "punti di aggancio" lungo l'asse delle x\
Poi su ogni prodotto, con classe "product-slide" dichiaro che i "punti di aggancio" si trovano al centro dell'elemento e che questi punti di aggancio devono essere per forza raggiunti durante lo scroll senza saltarne più di uno con un singolo scroll.

Poi sono stati usati dei comandi del tipo:
- scrollbar-width: none; per i motori Gecko, tipo Firefox.
- -ms-overflow-style: none; per i motori Trident/EdgeHTML, come Edge o Explorer.
- ::-webkit-scrollbar { display: none; } per i motori Blink/WebKit come Chrome, Opera e Safari.
Trovati sempre grazie al sito di w3School: https://www.w3schools.com/howto/howto_css_hide_scrollbars.asp e servono per nascondere la barra di scorrimento su i vari tipi di browser.\
Accanto ad ogni prodotto è presente la sua immagine anziché il modello 3d, in quanto questi funzionano tramite uno script. Stessa cosa per i pulsanti di navigazione. L'unica opzione che mi è venuta in mente è stata quella di inserire una coppia di bottoni per ogni singola slide e poi collegarli con il tag a che punta alla slide successiva o precedente.  L'unico problema era il modo in cui quel tag mi spostava la visuale per visualizzare l'elemento a cui puntava, cioè allineato con inizio pagina. L'header nella home però ha position fixed e quindi questo si sovrappone nascondendo parzialmente le slide. Per ovviare al problema l'unica cosa che sono riuscito a fare è aggiungere la proprietà scroll-padding-top (inizialmente al contenitore delle slide, ma non funzionava. Poi allora lo ho spostato a tutto il body e html). Ovviamente tutte le componenti riguardanti il carosello con JS sono nascoste tramite il CSS.\
Parlando invece del carosello usando JS. L'idea è stata quella di inserire uno script che, appena la pagina venisse caricata, aggiungesse la classe js-carousel-active al body. In questo modo, se JS è disattivato, la classe non verrà mai creata e quindi la navigazione tramite JS non sarà mai visibile/funzionante. Se invece JS è attivo, allora sfruttiamo la nuova classe per nascondere immagini e bottoni del carosello solo con CSS e far apparire i modelli 3D, i bottoni di navigazione tramite JS, i dots che mostrano a che slide siamo e le opzioni di visualizzazione del modello 3D per disattivare o attivare la sua rotazione automatica.

== Modelli 3D (model-viewer)
L'idea era quella di aggiungere un modo diverso da solito per visualizzare i prodotti, in questo modo, suscitiamo un sentimento di stupore nell'utente facendolo familiarizzare di più con la pagina e aumentando le probabilità che esso torni nel sito e che si fidi del sito. Mi sono ricordato dell'esistenza dei modelli 3D grazie ai social media (tik tok), nella quale ho visto dei video che li mostravano. Facendo poi un po' di ricerche ho trovato il sito: https://modelviewer.dev nel quale mostravano delle righe di codice che ho inserito.\
Praticamente il modello funziona tramite uno script con un src a delle api di google, aggiunto nell'head del documento. Se il JS è disattivato i modelli non funzionano, ed è per questo motivo che ho creato il carosello che funziona anche solo con CSS. Poi ovviamente i tag model-viewer sono stati aggiunti in html con le rispettive proprietà:
- loading = "lazy"; per non far caricare subito il modello, ma solo quando l'utente scorre fino a visualizzarlo. Così risparmiamo risorse (trovato su w3Schools)
- auto-rotate; per far ruotare su se stesso il modello, lungo l'asse delle x.
- camera-controls; per permettere all'utente di ruotare il modello e zoommare.
- shadow-intensity = "1"; controlla l'opacità dell'ombra proiettata dal modello, il valore va da 0 a 1.
- disable-zoom; esplicito che non posso zoommare.
Nel mio caso ho deciso di considerare il modello 3D come un contenuto non informativo, dato che non mostra nulla di essenziale per la comprensione della pagina o del prodotto. Ma solo un modo per rendere la pagina più accattivante e per far familiarizzare l'utente con il prodotto. Quindi il modello e le cose a lui annesse non saranno visibili agli screenreader\
Ovviamente i modelli 3D possono essere molto pesanti e rendere più lento il caricamento della pagina, la soluzione sarebbe quella di crearli da sé, ma questo non è lo scopo del corso. Ho cercato 3 modelli 3D che non pesassero troppo, molto limitata come ricerca perché la maggior parte era coperto da copyright.\
Ci terrei a dire che siamo coscienti del peso di questi modelli e nella realizzazione di un vero e proprio sito si sarebbe presa in considerazione la soluzione di creare modelli custom o proprio di non metterli per velocizzare il caricamento. Ma in questo caso sono stati aggiunti per scopo puramente accademico e per comprendere la loro funzionalità.

== Parallasse
Anche in questo caso l'idea era quella di suscitare stupore all'utente. E sì, anche in questo caso l'idea è sorta grazie ai social media (sempre tik tok). Solo che in questo caso non sono riuscito a trovare molte fonti sulla funzionalità e quelle trovate non mi parevano troppo attendibili. Per questo motivo, mi sono fatto aiutare dall'intelligenza artificiale a creare lo script JS per la parallasse. L'idea di inserire tante immagini differenti ero riuscito da solo a comprenderla, infatti le avevo aggiunte e poi posizionate correttamente tramite CSS. Ho usato immagini svg in modo che pesassero poco. Poi l'ai mi ha aiutato a comprendere che logica creare nello script.\
Nello script si va a creare un array composto da 8 numeri, 8 come il numero dei layer. Questi numeri rappresentano la velocità di aggiornamento con la quale si muovono i layer. In generale si va ad applicare un traslateX o translateY ai layer, tranne che per il layer 4 e 7 che sono posizionati differentemente nel CSS, a questi applichiamo solo un translateY. E per calcolare di quanto spostare i layer, andiamo a moltiplicare la velocità di aggiornamento con quanto l'utente ha scrollato. Per cercare di risparmiare risorse, esiste la variabile ticking che funge da semaforo per evitare che i calcoli vengano applicati anche quando l'utente non vede i layer. In più, quando avviene uno scroll viene chiamata la funzione di updateParallax, ma questo consuma molte risorse se l'utente scrolla molto velocemente perché potrebbero essere chiamata moltissime volte in poco tempo la funzione. Per questo motivo ho usato "requestAnimationFrame". In questo modo, anche se l'utente genera 200 eventi di scroll in un secondo, i calcoli per il parallax verranno eseguiti al massimo X volte al secondo, dove X è il refresh rate dello schermo. Questo dovrebbe aiutare ad avere una pagina più fluida.\
Anche in questo caso, era tutto puramente accademico e per cercare di capire come funziona, Ma non è la soluzione migliore da applicare ad un sito. Anche perchè "rompe" la separazione tra comportamento e presentazione, ma è un compromesso necessario per creare un parallax. Comunque, se JS è disattivato, il tutto funziona perfettamente lo stesso, perché le immagini sono posizionate benissimo già in partenza tramite CSS e quindi al massimo non si vede l'effetto di parallasse. Ma il contenuto e la bellezza della hero section rimangono comunque integre. Al massimo l'utente sarebbe un po' meno stupefatto.

== Link visitati
Sappiamo del fatto che non si deve usare solo il colore per veicolare l'informazione. Per questo motivo ai link visitati, oltre che cambiare il colore, volevamo aggiungere a lato una spunta dello stesso colore del link visitato. In questo progetto però questa funzionalità non è presente. Sappiamo della grave mancanza ma non siamo riusciti a ovviare al problema. Abbiamo provato a fare questa cosa tramite CSS con il content:'', ma non funzionava. Abbiamo provato ad aggiungere l'immagine direttamente nell'html e gestirla con CSS dandogli un display none di base e poi un display block quando il link era visitato, ma non funzionava. Non siamo riusciti ad implementarla.
D'altro canto però, funziona il fatto che il link a cui mi trovo è contrassegnato dall'immagine di un pin, per far capire all'utente dove si trova (oltre che alla breadcrumb). Questo nell'header. Non abbiamo capito come mai questo funzioni e l'icona di check dei link visitati no. Perché il pin è già presente nell'html e viene gestito con display none dal CSS.

= 8. CSS
Per quanto riguarda il CSS, l’obiettivo principale è stato uniformare lo stile dell’intero sito, prestando particolare attenzione alla scelta coerente di colori, font e dimensioni del testo, così da garantire un’esperienza di navigazione chiara, accessibile e piacevole per tutti gli utenti. Si è cercato di centralizzare e riutilizzare il più possibile le regole di stile, riducendo le ripetizioni e mantenendo il codice ordinato, leggibile e facilmente manutenibile. Inoltre, il foglio di stile è stato organizzato in tre file distinti in base alla destinazione d’uso: style.css per gli stili generali del sito, mini.css dedicato all’adattamento per dispositivi mobili e print.css per la gestione della visualizzazione in fase di stampa tramite media query specifiche.

== Flexbox
Per la gestione del layout è stato fatto ampio uso di Flexbox, che ha permesso di organizzare in modo flessibile e responsivo gli elementi nelle varie sezioni del sito. L’utilizzo di display: flex ha facilitato l’allineamento, la distribuzione degli spazi e l’adattamento dei contenuti a diverse dimensioni di schermo, riducendo il ricorso a soluzioni più complesse o a strutture rigide. Questo approccio ha contribuito a rendere il codice più pulito e modulare, migliorando al tempo stesso la coerenza visiva e la responsività generale del sito.

In particolare è stato impiegato, ad esempio, nella sezione delle statistiche (pagina About) per distribuire in modo uniforme i vari blocchi informativi sulla stessa riga, garantendo equilibrio visivo e adattabilità alle diverse dimensioni di schermo.
Flexbox è stato inoltre utilizzato nelle sezioni con immagini e testi affiancati (come le tard-card dei tè nella pagina "Il nostro tè" e le about-values nella pagina About), permettendo di allineare verticalmente i contenuti, gestire gli spazi tra gli elementi tramite gap e alternare facilmente la disposizione degli elementi. Questo approccio ha reso più semplice il passaggio alla visualizzazione mobile, dove la direzione del layout è stata modificata in colonna tramite media query. L’uso di Flexbox ha quindi contribuito a ottenere un design più flessibile, ordinato e responsivo, migliorando sia la struttura del codice sia l’esperienza utente.

= 9. Uso dell'AI nel progetto
Durante lo sviluppo del progetto, l'Intelligenza Artificiale è stata utilizzata in modo limitato e mirato. In particolare, sono stati impiegati strumenti di AI per:
- Generazione delle immagini per evitare di incappare in problemi di copyright.
- Codice per la gestione del Parallasse nella pagina HOME.
- Eventuale supporto per CSS.
L'uso dell'AI è stato sempre supervisionato e integrato con il lavoro manuale del team, assicurando che il prodotto finale rispecchiasse le competenze acquisite durante il corso e rispettasse gli standard di qualità richiesti.

= 10. Conclusioni
Il progetto ha permesso di approfondire le tecnologie HTML, CSS e JavaScript, con un focus particolare sull'inclusività. Il sito risultante è non solo esteticamente gradevole, ma utilizzabile da una vasta gamma di utenti, rispettando gli standard universitari e internazionali.
