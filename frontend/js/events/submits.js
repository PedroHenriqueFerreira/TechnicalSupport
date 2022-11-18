import * as checkErrors from './../utils/checkErrors.js';
import toast from './../utils/toast.js';
import addicional from '../pages/addicional.js';
import userUpdate from '../pages/userUpdate.js';
import equipmentUpdate from '../pages/equipmentUpdate.js';
import routes from '../routes.js';
import dataURItoBlob from '../utils/dataURItoBlob.js';
import createElement from '../utils/createElement.js';

const submitEvent = e => {
    e.preventDefault();
    
    const loader = document.querySelector('.loader-container');
    loader.appendChild(createElement('span', {}));

    const method = e.target.getAttribute('method');
    const action = e.target.getAttribute('action');
    const page = e.target.getAttribute('page');
    
    const errors = [];
    const body = new FormData();

    let stop = true;

    switch(action) {
        case '/user/update':
            userUpdate(e.target);
            break;
        case '/equipment/update':
            equipmentUpdate(e.target);
            break;
        default:
            stop = false;           
    }

    if(stop) {
        scrollTo(0, 0);
        loader.innerHTML = '';
        return;
    }

    [
        ...e.target.parentElement.querySelectorAll('input'), 
        ...e.target.parentElement.querySelectorAll('textarea')
    ].forEach((elem) => {
        let value = elem.value;
        let label = elem.parentElement.querySelector('.label');
        let append = true;

        if(label) {
            label = label.innerText.toLowerCase();
        } else {
            label = '';
        }
        
        if(elem.type !== 'hidden') {
            switch(label) {
                case 'nome':
                    checkErrors.name(value, errors);
                    break;
                case 'email':
                    checkErrors.email(value, errors);
                    break;
                case 'senha':
                    checkErrors.password(value, errors);
                    break;
                case 'senha atual':
                    checkErrors.currentPassword(value, errors);
                    break;
                case 'senha nova':
                    checkErrors.newPassword(value, errors);
                    break;
                case 'confirmar senha':
                    checkErrors.confirmPass(value, errors);
                    break;
                case 'lembrar de mim': 
                    value = elem.checked;
                    break;
                case 'perfil':
                    value = elem.files ? elem.files[0] : '';
                    checkErrors.image(value, errors);
                    break;
                case 'cpf':
                    checkErrors.cpf(value, errors);
                    break;
                case 'endereço':
                    checkErrors.address(value, errors);
                    break;
                case 'descrição': 
                    checkErrors.description(value, errors);
                    break;
                case 'número':
                    checkErrors.number(errors);
                    break;
                case 'especificações':
                    checkErrors.specifications(value, errors);
                    break;
                case 'relatório':
                    checkErrors.report(value, errors);
                    break;
                case 'custo':
                    checkErrors.cost(value, errors);
                    break;
                default:
                    if(elem.type === 'file' && !elem.className.match('disabled')) {
                        value = elem.files ? elem.files[0] : '';
                        
                        if(elem.className.match('required')) {
                            checkErrors.image(value, errors);
                        } else if (elem.className.match('multiple')) {
                            const fileErrors = checkErrors.images(errors);
                            if(!fileErrors) {
                                document.querySelectorAll('.img-block .img-view input').forEach((imgView) => {
                                    if(imgView.getAttribute('value')) {
                                        const file = dataURItoBlob(imgView.getAttribute('value'));
                                        body.append(imgView.getAttribute('name'), file, imgView.nextElementSibling.getAttribute('complete'));
                                    }
                                });
                            }
                            append = false;
                        } else if (!value) {
                            append = false;
                        }
                    }

                    if(elem.type === 'radio') {
                        if(elem.getAttribute('main')) {
                            checkErrors.radio(errors);
                        }

                        if(!elem.checked) {
                            append = false;
                        }
                    }
                    break;
            }
        }

        if(append) {
            body.append(elem.name, value);
        }
    });

    if(errors.length > 0) {
        loader.innerHTML = '';
        toast(errors);
    } else {
        fetch(action, {
            method,
            body,
        }).then((res) => res.json())
        .then((res) => {
            loader.innerHTML = '';
            if(res.errors) {
                return toast(res.errors);
            } else if(res.success) {
                toast([res.success]);
            }

            switch(action) {
                case '/user/action/check/register':
                    addicional(body.get('name'), body.get('email'), body.get('password'));
                    break;
                default:
                    routes(page);
                    break;
            }
        }).catch((err) => {
            loader.innerHTML = '';
            console.error(err);
            toast(['Houve algum erro']);
        });
    }
}

export default submitEvent;
