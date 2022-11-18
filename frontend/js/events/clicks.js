import toast from '../utils/toast.js';
import routes from './../routes.js';
import createElement from './../utils/createElement.js';

const clickEvent = async (e) => {
  const { className, tagName, parentElement, id } = e.target;

  if(tagName === 'A') {
    e.preventDefault();
    
    const href = e.target.getAttribute('href');
    const action = e.target.getAttribute('action');

    if(href === 'back') {
      history.back();
    } else if(href === 'this') {
      routes(location.pathname);
    } else {
      if(action) {
        fetch(action)
          .then((res) => res.json())
          .then((res) => {
            if(res.errors) {
              toast(res.errors);
            } else {
              routes(href);
            }
          })
          .catch((err) => console.error(err));
      } else {
        routes(href);
      }
    }
  }

  if(id === 'add_number') {
    if(document.querySelectorAll('input[name^="number"]').length === 4) {
      toast(['O limite de números foi atingo']);
      return;
    }

    const remove = createElement('span', { class: 'remove', id: 'remove_number' });
    const input = createElement('input', { class: 'custom no-label', type: 'text', placeholder: '+__ (  ) _____-____', name: 'number[]', onkeyup: 'mask("+## (##) #####-####", this, event, true)', maxlength: '19' });
    const span = createElement('span', {});
    const label = createElement('div', { class: 'input-block' }, [remove, input, span]);
    
    document.querySelector('button').insertAdjacentElement('beforebegin', label);
  }

  if(id === 'remove_number') {
    const ElemId = parseInt(parentElement.getAttribute('code'));

    if(ElemId) {
      parentElement.parentElement.insertAdjacentElement('afterbegin', createElement('input', { type: 'hidden', name: 'deleted_number[]', value: ElemId }));
    }

    parentElement.remove();
  }

  if(e.target.getAttribute('class') === 'delete_image') {
    if (document.querySelector('.account-container.equipment.update')) {
      const deletedCode = e.target.parentElement.parentElement.querySelector('input');

      if(deletedCode) {
        document.querySelector('form').insertAdjacentElement('afterbegin', createElement('input', { type: 'hidden', name: 'deleted_photo[]', value: deletedCode.getAttribute('code') }));
      }
    }

    e.target.parentElement.parentElement.remove();

    const imgName = document.querySelector('.input_file_visible');
    const imagesBlock = document.querySelector('.img-block div');

    const filesName = [];

    imagesBlock.querySelectorAll('.img-view .label').forEach((imageView) => {
      filesName.push(imageView.getAttribute('complete'));
    });

    imgName.innerText = filesName.join(', ');

    if(!imgName.innerText) {
      imgName.innerText = 'Escolher fotos';
    }
  }

  if(className === 'tr') {
    routes(e.target.getAttribute('href'));
  }
}

export default clickEvent;
