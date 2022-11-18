import input, { inputFile, inputMultiple } from '../pages/components/input.js';
import createElement from '../utils/createElement.js';

export default (name, email, password) => {
  const accountContainer = document.querySelector('.account-container');
  
  accountContainer.classList.remove('register');
  accountContainer.classList.add('addicional');
  const form = accountContainer.querySelector('form');
  form.action = '/user/action/register';
  form.setAttribute('page', '/login');
  accountContainer.querySelector('h1').innerText = 'Dados adicionais';
  accountContainer.querySelectorAll('form label').forEach((elem) => elem.remove());
  
  const submitButton = accountContainer.querySelector('form button');
  submitButton.innerText = 'Finalizar';
  
  accountContainer.querySelector('a').remove();
  
  submitButton.insertAdjacentElement('beforebegin', 
  createElement('input', { type: 'hidden', name: 'name', value: name })
  );

  submitButton.insertAdjacentElement('beforebegin', 
  createElement('input', { type: 'hidden', name: 'email', value: email })
  );
  
  submitButton.insertAdjacentElement('beforebegin', 
    createElement('input', { type: 'hidden', name: 'password', value: password })
    );
    
  submitButton.insertAdjacentElement('beforebegin', 
  input('CPF', 'text', 'cpf', { onkeyup: 'mask("###.###.###-##", this, event, true)', maxlength: '14' }, '___.___.___-__')
  );
  
  submitButton.insertAdjacentElement('beforebegin', 
    input('Endereço', 'text', 'address')
  );

  submitButton.insertAdjacentElement('beforebegin', 
  inputMultiple('add_number', '+ Novo número', 'number', 'Número', 'text', '+__ (  ) _____-____', 'number[]', { onkeyup: 'mask("+## (##) #####-####", this, event, true)', maxlength: '19' })
  );

  submitButton.insertAdjacentElement('afterend', createElement('a', { class: 'custom', href: '/register', innerText: 'Voltar' }));

  const imgContainer = accountContainer.querySelector('.img-container');
    
  imgContainer.insertAdjacentElement('afterend', createElement('label', { class: 'img-container profile editable' }, [createElement('input', { type: 'file', class: 'hidden required', name: 'photo', accept: 'image/*' }), createElement('img', { src: '/frontend/assets/profile.svg', alt: '', class: 'img-load' })]));
  
  imgContainer.remove();
}