import createElement from '../utils/createElement.js';

export default (form) => {
  form.action = `/equipment/action/${form.querySelector('input[name="code"]').value}/update`;

  [
    ...form.querySelectorAll('.input-block input'), 
    ...form.querySelectorAll('.input-block textarea')
  ].forEach((elem) => {
    elem.removeAttribute('readonly');
  });

  const fileInput = form.querySelector('.input-block.file.disabled');

  fileInput.classList.remove('disabled');
  fileInput.appendChild(createElement('span', { class: 'input_file_visible', innerText: 'Escolher fotos' }));
  fileInput.appendChild(createElement('input', { class: 'custom multiple', type: 'file', multiple: 'true', accept: 'image/*' }));
  fileInput.appendChild(createElement('span', { }));

  form.querySelectorAll('svg').forEach((elem) => {
    elem.style.display = 'block';
  });

  form.insertAdjacentElement('beforeend', createElement('a', { href: 'this', class: 'custom', innerText: 'Voltar' }));

  form.querySelector('button').innerText = 'Finalizar';
}