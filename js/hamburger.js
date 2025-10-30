document.addEventListener('DOMContentLoaded', () => {
  // wacht tot de pagina klaar is voordat we naar knoppen en menu zoeken
  const btn     = document.querySelector('.hamburger-box'); // het hamburger icoon waar je op klikt
  const nav     = document.querySelector('nav');            // het navigatie menu zelf
  const overlay = document.getElementById('navOverlay');    // donkere achtergrond achter het menu

  // als er iets niet gevonden wordt dan stoppen we, voorkomt errors
  if (!btn || !nav || !overlay) return;

  // dit is een lijst van elementen waar je met tab op kan focussen, dus voor toegankelijkheid
  const focusableSel = [
    'a[href]', 'button:not([disabled])', 'input:not([disabled])',
    'select:not([disabled])', 'textarea:not([disabled])', '[tabindex]:not([tabindex="-1"])'
  ].join(',');

  function openNav() {
    // menu open zetten, aria zorgt ervoor dat screenreaders weten dat het menu open is
    btn.setAttribute('aria-expanded', 'true');
    nav.classList.add('is-open'); // menu zichtbaar maken
    nav.setAttribute('aria-hidden', 'false');
    overlay.hidden = false; // overlay laten zien
    document.body.classList.add('nav-open'); // body krijgt class zodat scroll geblokkeerd wordt

    // eerste element in het menu focussen zodat keyboard gebruikers meteen erin zitten
    const first = nav.querySelector(focusableSel);
    first && first.focus();
    document.addEventListener('keydown', onKeydown); // luister naar toetsen zoals escape en tab
  }

  function closeNav() {
    // menu sluiten en alles resetten naar normale staat
    btn.setAttribute('aria-expanded', 'false');
    nav.classList.remove('is-open');
    nav.setAttribute('aria-hidden', 'true');
    overlay.hidden = true;
    document.body.classList.remove('nav-open');
    document.removeEventListener('keydown', onKeydown);
    btn.focus(); // focus terugzetten op de hamburger knop
  }

  function toggleNav() {
    // kijken of het menu open is en dan flippen tussen open en dicht
    const expanded = btn.getAttribute('aria-expanded') === 'true';
    expanded ? closeNav() : openNav();
  }

  function onKeydown(e) {
    // escape sluit het menu
    if (e.key === 'Escape') {
      closeNav();
      return;
    }

    // tab key moeten we handmatig beperken zodat focus binnen het menu blijft als het open staat
    if (e.key === 'Tab' && nav.classList.contains('is-open')) {
      const focusables = Array.from(nav.querySelectorAll(focusableSel))
        .filter(el => el.offsetParent !== null); // alleen zichtbare dingen

      if (focusables.length === 0) return;

      const first = focusables[0];
      const last  = focusables[focusables.length - 1];

      // shift+tab gaat normaal terug maar wij houden focus binnen menu
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault(); last.focus();
      } 
      // tab normaal vooruit maar bij laatste springen we terug naar eerste
      else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault(); first.focus();
      }
    }
  }

  // klik event op de hamburger knop om menu open/dicht te togglen
  btn.addEventListener('click', toggleNav);

  // klik op de overlay sluit het menu ook
  overlay.addEventListener('click', closeNav);

  // als je ergens op een link in het menu klikt sluit het menu automatisch
  nav.addEventListener('click', (e) => {
    const a = e.target.closest('a[href]');
    if (a) closeNav();
  });

  // dit checkt of we op desktop zitten of mobiel, zelfde waarde als in CSS
  const mql = window.matchMedia('(min-width: 901px)');

  // als we op desktop beginnen dan zorgen we dat het menu standaard open is en netjes staat
  if (mql.matches) {
    nav.setAttribute('aria-hidden', 'false');
    btn.setAttribute('aria-expanded', 'false');
    nav.classList.remove('is-open');
    overlay.hidden = true;
    document.body.classList.remove('nav-open');
  } else {
    // mobiel start altijd met menu dicht
    nav.setAttribute('aria-hidden', 'true');
  }

  // als de schermgrootte verandert naar desktop terug, menu resetten
  mql.addEventListener('change', () => {
    if (mql.matches) {
      btn.setAttribute('aria-expanded', 'false');
      nav.classList.remove('is-open');
      nav.setAttribute('aria-hidden', 'false');
      overlay.hidden = true;
      document.body.classList.remove('nav-open');
    } else {
      // zodra we weer naar mobiel gaan menu verbergen
      nav.setAttribute('aria-hidden', 'true');
    }
  });
});
