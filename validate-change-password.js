// js/validate-change-password.js
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('cpForm');
  if (!form) return;

  const cur=form.current, np=form.next, cf=form.confirm;

  [cur,np,cf].forEach(el=> el?.addEventListener('input', ()=> el.setCustomValidity('')));

  function match(){
    cf.setCustomValidity('');
    if (np.value && cf.value && np.value !== cf.value) {
      cf.setCustomValidity('New passwords do not match.');
      return false;
    }
    return true;
  }
  np.addEventListener('input', match);
  cf.addEventListener('input', match);

  form.addEventListener('submit', (e)=>{
    e.preventDefault();

    if (!cur.value) cur.setCustomValidity('Enter current password.');
    if (!np.value)  np.setCustomValidity('Enter a new password.');
    match();

    if (!form.checkValidity()){
      form.reportValidity();
      return;
    }
    alert('Password updated.');
    window.location.href='profile.html';
  });
});
