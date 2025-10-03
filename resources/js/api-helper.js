// Simple cache-first helper for GET endpoints with localStorage TTL to reduce bursts
const LS_KEY = "apiCache.v1";
const store = {
  getAll() {
    try { return JSON.parse(localStorage.getItem(LS_KEY) || "{}") || {}; } catch { return {}; }
  },
  setAll(all) {
    try { localStorage.setItem(LS_KEY, JSON.stringify(all)); } catch {}
  },
  get(key) { return this.getAll()[key]; },
  set(key, val) { const all = this.getAll(); all[key] = val; this.setAll(all); },
  del(key) { const all = this.getAll(); delete all[key]; this.setAll(all); }
};

export async function getCached(url, { ttlMs = 2 * 60 * 1000, force = false, mapper } = {}) {
  const now = Date.now();
  const k = `GET:${url}`;
  const cached = store.get(k);
  if (!force && cached && (now - (cached.time || 0) < ttlMs) && cached.data != null) {
    // background revalidation
    try { fetch(url, { credentials: 'same-origin' }).then(r => r.json()).then(d => {
      if (mapper) try { d = mapper(d); } catch {}
      store.set(k, { time: Date.now(), data: d });
    }).catch(() => {}); } catch {}
    return cached.data;
  }
  const res = await fetch(url, { credentials: 'same-origin' });
  if (!res.ok) throw new Error(await res.text().catch(() => res.statusText));
  let data = await res.json();
  if (mapper) try { data = mapper(data); } catch {}
  store.set(k, { time: Date.now(), data });
  return data;
}

export function invalidate(url) { store.del(`GET:${url}`); }
