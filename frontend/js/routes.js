import isJSON from './utils/isJSON.js';
import toast from './utils/toast.js';
import createElement from './utils/createElement.js';
import renderSlide from './utils/renderSlide.js';

const routes = (e, push = true, replace = false) => { 
    const loader = document.querySelector('.loader-container');
    loader.appendChild(createElement('span', {}));

    fetch(`${e}${e.indexOf('?') !== -1 ? '&' : '?'}reduced=true`).then(result => result.text())
    .then(result => {
        loader.innerHTML = '';
        if(isJSON(result)) {
            toast(JSON.parse(result).errors);
        } else {
            if(push) {
                history.pushState(e, e, e);
            } 
            
            if(replace) {
                history.replaceState(e, e, e);
            }
            
            document.querySelector('#root').insertAdjacentHTML('afterend', result);
            document.querySelector('#root').remove();
            renderSlide();
            scrollTo(0, 0);
        }
        document.title = `ServiTech PC • ${location.pathname}`;
    })
    .catch(err => { 
        loader.innerHTML = '';
        console.error(err);
        toast(['Houve algum erro']);
    });
}

window.addEventListener('popstate', pop => {
    routes(location.pathname, false);
});

export default routes;
