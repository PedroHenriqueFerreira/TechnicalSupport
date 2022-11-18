import createElement from '../../utils/createElement.js';

export default (label, type, name, rest = {}, placeholder = '') => {
  const labelElem = createElement('span', { class: 'label', innerText: label });
  const input = createElement('input', { type, name, class: 'custom', ...rest, placeholder });
  const span = createElement('span', {});

  return createElement('label', { class: 'input-block' }, [labelElem, input, span]);
}

export const inputFile = (label, fileLabel, name, src) => {
  const labelElem = createElement('span', { class: 'label', innerText: label });
  const fileLabelElem = createElement('span', { class: 'input_file_visible', innerText: fileLabel });
  const input = createElement('input', { type: 'file', class: 'custom', accept: 'image/*', name });
  const span = createElement('span', {});
  const img = createElement('img', { src, alt: '', class: 'profile img-load' });

  return createElement('label', { class: 'input-block file' }, [labelElem, fileLabelElem, input, span, img]);
}

export const inputMultiple = (addId, addLabel, id, label, type, placeholder, name, rest = {}) => {
  
  const addLabelElem = createElement('span', { class: 'add', id: addId, innerText: addLabel  });
  const labelElem = createElement('label', { class: 'label', for: id, innerText: label });
  const input = createElement('input', { class: 'custom', id, type, placeholder, name, ...rest });
  const span = createElement('span', {});

  return createElement('div', { class: 'input-block' }, [addLabelElem, labelElem, input, span]);
}

export const imgView = async () => {
  const fetchedImg = await fetch('/frontend/assets/refused.svg');
  const svgElem = await fetchedImg.text();
  
  return function(label, completeLabel, img) {
    const inputElem = createElement('input', { type: 'hidden', name: 'photo[]', value: img, class: 'disabled', accept: 'image/*' });
    const spanElem = createElement('span', { class: 'label', innerText: label, complete: completeLabel });
    const imgElem = createElement('img', { src: img, alt: '' });

    return createElement('div', { class: 'img-view' }, [inputElem, spanElem, svgElem, imgElem]);
  } 
}