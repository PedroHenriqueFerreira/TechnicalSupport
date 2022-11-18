import createElement from '../utils/createElement.js';
import input from './components/input.js';

export default (form) => {
  form.action = '/user/action/update';
  form.setAttribute('page', '/profile');

  form.querySelector('h1').innerText = 'Editar perfil';

  form.querySelectorAll('.input-block input').forEach((elem) => {
    elem.removeAttribute('readonly');
    
    if(elem.name === 'address') {
      const pass = input('Senha atual', 'password', 'password');
      const newPass = input('Senha nova', 'password', 'new_password');
      elem.parentElement.insertAdjacentElement('afterend', createElement('div', { class: 'row passwords' }, [pass, newPass]));
    }
  });
      
  
  form.querySelector('#number').parentElement.insertAdjacentElement('afterbegin', createElement('span', { class: 'add', id: 'add_number', innerText: '+ Novo número'  }));
  
  form.querySelectorAll('.custom.no-label').forEach((elem) => {
    elem.insertAdjacentElement('beforebegin', createElement('span', { class: 'remove', id: 'remove_number' }));
  });
  
  const button = form.querySelector('button');
  button.innerText = 'Salvar';
  button.insertAdjacentElement('afterend', createElement('a', { class: 'custom', href: '/profile', innerText: 'Voltar' }));

  const src = form.nextElementSibling.querySelector('img').src;
  form.nextElementSibling.remove();

  form.insertAdjacentElement('afterend', createElement('label', { class: 'img-container profile editable' }, [createElement('input', { type: 'file', class: 'hidden', name: 'photo', accept: 'image/*' }), createElement('img', { src: src, alt: '', class: 'img-load' })]));
  
  document.querySelector('#delete_account').remove();
}