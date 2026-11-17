const base = 'http://localhost:8000/community-skill-exchange/gateway/index.php';
const jar = {};

function setCookieFromHeaders(headers) {
  const raw = headers.get('set-cookie');
  if (!raw) return;
  const value = raw.split(';')[0];
  const [name, ...rest] = value.split('=');
  jar[name] = rest.join('=');
}

function cookieHeader() {
  return Object.entries(jar).map(([k, v]) => k + '=' + v).join('; ');
}

async function call(path, options = {}) {
  const res = await fetch(base + path, {
    ...options,
    headers: {
      ...(options.headers || {}),
      ...(Object.keys(jar).length ? { Cookie: cookieHeader() } : {})
    }
  });
  setCookieFromHeaders(res.headers);
  const text = await res.text();
  let data;
  try { data = JSON.parse(text); } catch { data = text; }
  console.log('PATH', path, 'STATUS', res.status);
  console.log(JSON.stringify(data, null, 2));
  return { res, data };
}

(async () => {
  console.log('--- login alice');
  await call('/api/users/login', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ email: 'alice@example.com', password: 'password123' }) });
  console.log('--- profile');
  await call('/api/users/profile');
  console.log('--- matches');
  await call('/api/matches');
  console.log('--- skills');
  await call('/api/skills');
  console.log('--- request exchange');
  await call('/api/exchanges/request', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ receiver_id: 2, offered_skill_id: 1, requested_skill_id: 2, message: 'Testing exchange request' }) });
  console.log('--- exchange list');
  await call('/api/exchanges');

  Object.keys(jar).forEach((k) => delete jar[k]);

  console.log('--- login admin');
  await call('/api/users/login', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ email: 'admin@community.test', password: 'admin123' }) });
  console.log('--- admin dashboard');
  await call('/api/reports/dashboard');
  console.log('--- admin members');
  await call('/api/admin/members');
})().catch((err) => {
  console.error(err);
  process.exit(1);
});
