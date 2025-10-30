document.addEventListener('DOMContentLoaded', () => {
  // eerst wachten tot de html klaar is, dan pas met het filter-script beginnen

  // pak de selects en knoppen die je voor het filter gebruikt
  const selMerk        = document.getElementById('brand');        
  const selModel       = document.getElementById('model');        
  const selCarrosserie = document.getElementById('carrosserie');  
  const selBrandstof   = document.getElementById('brandstof');    
  const selBouwjaar    = document.getElementById('bouwjaar');     
  const selPrijs       = document.getElementById('prijs');        
  const btnVinden      = document.querySelector('.filterBtn');   
  const btnReset       = document.querySelector('.filterReset');  // knop om alles te resetten

  // verzamel alle auto-kaarten die je gaat tonen of verbergen
  const cards = Array.from(document.querySelectorAll('.carCard'));

  // sanity check: als basiselementen ontbreken, netjes stoppen en uitleggen waarom
  if (!selMerk || !selModel || cards.length === 0) {
    console.warn('[filter] Selects of .carCard niet gevonden; script stopt.');
    return;
  }

  // helper: consistent vergelijken door te lowercasen en spaties te trimmen
  const toLower = v => (v || '').toString().trim().toLowerCase();

  // generieke optie-vuller: leegmaken, placeholder toevoegen, keuzes injecteren
  // vorige selectie proberen te behouden als die nog geldig is
  function fillOptions(select, values, placeholderText) {
    const prev = select.value;
    select.innerHTML = '';
    const opt0 = document.createElement('option');
    opt0.value = '';
    opt0.textContent = placeholderText;
    select.appendChild(opt0);

    values.forEach(v => {
      const o = document.createElement('option');
      o.value = String(v);
      // prijs krijgt nette nl-NL weergave, overige waarden eerste letter hoofdletter
      if (select === selPrijs) {
        o.textContent = 'Tot €' + Number(v).toLocaleString('nl-NL');
      } else {
        o.textContent = (typeof v === 'string' ? v.charAt(0).toUpperCase() + v.slice(1) : v);
      }
      select.appendChild(o);
    });

    // als de oude keuze nog bestaat, terugzetten voor betere UX
    if (values.map(String).includes(prev)) select.value = prev;
  }

  // merk -> modellen index opbouwen zodat modelkeuze afhankelijk wordt van merk
  const merkModelMap = {};
  cards.forEach(card => {
    const merk  = toLower(card.dataset.merk);
    const model = toLower(card.dataset.model);
    if (!merkModelMap[merk]) merkModelMap[merk] = new Set();
    if (model) merkModelMap[merk].add(model);
  });

  // prijsdrempels uit de bestaande html optuigen, dan later filteren op wat relevant is
  const initialPriceThresholds = selPrijs
    ? Array.from(selPrijs.querySelectorAll('option'))
        .map(o => parseInt(o.value, 10))
        .filter(n => !Number.isNaN(n))
        .sort((a, b) => a - b)
    : [];

  // basiscontrole: match alleen op merk, model en carrosserie
  // gebruik dit om afhankelijke selects te vullen
  function baseMatch(card) {
    const d = card.dataset;
    const fMerk        = toLower(selMerk.value);
    const fModel       = toLower(selModel.value);
    const fCarrosserie = selCarrosserie ? toLower(selCarrosserie.value) : '';

    return (
      (!fMerk        || d.merk        === fMerk) &&
      (!fModel       || d.model       === fModel) &&
      (!fCarrosserie || d.carrosserie === fCarrosserie)
    );
  }

  // modelopties bijwerken op basis van de huidige merkselectie
  // zonder merk: alle modellen laten zien die in de kaarten voorkomen
  function updateModelOptions() {
    const merk = toLower(selMerk.value);
    if (merk && merkModelMap[merk]) {
      const models = Array.from(merkModelMap[merk]).sort();
      fillOptions(selModel, models, 'Alle modellen');
    } else {
      const allModels = Array.from(
        new Set(cards.map(c => toLower(c.dataset.model)).filter(Boolean))
      ).sort();
      fillOptions(selModel, allModels, 'Alle modellen');
    }
  }

  // bouwjaar- en prijsopties dynamisch beperken tot wat nog in beeld kan komen
  function updateYearAndPriceOptions() {
    const subset = cards.filter(baseMatch);

    const years = Array.from(new Set(
      subset.map(c => parseInt(c.dataset.bouwjaar, 10)).filter(n => !Number.isNaN(n))
    )).sort((a, b) => a - b);

    // alleen drempels laten zien waar minimaal één auto onder valt
    const thresholds = initialPriceThresholds.filter(T =>
      subset.some(c => parseInt(c.dataset.prijs, 10) <= T)
    );

    if (selBouwjaar) fillOptions(selBouwjaar, years, 'Bouwjaren:');
    if (selPrijs)    fillOptions(selPrijs, thresholds, 'Prijzen:');
  }

  // hoofdlogica: alle filters toepassen en kaarten tonen/verbergen
  function applyFilters() {
    const fMerk        = toLower(selMerk.value);
    const fModel       = toLower(selModel.value);
    const fCarrosserie = selCarrosserie ? toLower(selCarrosserie.value) : '';
    const fBrandstof   = selBrandstof   ? toLower(selBrandstof.value)   : '';
    const fBouwjaar    = selBouwjaar && selBouwjaar.value ? parseInt(selBouwjaar.value, 10) : null;
    const fPrijsMax    = selPrijs    && selPrijs.value    ? parseInt(selPrijs.value, 10)    : null;

    let visibleCount = 0;

    cards.forEach(card => {
      const d = card.dataset;
      const match =
        (!fMerk        || d.merk        === fMerk) &&
        (!fModel       || d.model       === fModel) &&
        (!fCarrosserie || d.carrosserie === fCarrosserie) &&
        (!fBrandstof   || d.brandstof   === fBrandstof) &&
        (fBouwjaar === null || parseInt(d.bouwjaar, 10) === fBouwjaar) &&
        (fPrijsMax === null || parseInt(d.prijs, 10) <= fPrijsMax);

      const show = !!match;
      card.style.display = show ? '' : 'none';
      if (show) visibleCount++;
    });

    // knoplabel bijwerken zodat direct zichtbaar is hoeveel resultaten er overblijven
    if (btnVinden) btnVinden.textContent = `Vinden (${visibleCount})`;
  }

  // alles terug naar beginstand, afhankelijke selects opnieuw opbouwen en filters opnieuw toepassen
  function resetFilters() {
    selMerk.value = '';
    updateModelOptions();
    if (selCarrosserie) selCarrosserie.value = '';
    if (selBrandstof)   selBrandstof.value   = '';
    if (selBouwjaar)    selBouwjaar.value    = '';
    if (selPrijs)       selPrijs.value       = '';
    updateYearAndPriceOptions();
    applyFilters();
  }

  // event-afhandeling: bij elke wijziging de keten netjes doorlopen
  selMerk.addEventListener('change', () => {
    updateModelOptions();          // eerst modelkeuzes herberekenen
    updateYearAndPriceOptions();   // daarna bouwjaar en prijs laten aansluiten
    applyFilters();                // en tot slot de kaarten bijwerken
  });

  selModel.addEventListener('change', () => {
    updateYearAndPriceOptions();
    applyFilters();
  });

  selCarrosserie && selCarrosserie.addEventListener('change', () => {
    updateYearAndPriceOptions();
    applyFilters();
  });

  selBrandstof && selBrandstof.addEventListener('change', applyFilters);
  selBouwjaar  && selBouwjaar.addEventListener('change', applyFilters);
  selPrijs     && selPrijs.addEventListener('change', applyFilters);
  btnReset     && btnReset.addEventListener('click', resetFilters);

  // klik op vinden: alleen iets doen als er minimaal één filter ingevuld is, daarna smooth scrollen naar de resultaten
  if (btnVinden) {
    btnVinden.addEventListener('click', () => {
      const merk = selMerk?.value.trim();
      const model = selModel?.value.trim();
      const carrosserie = selCarrosserie?.value.trim();
      const brandstof = selBrandstof?.value.trim();
      const bouwjaar = selBouwjaar?.value.trim();
      const prijs = selPrijs?.value.trim();

      const ietsIngevuld = merk || model || carrosserie || brandstof || bouwjaar || prijs;

      if (ietsIngevuld) {
        applyFilters();

        const target = document.getElementById('carProducts');
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      } else {
        alert('Vul eerst één of meer filters in voordat je zoekt.');
      }
    });
  }

  // init: eerst modelopties klaarzetten, dan jaar/prijs, en dan meteen de huidige set tonen
  updateModelOptions();
  updateYearAndPriceOptions();
  applyFilters();
});