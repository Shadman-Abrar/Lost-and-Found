// Per-tab session (sessionStorage)
const S = { email: 'lf_email', role: 'lf_role' };

export function getSession(){
  return { email: sessionStorage.getItem(S.email), role: sessionStorage.getItem(S.role) };
}
export function login(email, role){
  sessionStorage.setItem(S.email, email);
  sessionStorage.setItem(S.role, role);
  window.location.href = role === 'admin' ? 'admin-dashboard.html' : 'profile.html';
}
export function logout(){
  sessionStorage.removeItem(S.email);
  sessionStorage.removeItem(S.role);
}
export function requireLogin(){
  const { role } = getSession();
  if(!role) window.location.replace('login.html');
  return role;
}
export function guardUserOnly(){
  const role = requireLogin();
  if(role === 'admin') window.location.replace('admin-dashboard.html');
}
export function guardAdminOnly(){
  const { role } = getSession();
  if(role !== 'admin') window.location.replace('login.html');
}
