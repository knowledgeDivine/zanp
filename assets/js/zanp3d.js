/* ZANP 3D helpers: model loading, stages, thumbnails. Uses three.js r128 (local copy in assets/vendor). */
(function () {
  const BASE = window.ZANP_BASE || '/';
  const DEFAULT = BASE + 'assets/models/shoe.glb';
  const cache = {};

  function load(url) {
    if (cache[url]) return cache[url];
    return (cache[url] = new Promise((ok, no) => {
      new THREE.GLTFLoader().load(url, async (g) => {
        const mats = [];
        if (url === DEFAULT) for (let i = 0; i < 3; i++) { try { mats.push(await g.parser.getDependency('material', i)); } catch (e) {} }
        ok({ scene: g.scene, mats });
      }, undefined, no);
    }));
  }

  // Returns { group, size, setVariant(i, tint) }
  async function build(o) {
    o = o || {};
    const d = await load(o.model || DEFAULT);
    const root = d.scene.clone(true);
    const meshes = [];
    root.traverse((n) => { if (n.isMesh) meshes.push(n); });
    function apply(i, tint) {
      meshes.forEach((m) => {
        const base = d.mats[i | 0] || m.material;
        m.material = base.clone();
        if (tint) m.material.color.set(tint);
        m.material.needsUpdate = true;
      });
    }
    if (d.mats.length) apply(o.variant, o.tint);
    root.updateMatrixWorld(true);
    const box = new THREE.Box3().setFromObject(root);
    const size = box.getSize(new THREE.Vector3()), c = box.getCenter(new THREE.Vector3());
    root.position.sub(c);
    const group = new THREE.Group(); group.add(root);
    const k = 3.2 / Math.max(size.x, size.y, size.z);
    group.scale.setScalar(k);
    return { group, size: size.multiplyScalar(k), setVariant: (i, t) => d.mats.length && apply(i, t) };
  }

  function stage(el, opt) {
    opt = opt || {};
    const r = new THREE.WebGLRenderer({ antialias: true, alpha: true, preserveDrawingBuffer: !!opt.keep });
    r.outputEncoding = THREE.sRGBEncoding; r.toneMapping = THREE.ACESFilmicToneMapping; r.toneMappingExposure = 1.05;
    r.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    el.appendChild(r.domElement);
    const scene = new THREE.Scene(), cam = new THREE.PerspectiveCamera(30, 1, 0.1, 100);
    cam.position.set(0, 1.2, 9);
    scene.environment = new THREE.PMREMGenerator(r).fromScene(new THREE.RoomEnvironment(), 0.04).texture;
    const dl = new THREE.DirectionalLight(0xffffff, 0.7); dl.position.set(3, 6, 5); scene.add(dl);
    const rim = new THREE.PointLight(0xff4d12, 1.3, 40); rim.position.set(-5, 2, -3); scene.add(rim);
    const resize = () => { const w = el.clientWidth || 1, h = el.clientHeight || 1; r.setSize(w, h); cam.aspect = w / h; cam.updateProjectionMatrix(); };
    resize(); new ResizeObserver(resize).observe(el);
    let visible = true; if (!opt.always) new IntersectionObserver((e) => { visible = e[0].isIntersecting; }).observe(el);
    const fns = [], clock = new THREE.Clock();
    (function loop() { requestAnimationFrame(loop); if (!visible) return; const dt = Math.min(clock.getDelta(), 0.05); fns.forEach((f) => f(dt)); r.render(scene, cam); })();
    return { r, scene, cam, onFrame: (f) => fns.push(f) };
  }

  function shadow(scene, size) {
    const cv = document.createElement('canvas'); cv.width = cv.height = 128;
    const x = cv.getContext('2d'), g = x.createRadialGradient(64, 64, 4, 64, 64, 64);
    g.addColorStop(0, 'rgba(0,0,0,.32)'); g.addColorStop(1, 'rgba(0,0,0,0)'); x.fillStyle = g; x.fillRect(0, 0, 128, 128);
    const m = new THREE.Mesh(new THREE.PlaneGeometry(1, 1), new THREE.MeshBasicMaterial({ map: new THREE.CanvasTexture(cv), transparent: true, depthWrite: false }));
    m.rotation.x = -Math.PI / 2; m.scale.set(size.x * 1.5, size.x * 0.7, 1); m.position.y = -size.y / 2 - 0.05; scene.add(m); return m;
  }

  // Thumbnails: one shared offscreen renderer, jobs run one after another.
  let T = null, chain = Promise.resolve();
  function thumb(o, px) {
    px = px || 640;
    chain = chain.then(async () => {
      if (!T) {
        const host = document.createElement('div'); host.style.cssText = 'position:fixed;left:-9999px;width:' + px + 'px;height:' + px + 'px';
        document.body.appendChild(host);
        T = stage(host, { keep: true, always: true }); T.r.setPixelRatio(1); T.r.setSize(px, px); T.cam.aspect = 1; T.cam.updateProjectionMatrix();
      }
      const s = await build(o); s.group.rotation.y = -0.65; T.scene.add(s.group);
      T.r.render(T.scene, T.cam); const url = T.r.domElement.toDataURL('image/png'); T.scene.remove(s.group); return url;
    });
    return chain;
  }

  window.ZANP = { load, build, stage, shadow, thumb };
})();
