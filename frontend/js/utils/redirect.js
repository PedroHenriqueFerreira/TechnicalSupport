import routes from '../routes.js';
import toast from '../utils/toast.js';

export default () => {
  const redirect = document.querySelector('.redirect');
  if(redirect) {
    toast([redirect.getAttribute('toast')]);
    routes(redirect.getAttribute('url'), false, true);
  }
}