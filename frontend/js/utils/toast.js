import createElement from './createElement.js';

export default (data) => {
  if(Array.isArray(data)) {
    data.forEach((message, idx) => {
      setTimeout(() => {
        const toast = createElement('div', { class: 'toast' });
        toast.innerText = message;
        document.querySelector('.toast-container').appendChild(toast);
      
        setTimeout(() => {
          toast.remove();
        }, (idx * 200) + 3000);
      }, idx * 250);
    });
  } else {
    setTimeout(() => {
      const toast = createElement('div', { class: 'toast' });
      toast.innerText = data;
      document.querySelector('.toast-container').appendChild(toast);
    
      setTimeout(() => {
        toast.remove();
      }, (idx * 200) + 3000);
    }, idx * 250);
  }
}