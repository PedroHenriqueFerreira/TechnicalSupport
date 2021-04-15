import routes from './../routes.js';

const clickEvent = e => {
  e.preventDefault();

  if(e.target.tagName === 'A') {
    routes(e.target.getAttribute('href'));
  }
}

export default clickEvent;
