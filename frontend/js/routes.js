const routes = (e) => {
    document.querySelector('.indeterminate-disabled').className = 'indeterminate';
    fetch(e).then(result => result.text())
        .then(result => {
            document.querySelector('#root').innerHTML = result;
        })
        .catch(err => console.log(err));
}

window.addEventListener('popstate', pop => {
    routes(location.pathname);
});

export default routes;
