(function () {
  const $ = (s, r) => (r || document).querySelector(s), $$ = (s, r) => Array.from((r || document).querySelectorAll(s));
  const BASE = window.ZANP_BASE || '/', reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const has3D = !!(window.THREE && window.ZANP);
  const note = (el, t) => { if (el) { const p = el.querySelector('.loading'); if (p) p.textContent = t; else el.insertAdjacentHTML('beforeend', '<p class="loading mono">' + t + '</p>'); } };
  const NO3D = 'The 3D view isn’t available in this browser.';
  const pad = (n) => String(n).padStart(2, '0');

  // ---- HUD: Lagos clock, scroll ticker, crosshair cursor and X/Y readouts
  const clock = $('#clock');
  if (clock) { const tick = () => { clock.textContent = new Date().toLocaleTimeString('en-GB', { timeZone: 'Africa/Lagos' }); }; tick(); setInterval(tick, 1000); }
  const tk = $('#ticker'); let bars = [];
  function buildTicker() { if (!tk) return; const n = Math.max(0, Math.floor((tk.clientWidth - 28) / 4)); tk.innerHTML = '<i></i>'.repeat(n); bars = $$('i', tk); paintTicker(); }
  function paintTicker() { if (!bars.length) return; const max = document.documentElement.scrollHeight - innerHeight, p = max > 0 ? scrollY / max : 0, on = Math.round(p * bars.length) + 4; bars.forEach((b, i) => b.classList.toggle('on', i < on)); }
  buildTicker(); addEventListener('resize', buildTicker); addEventListener('scroll', paintTicker, { passive: true });
  if (matchMedia('(pointer:fine)').matches) {
    document.body.classList.add('xh');
    document.body.insertAdjacentHTML('beforeend', '<div id="xh"><svg viewBox="0 0 34 34" width="34" height="34" fill="none" stroke="currentColor"><path d="M17 3v10M17 21v10M3 17h10M21 17h10"/><circle cx="17" cy="17" r="2.2"/></svg></div><div class="xy l">X: <span id="mx">0</span></div><div class="xy r">Y: <span id="my">0</span></div>');
    const xh = $('#xh'), mx = $('#mx'), my = $('#my');
    addEventListener('pointermove', (e) => { xh.style.opacity = 1; xh.style.transform = 'translate(' + e.clientX + 'px,' + e.clientY + 'px)'; mx.textContent = Math.round(e.clientX); my.textContent = Math.round(e.clientY); });
  }

  // ---- Dial rings (ticks around the shoe)
  $$('.dial').forEach((d) => {
    let t = ''; for (let i = 0; i < 180; i++) { const a = i * 2 * Math.PI / 180, big = i % 5 === 0, r1 = 98, r2 = big ? 94 : 96; t += '<line x1="' + (100 + r1 * Math.sin(a)).toFixed(2) + '" y1="' + (100 - r1 * Math.cos(a)).toFixed(2) + '" x2="' + (100 + r2 * Math.sin(a)).toFixed(2) + '" y2="' + (100 - r2 * Math.cos(a)).toFixed(2) + '"/>'; }
    d.innerHTML = '<svg viewBox="0 0 200 200"><g class="ring" stroke="#4a4a4a" stroke-width=".35">' + t + '</g><line x1="100" y1="-4" x2="100" y2="8" stroke="#ff4d12" stroke-width=".6"/></svg>';
  });

  function thumbs(root) {
    if (!has3D) return;
    $$('img[data-thumb]', root).forEach((img) => {
      ZANP.thumb({ variant: +img.dataset.variant, tint: img.dataset.tint, model: img.dataset.model || undefined })
        .then((u) => { img.src = u; img.classList.add('in'); }).catch(() => {});
    });
  }
  function pressed(group, btn) { $$('[aria-pressed]', group).forEach((b) => b.setAttribute('aria-pressed', b === btn ? 'true' : 'false')); }

  // ---- Home hero
  async function hero() {
    const el = $('#hero3d'); if (!el) return;
    if (!has3D) return note(el, NO3D);
    try {
      const st = ZANP.stage(el), shoe = await ZANP.build({ variant: 0 });
      el.querySelector('.loading')?.remove(); st.scene.add(shoe.group);
      let ry = -0.6, tx = 0, ty = 0, drag = false, lx = 0;
      el.addEventListener('pointerdown', (e) => { drag = true; lx = e.clientX; });
      addEventListener('pointerup', () => { drag = false; });
      el.addEventListener('pointermove', (e) => {
        if (drag) { ry += (e.clientX - lx) * 0.01; lx = e.clientX; }
        const b = el.getBoundingClientRect(); tx = (e.clientX - b.left) / b.width - 0.5; ty = (e.clientY - b.top) / b.height - 0.5;
      });
      st.onFrame((dt) => {
        const wide = el.clientWidth > el.clientHeight * 1.05, g = shoe.group;
        st.cam.position.z += ((wide ? 6.6 : 10) - st.cam.position.z) * 0.1;
        if (!drag && !reduce) ry += dt * 0.35;
        g.rotation.y = ry + tx * 0.5; g.rotation.x = ty * 0.2;
      });
      $$('.hero .dot').forEach((d) => d.addEventListener('click', () => { shoe.setVariant(+d.dataset.v); pressed(d.parentNode, d); $('#cwName').textContent = d.dataset.n; }));
    } catch (e) { note(el, NO3D); }
  }

  // ---- Spec story: big counting number, label, camera moves while you scroll
  function story() {
    const sec = $('#story'); if (!sec) return;
    const specs = JSON.parse(sec.dataset.specs || '[]'); if (!specs.length) return;
    const num = $('#num'), unit = $('#unit'), lbl = $('#lblL'), idxEl = $('#idx'), tagN = $('#tagN'), tagT = $('#tagT'), el = $('#story3d');
    const poses = [{ c: [0, 1.2, 9], r: -0.7 }, { c: [0, 9, 3.5], r: -0.3 }, { c: [3, -6, 6], r: -0.6 }, { c: [-7, 2, 6], r: 2.4 }];
    let cur = -1, raf = 0;
    const fmt = (n) => Math.round(n).toLocaleString('en-NG');
    function setSpec(i) {
      cur = i; const s = specs[i];
      lbl.textContent = s.label; unit.textContent = s.unit; idxEl.textContent = String(i + 1).padStart(3, '0'); tagN.textContent = pad(i + 1); tagT.textContent = s.tag;
      cancelAnimationFrame(raf);
      if (reduce) { num.textContent = fmt(s.value); return; }
      const t0 = performance.now();
      const step = (t) => { const k = Math.min(1, (t - t0) / 900), e = 1 - Math.pow(1 - k, 3); num.textContent = fmt(s.value * e); if (k < 1) raf = requestAnimationFrame(step); };
      raf = requestAnimationFrame(step);
    }
    function progressIdx() { const b = sec.getBoundingClientRect(), p = Math.min(1, Math.max(0, -b.top / (b.height - innerHeight))); return Math.min(specs.length - 1, Math.floor(p * specs.length)); }
    const onScroll = () => { const i = progressIdx(); if (i !== cur) setSpec(i); };
    addEventListener('scroll', onScroll, { passive: true }); setSpec(0);
    if (!has3D) return;
    let started = false;
    new IntersectionObserver(async (es) => {
      if (started || !es[0].isIntersecting) return; started = true;
      try {
        const st = ZANP.stage(el), shoe = await ZANP.build({ variant: 1 }); st.scene.add(shoe.group);
        const look = new THREE.Vector3(0, 0, 0);
        st.onFrame(() => {
          const g = poses[Math.min(poses.length - 1, cur < 0 ? 0 : cur)], w = el.clientWidth < 700 ? 1.4 : 0.85;
          st.cam.position.lerp(new THREE.Vector3(g.c[0] * w, g.c[1] * w, g.c[2] * w), reduce ? 1 : 0.06);
          shoe.group.rotation.y += (g.r - shoe.group.rotation.y) * (reduce ? 1 : 0.06);
          st.cam.lookAt(look);
        });
      } catch (e) {}
    }, { rootMargin: '300px' }).observe(sec);
  }

  // ---- Product viewer
  async function viewer() {
    const el = $('#viewer'); if (!el) return;
    if (!has3D) return note(el, NO3D);
    try {
      const st = ZANP.stage(el), shoe = await ZANP.build({ variant: +el.dataset.variant, tint: el.dataset.tint, model: el.dataset.model || undefined });
      el.querySelector('.loading')?.remove(); st.scene.add(shoe.group); ZANP.shadow(st.scene, shoe.size);
      shoe.group.rotation.y = -0.6;
      const ctl = new THREE.OrbitControls(st.cam, st.r.domElement);
      ctl.enableDamping = true; ctl.enablePan = false; ctl.minDistance = 5; ctl.maxDistance = 14; ctl.maxPolarAngle = Math.PI * 0.62; ctl.autoRotate = !reduce; ctl.autoRotateSpeed = 1.4;
      ctl.addEventListener('start', () => { ctl.autoRotate = false; });
      let goal = null; const V = { side: [0, 1.2, 9], front: [9, 1.2, 0], top: [0, 9, 0.1], back: [-9, 1.2, 0] };
      st.onFrame(() => { if (goal) { st.cam.position.lerp(goal, 0.12); if (st.cam.position.distanceTo(goal) < 0.05) goal = null; } ctl.update(); });
      $$('[data-view]').forEach((b) => b.addEventListener('click', () => { ctl.autoRotate = false; goal = new THREE.Vector3(...V[b.dataset.view]); }));
      $$('.info .dot').forEach((d) => d.addEventListener('click', () => { shoe.setVariant(+d.dataset.v, el.dataset.tint); pressed(d.parentNode, d); }));
    } catch (e) { note(el, 'The 3D model could not be loaded.'); }
  }

  // ---- Add to bag, newsletter, shop search
  const form = $('#addForm');
  if (form) form.addEventListener('submit', (e) => {
    e.preventDefault(); const msg = $('.formmsg', form);
    fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'fetch' } })
      .then((r) => r.json()).then((j) => { if (j.ok) { $('#cartCount').textContent = j.count; msg.textContent = 'Added to your bag.'; } else msg.textContent = j.error || 'Something went wrong.'; })
      .catch(() => { msg.textContent = 'Network error. Try again.'; });
  });
  $$('[data-newsletter]').forEach((f) => f.addEventListener('submit', (e) => {
    e.preventDefault(); const m = $('.nlmsg', f);
    fetch(BASE + 'api/subscribe.php', { method: 'POST', body: new FormData(f) }).then((r) => r.json())
      .then((j) => { m.textContent = j.ok ? j.message : j.error; if (j.ok) f.reset(); }).catch(() => { m.textContent = 'Network error. Try again.'; });
  }));
  const grid = $('#grid'), q = $('#q');
  if (grid && q) {
    let cat = ($('.chip[aria-pressed="true"]') || {}).dataset?.cat || '', t;
    const run = () => fetch(BASE + 'api/products.php?q=' + encodeURIComponent(q.value) + '&cat=' + encodeURIComponent(cat)).then((r) => r.text())
      .then((h) => { grid.innerHTML = h; thumbs(grid); history.replaceState(null, '', '?q=' + encodeURIComponent(q.value) + '&cat=' + encodeURIComponent(cat)); });
    q.addEventListener('input', () => { clearTimeout(t); t = setTimeout(run, 250); });
    $$('.chip[data-cat]').forEach((c) => c.addEventListener('click', () => { cat = c.dataset.cat; pressed(c.parentNode, c); run(); }));
  }

  // ---- Loader (home): counts to 100, finishes when the hero shoe is ready
  const loader = $('#loader'), ldn = $('#ldn');
  const heroP = hero(); story(); viewer(); thumbs(document);
  if (loader) {
    let shown = 0, ready = false, done = false; const t0 = performance.now();
    Promise.race([heroP, new Promise((r) => setTimeout(r, 8000))]).then(() => { ready = true; });
    (function tick() {
      const el = performance.now() - t0, target = ready && el > 1100 ? 100 : Math.min(92, el / 40);
      shown += (target - shown) * 0.12; if (target === 100 && shown > 99.4) shown = 100;
      ldn.textContent = String(Math.round(shown)).padStart(3, '0');
      if (shown >= 100 && !done) { done = true; loader.classList.add('done'); setTimeout(() => loader.remove(), 800); return; }
      requestAnimationFrame(tick);
    })();
  }
})();
