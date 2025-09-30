// js/validate-register.js
import { addAccount } from './accounts.js';

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('regForm');
  if (!form) return;

  const first=form.first, last=form.last, email=form.email, p1=form.password, p2=form.confirm, agree=form.agree;

  // Clear custom errors on input
  [first,last,email,p1,p2,agree].forEach(el=> el?.addEventListener('input', ()=> el.setCustomValidity('')));

  // Only ensure confirm matches password
  function match(){
    p2.setCustomValidity('');
    if (p1.value && p2.value && p1.value !== p2.value) {
      p2.setCustomValidity('Passwords do not match.');
      return false;
    }
    return true;
  }
  p1.addEventListener('input', match);
  p2.addEventListener('input', match);

  form.addEventListener('submit', (e)=>{
    e.preventDefault();

    // Simple presence checks; minlength is handled by browser
    if (!first.value.trim()) first.setCustomValidity('First name is required.');
    if (!last.value.trim())  last.setCustomValidity('Last name is required.');
    if (!email.value.trim()) email.setCustomValidity('Email is required.');
    if (!p1.value)           p1.setCustomValidity('Password is required.');
    if (!agree.checked)      agree.setCustomValidity('Please accept the terms.');

    match();

    if (!form.checkValidity()){
      form.reportValidity();
      return;
    }

    // Save demo account and redirect to login with prefilled email
    addAccount({
      email: email.value.trim(),
      first: first.value.trim(),
      last:  last.value.trim(),
      password: p1.value
    });

    alert('Account created. Please log in.');
    const url = new URL('login.html', window.location.href);
    url.searchParams.set('email', email.value.trim());
    window.location.href = url.toString();
  });
});
