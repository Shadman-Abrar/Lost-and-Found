document.addEventListener('DOMContentLoaded', () => {
  const form=document.getElementById('fpForm'); if(!form) return;
  const email=form.email; email.addEventListener('input', ()=> email.setCustomValidity(''));
  form.addEventListener('submit', (e)=>{
    if(!email.value.trim()) email.setCustomValidity('Email is required.');
    if(!form.checkValidity()){ e.preventDefault(); form.reportValidity(); return; }
    e.preventDefault(); alert('Reset link sent (demo).'); window.location.href='login.html';
  });
});
