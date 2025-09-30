const KEY = 'lf_accounts';
export function readAccounts(){ try{ return JSON.parse(localStorage.getItem(KEY)) || []; }catch{ return []; } }
export function writeAccounts(list){ localStorage.setItem(KEY, JSON.stringify(list)); }
export function addAccount({email, first, last, password}){
  const list = readAccounts();
  const ix = list.findIndex(a => a.email.toLowerCase() === email.toLowerCase());
  if (ix >= 0) list.splice(ix, 1);
  list.push({ email, first, last, password });
  writeAccounts(list);
}
