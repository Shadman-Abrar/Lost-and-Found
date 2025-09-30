import { getSession } from './session.js';

document.addEventListener('DOMContentLoaded', () => {
  const form=document.getElementById('profileForm'); if(!form) return;
  const { email } = getSession() || {};
  if(email && !form.email.value) form.email.value = email;

  [form.first, form.last, form.email].forEach(el=> el?.addEventListener('input', ()=> el.setCustomValidity('')));

  form.addEventListener('submit', (e)=>{
    if(!form.first.value.trim()) form.first.setCustomValidity('First name is required.');
    if(!form.last.value.trim())  form.last.setCustomValidity('Last name is required.');
    if(!form.email.value.trim()) form.email.setCustomValidity('Email is required.');
    if(!form.checkValidity()){ e.preventDefault(); form.reportValidity(); return; }
    e.preventDefault();
    const data = Object.fromEntries(new FormData(form).entries());
    localStorage.setItem('lf_profile', JSON.stringify(data));
    sessionStorage.setItem('lf_email', data.email);
    alert('Profile saved.');
  });
});
