import { login } from './session.js';
import { verifyAccount } from './accounts.js';

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('loginForm'); if(!form) return;
  const email = form.email, role = document.getElementById('role'), pwd = form.password;

  [email, role, pwd].forEach(el => el?.addEventListener('input', ()=> el.setCustomValidity('')));

  form.addEventListener('submit', (e)=>{
    e.preventDefault(); // always stop native submit

    if(!email.value.trim()) email.setCustomValidity('Email is required.');
    if(!role.value)         role.setCustomValidity('Please choose a role.');
    if(!pwd.value.trim())   pwd.setCustomValidity('Password is required.');

    if(!form.checkValidity()){
      form.reportValidity();
      return;
    }

    if(role.value === 'user'){
      const ok = verifyAccount(email.value.trim(), pwd.value);
      if(!ok){
        pwd.setCustomValidity('Email or password is incorrect, or account not registered.');
        form.reportValidity();
        pwd.setCustomValidity('');
        return;
      }
    }

    login(email.value.trim(), role.value);
  });
});
