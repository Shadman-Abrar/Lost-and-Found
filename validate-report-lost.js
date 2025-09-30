import { KEYS, addItem } from './store-items.js';
import { getSession } from './session.js';

document.addEventListener('DOMContentLoaded', () => {
  const form=document.getElementById('lostForm'); if(!form) return;
  const f={ title:form.title, category:form.category, location:form.location, date:form.date, description:form.description };
  Object.values(f).forEach(el=> el?.addEventListener('input', ()=> el.setCustomValidity('')));

  const fileIn=document.getElementById('lostPhoto'); const prev=document.getElementById('lostPreview');
  fileIn?.addEventListener('change', ()=>{
    const file=fileIn.files && fileIn.files;
    if(!file){ prev.src=''; prev.style.display='none'; return; }
    const r=new FileReader(); r.onload=e=>{ prev.src=e.target.result; prev.style.display='block'; }; r.readAsDataURL(file);
  });

  form.addEventListener('submit', (e)=>{
    if(!f.title.value.trim())       f.title.setCustomValidity('Please name the item.');
    if(!f.category.value)           f.category.setCustomValidity('Pick a category.');
    if(!f.location.value.trim())    f.location.setCustomValidity('Where was it lost?');
    if(!f.date.value)               f.date.setCustomValidity('Select the date.');
    if(!f.description.value.trim()) f.description.setCustomValidity('Add a short description.');
    if(!form.checkValidity()){ e.preventDefault(); form.reportValidity(); return; }

    e.preventDefault();
    const { email } = getSession() || {};
    addItem(KEYS.LOST, {
      id:'L'+Date.now(),
      owner: email || 'me@example.com',
      title:f.title.value.trim(),
      category:f.category.value,
      location:f.location.value.trim(),
      date:f.date.value,
      description:f.description.value.trim(),
      photo: prev?.src || ''
    });
    alert('Lost item reported.'); window.location.href='user-dashboard.html';
  });
});
