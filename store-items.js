export const KEYS = { LOST: 'lf_lost_items', FOUND: 'lf_found_items' };
export function readList(key){ try{ return JSON.parse(localStorage.getItem(key)) || []; } catch { return []; } }
export function writeList(key, list){ localStorage.setItem(key, JSON.stringify(list)); }
export function addItem(key, item){ const list = readList(key); list.push(item); writeList(key, list); }
