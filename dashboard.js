import { getSession } from './session.js';
import { KEYS, readList } from './store-items.js';

document.addEventListener('DOMContentLoaded', () => {
  const { email } = getSession() || {};
  const lostAll = readList(KEYS.LOST), foundAll = readList(KEYS.FOUND);
  const myLost = lostAll.filter(x=>x.owner===email), myFound = foundAll.filter(x=>x.owner===email);

  const cLost=document.getElementById('cLost'), cFound=document.getElementById('cFound'), notif=document.getElementById('notif');
  if(cLost) cLost.textContent=myLost.length;
  if(cFound) cFound.textContent=myFound.length;

  let hits=0;
  myLost.forEach(l=>{
    const tl=(l.title||'').toLowerCase();
    if(tl && foundAll.some(f=>(f.title||'').toLowerCase().includes(tl))) hits++;
  });
  if(notif) notif.textContent=hits;

  const UL=document.getElementById('listLost'), UF=document.getElementById('listFound');
  if(UL) UL.innerHTML = myLost.slice(-5).reverse().map(i=>`<li>${i.date} • ${i.title} • ${i.location}</li>`).join('') || '<em>No lost reports yet.</em>';
  if(UF) UF.innerHTML = myFound.slice(-5).reverse().map(i=>`<li>${i.date} • ${i.title} • ${i.location}</li>`).join('') || '<em>No found reports yet.</em>';
});
